<?php

namespace App\Services\Approval;

use App\Enums\ApprovalActionType;
use App\Enums\ApprovalStepPurpose;
use App\Enums\ApprovalStepType;
use App\Models\ApprovalAction;
use App\Models\ApprovalInstance;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\User;
use App\States\ApprovalInstance\Approved;
use App\States\ApprovalInstance\Cancelled;
use App\States\ApprovalInstance\ChangesRequested;
use App\States\ApprovalInstance\InProgress;
use App\States\ApprovalInstance\Pending;
use App\States\ApprovalInstance\Rejected;
use App\Notifications\ApprovalApprovedNotification;
use App\Notifications\ApprovalCancelledNotification;
use App\Notifications\ApprovalChangesRequestedNotification;
use App\Notifications\ApprovalRejectedNotification;
use App\Notifications\ApprovalStepAdvancedNotification;
use App\Notifications\ApprovalSubmittedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Core workflow engine for managing approval instances and processing actions.
 */
class WorkflowEngine
{
    public function __construct(
        protected ApprovalRuleEvaluator $ruleEvaluator,
    ) {}

    /**
     * Initialize a new approval workflow instance for a model.
     */
    public function initializeWorkflow(
        Model $approvable,
        ApprovalWorkflow $workflow,
        User $submitter,
    ): ApprovalInstance {
        return DB::transaction(function () use (
            $approvable,
            $workflow,
            $submitter,
        ) {
            // Create the approval instance
            $instance = ApprovalInstance::create([
                'approval_workflow_id' => $workflow->id,
                'approvable_type' => get_class($approvable),
                'approvable_id' => $approvable->id,
                'status' => Pending::class,
                'submitted_by' => $submitter->id,
            ]);

            // Log the initialization
            activity('approval')
                ->performedOn($instance)
                ->causedBy($submitter)
                ->log('Approval workflow initialized');

            return $instance;
        });
    }

    /**
     * Submit an instance for approval (move from pending to in progress).
     */
    public function submitForApproval(
        ApprovalInstance $instance,
        User $submitter,
    ): bool {
        if (! $instance->status instanceof Pending) {
            return false;
        }

        return DB::transaction(function () use ($instance, $submitter) {
            // Find the first applicable step (auto-skip if needed)
            $firstStep = $this->getNextApplicableStep($instance);

            if (! $firstStep) {
                return false;
            }

            // Update instance - transition to InProgress
            $instance->status->transitionTo(InProgress::class);
            $instance->current_step_id = $firstStep->id;
            $instance->submitted_at = now();
            $instance->save();

            // Log the submission
            activity('approval')
                ->performedOn($instance)
                ->causedBy($submitter)
                ->log('Submitted for approval');

            // Notify eligible approvers
            $this->resolveEligibleApprovers($firstStep)->each(
                fn (User $approver) => $approver->notify(new ApprovalSubmittedNotification($instance)),
            );

            return true;
        });
    }

    /**
     * Process an approval action (approve, reject, request changes).
     */
    public function processAction(
        ApprovalInstance $instance,
        ApprovalStep $step,
        ApprovalActionType $actionType,
        User $actor,
        ?string $comments = null,
        ?array $metadata = null,
    ): bool {
        if (! $instance->status instanceof InProgress) {
            return false;
        }

        if ($instance->current_step_id !== $step->id) {
            return false;
        }

        // Verify actor has permission to approve this step
        if (! $this->canActorApprove($actor, $step)) {
            return false;
        }

        return DB::transaction(function () use (
            $instance,
            $step,
            $actionType,
            $actor,
            $comments,
            $metadata,
        ) {
            // Record the action
            ApprovalAction::create([
                'approval_instance_id' => $instance->id,
                'approval_step_id' => $step->id,
                'action_type' => $actionType,
                'actor_id' => $actor->id,
                'comments' => $comments,
                'metadata' => $metadata,
            ]);

            // Handle based on action type
            return match ($actionType) {
                ApprovalActionType::Approve => $this->handleApproval(
                    $instance,
                    $step,
                    $actor,
                ),
                ApprovalActionType::Reject => $this->handleRejection(
                    $instance,
                    $actor,
                ),
                ApprovalActionType::RequestChanges => $this->handleRequestChanges($instance, $actor),
            };
        });
    }

    /**
     * Handle an approval action.
     */
    protected function handleApproval(
        ApprovalInstance $instance,
        ApprovalStep $step,
        User $actor,
    ): bool {
        // For parallel steps, check if enough approvals have been received
        if ($step->step_type === ApprovalStepType::Parallel) {
            $approvalCount = $instance
                ->actions()
                ->where('approval_step_id', $step->id)
                ->where('action_type', ApprovalActionType::Approve)
                ->count();

            if ($approvalCount < $step->required_approvals_count) {
                // Not enough approvals yet
                activity('approval')
                    ->performedOn($instance)
                    ->causedBy($actor)
                    ->log(
                        "Approved step (${approvalCount}/{$step->required_approvals_count})",
                    );

                return true;
            }
        }

        // Move to next step or complete
        $nextStep = $this->getNextApplicableStep($instance, $step);

        if ($nextStep) {
            // Evaluate conditional rules for the next step
            if (
                ! $this->ruleEvaluator->evaluate(
                    $nextStep->conditional_rules,
                    $instance->approvable,
                )
            ) {
                // Skip this step and try the next one
                return $this->handleApproval($instance, $nextStep, $actor);
            }

            $instance->current_step_id = $nextStep->id;
            $instance->save();

            activity('approval')
                ->performedOn($instance)
                ->causedBy($actor)
                ->log("Approved and moved to step: {$nextStep->name}");

            // Notify eligible approvers of the new step
            $this->resolveEligibleApprovers($nextStep)->each(
                fn (User $approver) => $approver->notify(new ApprovalStepAdvancedNotification($instance)),
            );
        } else {
            // Workflow complete - approved
            $instance->status->transitionTo(Approved::class);
            $instance->completed_at = now();
            $instance->save();

            // Fire the onApproved hook on the approvable model
            $approvable = $instance->approvable;
            if ($approvable && method_exists($approvable, 'onApproved')) {
                $approvable->onApproved();
            }

            activity('approval')
                ->performedOn($instance)
                ->causedBy($actor)
                ->log('Workflow approved');

            // Notify the submitter of approval
            $instance->submittedBy?->notify(new ApprovalApprovedNotification($instance));
        }

        return true;
    }

    /**
     * Handle a rejection action.
     */
    protected function handleRejection(
        ApprovalInstance $instance,
        User $actor,
    ): bool {
        $instance->status->transitionTo(Rejected::class);
        $instance->completed_at = now();
        $instance->save();

        activity('approval')
            ->performedOn($instance)
            ->causedBy($actor)
            ->log('Workflow rejected');

        // Notify the submitter of rejection
        $instance->submittedBy?->notify(new ApprovalRejectedNotification($instance));

        return true;
    }

    /**
     * Handle a request for changes action.
     */
    protected function handleRequestChanges(
        ApprovalInstance $instance,
        User $actor,
    ): bool {
        // Transition to changes requested state
        $instance->status->transitionTo(ChangesRequested::class);
        $instance->current_step_id = null;
        $instance->save();

        activity('approval')
            ->performedOn($instance)
            ->causedBy($actor)
            ->log('Changes requested');

        // Notify the submitter that changes are needed
        $instance->submittedBy?->notify(new ApprovalChangesRequestedNotification($instance));

        return true;
    }

    /**
     * Get the next step in the workflow.
     */
    protected function getNextStep(
        ApprovalInstance $instance,
        ?ApprovalStep $currentStep = null,
    ): ?ApprovalStep {
        $query = ApprovalStep::where(
            'approval_workflow_id',
            $instance->approval_workflow_id,
        )->orderBy('step_order');

        if ($currentStep) {
            $query->where('step_order', '>', $currentStep->step_order);
        }

        return $query->first();
    }

    /**
     * Check if an actor can approve a specific step.
     */
    protected function canActorApprove(User $actor, ApprovalStep $step): bool
    {
        return match ($step->approver_type->value) {
            'user' => in_array($actor->id, $step->approver_identifiers),
            'role' => $actor->hasAnyRole($step->approver_identifiers),
            'permission' => $actor->hasAnyPermission(
                $step->approver_identifiers,
            ),
            default => false,
        };
    }

    /**
     * Cancel an approval instance.
     */
    public function cancelApproval(
        ApprovalInstance $instance,
        User $actor,
    ): bool {
        // Check if already in a terminal state
        if (
            $instance->status instanceof Approved ||
            $instance->status instanceof Rejected ||
            $instance->status instanceof Cancelled
        ) {
            return false;
        }

        return DB::transaction(function () use ($instance, $actor) {
            $instance->status->transitionTo(Cancelled::class);
            $instance->completed_at = now();
            $instance->save();

            activity('approval')
                ->performedOn($instance)
                ->causedBy($actor)
                ->log('Workflow cancelled');

            // Notify submitter if they are not the one who cancelled
            $submitter = $instance->submittedBy;
            if ($submitter && $submitter->id !== $actor->id) {
                $submitter->notify(new ApprovalCancelledNotification($instance));
            }

            // Notify current step approvers if a step was active
            if ($instance->currentStep) {
                $this->resolveEligibleApprovers($instance->currentStep)->each(
                    fn (User $approver) => $approver->notify(new ApprovalCancelledNotification($instance)),
                );
            }

            return true;
        });
    }

    /**
     * Resubmit an instance after changes were requested.
     */
    public function resubmitWorkflow(
        ApprovalInstance $instance,
        User $submitter,
    ): bool {
        if (! $instance->status instanceof ChangesRequested) {
            return false;
        }

        return DB::transaction(function () use ($instance, $submitter) {
            $firstStep = $this->getNextApplicableStep($instance);

            if (! $firstStep) {
                return false;
            }

            $instance->status->transitionTo(InProgress::class);
            $instance->current_step_id = $firstStep->id;
            $instance->save();

            activity('approval')
                ->performedOn($instance)
                ->causedBy($submitter)
                ->log('Resubmitted for approval');

            // Notify eligible approvers of the resubmission
            $this->resolveEligibleApprovers($firstStep)->each(
                fn (User $approver) => $approver->notify(new ApprovalSubmittedNotification($instance)),
            );

            return true;
        });
    }

    /**
     * Resolve all users eligible to approve a given step.
     */
    public function resolveEligibleApprovers(ApprovalStep $step): Collection
    {
        return match ($step->approver_type->value) {
            'user' => User::whereIn('id', $step->approver_identifiers)->get(),
            'role' => User::role($step->approver_identifiers)->get(),
            'permission' => User::permission($step->approver_identifiers)->get(),
            default => collect(),
        };
    }

    /**
     * Determine if a step should be auto-skipped for a given user.
     *
     * Returns true if:
     * - Auto-skip is enabled in config (default: true)
     * - Step purpose is 'approval' (not 'action')
     * - The requester is in the list of approvers for this step
     */
    protected function shouldSkipStep(ApprovalStep $step, User $requester): bool
    {
        // Always execute action steps
        if ($step->step_purpose === ApprovalStepPurpose::Action) {
            return false;
        }

        // Check if auto-skip is enabled
        if (! config('approval.auto_skip_self_approval', true)) {
            return false;
        }

        // Check if requester can approve this step
        return $this->canActorApprove($requester, $step);
    }

    /**
     * Get the next applicable step (skipping steps where requester is approver).
     *
     * This will recursively skip approval steps where the requester is an approver,
     * but will always include action steps.
     */
    protected function getNextApplicableStep(
        ApprovalInstance $instance,
        ?ApprovalStep $currentStep = null,
    ): ?ApprovalStep {
        $nextStep = $this->getNextStep($instance, $currentStep);

        if (! $nextStep) {
            return null;
        }

        // Get the requester
        $requester = User::find($instance->submitted_by);

        if (! $requester) {
            return $nextStep;
        }

        // Check if this step should be skipped
        if ($this->shouldSkipStep($nextStep, $requester)) {
            // Log the auto-skip
            activity('approval')
                ->performedOn($instance)
                ->causedBy($requester)
                ->log(
                    "Auto-skipped step '{$nextStep->name}' (requester is approver)",
                );

            // Recursively get the next applicable step
            return $this->getNextApplicableStep($instance, $nextStep);
        }

        return $nextStep;
    }
}
