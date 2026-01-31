<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('approval_instance_id')
                ->constrained('approval_instances')
                ->cascadeOnDelete();
            $table
                ->foreignId('approval_step_id')
                ->constrained('approval_steps')
                ->cascadeOnDelete();
            $table->string('action_type'); // 'approve', 'reject', 'request_changes'
            $table
                ->foreignId('actor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamps(); // Immutable - no updates after creation

            $table->index(['approval_instance_id', 'created_at']);
            $table->index(['actor_id', 'action_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
    }
};
