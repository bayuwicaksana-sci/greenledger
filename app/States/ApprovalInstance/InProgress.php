<?php

namespace App\States\ApprovalInstance;

class InProgress extends ApprovalInstanceState
{
    public function color(): string
    {
        return 'blue';
    }

    public function label(): string
    {
        return 'In Progress';
    }
}
