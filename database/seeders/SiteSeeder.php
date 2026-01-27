<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Site::upsert(
            [
                [
                    'site_name' => 'Klaten',
                    'site_code' => 'klaten',
                    'is_active' => true,
                    'contact_info' => [
                        'name' => 'Bayu',
                    ],
                    'address' => 'Klaten, Jawa Tengah',
                ],
                [
                    'site_name' => 'Magelang',
                    'site_code' => 'magelang',
                    'is_active' => true,
                    'contact_info' => [
                        'name' => 'Mazbay',
                    ],
                    'address' => 'Magelang, Jawa Tengah',
                ],
            ],
            ['site_code'],
            ['is_active', 'contact_info', 'address'],
        );
    }
}
