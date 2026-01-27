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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->index()->constrained()->restrictOnDelete();
            $table->string('activity_name', 200);
            $table->text('description')->nullable();
            $table->decimal('budget_allocation', 15, 2);
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->string('status', 50)->default('PLANNED')->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Composite indexes for common query patterns
            $table->index(['program_id', 'status']);
            $table->index(['program_id', 'sort_order']);
        });

        // Check constraints
        DB::statement('ALTER TABLE activities ADD CONSTRAINT check_activities_budget_non_negative CHECK (budget_allocation >= 0)');
        DB::statement('ALTER TABLE activities ADD CONSTRAINT check_activities_dates CHECK (actual_end_date IS NULL OR actual_end_date >= actual_start_date)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE activities DROP CONSTRAINT IF EXISTS check_activities_budget_non_negative');
        DB::statement('ALTER TABLE activities DROP CONSTRAINT IF EXISTS check_activities_dates');
        Schema::dropIfExists('activities');
    }
};
