<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fiscal_year_id column (nullable initially for migration)
        Schema::table('coa_accounts', function (Blueprint $table) {
            $table
                ->foreignId('fiscal_year_id')
                ->nullable()
                ->after('site_id')
                ->constrained('fiscal_years')
                ->restrictOnDelete();

            $table->index('fiscal_year_id');
        });

        // Data migration: assign existing accounts to current active fiscal year
        $currentFY = DB::table('fiscal_years')
            ->where('is_closed', false)
            ->orderBy('year', 'desc')
            ->first();

        if ($currentFY) {
            DB::table('coa_accounts')
                ->whereNull('fiscal_year_id')
                ->update(['fiscal_year_id' => $currentFY->id]);
        }

        // Make fiscal_year_id NOT NULL after migration
        Schema::table('coa_accounts', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable(false)->change();
        });

        // Update unique constraint to include fiscal_year_id
        Schema::table('coa_accounts', function (Blueprint $table) {
            $table->dropUnique(['site_id', 'account_code']);
            $table->unique(['site_id', 'fiscal_year_id', 'account_code']);
        });
    }

    public function down(): void
    {
        Schema::table('coa_accounts', function (Blueprint $table) {
            // Restore original unique constraint
            $table->dropUnique(['site_id', 'fiscal_year_id', 'account_code']);
            $table->unique(['site_id', 'account_code']);

            // Drop foreign key and column
            $table->dropForeign(['fiscal_year_id']);
            $table->dropIndex(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });
    }
};
