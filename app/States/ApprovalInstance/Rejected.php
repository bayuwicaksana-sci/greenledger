<?php

namespace App\States\ApprovalInstance;

class Rejected extends ApprovalInstanceState
{
    public function color(): string
    {
        return 'red';
    }

    public function label(): string
    {
        return 'Rejected';
    }
}
