<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use Illuminate\Database\Seeder;

class FiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no fiscal years exist
        // Future years should be created via the admin UI, not auto-seeded
        if (FiscalYear::count() > 0) {
            return;
        }

        $currentYear = (int) date('Y');

        FiscalYear::create([
            'year' => $currentYear,
            'start_date' => $currentYear.'-01-01',
            'end_date' => $currentYear.'-12-31',
            'is_closed' => false,
        ]);
    }
}
