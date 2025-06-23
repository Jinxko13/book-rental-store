<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Requests\BookUpdate;
use App\Http\Resources\BookResponse;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(bookService $bookService){
        $this->bookService = $bookService;
    }

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $search = $request->query('search', '');
        $categories = $request->query('categories', []);
        $authors = $request->query('authors', []);
        $price_min = $request->query('price_min', null);
        $price_max = $request->query('price_max', null);

        if (is_string($categories)) {
            $categories = explode(',', $categories);
        }

        if (is_string($authors)) {
            $authors = explode(',', $authors);
        }
        $bookList = $this->bookService->GetListBook($page, $limit, $search, $categories, $authors, $price_min, $price_max);

        $bookResponse = [];

        foreach ($bookList["BookList"] as $book){
            $bookResponse[] = new BookResponse($book);
        }

        return response()->json([
            "numberRecord"=>$bookList["numberRecord"],
            "data" => $bookResponse,
        ], 200);
    }

    public function show($id){
        $Book = $this->bookService->GetBookById($id);
        return response()->json(new BookResponse($Book), 200);
    }


    public function create(BookRequest $BookRequest)
    {
        try {

            $CategoryList = $BookRequest['category_id'];

            $FileImage = $BookRequest['image'];

            $BookCreate = [
                'title'=>$BookRequest['title'],
                'author_id'=>$BookRequest['author_id'],
                'price'=>$BookRequest['price'],
                'description'=>$BookRequest['description'],
                'quantity'=>$BookRequest['quantity'],
            ];

            $Book = $this->bookService->CreateBook($BookCreate, $CategoryList, $FileImage);

            return response()->json([
                'data' => $Book,
                'message' => 'create book success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }


    public function update(BookRequest $BookRequest, string $id)
    {
        try {
            $CategoryList = $BookRequest['category_id'];

            $FileImage = $BookRequest['image'];

            $BookCreate = [
                'title'=>$BookRequest['title'],
                'author_id'=>$BookRequest['author_id'],
                'price'=>$BookRequest['price'],
                'description'=>$BookRequest['description'],
                'quantity'=>$BookRequest['quantity'],
            ];

            $this->bookService->UpdateBook($BookCreate, $CategoryList, $FileImage, $id);

            return response()->json([
                "message" => "update book success"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $this->bookService->DeleteBook($id);
            return response()->json([
                "message" => "delete book success"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "delete book fail",
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    public function getNewestBook()
    {
        try {
            $Book = $this->bookService->getNewestBook();
            return response()->json([
                "success" => true,
                "data" => $Book,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "get newest book fail",
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function getMonthlyBook(Request $request)
    {
        try {
            $month = $request->query('month');
            $year = $request->query('year');
            $data = $this->bookService->getMonthlyBook($month, $year);
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "get monthly book fail",
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
