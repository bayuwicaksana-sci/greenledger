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
        Schema::create('program_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('total_budget', 15, 2);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('harvest_revenue', 15, 2)->default(0);
            $table->decimal('testing_revenue', 15, 2)->default(0);
            $table->decimal('net_income', 15, 2)->default(0);
            $table->decimal('budget_utilization_pct', 5, 2)->default(0);
            $table->decimal('available_budget', 15, 2)->default(0);
            $table->decimal('pending_settlements', 15, 2)->default(0);
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamps();

            // Index for tracking financial activity
            $table->index('last_transaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_financials');
    }
};
