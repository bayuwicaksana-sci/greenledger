<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    // Client type constants
    public const CLIENT_TYPE_SISTER = 'SISTER_COMPANY';

    public const CLIENT_TYPE_EXTERNAL = 'EXTERNAL';

    protected $fillable = [
        'client_name',
        'client_type',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the revenue testing services for this client.
     */
    public function revenueTestingServices(): HasMany
    {
        return $this->hasMany(RevenueTestingService::class);
    }
}
