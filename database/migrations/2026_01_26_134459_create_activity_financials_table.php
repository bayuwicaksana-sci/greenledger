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
        Schema::create('activity_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('budget_allocation', 15, 2);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->decimal('budget_utilization_pct', 5, 2)->default(0);
            $table->decimal('available_budget', 15, 2)->default(0);
            $table->integer('transaction_count')->default(0);
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
        Schema::dropIfExists('activity_financials');
    }
};
