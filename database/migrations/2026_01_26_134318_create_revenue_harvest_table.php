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
        Schema::create('revenue_harvest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('program_id')->index()->constrained()->restrictOnDelete();
            $table->string('harvest_number', 50)->index();
            $table->date('harvest_date')->index();
            $table->integer('harvest_cycle');
            $table->string('crop_type', 100);
            $table->decimal('quantity_kg', 10, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total_revenue', 15, 2);
            $table->foreignId('buyer_id')->index()->constrained()->restrictOnDelete();
            $table->string('payment_method', 50);
            $table->date('payment_date');
            $table->string('bank_reference', 100)->nullable();
            $table->foreignId('coa_account_id')->index()->constrained()->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->string('status', 50)->default('POSTED')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('corrected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('correction_reason')->nullable();

            // Composite indexes for common query patterns
            $table->index(['program_id', 'harvest_date', 'harvest_cycle']);
            $table->index(['site_id', 'harvest_date']);

            // Unique constraint to prevent duplicate harvest numbers per site
            $table->unique(['site_id', 'harvest_number']);
        });

        // Check constraints
        DB::statement('ALTER TABLE revenue_harvest ADD CONSTRAINT check_harvest_quantity_positive CHECK (quantity_kg > 0)');
        DB::statement('ALTER TABLE revenue_harvest ADD CONSTRAINT check_harvest_price_positive CHECK (price_per_kg > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE revenue_harvest DROP CONSTRAINT IF EXISTS check_harvest_quantity_positive');
        DB::statement('ALTER TABLE revenue_harvest DROP CONSTRAINT IF EXISTS check_harvest_price_positive');
        Schema::dropIfExists('revenue_harvest');
    }
};
