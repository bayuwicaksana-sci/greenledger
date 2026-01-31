<?php

namespace App\Models\Traits;

use App\Enums\ApprovalInstanceStatus;
use App\Models\ApprovalInstance;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait RequiresApproval
{
    /**
     * Get all approval instances for this model.
     */
    public function approvalInstances(): MorphMany
    {
        return $this->morphMany(ApprovalInstance::class, 'approvable');
    }

    /**
     * Get the active approval instance.
     */
    public function getActiveApprovalInstance(): ?ApprovalInstance
    {
        return $this->approvalInstances()
            ->whereIn('status', [
                ApprovalInstanceStatus::Draft,
                ApprovalInstanceStatus::PendingApproval,
            ])
            ->latest()
            ->first();
    }

    /**
     * Check if this model has an active approval.
     */
    public function hasActiveApproval(): bool
    {
        return $this->getActiveApprovalInstance() !== null;
    }

    /**
     * Submit this model for approval.
     */
    public function submitForApproval(
        int $workflowId,
        ?int $userId = null,
    ): ApprovalInstance {
        // This will be implemented by the WorkflowEngine service
        // For now, just return a placeholder
        throw new \RuntimeException(
            'Use WorkflowEngine::initializeWorkflow() to submit for approval',
        );
    }

    /**
     * Get the latest approval status.
     */
    public function getApprovalStatus(): ?ApprovalInstanceStatus
    {
        $instance = $this->approvalInstances()->latest()->first();

        return $instance?->status;
    }

    /**
     * Check if this model is approved.
     */
    public function isApproved(): bool
    {
        return $this->getApprovalStatus() === ApprovalInstanceStatus::Approved;
    }

    /**
     * Check if this model is pending approval.
     */
    public function isPendingApproval(): bool
    {
        return $this->getApprovalStatus() ===
            ApprovalInstanceStatus::PendingApproval;
    }

    /**
     * Get the display name for this model in approval workflows.
     *
     * Can be overridden in the model to provide a custom name.
     */
    public static function getApprovalDisplayName(): string
    {
        // Check if model has a custom display name property
        if (defined('static::APPROVAL_DISPLAY_NAME')) {
            return static::APPROVAL_DISPLAY_NAME;
        }

        // Fall back to auto-generated name from class name
        $className = class_basename(static::class);

        // Convert PascalCase to Title Case with spaces
        return preg_replace('/(?<!^)([A-Z])/', ' $1', $className);
    }
}
