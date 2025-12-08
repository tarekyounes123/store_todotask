<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow all users to view reviews list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        return true; // Allow all users to view individual reviews
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a review
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin(); // Only the user who created the review or admin can update
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin(); // Only the user who created the review or admin can delete
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->isAdmin(); // Only admins can restore reviews
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete reviews
    }
}
