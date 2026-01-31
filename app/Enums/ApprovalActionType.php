<?php

namespace App\Enums;

enum ApprovalActionType: string
{
    case Approve = 'approve';
    case Reject = 'reject';
    case RequestChanges = 'request_changes';

    public function label(): string
    {
        return match ($this) {
            self::Approve => 'Approve',
            self::Reject => 'Reject',
            self::RequestChanges => 'Request Changes',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Approve => 'success',
            self::Reject => 'danger',
            self::RequestChanges => 'warning',
        };
    }
}
