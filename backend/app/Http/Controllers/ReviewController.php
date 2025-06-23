<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function index($bookId, Request $request)
    {
        try {
            $ratingFilter = $request->query('rating', null);

            $reviewsQuery = Review::with(['user:id,name'])
                ->where('book_id', $bookId);
            
            if ($ratingFilter) {
                $reviewsQuery->where('rating', $ratingFilter);
            }

            $reviews = $reviewsQuery->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => (int) $review->id,
                        'rating' => (float) $review->rating,
                        'comment' => $review->comment ?? '',
                        'created_at' => $review->created_at->toISOString(),
                        'image_url' => $review->image_url,
                        'user' => [
                            'id' => (int) $review->user->id,
                            'name' => $review->user->name ?? 'Ẩn danh'
                        ]
                    ];
                });

            return response()->json($reviews, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to load reviews'], 500);
        }
    }

    public function summary($bookId)
    {
        try {
            $reviews = Review::where('book_id', $bookId)->get();
            $total = $reviews->count();
            $average = $total ? round($reviews->avg('rating'), 1) : 0;
            
            $distribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $count = $reviews->where('rating', $i)->count();
                $distribution[$i] = $total ? round(($count / $total) * 100) : 0;
            }

            $result = [
                'average' => (float) $average,
                'total' => (int) $total,
                'distribution' => $distribution
            ];

            Log::info('Summary loaded for book ' . $bookId, $result);
            
            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error('Error loading summary: ' . $e->getMessage());
            return response()->json([
                'average' => 0,
                'total' => 0,
                'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'rating' => 'required|numeric|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
                'image' => 'nullable|image|max:2048',
            ]);

            // Check if user already reviewed this product
            $existingReview = Review::where('user_id', Auth::id())
                ->where('book_id', $request->book_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'error' => 'Bạn đã đánh giá sản phẩm này rồi',
                    'message' => 'Mỗi tài khoản chỉ có thể đánh giá một lần cho mỗi sản phẩm'
                ], 409); // 409 Conflict
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('reviews', 'public');
            }

            // Create new review (not updateOrCreate)
            $review = Review::create([
                'user_id' => Auth::id(),
                'book_id' => $request->book_id,
                'rating' => (float) $request->rating,
                'comment' => $request->comment,
                'image_url' => $imagePath ? Storage::url($imagePath) : null,
            ]);

            // Load user relationship for response
            $review->load('user:id,name');

            $formattedReview = [
                'id' => (int) $review->id,
                'rating' => (float) $review->rating,
                'comment' => $review->comment ?? '',
                'created_at' => $review->created_at->toISOString(),
                'image_url' => $review->image_url,
                'user' => [
                    'id' => (int) $review->user->id,
                    'name' => $review->user->name ?? 'Ẩn danh'
                ]
            ];

            Log::info('Review created', ['review_id' => $review->id, 'rating' => $review->rating]);
            
            return response()->json($formattedReview, 201);
        } catch (\Exception $e) {
            Log::error('Error storing review: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to save review'], 500);
        }
    }

    public function checkUserReview($bookId)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['has_reviewed' => false], 200);
            }

            $hasReviewed = Review::where('book_id', $bookId)
                ->where('user_id', Auth::id())
                ->exists();

            return response()->json(['has_reviewed' => (bool) $hasReviewed], 200);
        } catch (\Exception $e) {
            Log::error('Error checking user review: ' . $e->getMessage());
            return response()->json(['has_reviewed' => false], 500);
        }
    }

    public function debug($bookId)
    {
        if (!config('app.debug')) {
            return response()->json(['error' => 'Debug mode disabled'], 403);
        }

        $reviews = Review::with('user')->where('book_id', $bookId)->get();
        
        return response()->json([
            'raw_reviews' => $reviews,
            'count' => $reviews->count(),
            'ratings' => $reviews->pluck('rating'),
            'rating_types' => $reviews->map(fn($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'rating_type' => gettype($r->rating)
            ])
        ]);
    }
}