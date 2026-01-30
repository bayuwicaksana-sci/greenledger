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
        Schema::table('programs', function (Blueprint $table) {
            // Core fields
            $table->foreignId('site_id')->after('id')->index()->constrained()->restrictOnDelete();
            $table->string('program_code', 50)->unique()->after('site_id');
            $table->string('program_name', 200)->after('program_code');
            $table->text('description')->nullable()->after('program_name');

            // Tracking fields
            $table->integer('fiscal_year')->index()->after('description');
            $table->string('status', 50)->default('DRAFT')->index()->after('fiscal_year');
            $table->decimal('total_budget', 15, 2)->after('status');

            // Dates
            $table->date('start_date')->nullable()->after('total_budget');
            $table->date('end_date')->nullable()->after('start_date');
            $table->date('actual_start_date')->nullable()->after('end_date');
            $table->date('actual_end_date')->nullable()->after('actual_start_date');

            // Completion tracking
            $table->text('completion_reason')->nullable()->after('actual_end_date');
            $table->string('research_report_url', 500)->nullable()->after('completion_reason');

            // Archival
            $table->boolean('is_archived')->default(false)->index()->after('research_report_url');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete()->after('archived_at');
            $table->text('archive_reason')->nullable()->after('archived_by');

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('archive_reason');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Completion tracking
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            $table->timestamp('completed_at')->nullable()->after('completed_by');

            // Audit tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('completed_at');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');

            // Composite indexes for performance
            $table->index(['site_id', 'fiscal_year', 'status']);
            $table->index(['site_id', 'is_archived', 'status']);
            $table->index(['fiscal_year', 'status', 'is_archived']);
            $table->index('created_by');

            // Soft deletes
            $table->softDeletes()->after('updated_at');
        });

        // Check constraints (only for databases that support ALTER TABLE CHECK)
        // SQLite doesn't support adding CHECK constraints via ALTER TABLE
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
        // Drop check constraints first (only for non-SQLite databases)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE programs DROP CONSTRAINT IF EXISTS check_programs_budget_positive');
            DB::statement('ALTER TABLE programs DROP CONSTRAINT IF EXISTS check_programs_dates');
        }

        Schema::table('programs', function (Blueprint $table) {

            // Drop indexes
            $table->dropIndex(['site_id', 'fiscal_year', 'status']);
            $table->dropIndex(['site_id', 'is_archived', 'status']);
            $table->dropIndex(['fiscal_year', 'status', 'is_archived']);
            $table->dropIndex(['created_by']);

            // Drop columns in reverse order
            $table->dropSoftDeletes();
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['completed_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['archived_by']);
            $table->dropForeign(['site_id']);

            $table->dropColumn([
                'site_id',
                'program_code',
                'program_name',
                'description',
                'fiscal_year',
                'status',
                'total_budget',
                'start_date',
                'end_date',
                'actual_start_date',
                'actual_end_date',
                'completion_reason',
                'research_report_url',
                'is_archived',
                'archived_at',
                'archived_by',
                'archive_reason',
                'approved_by',
                'approved_at',
                'completed_by',
                'completed_at',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
