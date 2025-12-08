<?php

namespace App\Policies;

use App\Models\LandingPageElement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LandingPageElementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all landing page elements
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LandingPageElement $landingPageElement): bool
    {
        return $user->isAdmin(); // Only admins can view landing page elements
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create landing page elements
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LandingPageElement $landingPageElement): bool
    {
        return $user->isAdmin(); // Only admins can update landing page elements
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LandingPageElement $landingPageElement): bool
    {
        return $user->isAdmin(); // Only admins can delete landing page elements
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LandingPageElement $landingPageElement): bool
    {
        return $user->isAdmin(); // Only admins can restore landing page elements
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LandingPageElement $landingPageElement): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete landing page elements
    }
}
