<?php

namespace App\Models;

use App\Enums\ApprovalActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ApprovalAction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'approval_instance_id',
        'approval_step_id',
        'action_type',
        'actor_id',
        'comments',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'action_type' => ApprovalActionType::class,
            'metadata' => 'array',
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
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the approval instance this action belongs to.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalInstance::class,
            'approval_instance_id',
        );
    }

    /**
     * Get the step this action was taken on.
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    }

    /**
     * Get the user who performed this action.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Prevent updates to approval actions (immutable).
     */
    protected static function booted(): void
    {
        static::updating(function () {
            return false; // Prevent updates
        });
    }
}
