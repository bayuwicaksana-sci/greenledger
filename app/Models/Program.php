<?php

namespace App\Models;

use App\Models\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Program extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RequiresApproval, SoftDeletes;

    // Status constants
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_ARCHIVED = 'ARCHIVED';

    // Classification constants
    public const CLASSIFICATION_PROGRAM = 'PROGRAM';

    public const CLASSIFICATION_NON_PROGRAM = 'NON_PROGRAM';

    protected $fillable = [
        'classification',
        'site_id',
        'program_code',
        'program_name',
        'description',
        'fiscal_year_id',
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
        // PRD fields
        'program_type',
        'program_category',
        'non_program_category',
        'commodity_id',
        'research_associate_id',
        'research_officer_id',
        'planting_start_date',
        'estimated_duration_days',
        'harvest_type',
        'estimated_harvest_date',
        'harvest_frequency_value',
        'harvest_frequency_unit',
        'harvest_event_count',
        'first_harvest_date',
        'last_harvest_date',
        'prerequisite_program_id',
        'dependency_note',
        'background_text',
        'problem_statement',
        'hypothesis',
        'objectives',
        'journal_references',
        'trial_design',
        'trial_design_other',
        'num_treatments',
        'num_replications',
        'num_samples_per_replication',
        'plot_width_m',
        'plot_length_m',
        'google_maps_url',
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
            'objectives' => 'json',
            'planting_start_date' => 'date',
            'estimated_harvest_date' => 'date',
            'first_harvest_date' => 'date',
            'last_harvest_date' => 'date',
            'plot_width_m' => 'decimal:2',
            'plot_length_m' => 'decimal:2',
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
     * Get the fiscal year this program belongs to.
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
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

    /**
     * Get the commodity for this program.
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Get the research associate user.
     */
    public function researchAssociate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'research_associate_id');
    }

    /**
     * Get the research officer user.
     */
    public function researchOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'research_officer_id');
    }

    /**
     * Get the prerequisite program (self-referential).
     */
    public function prerequisiteProgram(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'prerequisite_program_id');
    }

    /**
     * Get the treatments for this program.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(ProgramTreatment::class)->orderBy('sort_order');
    }

    /**
     * Get the budget items for this program.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProgramBudgetItem::class)->orderBy('sort_order');
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('plot_map')->singleFile();

        $this->addMediaCollection('reference_files');
    }
}
