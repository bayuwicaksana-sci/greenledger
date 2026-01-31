<?php

namespace App\States\ApprovalInstance;

class Pending extends ApprovalInstanceState
{
    public function color(): string
    {
        return 'gray';
    }

    public function label(): string
    {
        return 'Pending';
    }
}
