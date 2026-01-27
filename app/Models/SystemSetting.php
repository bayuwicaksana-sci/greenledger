<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_system',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'setting_value' => 'array',
            'is_system' => 'boolean',
        ];
    }

    /**
     * Get the user who last updated the setting.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
