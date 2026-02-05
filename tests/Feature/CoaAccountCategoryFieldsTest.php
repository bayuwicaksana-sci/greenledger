<?php

use App\Models\CoaAccount;
use App\Models\Site;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->site = Site::factory()->create(['site_code' => 'CAT']);
    $this->user = User::factory()->create();

    $viewPermission = Permission::firstOrCreate(['name' => 'coa.view.all', 'guard_name' => 'web']);
    $this->user->givePermissionTo($viewPermission);
});

it('store validates that category is required', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('config.coa.store'), [
        'site_id' => $this->site->id,
        'account_code' => '8000',
        'account_name' => 'Missing Category',
        'account_type' => 'EXPENSE',
        // category omitted
    ]);

    $response->assertSessionHasErrors('category');
});

it('store validates category enum values', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('config.coa.store'), [
        'site_id' => $this->site->id,
        'account_code' => '8100',
        'account_name' => 'Invalid Category',
        'account_type' => 'EXPENSE',
        'category' => 'INVALID_VALUE',
    ]);

    $response->assertSessionHasErrors('category');
});

it('store accepts valid PROGRAM category', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('config.coa.store'), [
        'site_id' => $this->site->id,
        'account_code' => '8200',
        'account_name' => 'Program Account',
        'account_type' => 'EXPENSE',
        'category' => 'PROGRAM',
        'sub_category' => 'Research',
        'typical_usage' => 'Research costs',
        'tax_applicable' => true,
    ]);

    $response->assertRedirect();

    $account = CoaAccount::where('account_code', '8200')
        ->where('site_id', $this->site->id)
        ->first();

    expect($account)->not->toBeNull();
    expect($account->category)->toBe('PROGRAM');
    expect($account->sub_category)->toBe('Research');
    expect($account->typical_usage)->toBe('Research costs');
    expect($account->tax_applicable)->toBeTrue();
});

it('bulk store validates category per account', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('config.coa.bulk-store'), [
        'accounts' => [
            [
                '_temp_id' => 'temp_1',
                'site_id' => $this->site->id,
                'account_code' => '8300',
                'account_name' => 'Valid Bulk',
                'account_type' => 'EXPENSE',
                'category' => 'NON_PROGRAM',
                'is_active' => false,
            ],
            [
                '_temp_id' => 'temp_2',
                'site_id' => $this->site->id,
                'account_code' => '8400',
                'account_name' => 'Invalid Bulk',
                'account_type' => 'EXPENSE',
                'category' => 'BAD',
                'is_active' => false,
            ],
        ],
    ]);

    $response->assertSessionHasErrors('accounts.1.category');
});

it('update allows category change even on accounts with transactions', function () {
    $this->actingAs($this->user);

    // Create account with first_transaction_at set (simulates locked account)
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => '8500',
        'account_type' => 'EXPENSE',
        'category' => 'PROGRAM',
        'first_transaction_at' => now(),
        'is_active' => true,
    ]);

    $response = $this->put(route('config.coa.update', $account), [
        'site_id' => $account->site_id,
        'account_code' => $account->account_code,
        'account_name' => $account->account_name,
        'account_type' => $account->account_type,
        'category' => 'NON_PROGRAM',
        'sub_category' => 'Administrative',
        'is_active' => true,
    ]);

    $response->assertRedirect();

    $account->refresh();
    expect($account->category)->toBe('NON_PROGRAM');
    expect($account->sub_category)->toBe('Administrative');
});

it('template application correctly sets category and related fields', function () {
    $service = app(\App\Services\CoaAccountTemplateService::class);

    $count = $service->applyTemplate('standard_agricultural', $this->site->id);

    expect($count)->toBeGreaterThan(0);

    // Verify a known PROGRAM account exists with correct fields
    $programAccount = CoaAccount::where('site_id', $this->site->id)
        ->where('account_code', '5110')
        ->first();

    expect($programAccount)->not->toBeNull();
    expect($programAccount->category)->toBe('PROGRAM');
    expect($programAccount->sub_category)->toBe('Research');
    expect($programAccount->is_active)->toBeTrue();

    // Verify a known NON_PROGRAM account exists
    $nonProgramAccount = CoaAccount::where('site_id', $this->site->id)
        ->where('account_code', '6110')
        ->first();

    expect($nonProgramAccount)->not->toBeNull();
    expect($nonProgramAccount->category)->toBe('NON_PROGRAM');
    expect($nonProgramAccount->sub_category)->toBe('Administrative');
});
