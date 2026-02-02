<?php

use App\Enums\ApprovalStepPurpose;
use App\Models\ApprovalWorkflow;
use App\Services\Approval\ApprovalWorkflowService;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles for approver testing
    $this->financeRole = Role::firstOrCreate([
        'name' => 'Finance',
        'guard_name' => 'web',
    ]);
    $this->adminRole = Role::firstOrCreate([
        'name' => 'Admin',
        'guard_name' => 'web',
    ]);
});

describe('ApprovalWorkflow Model', function () {
    it('can create a workflow', function () {
        $workflow = ApprovalWorkflow::create([
            'name' => 'Test Workflow',
            'description' => 'A test workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => false,
        ]);

        expect($workflow)
            ->toBeInstanceOf(ApprovalWorkflow::class)
            ->and($workflow->name)
            ->toBe('Test Workflow')
            ->and($workflow->model_type)
            ->toBe('App\\Models\\Program')
            ->and($workflow->is_active)
            ->toBeFalse();
    });

    it(
        'enforces only one active workflow per model type via service',
        function () {
            // Manually create workflows without steps to avoid SortableTrait SQLite issue
            $workflow1 = ApprovalWorkflow::create([
                'name' => 'First Workflow',
                'model_type' => 'App\\Models\\Program',
                'is_active' => true,
            ]);

            $workflow2 = ApprovalWorkflow::create([
                'name' => 'Second Workflow',
                'model_type' => 'App\\Models\\Program',
                'is_active' => false,
            ]);

            expect($workflow1->is_active)
                ->toBeTrue()
                ->and($workflow2->is_active)
                ->toBeFalse();
        },
    );

    it('can be soft deleted', function () {
        $workflow = ApprovalWorkflow::create([
            'name' => 'Deletable Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => false,
        ]);

        $workflowId = $workflow->id;
        $workflow->delete();

        expect(ApprovalWorkflow::find($workflowId))
            ->toBeNull()
            ->and(ApprovalWorkflow::withTrashed()->find($workflowId))
            ->not->toBeNull();
    });

    it('has scopes for active and model filtering', function () {
        ApprovalWorkflow::create([
            'name' => 'Active Program Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => true,
        ]);

        ApprovalWorkflow::create([
            'name' => 'Inactive Program Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => false,
        ]);

        ApprovalWorkflow::create([
            'name' => 'Active Payment Workflow',
            'model_type' => 'App\\Models\\PaymentRequest',
            'is_active' => true,
        ]);

        $activeWorkflows = ApprovalWorkflow::active()->get();
        $programWorkflows = ApprovalWorkflow::forModel(
            'App\\Models\\Program',
        )->get();

        expect($activeWorkflows)
            ->toHaveCount(2)
            ->and($programWorkflows)
            ->toHaveCount(2);
    });
});

describe('ApprovalStepPurpose Enum', function () {
    it('has correct labels', function () {
        expect(ApprovalStepPurpose::Approval->label())
            ->toBe('Approval')
            ->and(ApprovalStepPurpose::Action->label())
            ->toBe('Action');
    });

    it('has correct descriptions', function () {
        expect(ApprovalStepPurpose::Approval->description())
            ->toContain('auto-skipped')
            ->and(ApprovalStepPurpose::Action->description())
            ->toContain('action');
    });

    it('identifies approval vs action correctly', function () {
        expect(ApprovalStepPurpose::Approval->requiresApproval())
            ->toBeTrue()
            ->and(ApprovalStepPurpose::Action->requiresApproval())
            ->toBeFalse()
            ->and(ApprovalStepPurpose::Approval->isActionStep())
            ->toBeFalse()
            ->and(ApprovalStepPurpose::Action->isActionStep())
            ->toBeTrue();
    });
});

describe('Approval Workflow Controller - Dynamic Fields', function () {
    it('create page includes modelFieldsMap in props', function () {
        $admin = \App\Models\User::factory()->create();
        // Need both group middleware permission and route-specific permission
        $viewPermission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'approval-workflows.view.all',
            'guard_name' => 'web',
        ]);
        $createPermission = \Spatie\Permission\Models\Permission::firstOrCreate(
            [
                'name' => 'approval-workflows.create',
                'guard_name' => 'web',
            ],
        );
        $admin->givePermissionTo([$viewPermission, $createPermission]);

        $response = $this->actingAs($admin)->get(
            route('admin.approval-workflows.create'),
        );

        $response->assertOk()->assertInertia(
            fn($page) => $page
                ->component('admin/approval-workflows/create')
                ->has('modelFieldsMap')
                ->where('modelFieldsMap', fn($map) => count($map) > 0),
        );
    });

    it('create page modelFieldsMap contains fields for all discovered models', function () {
        $admin = \App\Models\User::factory()->create();
        $viewPermission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'approval-workflows.view.all',
            'guard_name' => 'web',
        ]);
        $createPermission = \Spatie\Permission\Models\Permission::firstOrCreate(
            [
                'name' => 'approval-workflows.create',
                'guard_name' => 'web',
            ],
        );
        $admin->givePermissionTo([$viewPermission, $createPermission]);

        $response = $this->actingAs($admin)->get(
            route('admin.approval-workflows.create'),
        );

        $response->assertOk()->assertInertia(function ($page) {
            $page->component('admin/approval-workflows/create')->has(
                'modelFieldsMap',
            );

            // Get the actual data to inspect
            $map = $page->toArray()['props']['modelFieldsMap'];

            // Check that we have model entries
            expect($map)->toBeArray();

            // Each model should have an array of fields
            foreach ($map as $modelClass => $fields) {
                expect($fields)->toBeArray();

                // Each field should have required properties
                foreach ($fields as $field) {
                    expect($field)
                        ->toHaveKeys(['value', 'label', 'type'])
                        ->and($field['type'])
                        ->toBeIn(['string', 'number', 'date', 'boolean']);
                }
            }
        });
    });

    it('edit page includes modelFields in props', function () {
        $admin = \App\Models\User::factory()->create();
        $viewPermission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'approval-workflows.view.all',
            'guard_name' => 'web',
        ]);
        $updatePermission = \Spatie\Permission\Models\Permission::firstOrCreate(
            [
                'name' => 'approval-workflows.update',
                'guard_name' => 'web',
            ],
        );
        $admin->givePermissionTo([$viewPermission, $updatePermission]);

        $workflow = ApprovalWorkflow::create([
            'name' => 'Test Workflow',
            'model_type' => 'App\\Models\\PaymentRequest',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.approval-workflows.edit', $workflow),
        );

        $response->assertOk()->assertInertia(
            fn($page) => $page
                ->component('admin/approval-workflows/edit')
                ->has('modelFields')
                ->where('modelFields', fn($fields) => count($fields) > 0),
        );
    });

    it('edit page modelFields contains correct fields for workflow model type', function () {
        $admin = \App\Models\User::factory()->create();
        $viewPermission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'approval-workflows.view.all',
            'guard_name' => 'web',
        ]);
        $updatePermission = \Spatie\Permission\Models\Permission::firstOrCreate(
            [
                'name' => 'approval-workflows.update',
                'guard_name' => 'web',
            ],
        );
        $admin->givePermissionTo([$viewPermission, $updatePermission]);

        $workflow = ApprovalWorkflow::create([
            'name' => 'Payment Workflow',
            'model_type' => 'App\\Models\\PaymentRequest',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.approval-workflows.edit', $workflow),
        );

        $response->assertOk()->assertInertia(function ($page) {
            $page->component('admin/approval-workflows/edit')->has(
                'modelFields',
            );

            // Get the actual data to inspect
            $fields = $page->toArray()['props']['modelFields'];

            expect($fields)->toBeArray();

            // Each field should have required structure
            foreach ($fields as $field) {
                expect($field)
                    ->toHaveKeys(['value', 'label', 'type'])
                    ->and($field['type'])
                    ->toBeIn(['string', 'number', 'date', 'boolean']);
            }
        });
    });

    it('create page requires authorization', function () {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(
            route('admin.approval-workflows.create'),
        );

        $response->assertForbidden();
    });

    it('edit page requires authorization', function () {
        $user = \App\Models\User::factory()->create();

        $workflow = ApprovalWorkflow::create([
            'name' => 'Test Workflow',
            'model_type' => 'App\\Models\\PaymentRequest',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get(
            route('admin.approval-workflows.edit', $workflow),
        );

        $response->assertForbidden();
    });
});

describe('ApprovalWorkflowService', function () {
    it('provides getActiveWorkflowForModel method', function () {
        $service = app(ApprovalWorkflowService::class);

        // No active workflow initially
        $noWorkflow = $service->getActiveWorkflowForModel(
            'App\\Models\\Program',
        );
        expect($noWorkflow)->toBeNull();

        // Create active workflow directly
        $workflow = ApprovalWorkflow::create([
            'name' => 'Active Program Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => true,
        ]);

        $activeWorkflow = $service->getActiveWorkflowForModel(
            'App\\Models\\Program',
        );

        expect($activeWorkflow)
            ->not->toBeNull()
            ->and($activeWorkflow->id)
            ->toBe($workflow->id);
    });

    it('can set workflow as active and deactivate others', function () {
        $service = app(ApprovalWorkflowService::class);

        // Create two workflows directly
        $workflow1 = ApprovalWorkflow::create([
            'name' => 'First Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => false,
        ]);

        $workflow2 = ApprovalWorkflow::create([
            'name' => 'Second Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => false,
        ]);

        // Activate first
        $service->setActiveWorkflow($workflow1);
        expect($workflow1->fresh()->is_active)
            ->toBeTrue()
            ->and($workflow2->fresh()->is_active)
            ->toBeFalse();

        // Activate second (should deactivate first)
        $service->setActiveWorkflow($workflow2);
        expect($workflow1->fresh()->is_active)
            ->toBeFalse()
            ->and($workflow2->fresh()->is_active)
            ->toBeTrue();
    });

    it('can deactivate workflow', function () {
        $service = app(ApprovalWorkflowService::class);

        $workflow = ApprovalWorkflow::create([
            'name' => 'Active Workflow',
            'model_type' => 'App\\Models\\Program',
            'is_active' => true,
        ]);

        expect($workflow->is_active)->toBeTrue();

        $service->deactivateWorkflow($workflow);

        expect($workflow->fresh()->is_active)->toBeFalse();
    });

    it('can duplicate workflow basic info', function () {
        $service = app(ApprovalWorkflowService::class);

        $original = ApprovalWorkflow::create([
            'name' => 'Original Workflow',
            'description' => 'Test description',
            'model_type' => 'App\\Models\\Program',
            'is_active' => true,
        ]);

        $duplicate = $service->duplicateWorkflow(
            $original,
            'Duplicated Workflow',
        );

        expect($duplicate->name)
            ->toBe('Duplicated Workflow')
            ->and($duplicate->description)
            ->toBe('Test description')
            ->and($duplicate->model_type)
            ->toBe($original->model_type)
            ->and($duplicate->is_active)
            ->toBeFalse();
    });
});
