<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityFinancial extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'budget_allocation',
        'total_spent',
        'budget_utilization_pct',
        'available_budget',
        'transaction_count',
        'last_transaction_at',
    ];

    protected function casts(): array
    {
        return [
            'budget_allocation' => 'decimal:2',
            'total_spent' => 'decimal:2',
            'budget_utilization_pct' => 'decimal:2',
            'available_budget' => 'decimal:2',
            'last_transaction_at' => 'datetime',
        ];
    }

    /**
     * Get the activity.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
