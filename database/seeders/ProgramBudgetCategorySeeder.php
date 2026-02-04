<?php

namespace Database\Seeders;

use App\Models\ProgramBudgetCategory;
use Illuminate\Database\Seeder;

class ProgramBudgetCategorySeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $categories = [
            'Benih/Bibit',
            'Pupuk',
            'Fungisida',
            'Herbisida',
            'Insektisida',
            'Stimulants',
            'Tenaga Kerja',
            'Tenaga Kerja Pengamatan',
            'Peralatan',
            'Bahan Habis Pakai',
            'Sewa Alat',
            'Sewa Lahan',
            'ATK',
            'Biaya Pengiriman Dokumen',
        ];

        foreach ($categories as $index => $name) {
            ProgramBudgetCategory::updateOrCreate(
                ['category_name' => $name],
                ['sort_order' => $index],
            );
        }
    }
}
