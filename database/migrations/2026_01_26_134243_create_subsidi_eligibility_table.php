<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subsidi_eligibility', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('subsidi_type_id')
                ->index()
                ->constrained()
                ->restrictOnDelete();
            $table
                ->foreignId('user_id')
                ->index()
                ->constrained()
                ->restrictOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Composite indexes for common query patterns
            $table->index(['subsidi_type_id', 'user_id', 'effective_from']);
            $table->index(['user_id', 'is_active']);

            // Unique constraint to prevent duplicate eligibility records
            $table->unique(
                ['subsidi_type_id', 'user_id', 'effective_from'],
                'subsidi_user_from_unq',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsidi_eligibility');
    }
};
