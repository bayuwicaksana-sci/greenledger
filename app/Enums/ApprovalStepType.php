<?php

namespace App\Enums;

enum ApprovalStepType: string
{
    case Sequential = 'sequential';
    case Parallel = 'parallel';

    public function label(): string
    {
        return match ($this) {
            self::Sequential => 'Sequential',
            self::Parallel => 'Parallel',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Sequential => 'Steps execute in order, one at a time',
            self::Parallel => 'Multiple approvers can act concurrently',
        };
    }
}
