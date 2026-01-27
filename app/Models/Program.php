<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_ARCHIVED = 'ARCHIVED';

    protected $fillable = [
        'site_id',
        'program_code',
        'program_name',
        'description',
        'fiscal_year',
        'status',
        'total_budget',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'completion_reason',
        'research_report_url',
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason',
        'approved_by',
        'approved_at',
        'completed_by',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'total_budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
            'is_archived' => 'boolean',
            'archived_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the site that owns the program.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the activities for the program.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the program assignments.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ProgramAssignment::class);
    }

    /**
     * Get the users assigned to the program.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'program_assignments')
            ->withPivot('role_in_program', 'assigned_at', 'removed_at')
            ->withTimestamps();
    }

    /**
     * Get the revenue harvests for the program.
     */
    public function revenueHarvests(): HasMany
    {
        return $this->hasMany(RevenueHarvest::class);
    }

    /**
     * Get the revenue testing services for the program.
     */
    public function revenueTestingServices(): HasMany
    {
        return $this->hasMany(RevenueTestingService::class);
    }

    /**
     * Get the financial record for the program.
     */
    public function financials(): HasOne
    {
        return $this->hasOne(ProgramFinancial::class);
    }

    /**
     * Get the user who created the program.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the program.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the program.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who completed the program.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the user who archived the program.
     */
    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
