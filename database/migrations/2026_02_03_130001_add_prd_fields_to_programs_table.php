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
        Schema::table('programs', function (Blueprint $table) {
            // Classification
            $table->string('program_type', 50)->nullable();
            $table->string('program_category', 50)->nullable();
            $table->foreignId('commodity_id')->nullable()->constrained('commodities')->nullOnDelete();

            // Identity
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
            $table->foreignId('prerequisite_program_id')->nullable()->constrained('programs')->nullOnDelete();
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

            // Widen program_name
            $table->string('program_name', 350)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_program_id']);
            $table->dropForeign(['research_officer_id']);
            $table->dropForeign(['research_associate_id']);
            $table->dropForeign(['commodity_id']);

            $table->dropColumn([
                'program_type',
                'program_category',
                'commodity_id',
                'research_associate_id',
                'research_officer_id',
                'planting_start_date',
                'estimated_duration_days',
                'harvest_type',
                'estimated_harvest_date',
                'harvest_frequency_value',
                'harvest_frequency_unit',
                'harvest_event_count',
                'first_harvest_date',
                'last_harvest_date',
                'prerequisite_program_id',
                'dependency_note',
                'background_text',
                'problem_statement',
                'hypothesis',
                'objectives',
                'journal_references',
                'trial_design',
                'trial_design_other',
                'num_treatments',
                'num_replications',
                'num_samples_per_replication',
                'plot_width_m',
                'plot_length_m',
                'google_maps_url',
            ]);

            $table->string('program_name', 200)->nullable()->change();
        });
    }
};
