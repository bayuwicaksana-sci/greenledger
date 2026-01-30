<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser, User $targetUser): bool
    {
        // Can always view self
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        if ($authUser->can('users.view.all')) {
            return true;
        }

        if ($authUser->can('users.view.site')) {
            // Check if target user is in any of auth user's sites
            $authSiteIds = $authUser->sites->pluck('id');
            $targetSiteIds = $targetUser->sites->pluck('id');

            return $authSiteIds->intersect($targetSiteIds)->isNotEmpty()
                   || $authSiteIds->contains($targetUser->primary_site_id);
        }

        return false;
    }

    /**
     * Determine whether the user can deactivate another user.
     */
    public function deactivate(User $authUser, User $targetUser): bool
    {
        // Cannot deactivate self
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        return $authUser->can('users.deactivate');
    }

    /**
     * Determine whether the user can reset another user's password.
     */
    public function resetPassword(User $authUser, User $targetUser): bool
    {
        // Cannot reset own password via admin function
        if ($authUser->id === $targetUser->id) {
            return false;
        }

        return $authUser->can('users.reset-password');
    }
}
