<?php

use App\Enums\ApprovalActionType;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\CoaAccount;
use App\Models\Site;
use App\Models\User;
use App\Notifications\ApprovalApprovedNotification;
use App\Notifications\ApprovalCancelledNotification;
use App\Notifications\ApprovalChangesRequestedNotification;
use App\Notifications\ApprovalRejectedNotification;
use App\Notifications\ApprovalStepAdvancedNotification;
use App\Notifications\ApprovalSubmittedNotification;
use App\Services\Approval\WorkflowEngine;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Notification::fake();

    $this->site = Site::factory()->create(['site_code' => 'TST']);

    Permission::firstOrCreate(['name' => 'coa.view.all', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'coa.approve', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'coa.approve.second', 'guard_name' => 'web']);

    $this->submitter = User::factory()->create();
    $this->submitter->givePermissionTo('coa.view.all');

    $this->approver = User::factory()->create();
    $this->approver->givePermissionTo(['coa.view.all', 'coa.approve']);

    $this->approverTwo = User::factory()->create();
    $this->approverTwo->givePermissionTo(['coa.view.all', 'coa.approve.second']);

    $this->engine = app(WorkflowEngine::class);

    // Single-step workflow used by most tests
    $this->workflow = ApprovalWorkflow::firstOrCreate(
        ['name' => 'COA Approval Notify', 'model_type' => CoaAccount::class],
        ['is_active' => true],
    );

    ApprovalStep::firstOrCreate(
        ['approval_workflow_id' => $this->workflow->id, 'step_order' => 1],
        [
            'name' => 'Level 1 Approval',
            'step_type' => \App\Enums\ApprovalStepType::Sequential,
            'step_purpose' => \App\Enums\ApprovalStepPurpose::Approval,
            'approver_type' => \App\Enums\ApproverType::Permission,
            'approver_identifiers' => ['coa.approve'],
            'required_approvals_count' => 1,
        ],
    );
});

it('sends notification to approvers on submitForApproval', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'NA01',
        'account_name' => 'Notify Submit',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);

    Notification::assertSentTo(
        $this->approver,
        ApprovalSubmittedNotification::class,
    );
});

it('sends notification to submitter on final approval', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'NA02',
        'account_name' => 'Notify Approve',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    Notification::fake();

    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::Approve,
        actor: $this->approver,
        comments: 'Approved',
    );

    Notification::assertSentTo(
        $this->submitter,
        ApprovalApprovedNotification::class,
    );
});

it('sends notification to submitter on rejection', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'NA03',
        'account_name' => 'Notify Reject',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    Notification::fake();

    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::Reject,
        actor: $this->approver,
        comments: 'Rejected for review',
    );

    Notification::assertSentTo(
        $this->submitter,
        ApprovalRejectedNotification::class,
    );
});

it('sends notification to submitter on changes requested', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'NA04',
        'account_name' => 'Notify Changes',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    Notification::fake();

    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::RequestChanges,
        actor: $this->approver,
        comments: 'Please update the description',
    );

    Notification::assertSentTo(
        $this->submitter,
        ApprovalChangesRequestedNotification::class,
    );
});

it('sends notification to new step approvers on step advance in a two-step workflow', function () {
    $workflow = ApprovalWorkflow::firstOrCreate(
        ['name' => 'COA Two-Step Notify', 'model_type' => CoaAccount::class],
        ['is_active' => true],
    );

    ApprovalStep::firstOrCreate(
        ['approval_workflow_id' => $workflow->id, 'step_order' => 1],
        [
            'name' => 'Step 1',
            'step_type' => \App\Enums\ApprovalStepType::Sequential,
            'step_purpose' => \App\Enums\ApprovalStepPurpose::Approval,
            'approver_type' => \App\Enums\ApproverType::Permission,
            'approver_identifiers' => ['coa.approve'],
            'required_approvals_count' => 1,
        ],
    );

    ApprovalStep::firstOrCreate(
        ['approval_workflow_id' => $workflow->id, 'step_order' => 2],
        [
            'name' => 'Step 2',
            'step_type' => \App\Enums\ApprovalStepType::Sequential,
            'step_purpose' => \App\Enums\ApprovalStepPurpose::Approval,
            'approver_type' => \App\Enums\ApproverType::Permission,
            'approver_identifiers' => ['coa.approve.second'],
            'required_approvals_count' => 1,
        ],
    );

    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'NA05',
        'account_name' => 'Two Step Notify',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    Notification::fake();

    $this->engine->processAction(
        instance: $instance,
        step: $instance->currentStep,
        actionType: ApprovalActionType::Approve,
        actor: $this->approver,
        comments: 'Step 1 approved',
    );

    Notification::assertSentTo(
        $this->approverTwo,
        ApprovalStepAdvancedNotification::class,
    );
});

it('sends cancellation notification to submitter when cancelled by another user', function () {
    $account = CoaAccount::factory()->create([
        'site_id' => $this->site->id,
        'account_code' => 'NA06',
        'account_name' => 'Notify Cancel',
        'account_type' => 'EXPENSE',
        'is_active' => false,
    ]);

    $instance = $this->engine->initializeWorkflow($account, $this->workflow, $this->submitter);
    $this->engine->submitForApproval($instance, $this->submitter);
    $instance->refresh();

    Notification::fake();

    $this->engine->cancelApproval($instance, $this->approver);

    Notification::assertSentTo(
        $this->submitter,
        ApprovalCancelledNotification::class,
    );
});
