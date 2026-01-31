<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ApprovalPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all approval system permissions
        foreach ($this->definePermissions() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions to Manager and AVP roles
        $this->assignPermissionsToRoles();

        $this->command->info(
            'Approval system permissions created and assigned to Manager and AVP roles.',
        );
    }

    /**
     * Define all approval system permissions.
     */
    private function definePermissions(): array
    {
        return [
            // Approval Workflows (Admin - Configuration)
            'approval-workflows.view.all',
            'approval-workflows.create',
            'approval-workflows.update',
            'approval-workflows.delete',
            'approval-workflows.activate', // Activate/deactivate workflows
            'approval-workflows.duplicate', // Duplicate workflows

            // Approval Instances (Runtime - User Operations)
            'approval-instances.view.own',
            'approval-instances.view.assigned',
            'approval-instances.view.all',
            'approval-instances.submit',
            'approval-instances.cancel.own',
            'approval-instances.cancel.all',

            // Approval Actions (Runtime - Approval Operations)
            'approval-actions.approve',
            'approval-actions.reject',
            'approval-actions.request-changes',
            'approval-actions.view-history',
        ];
    }

    /**
     * Assign permissions to Manager and AVP roles.
     */
    private function assignPermissionsToRoles(): void
    {
        // Get all workflow management permissions (admin)
        $workflowPermissions = [
            'approval-workflows.view.all',
            'approval-workflows.create',
            'approval-workflows.update',
            'approval-workflows.delete',
            'approval-workflows.activate',
            'approval-workflows.duplicate',
        ];

        // Get all instance and action permissions (runtime)
        $runtimePermissions = [
            'approval-instances.view.all',
            'approval-instances.submit',
            'approval-instances.cancel.all',
            'approval-actions.approve',
            'approval-actions.reject',
            'approval-actions.request-changes',
            'approval-actions.view-history',
        ];

        // Manager gets full access
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->givePermissionTo(
            array_merge($workflowPermissions, $runtimePermissions),
        );

        // AVP gets full access
        $avpRole = Role::firstOrCreate(['name' => 'AVP']);
        $avpRole->givePermissionTo(
            array_merge($workflowPermissions, $runtimePermissions),
        );
    }
}
