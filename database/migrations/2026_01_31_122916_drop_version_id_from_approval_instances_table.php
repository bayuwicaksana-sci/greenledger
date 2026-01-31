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
            $table->dropForeign(['approval_workflow_version_id']);
            $table->dropColumn('approval_workflow_version_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_instances', function (Blueprint $table) {
            $table
                ->foreignId('approval_workflow_version_id')
                ->after('id')
                ->constrained('approval_workflow_versions')
                ->cascadeOnDelete();
        });
    }
};
