<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StockMovementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all stock movements
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StockMovement $stockMovement): bool
    {
        return $user->isAdmin(); // Only admins can view individual stock movements
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create stock movements
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StockMovement $stockMovement): bool
    {
        return $user->isAdmin(); // Only admins can update stock movements
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StockMovement $stockMovement): bool
    {
        return $user->isAdmin(); // Only admins can delete stock movements
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StockMovement $stockMovement): bool
    {
        return $user->isAdmin(); // Only admins can restore stock movements
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StockMovement $stockMovement): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete stock movements
    }
}
