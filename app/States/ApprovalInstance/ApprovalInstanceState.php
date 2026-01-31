<?php

namespace App\States\ApprovalInstance;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ApprovalInstanceState extends State
{
    abstract public function color(): string;

    abstract public function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, InProgress::class)
            ->allowTransition(InProgress::class, Approved::class)
            ->allowTransition(InProgress::class, Rejected::class)
            ->allowTransition(InProgress::class, ChangesRequested::class)
            ->allowTransition(ChangesRequested::class, InProgress::class)
            ->allowTransition(
                [Pending::class, InProgress::class, ChangesRequested::class],
                Cancelled::class,
            );
    }
}
