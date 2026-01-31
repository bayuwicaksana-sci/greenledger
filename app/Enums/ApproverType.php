<?php

namespace App\Enums;

enum ApproverType: string
{
    case User = 'user';
    case Role = 'role';
    case Permission = 'permission';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Specific User',
            self::Role => 'Role',
            self::Permission => 'Permission',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::User => 'Assign specific users as approvers',
            self::Role => 'Assign users with a specific role as approvers',
            self::Permission => 'Assign users with a specific permission as approvers',
        };
    }
}
