<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SiteUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $casts = [
        'granted_at' => 'date',
    ];
}
