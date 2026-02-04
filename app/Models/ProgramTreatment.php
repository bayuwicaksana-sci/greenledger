<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'treatment_code',
        'treatment_description',
        'specification',
        'sort_order',
    ];

    /**
     * Get the program that owns this treatment.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
