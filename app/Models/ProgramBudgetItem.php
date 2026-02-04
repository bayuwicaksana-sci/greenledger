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
        'category_id',
        'phase_id',
        'item_description',
        'specification',
        'unit',
        'qty',
        'unit_price',
        'subtotal',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
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
}
