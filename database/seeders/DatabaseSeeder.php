<?php

namespace Database\Seeders;

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
            CommoditySeeder::class,
            ProgramBudgetCategorySeeder::class,
            ProgramBudgetPhaseSeeder::class,
            ProgramSeeder::class,
            RolePermissionSeeder::class,
            ApprovalPermissionsSeeder::class,
            SiteCoaSeeder::class,
        ]);
    }
}
