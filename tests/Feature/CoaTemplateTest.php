<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Services\CoaAccountTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CoaTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_application_creates_accounts_with_hierarchy(): void
    {
        Config::set('coa_templates.test_template', [
            'name' => 'Test Template',
            'accounts' => [
                [
                    'code' => '5211',
                    'name' => 'Seeds',
                    'type' => 'EXPENSE',
                    'description' => 'Seed costs',
                    'parent' => null,
                    'category' => 'PROGRAM',
                    'sub_category' => 'Research',
                    'typical_usage' => 'Planting',
                    'tax_applicable' => false,
                ],
                [
                    'code' => '5212',
                    'name' => 'Fertilizer',
                    'type' => 'EXPENSE',
                    'description' => 'Fertilizer costs',
                    'parent' => '5211',
                    'category' => 'PROGRAM',
                    'sub_category' => 'Research',
                    'typical_usage' => 'Soil treatment',
                    'tax_applicable' => true,
                ],
            ],
        ]);

        $site = Site::factory()->create(['site_code' => 'KLT']);
        $service = new CoaAccountTemplateService;

        $count = $service->applyTemplate('test_template', $site->id);

        $this->assertEquals(2, $count);

        // Check Parent creation
        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '5211',
            'account_name' => 'Seeds',
            'category' => 'PROGRAM',
            'sub_category' => 'Research',
            'is_active' => true,
        ]);

        // Check Child creation
        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '5212',
            'account_name' => 'Fertilizer',
            'category' => 'PROGRAM',
            'sub_category' => 'Research',
            'tax_applicable' => true,
            'is_active' => true,
        ]);

        // Verify parent-child relationship
        $parent = \App\Models\CoaAccount::where('site_id', $site->id)
            ->where('account_code', '5211')
            ->first();
        $child = \App\Models\CoaAccount::where('site_id', $site->id)
            ->where('account_code', '5212')
            ->first();

        $this->assertEquals($parent->id, $child->parent_account_id);
    }
}
