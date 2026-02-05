<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coa_accounts', function (Blueprint $table) {
            $table->string('category', 20)->notNull()->default('NON_PROGRAM');
            $table->string('sub_category', 50)->nullable();
            $table->text('typical_usage')->nullable();
            $table->boolean('tax_applicable')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('coa_accounts', function (Blueprint $table) {
            $table->dropColumn(['category', 'sub_category', 'typical_usage', 'tax_applicable']);
        });
    }
};
