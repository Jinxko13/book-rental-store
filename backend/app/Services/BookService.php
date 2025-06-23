<?php

namespace App\Services;

use App\Helper\ImageHelper;
use App\Repositories\Interfaces\BookImageRepository;
use App\Repositories\Interfaces\BookRepository;
use Carbon\Carbon;

class BookService
{
    protected $bookRepository;
    protected $bookImageRepository;

    public function __construct(BookRepository $bookRepository, BookImageRepository $bookImageRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->bookImageRepository = $bookImageRepository;
    }

    public function GetListBook($page, $limit, $search, $categories, $authors, $price_min, $price_max)
    {
        $query = $this->bookRepository->with(["BookImage", "author", "categories"]);
        
        if (!empty($search)) {
            $query = $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if (!empty($categories)) {
            $query = $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            });
        }
        
        if (!empty($authors)) {
            $query = $query->whereHas('author', function ($query) use ($authors) {
                $query->whereIn('authors.id', $authors);
            });
        }
        
        if (!empty($price_min) || !empty($price_max)) {
            if (empty($price_min)) $price_min = 0;
            if (empty($price_max)) $price_max = 1000000;
            $query = $query->whereBetween('price', [$price_min, $price_max]);
        }
        
        // Sắp xếp: sách còn hàng (quantity > 0) lên trước, hết hàng xuống cuối
        // Sau đó sắp xếp theo ngày tạo mới nhất
        $BookList = $query->orderByRaw('CASE WHEN quantity > 0 THEN 0 ELSE 1 END')
                        ->orderBy('created_at', 'desc')
                        ->paginate($limit);
        
        return [
            'numberRecord' => $BookList->total(),
            'BookList' => $BookList,
        ];
    }

    public function GetBookById($id)
    {
        $Book = $this->bookRepository->with(["BookImage", "author", "categories"])->find($id);
        return $Book;
    }

    public function CreateBook($bookRequest, $category, $bookImage)
    {

        $book = $this->bookRepository->create($bookRequest);
        $book->categories()->attach($category);
        foreach ($bookImage as $imageFile) {
            $fileName = ImageHelper::UploadImage($imageFile);
            $image = [
                "book_id" => $book->id,
                "image_url" => ImageHelper::GetImageUrl($fileName),
            ];
            $this->bookImageRepository->create($image);
        }
        return $book;
    }

    public function UpdateBook($bookRequest, $bookCategory, $imageFile, $id)
    {
        $Book = $this->bookRepository->update($bookRequest, $id);
        $BookItem = $this->bookRepository->with("BookImage")->find($id);

        // attach category
        $BookItem->categories()->detach();
        $BookItem->categories()->attach($bookCategory);

        $BookImage = $BookItem->BookImage;

        foreach ($BookImage as $imageItem) {
            $ImageUrl = $imageItem->image_url;
            $fileName = basename(parse_url($ImageUrl, PHP_URL_PATH)); 
            ImageHelper::DeleteImage($fileName);
            $imageItem->delete();
        }
        if($imageFile){
            foreach ($imageFile as $image) {
                $fileName = ImageHelper::UploadImage($image);
                $Image = [
                    "book_id" => $id,
                    "image_url" => ImageHelper::GetImageUrl($fileName),
                ];
                $this->bookImageRepository->create($Image);
            }
        }

    }

    public function DeleteBook($id)
    {
        $Book = $this->bookRepository->find($id);
        $BookImage = $Book->BookImage;
        foreach ($BookImage as $imageItem) {
            $ImageUrl = $imageItem->image_url;
            $fileName = basename(parse_url($ImageUrl, PHP_URL_PATH)); 
            ImageHelper::DeleteImage($fileName);
        }
        $Book->delete();
    }

    public function getNewestBook()
    {
        $Book = $this->bookRepository->where('quantity', '>', 0)->with(["BookImage", "author", "categories"])->orderBy('created_at', 'desc')->paginate(12);
        return $Book;
    }

    public function getMonthlyBook($limit = 12)
    {
        $lastMonth = Carbon::now()->subMonth();
        $month = $lastMonth->month;
        $year = $lastMonth->year;
        
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $bookRentals = \DB::table('rental_details')
            ->join('rentals', 'rental_details.rental_id', '=', 'rentals.id')
            ->whereBetween('rentals.rental_date', [$start->toDateString(), $end->toDateString()])
            ->select('rental_details.book_id', \DB::raw('SUM(rental_details.quantity) as total_rented'))
            ->groupBy('rental_details.book_id')
            ->orderByDesc('total_rented')
            ->get();
        
        $bookIds = $bookRentals->pluck('book_id')->toArray();
        
        $books = $this->bookRepository->with(["BookImage", "author", "categories"])
            ->where('quantity', '>', 0)
            ->whereIn('id', $bookIds)
            ->limit($limit)
            ->get();
        
        $topBooks = $books->map(function ($book) use ($bookRentals) {
            $rentalInfo = $bookRentals->firstWhere('book_id', $book->id);
            $book->total_rented = $rentalInfo ? $rentalInfo->total_rented : 0;
            return $book;
        })->sortByDesc('total_rented')->values();
        
        return [
            'month' => $month,
            'year' => $year,
            'period' => $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
            'total_books' => $topBooks->count(),
            'data' => $topBooks
        ];
    }
}
