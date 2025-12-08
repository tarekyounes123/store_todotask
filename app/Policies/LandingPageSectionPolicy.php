<?php

namespace App\Policies;

use App\Models\LandingPageSection;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LandingPageSectionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view all landing page sections
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LandingPageSection $landingPageSection): bool
    {
        return $user->isAdmin(); // Only admins can view landing page sections
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create landing page sections
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LandingPageSection $landingPageSection): bool
    {
        return $user->isAdmin(); // Only admins can update landing page sections
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LandingPageSection $landingPageSection): bool
    {
        return $user->isAdmin(); // Only admins can delete landing page sections
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LandingPageSection $landingPageSection): bool
    {
        return $user->isAdmin(); // Only admins can restore landing page sections
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LandingPageSection $landingPageSection): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete landing page sections
    }
}
