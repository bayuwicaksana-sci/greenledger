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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 50)->unique()->index();
            $table->foreignId('site_id')->index()->constrained()->restrictOnDelete();
            $table->date('request_date');
            $table->decimal('total_amount', 15, 2);
            $table->text('purpose');
            $table->string('vendor_name', 200)->nullable();
            $table->boolean('is_multi_program')->default(false);
            $table->string('quotation_url', 500)->nullable();
            $table->string('status', 50)->default('DRAFT')->index();
            $table->string('batch_time', 50)->nullable()->index();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference', 100)->nullable();
            $table->timestamp('settlement_deadline')->nullable()->index();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Composite indexes for common query patterns
            $table->index(['site_id', 'status', 'created_at']);
            $table->index(['created_by', 'status', 'created_at']);
            $table->index(['status', 'settlement_deadline']);
        });

        // Check constraint (only for databases that support ALTER TABLE CHECK)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE payment_requests ADD CONSTRAINT check_payment_requests_amount_positive CHECK (total_amount > 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE payment_requests DROP CONSTRAINT IF EXISTS check_payment_requests_amount_positive');
        }
        Schema::dropIfExists('payment_requests');
    }
};
