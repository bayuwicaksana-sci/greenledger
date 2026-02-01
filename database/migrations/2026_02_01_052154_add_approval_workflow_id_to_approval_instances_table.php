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
        Schema::table('approval_instances', function (Blueprint $table) {
            // Add the approval_workflow_id column
            $table
                ->foreignId('approval_workflow_id')
                ->nullable()
                ->after('id')
                ->constrained('approval_workflows')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_instances', function (Blueprint $table) {
            $table->dropForeign(['approval_workflow_id']);
            $table->dropColumn('approval_workflow_id');
        });
    }
};
