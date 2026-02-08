<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetCommitment extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const STATUS_PENDING = 'PENDING';

    public const STATUS_COMMITTED = 'COMMITTED';

    public const STATUS_RELEASED = 'RELEASED';

    protected $fillable = [
        'fiscal_year_id',
        'program_id',
        'coa_account_id',
        'amount',
        'description',
        'status',
        'commitment_date',
        'committed_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commitment_date' => 'date',
        ];
    }

    /**
     * Get the fiscal year this commitment belongs to.
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    /**
     * Get the program this commitment belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the COA account this commitment is for.
     */
    public function coaAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class);
    }

    /**
     * Get the user who committed these funds.
     */
    public function committedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'committed_by');
    }
}
