<?php

namespace App\Models;

use App\Models\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentRequest extends Model
{
    use HasFactory, RequiresApproval;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_PAID = 'PAID';

    public const STATUS_SETTLED = 'SETTLED';

    public const STATUS_CANCELLED = 'CANCELLED';

    // Batch time constants
    public const BATCH_MORNING = 'MORNING';

    public const BATCH_AFTERNOON = 'AFTERNOON';

    protected $fillable = [
        'request_number',
        'site_id',
        'request_date',
        'total_amount',
        'purpose',
        'vendor_name',
        'is_multi_program',
        'quotation_url',
        'status',
        'batch_time',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'paid_at',
        'paid_by',
        'payment_reference',
        'settlement_deadline',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'total_amount' => 'decimal:2',
            'is_multi_program' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'paid_at' => 'datetime',
            'settlement_deadline' => 'datetime',
        ];
    }

    /**
     * Get the site.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the payment splits.
     */
    public function splits(): HasMany
    {
        return $this->hasMany(PaymentRequestSplit::class);
    }

    /**
     * Get the settlement.
     */
    public function settlement(): HasOne
    {
        return $this->hasOne(Settlement::class);
    }

    /**
     * Get the user who approved the request.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the request.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who processed payment.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the user who created the request.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
