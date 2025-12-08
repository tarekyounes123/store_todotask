<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Add a product to the user's favorites.
     */
    public function addToFavorites(Product $product, ?User $user = null): array
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('User not authenticated'),
                'is_favorited' => false
            ];
        }

        $added = $this->productRepository->addToFavorites($user, $product);

        if ($added) {
            return [
                'success' => true,
                'message' => __('Product added to favorites.'),
                'is_favorited' => true
            ];
        }

        return [
            'success' => true,
            'message' => __('Product is already in your favorites.'),
            'is_favorited' => true
        ];
    }

    /**
     * Remove a product from the user's favorites.
     */
    public function removeFromFavorites(Product $product, ?User $user = null): array
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('User not authenticated'),
                'is_favorited' => false
            ];
        }

        $removed = $this->productRepository->removeFromFavorites($user, $product);

        if ($removed) {
            return [
                'success' => true,
                'message' => __('Product removed from favorites.'),
                'is_favorited' => false
            ];
        }

        return [
            'success' => true,
            'message' => __('Product was not in your favorites.'),
            'is_favorited' => false
        ];
    }

    /**
     * Check if a product is favorited by the user.
     */
    public function isFavorited(Product $product, ?User $user = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        return $this->productRepository->isFavorited($user, $product);
    }

    /**
     * Get user's favorite products with optimized query.
     */
    public function getUserFavorites(?User $user = null, int $perPage = 12)
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return collect();
        }

        return $this->productRepository->getFavoriteProducts($user, $perPage);
    }
}