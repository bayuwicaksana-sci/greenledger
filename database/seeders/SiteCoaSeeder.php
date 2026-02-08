<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\Site;
use App\Services\CoaAccountTemplateService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SiteCoaSeeder extends Seeder
{
    public function __construct(
        protected CoaAccountTemplateService $templateService,
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current fiscal year (required for COA accounts)
        $fiscalYear = FiscalYear::where('is_closed', false)
            ->orderBy('year', 'desc')
            ->first();

        if (! $fiscalYear) {
            $this->command->error(
                'No open fiscal year found. Run FiscalYearSeeder first.',
            );

            return;
        }

        $this->command->info("Using fiscal year: {$fiscalYear->year}");

        $sites = Site::all();

        if ($sites->isEmpty()) {
            $this->command->info(
                'No sites found. Creating default Head Office site...',
            );
            $site = Site::create([
                'site_code' => 'HO',
                'site_name' => 'Head Office',
                'is_active' => true,
            ]);
            $sites = collect([$site]);
        }

        $templateKey = 'standard_agricultural';

        foreach ($sites as $site) {
            $this->command->info(
                "Seeding COA for site: {$site->site_name} ({$site->site_code})",
            );

            try {
                // Apply template to site for the current fiscal year
                $count = $this->templateService->applyTemplate(
                    $templateKey,
                    $site->id,
                    $fiscalYear->id,
                    true, // skipExisting
                );

                if ($count > 0) {
                    $this->command->info(
                        "  - Successfully initialized {$count} accounts.",
                    );
                } else {
                    $this->command->info(
                        '  - COA already initialized. Skipped.',
                    );
                }
            } catch (\Exception $e) {
                $this->command->error('  - Failed: '.$e->getMessage());
                Log::error(
                    "SiteCoaSeeder failed for site {$site->id}: ".
                        $e->getMessage(),
                );
            }
        }
    }
}
