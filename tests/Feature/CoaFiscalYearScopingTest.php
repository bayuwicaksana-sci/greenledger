<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Buyer;
use App\Models\Client;
use App\Models\CoaAccount;
use App\Models\CoaBudgetAllocation;
use App\Models\FiscalYear;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestSplit;
use App\Models\Program;
use App\Models\RevenueHarvest;
use App\Models\RevenueTestingService;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoaFiscalYearScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** 1. Default year — no ?fiscal_year param returns current year */
    public function test_default_fiscal_year_is_current_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $this->actingAs($user)
            ->get(route('config.coa.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $this->assertEquals((int) date('Y'), $page->toArray()['props']['selectedFiscalYear']);
            });
    }

    /** 2. Fiscal years list — response has non-empty fiscalYears prop */
    public function test_fiscal_years_list_is_returned()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        FiscalYear::firstOrCreate(
            ['year' => (int) date('Y')],
            ['start_date' => date('Y').'-01-01', 'end_date' => date('Y').'-12-31', 'is_closed' => false],
        );

        $this->actingAs($user)
            ->get(route('config.coa.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $fiscalYears = $page->toArray()['props']['fiscalYears'];
                $this->assertIsArray($fiscalYears);
                $this->assertTrue(count($fiscalYears) > 0);
            });
    }

    /** 3. Expense actual scoping — splits in different years return per-year sums */
    public function test_expense_actual_scoped_by_fiscal_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();
        $coa = CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_type' => 'EXPENSE',
        ]);

        $fy2025 = FiscalYear::firstOrCreate(
            ['year' => 2025],
            ['start_date' => '2025-01-01', 'end_date' => '2025-12-31', 'is_closed' => false],
        );
        $fy2026 = FiscalYear::firstOrCreate(
            ['year' => 2026],
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_closed' => false],
        );

        $program2025 = Program::create([
            'site_id' => $site->id,
            'program_code' => 'EXP-2025',
            'program_name' => 'Expense Program 2025',
            'fiscal_year' => 2025,
            'status' => 'ACTIVE',
            'total_budget' => 100000,
        ]);

        $program2026 = Program::create([
            'site_id' => $site->id,
            'program_code' => 'EXP-2026',
            'program_name' => 'Expense Program 2026',
            'fiscal_year' => 2026,
            'status' => 'ACTIVE',
            'total_budget' => 100000,
        ]);

        $activity2025 = Activity::create([
            'program_id' => $program2025->id,
            'activity_name' => 'Activity 2025',
            'budget_allocation' => 50000,
        ]);

        $activity2026 = Activity::create([
            'program_id' => $program2026->id,
            'activity_name' => 'Activity 2026',
            'budget_allocation' => 50000,
        ]);

        $pr2025 = PaymentRequest::create([
            'request_number' => 'PR-EXP-2025',
            'site_id' => $site->id,
            'request_date' => '2025-06-01',
            'total_amount' => 1000,
            'purpose' => 'Test 2025',
        ]);

        $pr2026 = PaymentRequest::create([
            'request_number' => 'PR-EXP-2026',
            'site_id' => $site->id,
            'request_date' => '2026-06-01',
            'total_amount' => 2000,
            'purpose' => 'Test 2026',
        ]);

        PaymentRequestSplit::create([
            'payment_request_id' => $pr2025->id,
            'program_id' => $program2025->id,
            'activity_id' => $activity2025->id,
            'coa_account_id' => $coa->id,
            'split_amount' => 1000,
            'split_percentage' => 100,
        ]);

        PaymentRequestSplit::create([
            'payment_request_id' => $pr2026->id,
            'program_id' => $program2026->id,
            'activity_id' => $activity2026->id,
            'coa_account_id' => $coa->id,
            'split_amount' => 2000,
            'split_percentage' => 100,
        ]);

        // Request year 2025
        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2025]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(1000.0, (float) $found['actual_amount']);
            });

        // Request year 2026
        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2026]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(2000.0, (float) $found['actual_amount']);
            });
    }

    /** 4. Revenue actual scoping — harvest + testing service sum for the year */
    public function test_revenue_actual_scoped_by_fiscal_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();
        $coa = CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_type' => 'REVENUE',
        ]);

        $fy2026 = FiscalYear::firstOrCreate(
            ['year' => 2026],
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_closed' => false],
        );

        $program = Program::create([
            'site_id' => $site->id,
            'program_code' => 'REV-2026',
            'program_name' => 'Revenue Program 2026',
            'fiscal_year' => 2026,
            'status' => 'ACTIVE',
            'total_budget' => 100000,
        ]);

        $buyer = Buyer::create([
            'buyer_name' => 'Test Buyer',
            'is_active' => true,
        ]);

        $client = Client::create([
            'client_name' => 'Test Client',
            'client_type' => 'EXTERNAL',
            'is_active' => true,
        ]);

        RevenueHarvest::create([
            'site_id' => $site->id,
            'program_id' => $program->id,
            'harvest_number' => 'HRV-001',
            'harvest_date' => '2026-03-15',
            'harvest_cycle' => 1,
            'crop_type' => 'Rice',
            'quantity_kg' => 500,
            'price_per_kg' => 10,
            'total_revenue' => 5000,
            'buyer_id' => $buyer->id,
            'payment_method' => 'CASH',
            'payment_date' => '2026-03-16',
            'coa_account_id' => $coa->id,
        ]);

        RevenueTestingService::create([
            'site_id' => $site->id,
            'program_id' => $program->id,
            'service_number' => 'SVC-001',
            'client_id' => $client->id,
            'client_type' => 'EXTERNAL',
            'service_type' => 'Lab Test',
            'service_description' => 'Testing service',
            'contract_value' => 3000,
            'start_date' => '2026-04-01',
            'coa_account_id' => $coa->id,
        ]);

        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2026]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(8000.0, (float) $found['actual_amount']); // 5000 + 3000
            });
    }

    /** 5. Budget from allocations — per-year allocations returned correctly */
    public function test_budget_allocation_scoped_by_fiscal_year()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create(['account_type' => 'EXPENSE']);

        $fy2025 = FiscalYear::firstOrCreate(
            ['year' => 2025],
            ['start_date' => '2025-01-01', 'end_date' => '2025-12-31', 'is_closed' => false],
        );
        $fy2026 = FiscalYear::firstOrCreate(
            ['year' => 2026],
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_closed' => false],
        );

        CoaBudgetAllocation::create([
            'coa_account_id' => $coa->id,
            'fiscal_year_id' => $fy2025->id,
            'budget_amount' => 10000,
        ]);

        CoaBudgetAllocation::create([
            'coa_account_id' => $coa->id,
            'fiscal_year_id' => $fy2026->id,
            'budget_amount' => 25000,
        ]);

        // Year 2025
        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2025]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(10000.0, (float) $found['allocated_budget']);
            });

        // Year 2026
        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2026]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(25000.0, (float) $found['allocated_budget']);
            });
    }

    /** 6. No allocation = zero budget */
    public function test_no_allocation_returns_zero_budget()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $coa = CoaAccount::factory()->create(['account_type' => 'EXPENSE']);

        FiscalYear::firstOrCreate(
            ['year' => 2027],
            ['start_date' => '2027-01-01', 'end_date' => '2027-12-31', 'is_closed' => false],
        );

        // No allocation created for 2027
        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2027]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(0.0, (float) $found['allocated_budget']);
            });
    }

    /** 7. Testing service with null program_id excluded from actual */
    public function test_testing_service_with_null_program_excluded()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();
        $coa = CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_type' => 'REVENUE',
        ]);

        FiscalYear::firstOrCreate(
            ['year' => 2026],
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_closed' => false],
        );

        $client = Client::create([
            'client_name' => 'Orphan Client',
            'client_type' => 'EXTERNAL',
            'is_active' => true,
        ]);

        // Testing service with program_id = null
        RevenueTestingService::create([
            'site_id' => $site->id,
            'program_id' => null,
            'service_number' => 'SVC-NULL-001',
            'client_id' => $client->id,
            'client_type' => 'EXTERNAL',
            'service_type' => 'Lab Test',
            'service_description' => 'No program link',
            'contract_value' => 9999,
            'start_date' => '2026-05-01',
            'coa_account_id' => $coa->id,
        ]);

        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2026]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(0.0, (float) $found['actual_amount']);
            });
    }

    /** 8. Balance calculation — actual - allocated_budget */
    public function test_balance_is_actual_minus_allocated_budget()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('coa.view.all');

        $site = Site::factory()->create();
        $coa = CoaAccount::factory()->create([
            'site_id' => $site->id,
            'account_type' => 'EXPENSE',
        ]);

        $fy2026 = FiscalYear::firstOrCreate(
            ['year' => 2026],
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_closed' => false],
        );

        CoaBudgetAllocation::create([
            'coa_account_id' => $coa->id,
            'fiscal_year_id' => $fy2026->id,
            'budget_amount' => 10000,
        ]);

        $program = Program::create([
            'site_id' => $site->id,
            'program_code' => 'BAL-2026',
            'program_name' => 'Balance Test Program',
            'fiscal_year' => 2026,
            'status' => 'ACTIVE',
            'total_budget' => 100000,
        ]);

        $activity = Activity::create([
            'program_id' => $program->id,
            'activity_name' => 'Balance Activity',
            'budget_allocation' => 50000,
        ]);

        $pr = PaymentRequest::create([
            'request_number' => 'PR-BAL-001',
            'site_id' => $site->id,
            'request_date' => '2026-01-15',
            'total_amount' => 7500,
            'purpose' => 'Balance test',
        ]);

        PaymentRequestSplit::create([
            'payment_request_id' => $pr->id,
            'program_id' => $program->id,
            'activity_id' => $activity->id,
            'coa_account_id' => $coa->id,
            'split_amount' => 7500,
            'split_percentage' => 100,
        ]);

        // Balance = actual (7500) - allocated_budget (10000) = -2500
        $this->actingAs($user)
            ->get(route('config.coa.index', ['fiscal_year' => 2026]))
            ->assertInertia(function ($page) use ($coa) {
                $accounts = collect($page->toArray()['props']['accounts']);
                $found = $accounts->first(fn ($a) => $a['id'] === $coa->id);
                $this->assertNotNull($found);
                $this->assertEquals(-2500.0, (float) $found['balance']);
            });
    }

    /** 9. FK constraint — program with nonexistent fiscal_year throws on MySQL */
    public function test_program_fk_constraint_rejects_missing_fiscal_year()
    {
        if (\DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not enforce FK added via ALTER TABLE.');
        }

        $site = Site::factory()->create();

        $this->expectException(QueryException::class);

        Program::create([
            'site_id' => $site->id,
            'program_code' => 'FK-FAIL',
            'program_name' => 'FK Test',
            'fiscal_year' => 9999, // Does not exist in fiscal_years
            'status' => 'ACTIVE',
            'total_budget' => 1000,
        ]);
    }
}
