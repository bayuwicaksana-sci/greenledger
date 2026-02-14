<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_PLANNED = 'PLANNED';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'program_id',
        'activity_name',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
        ];
    }

    /**
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Budget items assigned to this activity.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProgramBudgetItem::class, 'activity_id');
    }

    /**
     * Get the payment splits.
     */
    public function paymentSplits(): HasMany
    {
        return $this->hasMany(PaymentRequestSplit::class);
    }

    /**
     * Get the financial record.
     */
    public function financials(): HasOne
    {
        return $this->hasOne(ActivityFinancial::class);
    }

    /**
     * Get the user who created the activity.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the activity.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calculate total budget from BoQ items.
     */
    public function getBudgetAttribute(): float
    {
        return $this->budgetItems()->sum('subtotal');
    }

    /**
     * Get budget as formatted string for display.
     */
    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp '.number_format($this->budget, 0, ',', '.');
    }
}
