<?php

use App\Enums\ApprovalActionType;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\CoaAccount;
use App\Models\Site;
use App\Models\User;
use App\Services\Approval\WorkflowEngine;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->site = Site::factory()->create(['site_code' => 'TST']);

    $viewPermission = Permission::firstOrCreate(['name' => 'coa.view.all', 'guard_name' => 'web']);
    $approvePermission = Permission::firstOrCreate(['name' => 'coa.approve', 'guard_name' => 'web']);

    // Submitter: only view permission (no approve — prevents auto-skip)
    $this->user = User::factory()->create();
    $this->user->givePermissionTo($viewPermission);

    // Approver: has the approve permission
    $this->approver = User::factory()->create();
    $this->approver->givePermissionTo([$viewPermission, $approvePermission]);

    // Seed approval workflow for CoaAccount
    $this->workflow = ApprovalWorkflow::firstOrCreate(
        ['name' => 'COA Account Approval', 'model_type' => CoaAccount::class],
        ['is_active' => true],
    );

    ApprovalStep::firstOrCreate(
        ['approval_workflow_id' => $this->workflow->id, 'step_order' => 1],
        [
            'name' => 'COA Approval',
            'step_type' => \App\Enums\ApprovalStepType::Sequential,
            'step_purpose' => \App\Enums\ApprovalStepPurpose::Approval,
            'approver_type' => \App\Enums\ApproverType::Permission,
            'approver_identifiers' => ['coa.approve'],
            'required_approvals_count' => 1,
        ],
    );
});

it('account created via store has is_active = false', function () {
    $this->actingAs($this->user);

    $this->post(route('config.coa.store'), [
        'site_id' => $this->site->id,
        'account_code' => '9000',
        'account_name' => 'Test Account',
        'account_type' => 'EXPENSE',
        'category' => 'NON_PROGRAM',
        'sub_category' => 'Administrative',
        'typical_usage' => 'General use',
        'tax_applicable' => false,
        'is_active' => true, // Intentionally true — controller should force false
    ]);

    $account = CoaAccount::where('account_code', '9000')
        ->where('site_id', $this->site->id)
        ->first();

    expect($account)->not->toBeNull();
    expect($account->is_active)->toBeFalse();
});

it('approval workflow instance is created on store', function () {
    $this->actingAs($this->user);

    $this->post(route('config.coa.store'), [
        'site_id' => $this->site->id,
        'account_code' => '9100',
        'account_name' => 'Workflow Test Account',
        'account_type' => 'EXPENSE',
        'category' => 'PROGRAM',
        'sub_category' => 'Research',
        'tax_applicable' => false,
    ]);

    $account = CoaAccount::where('account_code', '9100')
        ->where('site_id', $this->site->id)
        ->first();

    expect($account)->not->toBeNull();
    expect($account->approvalInstances)->toHaveCount(1);

    $instance = $account->approvalInstances->first();
    expect($instance->approval_workflow_id)->toBe($this->workflow->id);
});

it('after approval action, account becomes active via onApproved', function () {
    $this->actingAs($this->user);

    $this->post(route('config.coa.store'), [
        'site_id' => $this->site->id,
        'account_code' => '9200',
        'account_name' => 'Approve Me',
        'account_type' => 'EXPENSE',
        'category' => 'NON_PROGRAM',
        'sub_category' => 'Financial',
        'tax_applicable' => false,
    ]);

    $account = CoaAccount::where('account_code', '9200')
        ->where('site_id', $this->site->id)
        ->first();

    $instance = $account->approvalInstances()->with('currentStep')->first();

    // Process approve action as the approver user
    $engine = app(WorkflowEngine::class);
    $success = $engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::Approve,
        actor: $this->approver,
        comments: 'Approved for testing',
    );

    expect($success)->toBeTrue();

    // Trigger the onApproved hook (normally done by controller)
    $instance->refresh();
    if ($instance->status instanceof \App\States\ApprovalInstance\Approved) {
        $instance->approvable->onApproved();
    }

    $account->refresh();
    expect($account->is_active)->toBeTrue();
});

it('template application creates accounts with is_active = true bypassing approval', function () {
    $service = app(\App\Services\CoaAccountTemplateService::class);

    $count = $service->applyTemplate('standard_agricultural', $this->site->id, skipExisting: true);

    expect($count)->toBeGreaterThan(0);

    $accounts = CoaAccount::where('site_id', $this->site->id)->get();

    foreach ($accounts as $account) {
        expect($account->is_active)->toBeTrue();
    }

    // No approval instances should be created for template accounts
    $withApprovals = $accounts->filter(fn ($a) => $a->approvalInstances->count() > 0);
    expect($withApprovals)->toHaveCount(0);
});
