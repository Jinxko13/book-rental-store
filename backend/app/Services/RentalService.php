<?php

namespace App\Services;

use App\Enums\RangeEnum;
use App\Models\Book;
use App\Models\Rental;
use App\Models\RentalReturn;
use App\Repositories\Interfaces\RentalRepository;
use App\Repositories\Interfaces\RentalDetailRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
class RentalService
{
    protected $rentalRepository;
    protected $rentalDetailRepository;

    public function __construct(RentalRepository $rentalRepository, RentalDetailRepository $rentalDetailRepository)
    {
        $this->rentalRepository = $rentalRepository;
        $this->rentalDetailRepository = $rentalDetailRepository;
    }

    public function createRental($data)
    {
        $totalDeposit = 0;
        foreach ($data['books'] as $book) {
            $bookDetails = Book::findOrFail($book['book_id']);
            $totalDeposit += $book['quantity'] * $bookDetails->price;
        }
        $data['deposit'] = $totalDeposit;
        DB::beginTransaction();
        try {
            // Tạo đơn thuê
            $dataToInsert = [
                'user_id' => $data['user_id'],
                'rental_date' => $data['rental_date'],
                'due_date' => $data['due_date'],
                'status' => 'renting',
                'deposit' => $data['deposit'],
                'rental_fee' => $data['rental_fee'],
                'paid' => $data['paid'],
                'refund_amount' => 0,
            ];

            if (!empty($data['discount'])) {
                $dataToInsert['discount_id'] = $data['discount'];
            }
            
            $rental = $this->rentalRepository->create($dataToInsert);

            // Tạo rental details
            foreach ($data['books'] as $book) {
                $bookDetails = Book::findOrFail($book['book_id']);

                if ($bookDetails->quantity < $book['quantity']) {
                    throw new \Exception('Số lượng sách không đủ để thuê.');
                }

                $bookDetails->decrement('quantity', $book['quantity']);

                $this->rentalDetailRepository->create([
                    'rental_id' => $rental->id,
                    'book_id' => $book['book_id'],
                    'quantity' => $book['quantity'],
                    'book_price' => $bookDetails->price,
                ]);
            }

            DB::commit();

            return $rental;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getStatistics($date,$range)
    {
        $statistics = [];
        $date = Carbon::parse($date);
        $dateStart = $date->copy()->startOfDay()->timezone('UTC');
        $dateEnd = $date->copy()->endOfDay()->timezone('UTC');
        if($range == RangeEnum::WEEK){
            $dateStart = $date->copy()->startOfWeek()->timezone('UTC');
            $dateEnd = $date->copy()->endOfWeek()->timezone('UTC');
        }
        if($range == RangeEnum::MONTH){
            $dateStart = $date->copy()->startOfMonth()->timezone('UTC');
            $dateEnd = $date->copy()->endOfMonth()->timezone('UTC');
        }
        $total = Rental::query()->whereBetween('created_at', [$dateStart,$dateEnd])->get()->count();
        $statistics['total_rentals'] = $total;
        $statistics['total_users'] = Rental::query()->whereBetween('created_at', [$dateStart,$dateEnd])->distinct('user_id')->count('user_id');
        $bookList = [];
        $rentalList = Rental::query()->whereBetween('created_at', [$dateStart,$dateEnd])->get();
        $Revenue = 0;
        foreach ($rentalList as $rental) {
            $rentalDetailList = $rental->rentalDetails;
            foreach ($rentalDetailList as $rentalDetail) {
                $title = $rentalDetail->book->title;
                if (!isset($bookList[$title])) {
                    $bookList[$title] = 0;
                }
                $bookList[$title] += 1;
            }
        }
        $rentalReturnList = RentalReturn::query()->whereBetween('created_at', [$dateStart,$dateEnd])->get();
        foreach ($rentalReturnList as $rentalReturn) {
            $totalPrice = $rentalReturn->rental_fee + $rentalReturn->rental->deposit;
            $Revenue += $totalPrice - $rentalReturn->refund_amount;
        }
        arsort($bookList);
        $mostRentedBookTitle = !empty($BookList) ? array_key_first($BookList) : "khong co luot muon";
        $statistics['top_books'] = $mostRentedBookTitle;
        $statistics['revenue'] = $Revenue;
        return $statistics;
    }

    public function getRentals($page, $limit, $search, $status) {
        // Build query with filters
        $query = $this->rentalRepository->with('user')->with('rentalDetails.book');
    
        // Apply search filter if provided
        if (!empty($search)) {
            $query = $query->where(function($q) use ($search) {
                  $q->whereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter if provided
        if (!empty($status)) {
            $query = $query->where('status', 'like', "%{$status}%");
        }

        // Get paginated results
        $rentals = $query->orderBy('created_at', 'desc')
                        ->paginate($limit);

        return $rentals;
    }

    public function deleteRentalOrder($id){
        return $this->rentalRepository->delete($id);
    }

    public function preReturn(string $rentalId, array $returnData){
        return $this->rentalRepository->preReturn($rentalId, $returnData);
    }

    public function returnBooks(string $rentalId, array $returnData){
        try {
            return $this->rentalRepository->returnBooks($rentalId, $returnData);
        } catch (\Exception $e) {
            return response()->json(['error' => "Đã xảy ra lỗi khi trả sách"], 500);
        }
    }

    public function extendRental(string $rentalId, int $extendDays){
        try {
            return $this->rentalRepository->extendRental($rentalId, $extendDays);
        } catch (\Exception $e) {
            return response()->json(['error' => "Đã xảy ra lỗi khi gia hạn đơn thuê"], 500);
        }
    }

    public function cancelRentalOrder($id)
    {
        $rental = $this->rentalRepository->find($id);
        if (auth()->user()->role === 'customer'){
            if ($rental->user->id != auth()->user()->id){
                throw new \Exception("Bạn không có quyền hủy đơn thuê này.");
            }
        }
        if (!$rental) {
            throw new \Exception("Đơn thuê không tồn tại.");
        }

        if ($rental->status === 'cancel') {
            throw new \Exception("Đơn thuê đã được hủy.");
        }

        if ($rental->status === 'return') {
            throw new \Exception("Đơn thuê đã được trả.");
        }

        $rental->status = 'cancel';
        $rental->save();
        return [
            "rental" => $rental,
            "message" => "Đơn thuê đã được hủy.",
        ];
    }
}