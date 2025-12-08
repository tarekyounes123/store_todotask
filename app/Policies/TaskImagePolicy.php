<?php

namespace App\Policies;

use App\Models\TaskImage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskImagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all task images
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskImage $taskImage): bool
    {
        return $user->id === $taskImage->task->user_id || $user->isAdmin(); // User can view images of their tasks or if admin
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->id === $taskImage->task->user_id; // User can create images for their tasks or if admin
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskImage $taskImage): bool
    {
        return $user->id === $taskImage->task->user_id || $user->isAdmin(); // User can update images of their tasks or if admin
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskImage $taskImage): bool
    {
        return $user->id === $taskImage->task->user_id || $user->isAdmin(); // User can delete images of their tasks or if admin
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskImage $taskImage): bool
    {
        return $user->isAdmin(); // Only admins can restore task images
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskImage $taskImage): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete task images
    }
}
