<?php

namespace App\States\ApprovalInstance;

class Cancelled extends ApprovalInstanceState
{
    public function color(): string
    {
        return 'gray';
    }

    public function label(): string
    {
        return 'Cancelled';
    }
}
