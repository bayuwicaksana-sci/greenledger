<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalSignature extends Model
{
    use HasFactory;

    // Entity type constants
    public const ENTITY_TYPE_PAYMENT_REQUEST = 'PAYMENT_REQUEST';

    public const ENTITY_TYPE_BUDGET = 'BUDGET';

    public const ENTITY_TYPE_SETTLEMENT = 'SETTLEMENT';

    public const ENTITY_TYPE_PROGRAM = 'PROGRAM';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'signature_hash',
        'signature_data',
        'signed_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created the signature.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
