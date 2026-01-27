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
        Schema::create('settlement_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_split_id')->index()->constrained('payment_request_splits')->restrictOnDelete();
            $table->decimal('actual_split_amount', 15, 2);
            $table->decimal('surplus_split_amount', 15, 2);
            $table->timestamps();

            // Composite index for common query pattern
            $table->index(['settlement_id', 'payment_split_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_splits');
    }
};
