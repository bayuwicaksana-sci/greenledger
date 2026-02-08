<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new fiscal_year_id column if it doesn't exist
        if (! Schema::hasColumn('programs', 'fiscal_year_id')) {
            Schema::table('programs', function (Blueprint $table) {
                $table
                    ->foreignId('fiscal_year_id')
                    ->nullable()
                    ->after('site_id')
                    ->constrained('fiscal_years')
                    ->restrictOnDelete();
            });
        }

        // Migrate data: match fiscal_year integer to fiscal_years.year (only if old column exists)
        if (Schema::hasColumn('programs', 'fiscal_year')) {
            DB::statement('
                UPDATE programs p
                SET fiscal_year_id = (
                    SELECT id FROM fiscal_years 
                    WHERE year = p.fiscal_year
                    LIMIT 1
                )
                WHERE p.fiscal_year IS NOT NULL
                  AND p.fiscal_year_id IS NULL
            ');
        }

        // Drop old fiscal_year column if it exists
        if (Schema::hasColumn('programs', 'fiscal_year')) {
            Schema::table('programs', function (Blueprint $table) {
                // Try to drop FK constraint (may not exist)
                try {
                    $table->dropForeign(['fiscal_year']);
                } catch (\Exception $e) {
                    // Constraint doesn't exist, that's fine
                }
                $table->dropColumn('fiscal_year');
            });
        }

        // Make fiscal_year_id NOT NULL (need to drop/re-add FK for this)
        Schema::table('programs', function (Blueprint $table) {
            // Drop FK, modify column, re-add FK
            $table->dropForeign(['fiscal_year_id']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('fiscal_year_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('programs', function (Blueprint $table) {
            $table
                ->foreign('fiscal_year_id')
                ->references('id')
                ->on('fiscal_years')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // Restore old integer column
        Schema::table('programs', function (Blueprint $table) {
            $table->integer('fiscal_year')->nullable()->after('site_id');
        });

        // Migrate data back
        DB::statement('
            UPDATE programs p
            SET fiscal_year = (
                SELECT year FROM fiscal_years 
                WHERE id = p.fiscal_year_id
                LIMIT 1
            )
        ');

        // Drop FK column
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });
    }
};
