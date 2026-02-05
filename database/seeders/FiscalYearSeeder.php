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
        $currentYear = (int) date('Y');

        FiscalYear::upsert(
            [
                [
                    'year' => $currentYear - 1,
                    'start_date' => ($currentYear - 1).'-01-01',
                    'end_date' => ($currentYear - 1).'-12-31',
                    'is_closed' => true,
                ],
                [
                    'year' => $currentYear,
                    'start_date' => $currentYear.'-01-01',
                    'end_date' => $currentYear.'-12-31',
                    'is_closed' => false,
                ],
                [
                    'year' => $currentYear + 1,
                    'start_date' => ($currentYear + 1).'-01-01',
                    'end_date' => ($currentYear + 1).'-12-31',
                    'is_closed' => false,
                ],
            ],
            ['year'],
            ['start_date', 'end_date', 'is_closed'],
        );
    }
}
