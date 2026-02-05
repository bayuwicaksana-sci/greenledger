<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill NULL start_date/end_date rows in PHP (works on SQLite + MySQL)
        $rows = DB::table('fiscal_years')->whereNull('start_date')->orWhereNull('end_date')->get();

        foreach ($rows as $row) {
            $updates = [];

            if ($row->start_date === null) {
                $updates['start_date'] = "{$row->year}-01-01";
            }

            if ($row->end_date === null) {
                $updates['end_date'] = "{$row->year}-12-31";
            }

            if (! empty($updates)) {
                DB::table('fiscal_years')->where('id', $row->id)->update($updates);
            }
        }

        // Now make the columns non-nullable
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });
    }
};
