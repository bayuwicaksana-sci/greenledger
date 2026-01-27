<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramAssignment extends Model
{
    use HasFactory;

    // Role constants
    public const ROLE_LEAD = 'LEAD';

    public const ROLE_MEMBER = 'MEMBER';

    public $timestamps = false;

    protected $fillable = [
        'program_id',
        'user_id',
        'role_in_program',
        'assigned_at',
        'assigned_by',
        'removed_at',
        'removed_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    /**
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who assigned.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who removed.
     */
    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'removed_by');
    }
}
