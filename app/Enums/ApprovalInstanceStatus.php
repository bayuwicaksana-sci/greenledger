<?php

namespace App\Enums;

enum ApprovalInstanceStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingApproval => 'Pending Approval',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::PendingApproval => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'secondary',
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::Draft => in_array($newStatus, [
                self::PendingApproval,
                self::Cancelled,
            ]),
            self::PendingApproval => in_array($newStatus, [
                self::Approved,
                self::Rejected,
                self::Cancelled,
            ]),
            self::Approved, self::Rejected, self::Cancelled => false,
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Approved,
            self::Rejected,
            self::Cancelled,
        ]);
    }
}
