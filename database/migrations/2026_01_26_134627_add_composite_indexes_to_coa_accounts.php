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
        Schema::table('coa_accounts', function (Blueprint $table) {
            // Composite index for common query pattern
            $table->index(['site_id', 'account_type', 'is_active'], 'idx_coa_site_type_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coa_accounts', function (Blueprint $table) {
            $table->dropIndex('idx_coa_site_type_active');
        });
    }
};
