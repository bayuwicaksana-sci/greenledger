<?php

use App\Enums\ApprovalActionType;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\CoaAccount;
use App\Models\Site;
use App\Models\User;
use App\Services\Approval\WorkflowEngine;
use App\States\ApprovalInstance\Approved;
use App\States\ApprovalInstance\InProgress;
use App\States\ApprovalInstance\Pending;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Notification::fake();

    $this->site = Site::factory()->create(['site_code' => 'TST']);

    Permission::firstOrCreate(['name' => 'coa.view.all', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'coa.approve', 'guard_name' => 'web']);

    $this->submitter = User::factory()->create();
    $this->submitter->givePermissionTo('coa.view.all');

    $this->approver = User::factory()->create();
    $this->approver->givePermissionTo(['coa.view.all', 'coa.approve']);

    $this->submitter->sites()->attach($this->site->id, ['granted_at' => now()]);
    $this->approver->sites()->attach($this->site->id, ['granted_at' => now()]);

    $this->engine = app(WorkflowEngine::class);

    $this->workflow = ApprovalWorkflow::firstOrCreate(
        ['name' => 'COA Status Query Test', 'model_type' => CoaAccount::class],
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

it('approvals index returns instances in InProgress state for eligible approver', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'SQ01',
        'account_name' => 'Status Query Active',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);

    $this->actingAs($this->approver);
    $response = $this->get(route('approvals.index', ['site' => $this->site->site_code]));

    $response->assertStatus(200);
    $response->assertInertia(function ($page) use ($instance) {
        $page->has('approvals.data');
        $items = $page->toArray()['props']['approvals']['data'];

        expect(collect($items)->contains('id', $instance->id))->toBeTrue();
    });
});

it('approvals index does not return instances still in Pending state', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'SQ02',
        'account_name' => 'Status Query Pending',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    // Initialize but do NOT submit — stays in Pending
    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);

    $this->actingAs($this->approver);
    $response = $this->get(route('approvals.index', ['site' => $this->site->site_code]));

    $response->assertStatus(200);
    $response->assertInertia(function ($page) use ($instance) {
        $items = $page->toArray()['props']['approvals']['data'] ?? [];

        expect(collect($items)->contains('id', $instance->id))->toBeFalse();
    });
});

it('getActiveApprovalInstance returns instance in ChangesRequested state', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'SQ03',
        'account_name' => 'Status Query Changes',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::RequestChanges,
        actor: $this->approver,
        comments: 'Please revise',
    );

    $account->refresh();
    $active = $account->getActiveApprovalInstance();

    expect($active)->not->toBeNull();
    expect($active->id)->toBe($instance->id);
    expect($active->status)->toBeInstanceOf(\App\States\ApprovalInstance\ChangesRequested::class);
});

it('appended status attributes return correct values for each state', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'SQ04',
        'account_name' => 'Status Attributes',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);

    // Pending state
    $instance->refresh();
    expect($instance->status_value)->toBe('pending');
    expect($instance->status_label)->toBe('Pending');
    expect($instance->status_color)->toBeString();

    // Submit → InProgress
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();
    expect($instance->status_value)->toBe('pending_approval');
    expect($instance->status_label)->toBe('In Progress');

    // Approve → Approved
    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::Approve,
        actor: $this->approver,
        comments: 'All good',
    );
    $instance->refresh();
    expect($instance->status_value)->toBe('approved');
    expect($instance->status_label)->toBe('Approved');
});
