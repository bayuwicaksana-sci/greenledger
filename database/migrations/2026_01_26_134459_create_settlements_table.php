<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('actual_amount', 15, 2);
            $table->decimal('surplus_amount', 15, 2);
            $table->string('receipt_url', 500)->nullable();
            $table->text('settlement_notes')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 50)->default('PENDING')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Composite index for common query pattern
            $table->index(['status', 'submitted_at']);
        });

        // Check constraints
        DB::statement('ALTER TABLE settlements ADD CONSTRAINT check_settlement_actual_non_negative CHECK (actual_amount >= 0)');
        DB::statement('ALTER TABLE settlements ADD CONSTRAINT check_settlement_surplus_non_negative CHECK (surplus_amount >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE settlements DROP CONSTRAINT IF EXISTS check_settlement_actual_non_negative');
        DB::statement('ALTER TABLE settlements DROP CONSTRAINT IF EXISTS check_settlement_surplus_non_negative');
        Schema::dropIfExists('settlements');
    }
};
