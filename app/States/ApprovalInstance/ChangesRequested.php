<?php

namespace App\States\ApprovalInstance;

class ChangesRequested extends ApprovalInstanceState
{
    public function color(): string
    {
        return 'yellow';
    }

    public function label(): string
    {
        return 'Changes Requested';
    }
}
