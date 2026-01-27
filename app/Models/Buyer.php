<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buyer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'buyer_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'payment_terms',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the revenue harvests for this buyer.
     */
    public function revenueHarvests(): HasMany
    {
        return $this->hasMany(RevenueHarvest::class);
    }
}
