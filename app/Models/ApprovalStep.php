<?php

namespace App\Models;

use App\Enums\ApprovalStepPurpose;
use App\Enums\ApprovalStepType;
use App\Enums\ApproverType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ApprovalStep extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $fillable = [
        'approval_workflow_id',
        'name',
        'description',
        'step_order',
        'step_type',
        'step_purpose',
        'required_approvals_count',
        'approver_type',
        'approver_identifiers',
        'conditional_rules',
    ];

    public $sortable = [
        'order_column_name' => 'step_order',
        'sort_when_creating' => true,
    ];

    protected function casts(): array
    {
        return [
            'step_type' => ApprovalStepType::class,
            'step_purpose' => ApprovalStepPurpose::class,
            'approver_type' => ApproverType::class,
            'approver_identifiers' => 'array',
            'conditional_rules' => 'array',
            'required_approvals_count' => 'integer',
        ];
    }

    /**
     * Get the workflow this step belongs to.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalWorkflow::class,
            'approval_workflow_id',
        );
    }

    /**
     * Get the approval actions for this step.
     */
    public function actions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class);
    }

    /**
     * Build the sortable query.
     */
    public function buildSortQuery()
    {
        return static::query()->where(
            'approval_workflow_id',
            $this->approval_workflow_id,
        );
    }
}
