<?php

namespace App\Repositories\Eloquent;

use App\Models\Rental;
use App\Models\Book;
use App\Models\RentalReturn;
use App\Repositories\Interfaces\RentalRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Traits\CalculateRentalFee;

class RentalRepositoryEloquent extends BaseRepository implements RentalRepository
{
    use CalculateRentalFee;
    public function model()
    {
        return Rental::class;
    }

    /**
     * Tìm kiếm theo các điều kiện với phân trang
     *
     * @param array $conditions
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchRentals(array $conditions, $perPage = 10)
    {
        $query = Rental::query();

        // Tìm theo tên người dùng (nếu có)
        if (!empty($conditions['username'])) {
            $query->whereHas('user', function ($q) use ($conditions) {
                $q->where('name', 'like', '%' . $conditions['username'] . '%');
            });
        }

        // Tìm theo trạng thái đơn (nếu có)
        if (!empty($conditions['status'])) {
            $query->where('status', $conditions['status']);
        }

        // Tìm theo ngày thuê (nếu có)
        if (!empty($conditions['rental_date_from'])) {
            $query->whereDate('rental_date', '>=', $conditions['rental_date_from']);
        }
        if (!empty($conditions['rental_date_to'])) {
            $query->whereDate('rental_date', '<=', $conditions['rental_date_to']);
        }

        // Tìm theo ngày hết hạn (nếu có)
        if (!empty($conditions['due_date_from'])) {
            $query->whereDate('due_date', '>=', $conditions['due_date_from']);
        }
        if (!empty($conditions['due_date_to'])) {
            $query->whereDate('due_date', '<=', $conditions['due_date_to']);
        }

        // Phân trang kết quả
        return $query->paginate($perPage);
    }

    public function returnBooks(string $rentalId, array $returnData){
        DB::beginTransaction();

        try {
            $rental = Rental::findOrFail($rentalId);

            if ($rental->status === 'returned') {
                throw new \Exception('Đơn thuê đã được trả trước đó.');
            }

            $preReturnResult = $this->preReturn($rentalId, $returnData);
        
            if (!$preReturnResult['success']) {
                throw new \Exception($preReturnResult['message']);
            }

            $feeBreakdown = $preReturnResult['fee_breakdown'];
            $condition = $preReturnResult['overall_condition'];

            foreach ($returnData as $item) {
                if ($item['status'] !== 'lost') {
                    $book = Book::find($item['book_id']);
                    if ($book) {
                        $book->quantity += 1;
                        $book->save();
                    }
                }
            }

            
            RentalReturn::create([
                'rental_id' => $rental->id,
                'return_date' => now()->toDateString(),
                'condition' => $condition,
                'refund_amount' => $feeBreakdown['refund_amount'],
                'penalty_fee' => $feeBreakdown['penalty_fee'],
                'late_fee' => $feeBreakdown['late_fee'],
            ]);
            DB::commit();
            $rental->status = 'returned';
            $rental->save();
            
            return [
                'success' => true,
                'message' => 'Đơn thuê đã được trả thành công.',
                'refund_amount' => $feeBreakdown['refund_amount'],
                'late_fee' => $feeBreakdown['late_fee'],
                'penalty_fee' => $feeBreakdown['penalty_fee'],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Lỗi khi trả sách: ' . $e->getMessage());
        }
    }

    public function preReturn(string $rentalId, array $returnData)
    {
        try {
            $rental = Rental::findOrFail($rentalId);
            
            if ($rental->status === 'returned') {
                throw new \Exception('Đơn thuê đã được trả trước đó.');
            }

            $lateFee = $this->calculateLateFee($rental->due_date, now()->toDateString());
            $rentalFee = $rental->rental_fee;
            
            $penaltyFee = 0;
            $hasLost = false;
            $hasDamaged = false;
            $booksDetail = [];

            foreach ($returnData as $item) {
                $bookId = $item['book_id'];
                $status = $item['status'];
                $book = Book::find($bookId);

                $bookDetail = [
                    'book_id' => $bookId,
                    'book_name' => $book ? $book->title : 'Không tìm thấy sách',
                    'status' => $status,
                    'penalty_amount' => 0
                ];

                if ($status === 'damaged') {
                    $bookDetail['penalty_amount'] = $rental->deposit / 2;
                    $penaltyFee += ($rental->deposit / 2);
                    $hasDamaged = true;
                }

                if ($status === 'lost') {
                    $bookDetail['penalty_amount'] = $rental->deposit;
                    $penaltyFee += $rental->deposit;
                    $hasLost = true;
                }

                $booksDetail[] = $bookDetail;
            }

            $totalDeductions = $lateFee + $rentalFee + $penaltyFee;
            $refundAmount = $rental->deposit - $totalDeductions;

            $condition = $hasLost ? 'lost' : ($hasDamaged ? 'damaged' : 'good');

            return [
                'success' => true,
                'rental_info' => [
                    'rental_id' => $rental->id,
                    'user_name' => $rental->user->name ?? 'N/A',
                    'rental_date' => $rental->rental_date,
                    'due_date' => $rental->due_date,
                    'deposit' => $rental->deposit,
                    'rental_fee' => $rental->rental_fee,
                ],
                'fee_breakdown' => [
                    'late_fee' => $lateFee,
                    'rental_fee' => $rentalFee,
                    'penalty_fee' => $penaltyFee,
                    'total_deductions' => $totalDeductions,
                    'refund_amount' => $refundAmount,
                ],
                'books_detail' => $booksDetail,
                'overall_condition' => $condition,
                'return_date' => now()->toDateString(),
                'is_overdue' => $lateFee > 0,
                'has_penalties' => $penaltyFee > 0,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi xem trước thông tin trả sách: ' . $e->getMessage()
            ];
        }
    }

    public function extendRental(string $rentalId, int $extendDays){
        $rental = Rental::findOrFail($rentalId);
        $rental->due_date = Carbon::parse($rental->due_date)->addDays($extendDays);
        $rental->save();
        return $rental;
    }
}
