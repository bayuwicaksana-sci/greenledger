<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_assignments', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('program_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();
            $table
                ->foreignId('user_id')
                ->index()
                ->constrained()
                ->restrictOnDelete();
            $table->string('role_in_program', 50);
            $table->timestamp('assigned_at')->useCurrent();
            $table
                ->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('removed_at')->nullable();
            $table
                ->foreignId('removed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Composite indexes for common query patterns
            $table->index(['program_id', 'user_id', 'role_in_program']);
            $table->index(['user_id', 'removed_at']);
        });

        // Create partial unique index for active assignments
        // DB::statement('CREATE UNIQUE INDEX idx_program_user_active ON program_assignments (program_id, user_id) WHERE removed_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // DB::statement('DROP INDEX IF EXISTS idx_program_user_active');
        Schema::dropIfExists('program_assignments');
    }
};
