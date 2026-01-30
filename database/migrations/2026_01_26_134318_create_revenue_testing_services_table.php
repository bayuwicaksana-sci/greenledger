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
        Schema::create('revenue_testing_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('program_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->string('service_number', 50)->unique()->index();
            $table->foreignId('client_id')->index()->constrained()->restrictOnDelete();
            $table->string('client_type', 50);
            $table->string('service_type', 100);
            $table->text('service_description');
            $table->decimal('contract_value', 15, 2);
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->date('payment_received_date')->nullable();
            $table->string('payment_status', 50)->default('PENDING')->index();
            $table->foreignId('coa_account_id')->index()->constrained()->restrictOnDelete();
            $table->string('contract_url', 500)->nullable();
            $table->string('status', 50)->default('DRAFT')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Composite indexes for common query patterns
            $table->index(['site_id', 'payment_status']);
            $table->index(['client_id', 'status']);
        });

        // Check constraint (only for databases that support ALTER TABLE CHECK)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE revenue_testing_services ADD CONSTRAINT check_testing_contract_positive CHECK (contract_value > 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE revenue_testing_services DROP CONSTRAINT IF EXISTS check_testing_contract_positive');
        }
        Schema::dropIfExists('revenue_testing_services');
    }
};
