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
        Schema::create('subsidi_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number', 50)->unique()->index();
            $table->foreignId('user_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('subsidi_type_id')->index()->constrained()->restrictOnDelete();
            $table->date('claim_month');
            $table->decimal('claim_amount', 15, 2);
            $table->string('evidence_url', 500)->nullable();
            $table->string('status', 50)->default('DRAFT')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Composite indexes for common query patterns
            $table->index(['user_id', 'claim_month']);
            $table->index(['status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsidi_claims');
    }
};
