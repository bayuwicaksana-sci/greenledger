<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubsidiType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_code',
        'type_name',
        'description',
        'monthly_pagu',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_pagu' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the eligibilities for this subsidi type.
     */
    public function eligibilities(): HasMany
    {
        return $this->hasMany(SubsidiEligibility::class);
    }

    /**
     * Get the claims for this subsidi type.
     */
    public function claims(): HasMany
    {
        return $this->hasMany(SubsidiClaim::class);
    }
}
