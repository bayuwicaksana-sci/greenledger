<?php

namespace App\Models;

use App\Models\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory, RequiresApproval;

    // Status constants
    public const STATUS_PENDING = 'PENDING';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_REVISION_REQUESTED = 'REVISION_REQUESTED';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'payment_request_id',
        'actual_amount',
        'surplus_amount',
        'receipt_url',
        'settlement_notes',
        'submitted_at',
        'submitted_by',
        'status',
        'reviewed_at',
        'reviewed_by',
        'review_notes',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'actual_amount' => 'decimal:2',
            'surplus_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the payment request.
     */
    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    /**
     * Get the settlement splits.
     */
    public function splits(): HasMany
    {
        return $this->hasMany(SettlementSplit::class);
    }

    /**
     * Get the user who submitted the settlement.
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who reviewed the settlement.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who approved the settlement.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
