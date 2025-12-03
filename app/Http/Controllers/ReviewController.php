<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        // Also create/update the rating entry
        Rating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ],
            [
                'rating' => $request->rating,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $review->load('user')
        ]);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function storeComment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'comment' => 'required|string|max:500',
        ]);

        // For comments, we'll store them in the reviews table with rating = 0
        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => 0, // No rating for comments
            'review' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully',
            'comment' => $review->load('user')
        ]);
    }

    /**
     * Get all reviews for a product.
     */
    public function getReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user:id,name,profile_picture')
            ->where('rating', '>', 0) // Only get reviews with ratings, not pure comments
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

    /**
     * Get all comments for a product.
     */
    public function getComments(Product $product)
    {
        $comments = $product->reviews()
            ->with('user:id,name,profile_picture')
            ->where('rating', 0) // Only get comments (reviews without ratings)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    /**
     * Get average rating and review count for a product.
     */
    public function getRatingStats(Product $product)
    {
        $avgRating = $product->reviews()
            ->where('rating', '>', 0)
            ->avg('rating');

        $reviewCount = $product->reviews()
            ->where('rating', '>', 0)
            ->count();

        return response()->json([
            'success' => true,
            'average_rating' => round($avgRating, 1) ?? 0,
            'review_count' => $reviewCount
        ]);
    }
}
