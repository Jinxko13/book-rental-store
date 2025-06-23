<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\UpdateRentalStatusJob;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\TopBookWeekController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| AUTH Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'getProfile']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN & STAFF Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin,staff'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::post('/users', [UserController::class, 'store']);

    Route::get('/rentals',[RentalController::class,'index']);
    Route::get('/rentals/overdue', [RentalController::class, 'getOverdueRentals']);
    Route::apiResource('authors', AuthorController::class)->only(['update','store']);
    Route::post('/rental/extend/{rental_id}', [RentalController::class, 'extendRental']);
    Route::post('/rental/return/{rental_id}', [RentalController::class, 'returnRental']);
    Route::post('/rental/pre-return/{rental_id}', [RentalController::class, 'preReturn']);
});

/*
|--------------------------------------------------------------------------
| ADMIN Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    Route::delete('rentals/{rental}', [RentalController::class, 'destroy']);
    Route::get('rentals/search', [RentalController::class, 'searchRentals']);
    Route::get('/check-overdue', [RentalController::class, 'checkOverdue']);
    Route::get('/overdue-rentals', [RentalController::class, 'getOverdueRentals']);

    //Book
    Route::delete('/books/{id}',[BookController::class,'destroy']);
    Route::post('/books',[BookController::class,'create']);
    Route::put('/books/{id}',[BookController::class,'update']);

    //Category
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('discounts', DiscountController::class);

    // Author
    Route::apiResource('authors', AuthorController::class)->only(['destroy']);

    // Dashboard
    Route::get("/dashboard/chart",[DashboardController::class,'getDashboardCharData']);
    Route::get('/users/{user}', [UserController::class, 'show']);
});
Route::get("/dashboard/statistics",[DashboardController::class,'getDashboard']);
Route::get('/books', [BookController::class,'index']);
Route::get('/books/{id}', [BookController::class,'show']);

/*
|--------------------------------------------------------------------------
| PUBLIC Routes
|--------------------------------------------------------------------------
*/
Route::get('/books',[BookController::class,'index']);
Route::get('/book/newest', [BookController::class, 'getNewestBook']);
Route::get('/book/monthly', [BookController::class, 'getMonthlyBook']);
Route::put('/books/{id}',[BookController::class,'update']);
Route::apiResource('categories', CategoryController::class)->only(['show', 'index']);
Route::apiResource('authors', AuthorController::class)->only(['show', 'index']);;
Route::get('/top-book-week/recent', [TopBookWeekController::class, 'recentTopBooks']);
Route::get('/top-book/monthly', [RentalController::class, 'getTopBooksByMonth']);
Route::get('/top-book/weekly', [RentalController::class, 'getTopBooksWeekly']);

/*
|--------------------------------------------------------------------------
| Authenticated (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('rentals', [RentalController::class, 'create']);
    Route::get('rentals/calculate-fee', [RentalController::class, 'calculateRentalFee']);
    Route::put('/rental/cancel/{id}', [RentalController::class, 'cancelRental']);
    Route::get('user/rentals', [UserController::class, 'getUserOrders']);
    // DISCOUNT routes
    Route::apiResource('discounts', DiscountController::class)->only(['show', 'index']);
    Route::get('/discounts/apply/{code}', [DiscountController::class, 'applyPromotions']);
    Route::get('/export/rental',[RentalController::class,'exportRentalReport']);
    Route::post('/start-jobs', function () {
        UpdateRentalStatusJob::dispatch();
        return response()->json(['message' => 'Job đã được đưa vào hàng đợi và đang chờ xử lý.']);
    });
    Route::post('/sendreport', [RentalController::class, 'SendEmailRentalReport']);
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
});

Route::get('/test', [RentalController::class, 'test'])->name('test');

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/reviews', [ReviewController::class, 'store']);
// });
// Route::get('/book/{id}/reviews', [ReviewController::class, 'index']);
// Route::get('/book/{id}/rating-summary', [ReviewController::class, 'summary']);

Route::prefix('book/{bookId}')->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/summary', [ReviewController::class, 'summary']);
    Route::get('/reviews/check', [ReviewController::class, 'checkUserReview'])->middleware('auth:sanctum');
    
    Route::get('/reviews/debug', [ReviewController::class, 'debug']);
});

Route::post('/reviews', [ReviewController::class, 'store'])->middleware('auth:sanctum');