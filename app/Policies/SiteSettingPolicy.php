<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SiteSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Only admins can view site settings list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SiteSetting $siteSetting): bool
    {
        return $user->isAdmin(); // Only admins can view individual site settings
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create site settings
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiteSetting $siteSetting): bool
    {
        return $user->isAdmin(); // Only admins can update site settings
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiteSetting $siteSetting): bool
    {
        return $user->isAdmin(); // Only admins can delete site settings
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiteSetting $siteSetting): bool
    {
        return $user->isAdmin(); // Only admins can restore site settings
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiteSetting $siteSetting): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete site settings
    }
}
