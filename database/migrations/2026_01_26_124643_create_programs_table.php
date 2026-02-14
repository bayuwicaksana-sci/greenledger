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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            // Classification
            $table->string('classification', 20)->default('PROGRAM')->index();

            // Core fields
            $table->foreignId('site_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('fiscal_year_id')
                ->constrained('fiscal_years')
                ->restrictOnDelete();
            $table->string('program_code', 50);
            $table->string('program_name', 350);
            $table->text('description')->nullable();

            // Status & budget
            $table->string('status', 50)->default('DRAFT')->index();
            $table->decimal('total_budget', 15, 2);

            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

            // Completion tracking
            $table->text('completion_reason')->nullable();
            $table->string('research_report_url', 500)->nullable();

            // Archival
            $table->boolean('is_archived')->default(false)->index();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('archive_reason')->nullable();

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Completion tracking
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();

            // PRD Classification fields
            $table->string('program_type', 50)->nullable();
            $table->string('program_category', 50)->nullable();
            $table->string('non_program_category', 100)->nullable();
            $table->unsignedBigInteger('commodity_id')->nullable();

            // Research identity
            $table->foreignId('research_associate_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('research_officer_id')->nullable()->constrained('users')->nullOnDelete();

            // Timeline
            $table->date('planting_start_date')->nullable();
            $table->unsignedInteger('estimated_duration_days')->nullable();

            // Harvest
            $table->string('harvest_type', 20)->nullable();
            $table->date('estimated_harvest_date')->nullable();
            $table->unsignedSmallInteger('harvest_frequency_value')->nullable();
            $table->string('harvest_frequency_unit', 10)->nullable();
            $table->unsignedSmallInteger('harvest_event_count')->nullable();
            $table->date('first_harvest_date')->nullable();
            $table->date('last_harvest_date')->nullable();

            // Dependencies
            $table->text('dependency_note')->nullable();

            // Scientific Background
            $table->text('background_text')->nullable();
            $table->text('problem_statement')->nullable();
            $table->text('hypothesis')->nullable();
            $table->json('objectives')->nullable();
            $table->text('journal_references')->nullable();

            // Experimental Design
            $table->string('trial_design', 50)->nullable();
            $table->string('trial_design_other', 100)->nullable();
            $table->unsignedSmallInteger('num_treatments')->nullable();
            $table->unsignedSmallInteger('num_replications')->nullable();
            $table->unsignedSmallInteger('num_samples_per_replication')->nullable();
            $table->decimal('plot_width_m', 8, 2)->nullable();
            $table->decimal('plot_length_m', 8, 2)->nullable();
            $table->string('google_maps_url', 500)->nullable();

            // Audit tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Unique & composite indexes
            $table->unique(['site_id', 'program_code']);
            $table->index(['site_id', 'fiscal_year_id', 'status']);
            $table->index(['site_id', 'is_archived', 'status']);
            $table->index(['fiscal_year_id', 'status', 'is_archived']);
            $table->index('created_by');
        });

        // Self-referencing FK must be added after the table is created
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('prerequisite_program_id')
                ->nullable()
                ->after('last_harvest_date')
                ->constrained('programs')
                ->nullOnDelete();
        });

        // Check constraints (MySQL only)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE programs ADD CONSTRAINT check_programs_budget_positive CHECK (total_budget > 0)');
            DB::statement('ALTER TABLE programs ADD CONSTRAINT check_programs_dates CHECK (actual_end_date IS NULL OR actual_end_date >= actual_start_date)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
