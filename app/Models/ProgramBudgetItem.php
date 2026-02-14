<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramBudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'activity_id',
        'category_id',
        'phase_id',
        'coa_account_id',
        'item_description',
        'specification',
        'unit',
        'qty',
        'unit_price',
        'subtotal',
        'days',
        'estimated_realization_date',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'estimated_realization_date' => 'date',
        ];
    }

    /**
     * Get the program that owns this budget item.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Activity this budget item belongs to (nullable - can be unassigned).
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the category for this budget item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProgramBudgetCategory::class, 'category_id');
    }

    /**
     * Get the phase for this budget item.
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProgramBudgetPhase::class, 'phase_id');
    }

    /**
     * Get the COA account for this budget item.
     */
    public function coaAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class);
    }
}
