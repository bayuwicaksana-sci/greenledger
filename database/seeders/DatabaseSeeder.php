<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed master data first
        $this->call([
            SiteSeeder::class,
            FiscalYearSeeder::class,
            BuyerSeeder::class,
            ClientSeeder::class,
            SubsidiTypeSeeder::class,
            CoaAccountSeeder::class,
            SystemSettingSeeder::class,
            ProgramSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
