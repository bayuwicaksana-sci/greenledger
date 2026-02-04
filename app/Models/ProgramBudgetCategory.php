<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramBudgetCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_name',
        'sort_order',
    ];

    /**
     * Get the budget items for this category.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProgramBudgetItem::class, 'category_id');
    }
}
