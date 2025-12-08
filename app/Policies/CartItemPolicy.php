<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all cart items
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CartItem $cartItem): bool
    {
        return $user->id === $cartItem->cart->user_id || $user->isAdmin(); // User can view items in their own cart or if admin
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can add to cart
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CartItem $cartItem): bool
    {
        return $user->id === $cartItem->cart->user_id || $user->isAdmin(); // User can update items in their own cart or if admin
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CartItem $cartItem): bool
    {
        return $user->id === $cartItem->cart->user_id || $user->isAdmin(); // User can delete items from their own cart or if admin
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CartItem $cartItem): bool
    {
        return $user->isAdmin(); // Only admins can restore
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CartItem $cartItem): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete
    }
}
