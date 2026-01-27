<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'site_code',
        'site_name',
        'address',
        'contact_info',
        'is_active',
    ];

    public function getRouteKeyName(): string
    {
        return 'site_code';
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'contact_info' => 'array',
        ];
    }

    /**
     * Get the users that have access to this site.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'site_user')
            ->withPivot('granted_at', 'granted_by')
            ->withTimestamps();
    }

    /**
     * Get the programs for this site.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the COA accounts for this site.
     */
    public function coaAccounts(): HasMany
    {
        return $this->hasMany(CoaAccount::class);
    }

    /**
     * Get the payment requests for this site.
     */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    /**
     * Get the revenue harvests for this site.
     */
    public function revenueHarvests(): HasMany
    {
        return $this->hasMany(RevenueHarvest::class);
    }

    /**
     * Get the revenue testing services for this site.
     */
    public function revenueTestingServices(): HasMany
    {
        return $this->hasMany(RevenueTestingService::class);
    }
}
