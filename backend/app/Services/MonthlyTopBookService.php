<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\MonthlyTopBookRepositoryInterface;
use Illuminate\Support\Facades\Log;

class MonthlyTopBookService
{
    protected $repo;

    public function __construct(MonthlyTopBookRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function generateMonthlyTopBooks()
    {
        $now = Carbon::now()->startOfMonth()->subMonth(); // tháng trước
        $year = $now->year;
        $month = $now->month;
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        // Xoá nếu đã có dữ liệu cũ (phòng trường hợp chạy lại)
        $this->repo->deleteByMonth($year, $month);

        $books = DB::table('rental_details')
            ->join('rentals', 'rental_details.rental_id', '=', 'rentals.id')
            ->join('books', 'rental_details.book_id', '=', 'books.id')
            ->leftJoin('book_images', 'book_images.book_id', '=', 'books.id')
            ->leftJoin('book_category', 'books.id', '=', 'book_category.book_id')
            ->leftJoin('categories', 'book_category.category_id', '=', 'categories.id')
            ->leftJoin('discounts', 'rentals.discount_id', '=', 'discounts.id')
            ->whereBetween('rentals.rental_date', [$start->toDateString(), $end->toDateString()])
            ->select(
                'books.id as book_id',
                'books.title',
                'books.price',
                DB::raw('MAX(book_images.image_url) as image'),
                DB::raw('GROUP_CONCAT(DISTINCT categories.name) as category'),
                DB::raw('MAX(discounts.discount_percent) as discount_percent'),
                DB::raw('SUM(rental_details.quantity) as total_rented')
            )
            ->groupBy('books.id', 'books.title', 'books.price')
            ->orderByDesc('total_rented')
            ->limit(15)
            ->get();

        $insertData = $books->map(function ($book) use ($year, $month) {
            return [
                'book_id' => $book->book_id,
                'title' => $book->title,
                'price' => $book->price,
                'image' => $book->image,
                'discount_percent' => $book->discount_percent,
                'category' => $book->category,
                'total_rented' => $book->total_rented,
                'year' => $year,
                'month' => $month,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $this->repo->bulkInsert($insertData);
    }
}