<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all order items
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderItem $orderItem): bool
    {
        return $user->id === $orderItem->order->user_id || $user->isAdmin(); // User can view items from their own orders or if admin
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create order items directly
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderItem $orderItem): bool
    {
        return $user->isAdmin(); // Only admins can update order items
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderItem $orderItem): bool
    {
        return $user->isAdmin(); // Only admins can delete order items
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderItem $orderItem): bool
    {
        return $user->isAdmin(); // Only admins can restore order items
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderItem $orderItem): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete order items
    }
}
