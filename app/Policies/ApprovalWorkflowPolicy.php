<?php

namespace App\Policies;

use App\Models\ApprovalWorkflow;
use App\Models\User;

class ApprovalWorkflowPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('approval-workflows.view.all');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApprovalWorkflow $approvalWorkflow): bool
    {
        return $user->can('approval-workflows.view.all');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('approval-workflows.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApprovalWorkflow $approvalWorkflow): bool
    {
        return $user->can('approval-workflows.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApprovalWorkflow $approvalWorkflow): bool
    {
        // Cannot delete if there are active instances
        if ($approvalWorkflow->is_active) {
            return false;
        }

        return $user->can('approval-workflows.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(
        User $user,
        ApprovalWorkflow $approvalWorkflow,
    ): bool {
        return $user->can('approval-workflows.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        ApprovalWorkflow $approvalWorkflow,
    ): bool {
        return $user->can('approval-workflows.delete');
    }

    /**
     * Determine whether the user can activate/deactivate workflows.
     */
    public function activate(
        User $user,
        ApprovalWorkflow $approvalWorkflow,
    ): bool {
        return $user->can('approval-workflows.activate');
    }

    /**
     * Determine whether the user can duplicate a workflow.
     */
    public function duplicate(
        User $user,
        ApprovalWorkflow $approvalWorkflow,
    ): bool {
        return $user->can('approval-workflows.duplicate');
    }

    /**
     * Determine whether the user can set a workflow as active.
     */
    public function setActive(
        User $user,
        ApprovalWorkflow $approvalWorkflow,
    ): bool {
        return $user->can('approval-workflows.activate');
    }
}
