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
        Schema::table('programs', function (Blueprint $table) {
            // Distinguish between "Research Program" and "Non-Program"
            // Values: 'PROGRAM', 'NON_PROGRAM'
            $table->string('classification', 20)->default('PROGRAM')->after('id')->index();

            // For Non-Programs
            $table->string('non_program_category', 100)->nullable()->after('program_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['classification', 'non_program_category']);
        });
    }
};
