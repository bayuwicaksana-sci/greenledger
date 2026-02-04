<?php

namespace Database\Seeders;

use App\Models\ProgramBudgetPhase;
use Illuminate\Database\Seeder;

class ProgramBudgetPhaseSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $phases = [
            'Pengolahan Lahan',
            'Penanaman',
            'Pengairan',
            'Pengendalian OPT',
            'Pemupukan',
            'Panen',
            'Pengambilan Data Destruktif',
            'Pengamatan Non-Destruktif',
        ];

        foreach ($phases as $index => $name) {
            ProgramBudgetPhase::updateOrCreate(
                ['phase_name' => $name],
                ['sort_order' => $index],
            );
        }
    }
}
