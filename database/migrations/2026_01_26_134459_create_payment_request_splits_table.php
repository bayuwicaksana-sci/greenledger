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
        Schema::create('payment_request_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('activity_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('coa_account_id')->index()->constrained()->restrictOnDelete();
            $table->decimal('split_amount', 15, 2);
            $table->decimal('split_percentage', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Composite indexes for common query patterns
            $table->index(['program_id', 'activity_id', 'created_at']);
            $table->index(['activity_id', 'program_id']);
        });

        // Check constraints
        DB::statement('ALTER TABLE payment_request_splits ADD CONSTRAINT check_split_amount_positive CHECK (split_amount > 0)');
        DB::statement('ALTER TABLE payment_request_splits ADD CONSTRAINT check_split_percentage_range CHECK (split_percentage >= 0 AND split_percentage <= 100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE payment_request_splits DROP CONSTRAINT IF EXISTS check_split_amount_positive');
        DB::statement('ALTER TABLE payment_request_splits DROP CONSTRAINT IF EXISTS check_split_percentage_range');
        Schema::dropIfExists('payment_request_splits');
    }
};
