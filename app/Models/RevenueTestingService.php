<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueTestingService extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_COMPLETED = 'COMPLETED';

    // Payment status constants
    public const PAYMENT_STATUS_PENDING = 'PENDING';

    public const PAYMENT_STATUS_RECEIVED = 'RECEIVED';

    // Client type constants
    public const CLIENT_TYPE_SISTER = 'SISTER_COMPANY';

    public const CLIENT_TYPE_EXTERNAL = 'EXTERNAL';

    protected $fillable = [
        'site_id',
        'program_id',
        'service_number',
        'client_id',
        'client_type',
        'service_type',
        'service_description',
        'contract_value',
        'start_date',
        'completion_date',
        'payment_received_date',
        'payment_status',
        'coa_account_id',
        'contract_url',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'contract_value' => 'decimal:2',
            'start_date' => 'date',
            'completion_date' => 'date',
            'payment_received_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
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
     * Get the client.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the COA account.
     */
    public function coaAccount(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class);
    }

    /**
     * Get the user who approved the service.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
