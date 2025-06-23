<?php

namespace App\Http\Controllers;

use App\Enums\RangeEnum;
use App\Exports\RentalExport;
use App\Jobs\SendReportEmailJob;
use App\Mail\SendReport;
use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use App\Repositories\Interfaces\RentalRepository;
use App\Services\RentalService;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\RentalReminderMail;

use App\Traits\CalculateRentalFee;

class RentalController extends Controller
{
    use CalculateRentalFee;

    protected $rentalService;
    protected $discountService;

    public function __construct(RentalService $rentalService, DiscountService $discountService)
    {
        $this->rentalService = $rentalService;
        $this->discountService = $discountService;
    }

    public function index(Request $request){
        
        try {
            // Get pagination parameters
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $search = $request->query('search', '');
            $status = $request->query('status', '');
            $rentals = $this->rentalService->getRentals($page, $limit, $search, $status);
            
            return response()->json([
                'success' => true,
                'data' => $rentals->items(),
                'pagination' => [
                    'total' => $rentals->total(),
                    'current_page' => $rentals->currentPage(),
                    'per_page' => $rentals->perPage(),
                    'last_page' => $rentals->lastPage(),
                    'from' => $rentals->firstItem(),
                    'to' => $rentals->lastItem()
                ]
                ],200);

        } catch (\Exception $e) {
            \Log::error('Error fetching rentals: ' . $e->getMessage(), [
                'request_params' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu thuê',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'rental_date' => 'required|date',
            'due_date' => 'required|date',
            'books' => 'required|array',
            'deposit' => 'required|numeric',
            'paid' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
        ]);
        $validatedData['user_id'] = auth()->user()->id;

        if($validatedData['discount']){
            $validatedData['rental_fee'] = $this->calculateRentalFee($validatedData['rental_date'], $validatedData['due_date']) * (1 - $this->discountService->apply($validatedData['discount']) / 100);
        } else {
            $validatedData['rental_fee'] = $this->calculateRentalFee($validatedData['rental_date'], $validatedData['due_date']);
        }

        try {
            $rental = $this->rentalService->createRental($validatedData);
            return response()->json($rental, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function preReturn(Request $request, string $rentalId){
        try {
            $validatedData = $request->validate([
                'book_conditions' => 'required|array',
            ]);
            $response = $this->rentalService->preReturn($rentalId, $validatedData['book_conditions']);
            return response()->json($response,200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'thông tin trả sách không hợp lệ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function returnRental(Request $request, $rentalId)
    {
        try {
            $validatedData = $request->validate([
                'book_conditions' => 'required|array',
            ]);
            $result = $this->rentalService->returnBooks($rentalId, $validatedData['book_conditions']);
            return response()->json([
                'success' => true,
                'message' => 'Trả sách thành công',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trả sách thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOverdueRentals(Request $request)
    {
        try{
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $search = $request->query('search', '');
            $status = 'overdue';
            $rentals = $this->rentalService->getRentals($page, $limit, $search, $status);
                
            return response()->json([
                'success' => true,
                'data' => $rentals->items(),
                'pagination' => [
                    'total' => $rentals->total(),
                    'current_page' => $rentals->currentPage(),
                    'per_page' => $rentals->perPage(),
                    'last_page' => $rentals->lastPage(),
                    'from' => $rentals->firstItem(),
                    'to' => $rentals->lastItem()
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching overdue rentals: ' . $e->getMessage(), [
                'request_params' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu thuê',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function exportRentalReport()
    {
        $date = Carbon::today();
        $rentalReport = $this->rentalService->getStatistics($date,RangeEnum::Daily);
        $data = [];
        foreach ($rentalReport as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $data[] = [$key, $value];
        }
        return Excel::Download(new RentalExport($data),'RentalReport.xlsx');
    }

    public function SendEmailRentalReport(Request $request){
        try {
            User::query()->where('role', 'admin')->get();

            $rentalReport = $this->rentalService->getStatistics(carbon::today(),RangeEnum::Daily);

            $file = $request->file('file');

            $filePath = public_path('excel/');

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move($filePath, $fileName);

            dispatch(new SendReportEmailJob("maixuanson2789@gmail.com", $fileName, $rentalReport));

            return response()->json([
                "message" => "send email success"
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                "message" => "send email fail",
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function getTopBooksByMonth(Request $request) {

        $month = $request->query('month');
        $year = $request->query('year');

        if (!$month || !$year) {
            return response()->json(['error' => 'Vui lòng chọn tháng và năm'], 400);
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $topBooks = \DB::table('rental_details')
            ->join('rentals', 'rental_details.rental_id', '=', 'rentals.id')
            ->join('books', 'rental_details.book_id', '=', 'books.id')
            ->whereBetween('rentals.rental_date', [$start->toDateString(), $end->toDateString()])
            ->select('books.id', 'books.title', \DB::raw('SUM(rental_details.quantity) as total_rented'))
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('total_rented')
            ->limit(15)
            ->get();

        return response()->json([
            'data'=>$topBooks
        ]);
    }

    public function getTopBooksWeekly(Request $request){

        $week = (int)$request->query('week');
        $month = (int)$request->query('month');
        $year = (int)$request->query('year');

        if (!$week || !$month || !$year) {
            return response()->json(['error' => 'Vui lòng chọn tuần, tháng và năm'], 400);
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();

        $startOfWeek = $startOfMonth->copy()->addWeeks($week - 1)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $start = $startOfWeek->lessThan($startOfMonth) ? $startOfMonth : $startOfWeek;
        $end = $endOfWeek->greaterThan($startOfMonth->copy()->endOfMonth()) 
            ? $startOfMonth->copy()->endOfMonth() : $endOfWeek;

        $topBooks = \DB::table('rental_details')
            ->join('rentals', 'rental_details.rental_id', '=', 'rentals.id')
            ->join('books', 'rental_details.book_id', '=', 'books.id')
            ->whereBetween('rentals.rental_date', [$start->toDateString(), $end->toDateString()])
            ->select('books.id', 'books.title', \DB::raw('SUM(rental_details.quantity) as total_rented'))
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('total_rented')
            ->limit(10)
            ->get();

        return response()->json([
            'week' => $week,
            'range' => [$start->toDateString(), $end->toDateString()],
            'data' => $topBooks
        ]);
    }

    public function destroy($id){
        try{
            $this->rentalService->deleteRentalOrder($id);
            return response()->json([
                'success' => true,
                'message' => 'Xóa đơn thuê thành công'
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Xóa đơn thuê thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function extendRental(Request $request, string $id){
        try {
            $validatedData = $request->validate([
                'extend_days' => 'required|numeric',
            ]);
            $this->rentalService->extendRental($id, $validatedData['extend_days']);
            return response()->json([
                'success' => true,
                'message' => 'Gia hạn đơn thuê thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gia hạn đơn thuê thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancelRental($id)
    {
        try {
            $resp=$this->rentalService->cancelRentalOrder($id);
            return response()->json([
                'success' => true,
                'message' => 'Hủy đơn thuê thành công',
                'data' => $resp
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Hủy đơn thuê thất bại',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}


