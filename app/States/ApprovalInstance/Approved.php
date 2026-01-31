<?php

namespace App\States\ApprovalInstance;

class Approved extends ApprovalInstanceState
{
    public function color(): string
    {
        return 'green';
    }

    public function label(): string
    {
        return 'Approved';
    }
}
