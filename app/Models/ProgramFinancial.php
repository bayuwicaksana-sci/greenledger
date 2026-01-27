<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramFinancial extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'total_budget',
        'total_spent',
        'total_revenue',
        'harvest_revenue',
        'testing_revenue',
        'net_income',
        'budget_utilization_pct',
        'available_budget',
        'pending_settlements',
        'last_transaction_at',
    ];

    protected function casts(): array
    {
        return [
            'total_budget' => 'decimal:2',
            'total_spent' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'harvest_revenue' => 'decimal:2',
            'testing_revenue' => 'decimal:2',
            'net_income' => 'decimal:2',
            'budget_utilization_pct' => 'decimal:2',
            'available_budget' => 'decimal:2',
            'pending_settlements' => 'decimal:2',
            'last_transaction_at' => 'datetime',
        ];
    }

    /**
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
