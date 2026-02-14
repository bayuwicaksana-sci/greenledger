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
        Schema::create('coa_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->index()->constrained();
            $table->foreignId('fiscal_year_id')
                ->constrained('fiscal_years')
                ->restrictOnDelete();
            $table->string('account_code')->index();
            $table->string('account_name');
            $table->string('abbreviation', 10)->nullable();
            $table->string('account_type')->index();
            $table->text('short_description')->nullable();
            $table->integer('hierarchy_level');
            $table->foreignId('parent_account_id')
                ->nullable()
                ->constrained('coa_accounts', 'id')
                ->nullOnDelete();
            $table->decimal('initial_budget', 15, 2)->default(0);
            $table->boolean('budget_control')->default(false)->index();
            $table->string('category', 20)->notNull()->default('NON_PROGRAM');
            $table->string('sub_category', 50)->nullable();
            $table->text('typical_usage')->nullable();
            $table->boolean('tax_applicable')->default(false);
            $table->boolean('is_active')->default(false)->index();
            $table->timestamp('first_transaction_at')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->unique(['site_id', 'fiscal_year_id', 'account_code']);
            $table->index('fiscal_year_id');
            $table->index(['site_id', 'account_type', 'is_active'], 'idx_coa_site_type_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_accounts');
    }
};
