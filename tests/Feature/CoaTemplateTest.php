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

    public function test_template_application_prefixes_site_code(): void
    {
        // Mock template config with NEW structure
        Config::set('coa_templates.test_template', [
            'name' => 'Test Template',
            'accounts' => [
                [
                    'code' => '5211',
                    'category' => 'MAT',
                    'subcategory' => 'SED',
                    'name' => 'Seeds',
                    'type' => 'EXPENSE',
                    'parent' => null,
                ],
                [
                    'code' => '5212',
                    'category' => 'MAT',
                    'subcategory' => 'FER',
                    'name' => 'Fertilizer',
                    'type' => 'EXPENSE',
                    'parent' => '5211', // Parent is referenced by base code
                ], // Child
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
            'category' => 'MAT',
            'subcategory' => 'SED',
            'sequence_number' => '001', // Default
            'account_name' => 'Seeds',
        ]);

        // Check Child creation
        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '5212',
            'category' => 'MAT',
            'subcategory' => 'FER',
            'sequence_number' => '001', // Default
            'account_name' => 'Fertilizer',
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
