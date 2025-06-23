<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TopBookWeekController extends Controller
{
    public function recentTopBooks()
    {
        $start = Carbon::now()->subDays(6)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $books = DB::table('rental_details')
        ->join('rentals', 'rental_details.rental_id', '=', 'rentals.id')
        ->join('books', 'rental_details.book_id', '=', 'books.id')
        ->leftJoin('book_images', 'book_images.book_id', '=', 'books.id')
        ->leftJoin('book_category', 'books.id', '=', 'book_category.book_id')
        ->leftJoin('categories', 'book_category.category_id', '=', 'categories.id')
        ->leftJoin('discounts', 'rentals.discount_id', '=', 'discounts.id')
        ->whereBetween('rentals.rental_date', [$start->toDateString(), $end->toDateString()])
        ->select(
            'books.id',
            'books.title',
            'books.price',
            DB::raw('MAX(book_images.image_url) as image'), // lấy đại diện 1 ảnh
            DB::raw('GROUP_CONCAT(DISTINCT categories.name) as category'), // gộp nhiều category
            DB::raw('MAX(discounts.discount_percent) as discount_percent'), // lấy giảm giá cao nhất
            DB::raw('SUM(rental_details.quantity) as total_rented'),
            DB::raw('MAX(discounts.valid_from) as discount_valid_from'),
            DB::raw('MAX(discounts.valid_to) as discount_valid_to'),
            DB::raw('MAX(rentals.rental_date) as latest_rental_date')
        )
        ->groupBy('books.id', 'books.title', 'books.price')
        ->orderByDesc('total_rented')
        ->limit(10)
        ->get();

        return response()->json([
            'range' => [$start->toDateString(), $end->toDateString()],
            'data' => $books,
        ]);
    }
}