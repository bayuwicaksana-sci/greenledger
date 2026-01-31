<?php

namespace App\Enums;

enum ApprovalStepPurpose: string
{
    case Approval = 'approval';
    case Action = 'action';

    public function label(): string
    {
        return match ($this) {
            self::Approval => 'Approval',
            self::Action => 'Action',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Approval => 'Requires approval (can be auto-skipped if requester is approver)',
            self::Action => 'Always executes regardless of requester',
        };
    }

    /**
     * Determine if this step requires approval.
     */
    public function requiresApproval(): bool
    {
        return $this === self::Approval;
    }

    /**
     * Determine if this step is an action step.
     */
    public function isActionStep(): bool
    {
        return $this === self::Action;
    }
}
