<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoaBudgetAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'coa_account_id',
        'fiscal_year_id',
        'budget_amount',
    ];

    protected function casts(): array
    {
        return [
            'budget_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the COA account for this allocation.
     */
    public function coaAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class);
    }

    /**
     * Get the fiscal year for this allocation.
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }
}
