<?php

namespace Tests\Feature;

use App\Models\CoaAccount;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoaAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Seed permissions if needed, usually DatabaseSeeder handles roles/permissions
    }

    public function test_can_view_coa_list_globally()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $this->actingAs($user)
            ->get(route('config.coa.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('config/coa/Index')
                    ->has('accounts')
                    ->has('sites'),
            );
    }

    public function test_can_create_coa_account()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all'); // Middleware requires this permission

        // Test viewing the create form
        $this->actingAs($user)
            ->get(route('config.coa.create'))
            ->assertOk()
            ->assertInertia(fn($page) => $page->component('config/coa/Create'));

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => 'KLT-TEST-001',
            'account_name' => 'Test Account',
            'account_type' => 'EXPENSE',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => 'KLT-TEST-001',
            'site_id' => $site->id,
        ]);
    }

    public function test_cannot_create_duplicate_coa_code_in_same_site()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');
        $site = Site::factory()->create();

        CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_code' => 'KLT-DUP-001',
        ]);

        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => 'KLT-DUP-001',
            'account_name' => 'Duplicate Account',
            'account_type' => 'EXPENSE',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('account_code');
    }

    public function test_can_update_coa_account()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create();

        $response = $this->actingAs($user)->put(
            route('config.coa.update', $coa),
            [
                'site_id' => $coa->site_id, // Should remain same or be ignored if we don't allow moving
                'account_code' => $coa->account_code,
                'account_name' => 'Updated Name',
                'account_type' => 'REVENUE', // Logic might prevent this in real app, but for CRUD it allows
                'is_active' => false,
            ],
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('coa_accounts', [
            'id' => $coa->id,
            'account_name' => 'Updated Name',
            'is_active' => false,
        ]);
    }
}
