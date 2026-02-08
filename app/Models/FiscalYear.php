<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['year', 'start_date', 'end_date', 'is_closed'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_closed' => 'boolean',
        ];
    }

    /**
     * Get the programs for this fiscal year.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'fiscal_year_id');
    }

    /**
     * Get the budget allocations for this fiscal year.
     */
    public function budgetAllocations(): HasMany
    {
        return $this->hasMany(CoaBudgetAllocation::class);
    }

    /**
     * Get the COA accounts for this fiscal year.
     */
    public function coaAccounts(): HasMany
    {
        return $this->hasMany(CoaAccount::class);
    }

    /**
     * Get the budget commitments for this fiscal year.
     */
    public function budgetCommitments(): HasMany
    {
        return $this->hasMany(BudgetCommitment::class);
    }

    /**
     * Calculate total committed amount for this fiscal year.
     */
    public function getTotalCommittedAttribute(): float
    {
        return $this->budgetCommitments()
            ->where('status', BudgetCommitment::STATUS_COMMITTED)
            ->sum('amount');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_closed', false);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('is_closed', true);
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('year', $year);
    }
}
