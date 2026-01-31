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
            $table
                ->enum('step_purpose', ['approval', 'action'])
                ->default('approval')
                ->after('step_type')
                ->comment(
                    'approval: requires approval (can be auto-skipped), action: always executes',
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_steps', function (Blueprint $table) {
            $table->dropColumn('step_purpose');
        });
    }
};
