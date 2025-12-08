<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RatingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow all users to view ratings list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rating $rating): bool
    {
        return true; // Allow all users to view individual ratings
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a rating
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id || $user->isAdmin(); // Only the user who created the rating or admin can update
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id || $user->isAdmin(); // Only the user who created the rating or admin can delete
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Rating $rating): bool
    {
        return $user->isAdmin(); // Only admins can restore ratings
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Rating $rating): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete ratings
    }
}
