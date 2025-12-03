<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure user is authenticated for all actions
    }

    /**
     * Display a listing of the user's favorite products.
     */
    public function index(): View
    {
        $user = Auth::user();
        $favoriteProducts = $user->favoriteProducts()->with('category', 'images')->paginate(12);

        return view('favorites.index', compact('favoriteProducts'));
    }

    /**
     * Add a product to the user's favorites.
     */
    public function add(Product $product): JsonResponse
    {
        $user = Auth::user();

        if (!$user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->attach($product);
            return response()->json(['success' => true, 'message' => 'Product added to favorites.']);
        }

        return response()->json(['success' => false, 'message' => 'Product already in favorites.'], 409);
    }

    /**
     * Remove a product from the user's favorites.
     */
    public function remove(Product $product): JsonResponse
    {
        $user = Auth::user();

        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->detach($product);
            return response()->json(['success' => true, 'message' => 'Product removed from favorites.']);
        }

        return response()->json(['success' => false, 'message' => 'Product not in favorites.'], 404);
    }
}
