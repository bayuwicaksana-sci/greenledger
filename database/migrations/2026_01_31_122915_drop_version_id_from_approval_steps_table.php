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
        Schema::table('approval_steps', function (Blueprint $table) {
            // Drop index first (SQLite requirement)
            $table->dropIndex(
                'approval_steps_approval_workflow_version_id_step_order_index',
            );
            // Then drop foreign key
            $table->dropForeign(['approval_workflow_version_id']);
            // Finally drop column
            $table->dropColumn('approval_workflow_version_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_steps', function (Blueprint $table) {
            $table
                ->foreignId('approval_workflow_version_id')
                ->constrained('approval_workflow_versions')
                ->cascadeOnDelete();
        });
    }
};
