<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueHarvest extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_POSTED = 'POSTED';

    public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';

    public const STATUS_CORRECTED = 'CORRECTED';

    // Payment method constants
    public const PAYMENT_METHOD_CASH = 'CASH';

    public const PAYMENT_METHOD_BANK_TRANSFER = 'BANK_TRANSFER';

    protected $table = 'revenue_harvest';

    protected $fillable = [
        'site_id',
        'program_id',
        'harvest_number',
        'harvest_date',
        'harvest_cycle',
        'crop_type',
        'quantity_kg',
        'price_per_kg',
        'total_revenue',
        'buyer_id',
        'payment_method',
        'payment_date',
        'bank_reference',
        'coa_account_id',
        'notes',
        'status',
        'reviewed_at',
        'reviewed_by',
        'created_by',
        'corrected_by',
        'correction_reason',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'payment_date' => 'date',
            'quantity_kg' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'reviewed_at' => 'datetime',
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
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the buyer.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Get the COA account.
     */
    public function coaAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class);
    }

    /**
     * Get the user who reviewed the harvest.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who corrected the record.
     */
    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
