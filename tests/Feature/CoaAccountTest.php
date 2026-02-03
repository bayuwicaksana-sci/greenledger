<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\CoaAccount;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestSplit;
use App\Models\Program;
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
                fn ($page) => $page
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
            ->assertInertia(fn ($page) => $page->component('config/coa/Create'));

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

    public function test_cannot_update_account_code_when_locked()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create([
            'account_code' => 'LOCKED-001',
            'first_transaction_at' => now(),
        ]);

        $response = $this->actingAs($user)->put(
            route('config.coa.update', $coa),
            [
                'account_code' => 'CHANGED-001',
                'account_name' => 'Changed Name',
            ],
        );

        $response->assertSessionHasErrors('account_code');
        $this->assertDatabaseHas('coa_accounts', [
            'id' => $coa->id,
            'account_code' => 'LOCKED-001',
        ]);
    }

    public function test_cannot_update_account_type_when_locked()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create([
            'account_type' => 'EXPENSE',
            'first_transaction_at' => now(),
        ]);

        $response = $this->actingAs($user)->put(
            route('config.coa.update', $coa),
            [
                'account_type' => 'REVENUE',
                'account_name' => 'Changed Name',
            ],
        );

        $response->assertSessionHasErrors('account_type');
        $this->assertDatabaseHas('coa_accounts', [
            'id' => $coa->id,
            'account_type' => 'EXPENSE',
        ]);
    }

    public function test_can_update_other_fields_when_locked()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create([
            'account_name' => 'Original Name',
            'first_transaction_at' => now(),
        ]);

        $response = $this->actingAs($user)->put(
            route('config.coa.update', $coa),
            [
                'account_name' => 'Updated Name',
                'short_description' => 'New description',
                'is_active' => false,
            ],
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('coa_accounts', [
            'id' => $coa->id,
            'account_name' => 'Updated Name',
            'short_description' => 'New description',
            'is_active' => false,
        ]);
    }

    public function test_cannot_delete_account_with_child_accounts()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $parent = CoaAccount::factory()->create();
        $child = CoaAccount::factory()->create([
            'parent_account_id' => $parent->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('config.coa.destroy', $parent),
        );

        $response->assertRedirect();
        $response->assertSessionHas(
            'error',
            'Cannot delete account with child accounts. Please remove or reassign child accounts first.',
        );

        $this->assertDatabaseHas('coa_accounts', [
            'id' => $parent->id,
        ]);
    }

    public function test_can_delete_account_without_children_or_transactions()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create();

        $response = $this->actingAs($user)->delete(
            route('config.coa.destroy', $coa),
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('coa_accounts', [
            'id' => $coa->id,
        ]);
    }

    public function test_can_bulk_create_multiple_accounts()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(
            route('config.coa.bulk-store'),
            [
                'accounts' => [
                    [
                        '_temp_id' => 'temp_1',
                        'site_id' => $site->id,
                        'account_code' => 'BULK-001',
                        'account_name' => 'Bulk Account 1',
                        'account_type' => 'EXPENSE',
                        'is_active' => true,
                    ],
                    [
                        '_temp_id' => 'temp_2',
                        'site_id' => $site->id,
                        'account_code' => 'BULK-002',
                        'account_name' => 'Bulk Account 2',
                        'account_type' => 'EXPENSE',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertRedirect(route('config.coa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => 'BULK-001',
            'site_id' => $site->id,
        ]);

        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => 'BULK-002',
            'site_id' => $site->id,
        ]);
    }

    public function test_can_bulk_create_with_parent_child_relationship()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(
            route('config.coa.bulk-store'),
            [
                'accounts' => [
                    [
                        '_temp_id' => 'temp_parent',
                        'site_id' => $site->id,
                        'account_code' => 'PARENT-001',
                        'account_name' => 'Parent Account',
                        'account_type' => 'EXPENSE',
                        'is_active' => true,
                    ],
                    [
                        '_temp_id' => 'temp_child',
                        'site_id' => $site->id,
                        'account_code' => 'CHILD-001',
                        'account_name' => 'Child Account',
                        'account_type' => 'EXPENSE',
                        'parent_temp_id' => 'temp_parent',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertRedirect(route('config.coa.index'));

        $parent = CoaAccount::where('account_code', 'PARENT-001')->first();
        $child = CoaAccount::where('account_code', 'CHILD-001')->first();

        $this->assertNotNull($parent);
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->parent_account_id);
        $this->assertEquals(1, $parent->hierarchy_level);
        $this->assertEquals(2, $child->hierarchy_level);
    }

    public function test_bulk_create_validates_duplicate_codes_in_batch()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(
            route('config.coa.bulk-store'),
            [
                'accounts' => [
                    [
                        '_temp_id' => 'temp_1',
                        'site_id' => $site->id,
                        'account_code' => 'DUP-001',
                        'account_name' => 'Account 1',
                        'account_type' => 'EXPENSE',
                        'is_active' => true,
                    ],
                    [
                        '_temp_id' => 'temp_2',
                        'site_id' => $site->id,
                        'account_code' => 'DUP-001',
                        'account_name' => 'Account 2',
                        'account_type' => 'EXPENSE',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertSessionHasErrors('accounts.1.account_code');
    }

    public function test_bulk_create_validates_existing_codes()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_code' => 'EXISTING-001',
        ]);

        $response = $this->actingAs($user)->post(
            route('config.coa.bulk-store'),
            [
                'accounts' => [
                    [
                        '_temp_id' => 'temp_1',
                        'site_id' => $site->id,
                        'account_code' => 'EXISTING-001',
                        'account_name' => 'Duplicate Account',
                        'account_type' => 'EXPENSE',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertSessionHasErrors('accounts.0.account_code');
    }

    public function test_bulk_create_requires_at_least_one_account()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $response = $this->actingAs($user)->post(
            route('config.coa.bulk-store'),
            [
                'accounts' => [],
            ],
        );

        $response->assertSessionHasErrors('accounts');
    }

    // --- Import Tests ---

    public function test_can_validate_import_rows()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create(['site_code' => 'KLT']);

        $response = $this->actingAs($user)->post(
            route('config.coa.import.validate'),
            [
                'rows' => [
                    [
                        'site_code' => 'KLT',
                        'account_code' => 'KLT-IMP-001',
                        'account_name' => 'Import Test',
                        'account_type' => 'EXPENSE',
                        'short_description' => '',
                        'parent_account_code' => '',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertOk();
        $response->assertJson(['valid' => true]);
    }

    public function test_can_import_accounts()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create(['site_code' => 'KLT']);

        $response = $this->actingAs($user)->post(
            route('config.coa.import'),
            [
                'rows' => [
                    [
                        'site_code' => 'KLT',
                        'account_code' => 'KLT-IMP-001',
                        'account_name' => 'Import Account 1',
                        'account_type' => 'REVENUE',
                        'short_description' => 'First imported account',
                        'parent_account_code' => '',
                        'is_active' => true,
                    ],
                    [
                        'site_code' => 'KLT',
                        'account_code' => 'KLT-IMP-002',
                        'account_name' => 'Import Account 2',
                        'account_type' => 'REVENUE',
                        'short_description' => 'Child of first',
                        'parent_account_code' => 'KLT-IMP-001',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('count', 2);

        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => 'KLT-IMP-001',
            'site_id' => $site->id,
            'hierarchy_level' => 1,
        ]);

        $parent = CoaAccount::where('account_code', 'KLT-IMP-001')->first();
        $child = CoaAccount::where('account_code', 'KLT-IMP-002')->first();

        $this->assertNotNull($parent);
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->parent_account_id);
        $this->assertEquals(2, $child->hierarchy_level);
    }

    public function test_import_validation_detects_invalid_site()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $response = $this->actingAs($user)->post(
            route('config.coa.import.validate'),
            [
                'rows' => [
                    [
                        'site_code' => 'NONEXISTENT',
                        'account_code' => 'TEST-001',
                        'account_name' => 'Bad Site',
                        'account_type' => 'EXPENSE',
                        'short_description' => '',
                        'parent_account_code' => '',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertOk();
        $response->assertJsonPath('valid', false);
        $this->assertArrayHasKey('0', $response->json('errors'));
    }

    public function test_import_validation_detects_duplicate_codes_in_batch()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        Site::factory()->create(['site_code' => 'KLT']);

        $response = $this->actingAs($user)->post(
            route('config.coa.import.validate'),
            [
                'rows' => [
                    [
                        'site_code' => 'KLT',
                        'account_code' => 'DUP-CODE',
                        'account_name' => 'First',
                        'account_type' => 'EXPENSE',
                        'short_description' => '',
                        'parent_account_code' => '',
                        'is_active' => true,
                    ],
                    [
                        'site_code' => 'KLT',
                        'account_code' => 'DUP-CODE',
                        'account_name' => 'Second',
                        'account_type' => 'EXPENSE',
                        'short_description' => '',
                        'parent_account_code' => '',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertOk();
        $response->assertJsonPath('valid', false);
        $this->assertArrayHasKey('1', $response->json('errors'));
    }

    public function test_import_validation_detects_missing_parent_reference()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        Site::factory()->create(['site_code' => 'KLT']);

        $response = $this->actingAs($user)->post(
            route('config.coa.import.validate'),
            [
                'rows' => [
                    [
                        'site_code' => 'KLT',
                        'account_code' => 'CHILD-001',
                        'account_name' => 'Orphan Child',
                        'account_type' => 'EXPENSE',
                        'short_description' => '',
                        'parent_account_code' => 'MISSING-PARENT',
                        'is_active' => true,
                    ],
                ],
            ],
        );

        $response->assertOk();
        $response->assertJsonPath('valid', false);
        $this->assertArrayHasKey('0', $response->json('errors'));
    }

    // --- Template Tests ---

    public function test_can_list_templates()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->get(
            route('config.coa.templates.index').'?site_id='.$site->id,
        );

        $response->assertOk();
        $this->assertIsArray($response->json());
        $this->assertTrue(count($response->json()) > 0);

        // Each template should have expected structure with conflict info
        $firstTemplate = $response->json()[0];
        $this->assertArrayHasKey('key', $firstTemplate);
        $this->assertArrayHasKey('name', $firstTemplate);
        $this->assertArrayHasKey('accounts', $firstTemplate);
        $this->assertArrayHasKey('conflicts', $firstTemplate);
    }

    public function test_can_apply_template()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(
            route('config.coa.templates.apply'),
            [
                'template_key' => 'agricultural_research_revenue',
                'site_id' => $site->id,
            ],
        );

        $response->assertOk();
        $response->assertJsonPath('success', true);

        // Verify template accounts were created
        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '4-1000',
            'account_name' => 'Revenue',
            'account_type' => 'REVENUE',
            'hierarchy_level' => 1,
        ]);

        $parent = CoaAccount::where('site_id', $site->id)
            ->where('account_code', '4-1000')
            ->first();

        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '4-1100',
            'parent_account_id' => $parent->id,
            'hierarchy_level' => 2,
        ]);
    }

    public function test_apply_template_skips_existing_accounts()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        // Pre-create one account that exists in the template
        CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_code' => '4-1000',
            'account_name' => 'Pre-existing Revenue',
            'account_type' => 'REVENUE',
        ]);

        $response = $this->actingAs($user)->post(
            route('config.coa.templates.apply'),
            [
                'template_key' => 'agricultural_research_revenue',
                'site_id' => $site->id,
                'skip_existing' => true,
            ],
        );

        $response->assertOk();
        $response->assertJsonPath('success', true);

        // The pre-existing account name should remain unchanged
        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '4-1000',
            'account_name' => 'Pre-existing Revenue',
        ]);

        // Child accounts should still be created
        $this->assertDatabaseHas('coa_accounts', [
            'site_id' => $site->id,
            'account_code' => '4-1100',
        ]);
    }

    public function test_apply_template_with_invalid_key_returns_error()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(
            route('config.coa.templates.apply'),
            [
                'template_key' => 'nonexistent_template',
                'site_id' => $site->id,
            ],
        );

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }

    // --- Phase 4 Tests ---

    public function test_store_redirects_to_index()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $response = $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => 'REDIR-001',
            'account_name' => 'Redirect Test',
            'account_type' => 'EXPENSE',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('config.coa.index'));
    }

    public function test_initial_budget_persists_on_store()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $this->actingAs($user)->post(route('config.coa.store'), [
            'site_id' => $site->id,
            'account_code' => 'BUD-001',
            'account_name' => 'Budget Store Test',
            'account_type' => 'EXPENSE',
            'is_active' => true,
            'initial_budget' => 50000,
        ]);

        $this->assertDatabaseHas('coa_accounts', [
            'account_code' => 'BUD-001',
            'initial_budget' => 50000,
        ]);
    }

    public function test_initial_budget_persists_on_update()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create([
            'initial_budget' => 10000,
        ]);

        $this->actingAs($user)->put(
            route('config.coa.update', $coa),
            [
                'account_code' => $coa->account_code,
                'account_name' => $coa->account_name,
                'account_type' => $coa->account_type,
                'initial_budget' => 75000,
            ],
        );

        $this->assertDatabaseHas('coa_accounts', [
            'id' => $coa->id,
            'initial_budget' => 75000,
        ]);
    }

    public function test_actual_amount_returned_in_index_for_expense()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();

        $coa = CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_type' => 'EXPENSE',
        ]);

        $program = Program::create([
            'site_id' => $site->id,
            'program_code' => 'PGM-001',
            'program_name' => 'Test Program',
            'fiscal_year' => 2026,
            'status' => 'ACTIVE',
            'total_budget' => 100000,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $activity = Activity::create([
            'program_id' => $program->id,
            'activity_name' => 'Test Activity',
            'budget_allocation' => 100000,
            'status' => 'ACTIVE',
        ]);

        $paymentRequest = PaymentRequest::create([
            'request_number' => 'PR-001',
            'site_id' => $site->id,
            'request_date' => '2026-01-15',
            'total_amount' => 5000,
            'purpose' => 'Test',
            'vendor_name' => 'Vendor',
            'status' => 'PENDING',
        ]);

        PaymentRequestSplit::create([
            'payment_request_id' => $paymentRequest->id,
            'program_id' => $program->id,
            'activity_id' => $activity->id,
            'coa_account_id' => $coa->id,
            'split_amount' => 3500.00,
            'split_percentage' => 70,
        ]);

        $this->actingAs($user)
            ->get(route('config.coa.index'))
            ->assertOk()
            ->assertInertia(function ($page) use ($coa) {
                $data = $page->toArray();
                $accounts = collect($data['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);

                $this->assertNotNull($found, 'COA account not found in index response');
                $this->assertEquals(3500.00, (float) $found['actual_amount']);
            });
    }
}
