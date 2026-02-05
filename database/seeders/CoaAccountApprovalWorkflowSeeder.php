<?php

namespace Database\Seeders;

use App\Enums\ApprovalStepPurpose;
use App\Enums\ApprovalStepType;
use App\Enums\ApproverType;
use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use App\Models\CoaAccount;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CoaAccountApprovalWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate([
            'name' => 'coa.approve',
            'guard_name' => 'web',
        ]);

        $workflow = ApprovalWorkflow::firstOrCreate(
            [
                'name' => 'COA Account Approval',
                'model_type' => CoaAccount::class,
            ],
            [
                'description' => 'Approval workflow for COA account creation',
                'is_active' => true,
            ],
        );

        ApprovalStep::firstOrCreate(
            [
                'approval_workflow_id' => $workflow->id,
                'step_order' => 1,
            ],
            [
                'name' => 'COA Approval',
                'description' => 'Approve or reject the new COA account',
                'step_type' => ApprovalStepType::Sequential,
                'step_purpose' => ApprovalStepPurpose::Approval,
                'approver_type' => ApproverType::Permission,
                'approver_identifiers' => ['coa.approve'],
                'required_approvals_count' => 1,
            ],
        );

        $this->command->info('COA Account Approval workflow seeded.');
    }
}
