<?php

namespace App\Policies;

use App\Models\FiscalYear;
use App\Models\User;

class FiscalYearPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('fiscal-year.view') || $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('fiscal-year.view') || $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FiscalYear $fiscalYear): bool
    {
        // Cannot delete if there are associated programs
        if ($fiscalYear->programs()->count() > 0) {
            return false;
        }

        return $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('fiscal-year.manage');
    }

    /**
     * Determine whether the user can close the fiscal year.
     */
    public function close(User $user, FiscalYear $fiscalYear): bool
    {
        // Cannot close if already closed
        if ($fiscalYear->is_closed) {
            return false;
        }

        return $user->can('fiscal-year.close');
    }

    /**
     * Determine whether the user can reopen the fiscal year.
     */
    public function reopen(User $user, FiscalYear $fiscalYear): bool
    {
        // Can only reopen if currently closed
        if (! $fiscalYear->is_closed) {
            return false;
        }

        return $user->can('fiscal-year.reopen');
    }
}
