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
        Schema::table('program_budget_items', function (Blueprint $table) {
            $table->foreignId('coa_account_id')->nullable()->after('phase_id')
                ->constrained('coa_accounts')->nullOnDelete();
            $table->unsignedSmallInteger('days')->nullable()->after('qty')
                ->comment('Days from program start to realization');
            $table->date('estimated_realization_date')->nullable()->after('days');

            $table->index('coa_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_budget_items', function (Blueprint $table) {
            $table->dropForeign(['coa_account_id']);
            $table->dropIndex(['coa_account_id']);
            $table->dropColumn(['coa_account_id', 'days', 'estimated_realization_date']);
        });
    }
};
