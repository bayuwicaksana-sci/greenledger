<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CoaValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_control_can_be_enabled(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'coa.view.all']);
        $user->givePermissionTo('coa.view.all');
        $site = Site::factory()->create(['site_code' => 'KLT']);

        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => '1000',
            'category' => 'AST',
            'subcategory' => 'GEN',
            'sequence_number' => '001',
            'account_name' => 'Budgeted Asset',
            'account_type' => 'ASSET',
            'budget_control' => true,
            'initial_budget' => 1000,
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => '1000',
            'category' => 'AST',
            'subcategory' => 'GEN',
            'sequence_number' => '001',
            'budget_control' => 1,
        ]);
    }

    public function test_budget_control_defaults_to_false(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'coa.view.all']);
        $user->givePermissionTo('coa.view.all');
        $site = Site::factory()->create(['site_code' => 'KLT']);

        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => '2000',
            'category' => 'LIA',
            'subcategory' => 'GEN',
            'sequence_number' => '001',
            'account_name' => 'Liability',
            'account_type' => 'LIABILITY',
            // budget_control missing
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => '2000',
            'category' => 'LIA',
            'subcategory' => 'GEN',
            'sequence_number' => '001',
            'budget_control' => 0,
        ]);
    }

    public function test_account_code_validation_fields(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'coa.view.all']);
        $user->givePermissionTo('coa.view.all');
        $site = Site::factory()->create(['site_code' => 'KLT']);

        // Valid fields
        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => '1000',
            'category' => 'AST',
            'subcategory' => 'GEN',
            'sequence_number' => '001',
            'account_name' => 'Valid Code Components',
            'account_type' => 'ASSET',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => '1000',
            'category' => 'AST',
            'subcategory' => 'GEN',
            'sequence_number' => '001',
        ]);

        // Dot notation or other special chars should strictly be validated if needed.
        // For now we allow alphanumeric in base code.
        $response2 = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => '5.2.1', // if your regex allows dots
            'category' => 'EXP',
            'subcategory' => 'GEN',
            'sequence_number' => '002',
            'account_name' => 'Dot Notation',
            'account_type' => 'EXPENSE',
            'is_active' => true,
        ]);
        // Depending on your regex in request: 'regex:/^[a-zA-Z0-9]+$/'
        // If dots are intended to be disallowed, this should fail.
        // My previous view of Request allowed only alphanumeric.
        // So I expect this to FAIL if the regex is strict.
        // Let's assume strict alphanumeric and assert errors if so, OR fix the test to comply.
        // The Request regex I saw was /^[a-zA-Z0-9]+$/. So dots are invalid.
        $response2->assertSessionHasErrors(['account_code']);
    }

    public function test_all_account_types_are_accepted(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'coa.view.all']);
        $user->givePermissionTo('coa.view.all');
        $site = Site::factory()->create(['site_code' => 'TEST']);

        $types = ['ASSET', 'LIABILITY', 'EQUITY', 'REVENUE', 'EXPENSE'];

        foreach ($types as $i => $type) {
            $base = "500{$i}";
            $response = $this->actingAs($user)->post(
                route('config.coa.store'),
                [
                    'site_id' => $site->id,
                    'account_code' => $base,
                    'category' => 'TES',
                    'subcategory' => 'TYP',
                    'sequence_number' => '001',
                    'account_name' => "Test $type",
                    'account_type' => $type,
                    'is_active' => true,
                ],
            );

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('coa_accounts', [
                'account_code' => $base,
                'category' => 'TES',
                'subcategory' => 'TYP',
                'account_type' => $type,
            ]);
        }
    }

    public function test_invalid_account_type_is_rejected(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'coa.view.all']);
        $user->givePermissionTo('coa.view.all');
        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => '9000',
            'category' => 'BAD',
            'subcategory' => 'TYP',
            'sequence_number' => '001',
            'account_name' => 'Bad Type',
            'account_type' => 'INVALID_TYPE',
        ]);

        $response->assertSessionHasErrors(['account_type']);
    }
}
