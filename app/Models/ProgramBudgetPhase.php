<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramBudgetPhase extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'phase_name',
        'sort_order',
    ];

    /**
     * Get the budget items for this phase.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProgramBudgetItem::class, 'phase_id');
    }
}
