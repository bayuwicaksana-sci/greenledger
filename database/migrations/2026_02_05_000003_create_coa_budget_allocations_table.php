<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coa_budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_account_id')->constrained('coa_accounts')->restrictOnDelete();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->restrictOnDelete();
            $table->decimal('budget_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['coa_account_id', 'fiscal_year_id']);
        });

        // Seed: ensure current year's fiscal_year row exists, then bulk-insert allocations
        $currentYear = (int) date('Y');

        $fiscalYear = DB::table('fiscal_years')->where('year', $currentYear)->first();

        if (! $fiscalYear) {
            DB::table('fiscal_years')->insert([
                'year' => $currentYear,
                'start_date' => "{$currentYear}-01-01",
                'end_date' => "{$currentYear}-12-31",
                'is_closed' => false,
            ]);

            $fiscalYear = DB::table('fiscal_years')->where('year', $currentYear)->first();
        }

        // Bulk-insert one allocation per coa_account that has initial_budget > 0
        $accountsWithBudget = DB::table('coa_accounts')
            ->select('id', 'initial_budget')
            ->where('initial_budget', '>', 0)
            ->get();

        $allocations = [];
        $now = now()->toDateTimeString();

        foreach ($accountsWithBudget as $account) {
            $allocations[] = [
                'coa_account_id' => $account->id,
                'fiscal_year_id' => $fiscalYear->id,
                'budget_amount' => $account->initial_budget,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($allocations)) {
            DB::table('coa_budget_allocations')->insert($allocations);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('coa_budget_allocations');
    }
};
