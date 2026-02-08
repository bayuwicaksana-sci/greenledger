<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_commitments', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('fiscal_year_id')
                ->constrained('fiscal_years')
                ->restrictOnDelete();
            $table
                ->foreignId('program_id')
                ->constrained('programs')
                ->restrictOnDelete();
            $table
                ->foreignId('coa_account_id')
                ->constrained('coa_accounts')
                ->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->string('status')->default('PENDING'); // PENDING, COMMITTED, RELEASED
            $table->date('commitment_date');
            $table
                ->foreignId('committed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['fiscal_year_id', 'status']);
            $table->index('program_id');
            $table->index('coa_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_commitments');
    }
};
