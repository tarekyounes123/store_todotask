<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRepository
{
    /**
     * Get favorite products for a user with optimized query including the relationship check.
     */
    public function getFavoriteProducts(User $user, int $perPage = 12): LengthAwarePaginator
    {
        return $user
            ->favoriteProducts()
            ->with(['category', 'images'])
            ->latest('user_product_favorites.created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Add a product to user's favorites with optimized method.
     */
    public function addToFavorites(User $user, Product $product): bool
    {
        if (!$user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->attach($product->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return true;
        }
        return false;
    }

    /**
     * Remove a product from user's favorites.
     */
    public function removeFromFavorites(User $user, Product $product): bool
    {
        return $user->favoriteProducts()->detach($product->id) > 0;
    }

    /**
     * Check if a product is favorited by user using optimized method.
     */
    public function isFavorited(User $user, Product $product): bool
    {
        return $user->favoriteProducts()->where('product_id', $product->id)->exists();
    }

    /**
     * Get products with filters, sorting, and pagination
     */
    public function getProducts(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with('category', 'images');

        // Eager load favoritedBy relationship for the authenticated user
        if (auth()->check()) {
            $query->withExists(['favoritedBy as is_favorited_by_user' => function ($query) {
                $query->where('user_id', auth()->id());
            }]);
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply Category Filter
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Apply Price Range Filter
        if ($request->filled('min_price')) {
            $minPrice = floatval($request->input('min_price'));
            $query->where('price', '>=', $minPrice);
        }
        if ($request->filled('max_price')) {
            $maxPrice = floatval($request->input('max_price'));
            $query->where('price', '<=', $maxPrice);
        }

        // Apply Sorting
        $sortBy = $request->input('sort_by', 'latest');
        $query = $this->applySorting($query, $sortBy);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Apply sorting to product query
     */
    public function applySorting($query, string $sortBy)
    {
        switch ($sortBy) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
            case 'price_desc':
                return $query->orderBy('price', 'desc');
            case 'name_asc':
                return $query->orderBy('name', 'asc');
            case 'name_desc':
                return $query->orderBy('name', 'desc');
            case 'latest':
            default:
                return $query->latest(); // Default sorting
        }
    }

    /**
     * Get all categories for filtering
     */
    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::all();
    }

    /**
     * Get a product with optimized relationships
     */
    public function getProductWithRelationships(Product $product): Product
    {
        // Eager load category and images for the specific product
        $product->load('category', 'images');

        // Eager load favoritedBy relationship for the authenticated user
        if (auth()->check()) {
            $product->loadExists(['favoritedBy as is_favorited_by_user' => function ($query) {
                $query->where('user_id', auth()->id());
            }]);
        }

        return $product;
    }
}