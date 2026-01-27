<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettlementSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_id',
        'payment_split_id',
        'actual_split_amount',
        'surplus_split_amount',
    ];

    protected function casts(): array
    {
        return [
            'actual_split_amount' => 'decimal:2',
            'surplus_split_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the settlement.
     */
    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }

    /**
     * Get the payment split.
     */
    public function paymentSplit(): BelongsTo
    {
        return $this->belongsTo(PaymentRequestSplit::class, 'payment_split_id');
    }
}
