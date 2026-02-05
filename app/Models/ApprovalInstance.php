<?php

namespace App\Models;

use App\States\ApprovalInstance\Approved;
use App\States\ApprovalInstance\ApprovalInstanceState;
use App\States\ApprovalInstance\Cancelled;
use App\States\ApprovalInstance\ChangesRequested;
use App\States\ApprovalInstance\InProgress;
use App\States\ApprovalInstance\Pending;
use App\States\ApprovalInstance\Rejected;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\ModelStates\HasStates;

class ApprovalInstance extends Model
{
    use HasFactory, HasStates, LogsActivity, SoftDeletes;

    protected $appends = ['status_value', 'status_label', 'status_color'];

    protected $fillable = [
        'approval_workflow_id',
        'approvable_type',
        'approvable_id',
        'status',
        'current_step_id',
        'submitted_by',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalInstanceState::class,
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('approval');
    }

    /**
     * Get the model being approved.
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the workflow.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalWorkflow::class,
            'approval_workflow_id',
        );
    }

    /**
     * Get the current step.
     */
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(ApprovalStep::class, 'current_step_id');
    }

    /**
     * Get the user who submitted this for approval.
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get all approval actions.
     */
    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class)->orderBy('created_at');
    }

    /**
     * Get a frontend-compatible status string value.
     */
    public function getStatusValueAttribute(): string
    {
        return match (true) {
            $this->status instanceof Approved => 'approved',
            $this->status instanceof Rejected => 'rejected',
            $this->status instanceof Cancelled => 'cancelled',
            $this->status instanceof ChangesRequested => 'changes_requested',
            $this->status instanceof InProgress => 'pending_approval',
            $this->status instanceof Pending => 'pending',
            default => 'pending',
        };
    }

    /**
     * Get the human-readable status label from the state class.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Get the UI color for the current status.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }
}
