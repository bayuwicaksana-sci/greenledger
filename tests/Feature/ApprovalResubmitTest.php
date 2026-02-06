<?php

use App\Enums\ApprovalActionType;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\CoaAccount;
use App\Models\Site;
use App\Models\User;
use App\Notifications\ApprovalSubmittedNotification;
use App\Services\Approval\WorkflowEngine;
use App\States\ApprovalInstance\ChangesRequested;
use App\States\ApprovalInstance\InProgress;
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
        ['name' => 'COA Resubmit Test', 'model_type' => CoaAccount::class],
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

it('submitter can resubmit after changes are requested', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'RS01',
        'account_name' => 'Resubmit Me',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    // Request changes
    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::RequestChanges,
        actor: $this->approver,
        comments: 'Fix the name',
    );

    $instance->refresh();
    expect($instance->status)->toBeInstanceOf(ChangesRequested::class);
    expect($instance->current_step_id)->toBeNull();

    // Resubmit
    $success = $this->engine->resubmitWorkflow($instance, $this->submitter);

    $instance->refresh();
    expect($success)->toBeTrue();
    expect($instance->status)->toBeInstanceOf(InProgress::class);
    expect($instance->current_step_id)->not->toBeNull();
});

it('non-submitter cannot resubmit', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'RS02',
        'account_name' => 'Resubmit Blocked',
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
        comments: 'Fix it',
    );

    $instance->refresh();

    // Approver tries to resubmit — blocked either by policy (403) or controller guard (redirect with error)
    $this->actingAs($this->approver);
    $response = $this->post(route('approvals.resubmit', [
        'site' => $this->site->site_code,
        'approvalInstance' => $instance->id,
    ]));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Only the original submitter can resubmit.');
});

it('cannot resubmit if not in ChangesRequested state', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'RS03',
        'account_name' => 'Wrong State',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    // Instance is InProgress, not ChangesRequested
    $success = $this->engine->resubmitWorkflow($instance, $this->submitter);

    expect($success)->toBeFalse();
});

it('resubmit dispatches ApprovalSubmittedNotification to approvers', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'RS04',
        'account_name' => 'Resubmit Notify',
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
        comments: 'Fix it',
    );

    $instance->refresh();

    // Reset notification tracking
    Notification::fake();

    $this->engine->resubmitWorkflow($instance, $this->submitter);

    Notification::assertSentTo(
        $this->approver,
        ApprovalSubmittedNotification::class,
    );
});
