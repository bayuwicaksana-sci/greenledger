<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Ensure every fiscal_year value in programs has a matching fiscal_years row
        $missingYears = DB::table('programs')
            ->select('fiscal_year')
            ->distinct()
            ->whereNotIn('fiscal_year', DB::table('fiscal_years')->select('year'))
            ->get();

        foreach ($missingYears as $row) {
            DB::table('fiscal_years')->insert([
                'year' => $row->fiscal_year,
                'start_date' => "{$row->fiscal_year}-01-01",
                'end_date' => "{$row->fiscal_year}-12-31",
                'is_closed' => false,
            ]);
        }

        Schema::table('programs', function (Blueprint $table) {
            $table->foreign('fiscal_year')->references('year')->on('fiscal_years')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year']);
        });
    }
};
