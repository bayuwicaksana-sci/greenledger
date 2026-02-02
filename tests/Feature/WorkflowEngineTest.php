<?php

use App\Models\ApprovalInstance;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\User;
use App\Services\Approval\WorkflowEngine;
use App\States\ApprovalInstance\Approved;
use App\States\ApprovalInstance\InProgress;
use App\States\ApprovalInstance\Pending;
use App\States\ApprovalInstance\Rejected;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create test user
    $this->user = User::factory()->create();

    // Create roles for testing
    $this->approverRole = Role::firstOrCreate([
        'name' => 'Approver',
        'guard_name' => 'web',
    ]);
    $this->financeRole = Role::firstOrCreate([
        'name' => 'Finance',
        'guard_name' => 'web',
    ]);

    // Create a workflow without steps (to avoid SortableTrait SQLite issues)
    $this->workflow = ApprovalWorkflow::create([
        'name' => 'Test Workflow',
        'model_type' => 'App\\Models\\Program',
        'description' => 'Test workflow for engine testing',
        'is_active' => true,
    ]);
});

describe('WorkflowEngine - Initialization', function () {
    it('can initialize a workflow for a model', function () {
        $engine = app(WorkflowEngine::class);

        // Create a mock approvable model
        $mockModel = new class extends \Illuminate\Database\Eloquent\Model
        {
            protected $table = 'approval_workflows'; // Use existing table

            public $id = 999;
        };
        $mockModel->id = $this->workflow->id; // Use an existing ID

        $instance = $engine->initializeWorkflow(
            $mockModel,
            $this->workflow,
            $this->user,
        );

        expect($instance)
            ->toBeInstanceOf(ApprovalInstance::class)
            ->and($instance->status)
            ->toBeInstanceOf(Pending::class)
            ->and($instance->submitted_by)
            ->toBe($this->user->id)
            ->and($instance->approval_workflow_id)
            ->toBe($this->workflow->id);
    });
});

describe('WorkflowEngine - Submission', function () {
    it('cannot submit from non-pending state', function () {
        $engine = app(WorkflowEngine::class);

        // Create an instance already in progress
        $instance = ApprovalInstance::create([
            'approval_workflow_id' => $this->workflow->id,
            'approvable_type' => 'App\\Models\\Program',
            'approvable_id' => 1,
            'status' => InProgress::class,
            'submitted_by' => $this->user->id,
        ]);

        $result = $engine->submitForApproval($instance, $this->user);

        expect($result)->toBeFalse();
    });
});

describe('WorkflowEngine - Actor Authorization', function () {
    it('checks if user can approve via role', function () {
        $engine = app(WorkflowEngine::class);

        // Give user the approver role
        $this->user->assignRole($this->approverRole);

        // Create a step that requires the approver role
        // Note: We'll test this via reflection since canActorApprove is protected
        $step = new ApprovalStep([
            'approver_type' => 'role',
            'approver_identifiers' => [$this->approverRole->id],
        ]);

        // Use reflection to test protected method
        $reflection = new ReflectionClass($engine);
        $method = $reflection->getMethod('canActorApprove');
        $method->setAccessible(true);

        $canApprove = $method->invoke($engine, $this->user, $step);

        expect($canApprove)->toBeTrue();
    });

    it('denies users without required role', function () {
        $engine = app(WorkflowEngine::class);

        // User does NOT have the approver role

        $step = new ApprovalStep([
            'approver_type' => 'role',
            'approver_identifiers' => [$this->approverRole->id],
        ]);

        $reflection = new ReflectionClass($engine);
        $method = $reflection->getMethod('canActorApprove');
        $method->setAccessible(true);

        $canApprove = $method->invoke($engine, $this->user, $step);

        expect($canApprove)->toBeFalse();
    });

    it('checks if user can approve via user ID', function () {
        $engine = app(WorkflowEngine::class);

        $step = new ApprovalStep([
            'approver_type' => 'user',
            'approver_identifiers' => [$this->user->id],
        ]);

        $reflection = new ReflectionClass($engine);
        $method = $reflection->getMethod('canActorApprove');
        $method->setAccessible(true);

        $canApprove = $method->invoke($engine, $this->user, $step);

        expect($canApprove)->toBeTrue();
    });
});

describe('WorkflowEngine - Cancellation', function () {
    it('can cancel a pending workflow', function () {
        $engine = app(WorkflowEngine::class);

        $instance = ApprovalInstance::create([
            'approval_workflow_id' => $this->workflow->id,
            'approvable_type' => 'App\\Models\\Program',
            'approvable_id' => 1,
            'status' => Pending::class,
            'submitted_by' => $this->user->id,
        ]);

        $result = $engine->cancelApproval($instance, $this->user);

        expect($result)
            ->toBeTrue()
            ->and($instance->fresh()->status)
            ->toBeInstanceOf(\App\States\ApprovalInstance\Cancelled::class);
    });

    it('cannot cancel an already approved workflow', function () {
        $engine = app(WorkflowEngine::class);

        $instance = ApprovalInstance::create([
            'approval_workflow_id' => $this->workflow->id,
            'approvable_type' => 'App\\Models\\Program',
            'approvable_id' => 1,
            'status' => Approved::class,
            'submitted_by' => $this->user->id,
            'completed_at' => now(),
        ]);

        $result = $engine->cancelApproval($instance, $this->user);

        expect($result)->toBeFalse();
    });

    it('cannot cancel an already rejected workflow', function () {
        $engine = app(WorkflowEngine::class);

        $instance = ApprovalInstance::create([
            'approval_workflow_id' => $this->workflow->id,
            'approvable_type' => 'App\\Models\\Program',
            'approvable_id' => 1,
            'status' => Rejected::class,
            'submitted_by' => $this->user->id,
            'completed_at' => now(),
        ]);

        $result = $engine->cancelApproval($instance, $this->user);

        expect($result)->toBeFalse();
    });
});

describe('WorkflowEngine - State Transitions', function () {
    it('tracks status transitions correctly', function () {
        // Test that states are correctly assigned
        $instance = ApprovalInstance::create([
            'approval_workflow_id' => $this->workflow->id,
            'approvable_type' => 'App\\Models\\Program',
            'approvable_id' => 1,
            'status' => Pending::class,
            'submitted_by' => $this->user->id,
        ]);

        expect($instance->status)->toBeInstanceOf(Pending::class);

        // Test color method exists
        expect($instance->status->color())->toBeString();

        // Test label method exists
        expect($instance->status->label())->toBeString();
    });
});
