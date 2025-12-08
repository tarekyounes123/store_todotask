<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow all users to view products list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return true; // Allow all users to view individual products
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create products
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can update products
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can delete products
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can restore products
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete products
    }
}
