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
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('approval_workflow_version_id')
                ->constrained('approval_workflow_versions')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('step_order')->default(0); // For sortable trait
            $table->string('step_type'); // 'sequential' or 'parallel'
            $table->integer('required_approvals_count')->default(1); // For parallel steps
            $table->string('approver_type'); // 'user', 'role', or 'permission'
            $table->json('approver_identifiers'); // Array of user IDs, role names, or permission names
            $table->json('conditional_rules')->nullable(); // Rule DSL
            $table->timestamps();

            $table->index(['approval_workflow_version_id', 'step_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
