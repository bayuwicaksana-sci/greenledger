<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubsidiClaim extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_PAID = 'PAID';

    protected $fillable = [
        'claim_number',
        'user_id',
        'subsidi_type_id',
        'claim_month',
        'claim_amount',
        'evidence_url',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'paid_at',
        'paid_by',
    ];

    protected function casts(): array
    {
        return [
            'claim_month' => 'date',
            'claim_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the user who made the claim.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subsidi type.
     */
    public function subsidiType(): BelongsTo
    {
        return $this->belongsTo(SubsidiType::class);
    }

    /**
     * Get the user who approved the claim.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed payment.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
