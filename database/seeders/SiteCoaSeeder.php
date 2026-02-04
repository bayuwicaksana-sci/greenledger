<?php

namespace Database\Seeders;

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
                // Determine if we should really skip existing or force update?
                // Usually seeds skipExisting = true so they are idempotent.
                $count = $this->templateService->applyTemplate(
                    $templateKey,
                    $site->id,
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
