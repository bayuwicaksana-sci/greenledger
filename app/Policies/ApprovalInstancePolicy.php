<?php

namespace App\Policies;

use App\Enums\ApprovalInstanceStatus;
use App\Models\ApprovalInstance;
use App\Models\User;
use App\Services\Approval\WorkflowEngine;

class ApprovalInstancePolicy
{
    public function __construct(protected WorkflowEngine $workflowEngine) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can see their own instances
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApprovalInstance $approvalInstance): bool
    {
        // Can view if submitted by user, or if user is an approver
        if ($approvalInstance->submitted_by === $user->id) {
            return true;
        }

        // Check if user can approve current step
        if ($approvalInstance->currentStep) {
            return $this->canApprove($user, $approvalInstance);
        }

        return $user->can('approval-instances.view.all');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any user can initiate workflows
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApprovalInstance $approvalInstance): bool
    {
        // Can only update if in draft status and submitted by user
        return $approvalInstance->status === ApprovalInstanceStatus::Draft &&
            $approvalInstance->submitted_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApprovalInstance $approvalInstance): bool
    {
        return $user->can('approval-instances.cancel.all');
    }

    /**
     * Determine whether the user can submit for approval.
     */
    public function submit(User $user, ApprovalInstance $approvalInstance): bool
    {
        return $approvalInstance->status === ApprovalInstanceStatus::Draft &&
            $approvalInstance->submitted_by === $user->id;
    }

    /**
     * Determine whether the user can approve the current step.
     */
    public function approve(
        User $user,
        ApprovalInstance $approvalInstance,
    ): bool {
        if (
            $approvalInstance->status !==
            ApprovalInstanceStatus::PendingApproval
        ) {
            return false;
        }

        if (! $approvalInstance->currentStep) {
            return false;
        }

        return $this->canApprove($user, $approvalInstance);
    }

    /**
     * Determine whether the user can reject.
     */
    public function reject(User $user, ApprovalInstance $approvalInstance): bool
    {
        return $this->approve($user, $approvalInstance);
    }

    /**
     * Determine whether the user can request changes.
     */
    public function requestChanges(
        User $user,
        ApprovalInstance $approvalInstance,
    ): bool {
        return $this->approve($user, $approvalInstance);
    }

    /**
     * Determine whether the user can cancel the instance.
     */
    public function cancel(User $user, ApprovalInstance $approvalInstance): bool
    {
        // Submitter can cancel, or admin
        return $approvalInstance->submitted_by === $user->id ||
            $user->can('approval-instances.cancel.all');
    }

    /**
     * Check if user can approve based on step configuration.
     */
    protected function canApprove(
        User $user,
        ApprovalInstance $approvalInstance,
    ): bool {
        $step = $approvalInstance->currentStep;

        if (! $step) {
            return false;
        }

        return match ($step->approver_type->value) {
            'user' => in_array($user->id, $step->approver_identifiers),
            'role' => $user->hasAnyRole($step->approver_identifiers),
            'permission' => $user->hasAnyPermission(
                $step->approver_identifiers,
            ),
            default => false,
        };
    }
}
