<?php

namespace App\Policies;

use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductImagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow all users to view product images list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductImage $productImage): bool
    {
        return true; // Allow all users to view individual product images
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create product images
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductImage $productImage): bool
    {
        return $user->isAdmin(); // Only admins can update product images
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductImage $productImage): bool
    {
        return $user->isAdmin(); // Only admins can delete product images
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductImage $productImage): bool
    {
        return $user->isAdmin(); // Only admins can restore product images
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductImage $productImage): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete product images
    }
}
