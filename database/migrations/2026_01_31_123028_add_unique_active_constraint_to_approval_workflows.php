<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip for SQLite as it doesn't support generated columns with IF()
        // The unique constraint will be enforced at the application level for SQLite
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('approval_workflows', function (Blueprint $table) {
            // Add a generated column for active model types (MySQL compatible)
            DB::statement(
                'ALTER TABLE approval_workflows ADD COLUMN active_model_type VARCHAR(255) GENERATED ALWAYS AS (IF(is_active = 1, model_type, NULL)) STORED',
            );

            // Add unique constraint on the generated column
            $table->unique('active_model_type', 'unique_active_workflow');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('approval_workflows', function (Blueprint $table) {
            $table->dropUnique('unique_active_workflow');
            DB::statement(
                'ALTER TABLE approval_workflows DROP COLUMN active_model_type',
            );
        });
    }
};
