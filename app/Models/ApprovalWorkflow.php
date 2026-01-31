<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ApprovalWorkflow extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'description', 'model_type', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'model_type', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get all steps for this workflow.
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('step_order');
    }

    /**
     * Get all instances using this workflow.
     */
    public function instances(): HasMany
    {
        return $this->hasMany(ApprovalInstance::class);
    }

    /**
     * Scope to get only active workflows.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get workflows for a specific model.
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('model_type', $modelClass);
    }
}
