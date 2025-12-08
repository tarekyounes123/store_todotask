<?php

namespace App\Policies;

use App\Models\OrderNotification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderNotificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all order notifications
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderNotification $orderNotification): bool
    {
        return $user->id === $orderNotification->user_id || $user->isAdmin(); // User can view their own notifications or if admin
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create order notifications
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderNotification $orderNotification): bool
    {
        return $user->id === $orderNotification->user_id || $user->isAdmin(); // User can update their own notifications or if admin
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderNotification $orderNotification): bool
    {
        return $user->id === $orderNotification->user_id || $user->isAdmin(); // User can delete their own notifications or if admin
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderNotification $orderNotification): bool
    {
        return $user->isAdmin(); // Only admins can restore order notifications
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderNotification $orderNotification): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete order notifications
    }
}
