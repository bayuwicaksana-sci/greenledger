<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentRequestSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_request_id',
        'program_id',
        'activity_id',
        'coa_account_id',
        'split_amount',
        'split_percentage',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'split_amount' => 'decimal:2',
            'split_percentage' => 'decimal:2',
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
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the activity.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the COA account.
     */
    public function coaAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class);
    }

    /**
     * Get the settlement splits.
     */
    public function settlementSplits(): HasMany
    {
        return $this->hasMany(SettlementSplit::class, 'payment_split_id');
    }
}
