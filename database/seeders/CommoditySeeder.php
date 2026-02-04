<?php

namespace Database\Seeders;

use App\Models\Commodity;
use Illuminate\Database\Seeder;

class CommoditySeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $commodities = [
            ['commodity_code' => 'PADI', 'commodity_name' => 'Padi', 'description' => 'Komoditas padi sawah'],
            ['commodity_code' => 'JAGUNG', 'commodity_name' => 'Jagung', 'description' => 'Komoditas jagung'],
            ['commodity_code' => 'KEDELAI', 'commodity_name' => 'Kedelai', 'description' => 'Komoditas kedelai'],
            ['commodity_code' => 'SINGKONG', 'commodity_name' => 'Singkong', 'description' => 'Komoditas singkong'],
            ['commodity_code' => 'UBI_JALAR', 'commodity_name' => 'Ubi Jalar', 'description' => 'Komoditas ubi jalar'],
            ['commodity_code' => 'KACANG_TANAH', 'commodity_name' => 'Kacang Tanah', 'description' => 'Komoditas kacang tanah'],
            ['commodity_code' => 'SORGUM', 'commodity_name' => 'Sorgum', 'description' => 'Komoditas sorgum'],
            ['commodity_code' => 'PORANG', 'commodity_name' => 'Porang', 'description' => 'Komoditas porang'],
            ['commodity_code' => 'BAWANG_MERAH', 'commodity_name' => 'Bawang Merah', 'description' => 'Komoditas bawang merah'],
            ['commodity_code' => 'CABAI', 'commodity_name' => 'Cabai', 'description' => 'Komoditas cabai'],
        ];

        foreach ($commodities as $commodity) {
            Commodity::updateOrCreate(
                ['commodity_code' => $commodity['commodity_code']],
                $commodity,
            );
        }
    }
}
