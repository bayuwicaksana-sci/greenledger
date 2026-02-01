<?php

namespace App\Services\Approval;

use App\Models\ApprovalStep;
use App\Models\ApprovalWorkflow;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing approval workflows.
 */
class ApprovalWorkflowService
{
    /**
     * Create a new approval workflow with steps.
     */
    public function createWorkflow(
        string $name,
        string $modelType,
        array $steps,
        ?string $description = null,
        bool $setActive = false,
    ): ApprovalWorkflow {
        return DB::transaction(function () use (
            $name,
            $modelType,
            $steps,
            $description,
            $setActive,
        ) {
            $workflow = ApprovalWorkflow::create([
                'name' => $name,
                'model_type' => $modelType,
                'description' => $description,
                'is_active' => false,
            ]);

            // Create the steps
            foreach ($steps as $index => $stepData) {
                ApprovalStep::create([
                    'approval_workflow_id' => $workflow->id,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?? null,
                    'step_order' => $stepData['step_order'] ?? $index,
                    'step_type' => $stepData['step_type'],
                    'required_approvals_count' =>
                        $stepData['required_approvals_count'] ?? 1,
                    'approver_type' => $stepData['approver_type'],
                    'approver_identifiers' => $stepData['approver_identifiers'],
                    'conditional_rules' =>
                        $stepData['conditional_rules'] ?? null,
                ]);
            }

            activity('approval')
                ->performedOn($workflow)
                ->log('Workflow created with ' . count($steps) . ' steps');

            // Set as active if requested
            if ($setActive) {
                $this->setActiveWorkflow($workflow);
            }

            return $workflow->fresh('steps');
        });
    }

    /**
     * Update workflow steps.
     */
    public function updateSteps(ApprovalWorkflow $workflow, array $steps): bool
    {
        return DB::transaction(function () use ($workflow, $steps) {
            // Delete existing steps
            $workflow->steps()->delete();

            // Create new steps
            foreach ($steps as $index => $stepData) {
                ApprovalStep::create([
                    'approval_workflow_id' => $workflow->id,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?? null,
                    'step_order' => $stepData['step_order'] ?? $index,
                    'step_type' => $stepData['step_type'],
                    'required_approvals_count' =>
                        $stepData['required_approvals_count'] ?? 1,
                    'approver_type' => $stepData['approver_type'],
                    'approver_identifiers' => $stepData['approver_identifiers'],
                    'conditional_rules' =>
                        $stepData['conditional_rules'] ?? null,
                ]);
            }

            activity('approval')->performedOn($workflow)->log('Steps updated');

            return true;
        });
    }

    /**
     * Set a workflow as the active workflow for its model type.
     * Deactivates any other active workflow for the same model type.
     */
    public function setActiveWorkflow(ApprovalWorkflow $workflow): bool
    {
        return DB::transaction(function () use ($workflow) {
            // Deactivate all other workflows for this model type
            ApprovalWorkflow::where('model_type', $workflow->model_type)
                ->where('id', '!=', $workflow->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Activate this workflow
            $workflow->update(['is_active' => true]);

            activity('approval')
                ->performedOn($workflow)
                ->log('Workflow set as active for ' . $workflow->model_type);

            return true;
        });
    }

    /**
     * Deactivate a workflow.
     */
    public function deactivateWorkflow(ApprovalWorkflow $workflow): bool
    {
        return DB::transaction(function () use ($workflow) {
            $workflow->update(['is_active' => false]);

            activity('approval')
                ->performedOn($workflow)
                ->log('Workflow deactivated');

            return true;
        });
    }

    /**
     * Duplicate a workflow with all its steps.
     */
    public function duplicateWorkflow(
        ApprovalWorkflow $workflow,
        ?string $newName = null,
    ): ApprovalWorkflow {
        return DB::transaction(function () use ($workflow, $newName) {
            // Load steps
            $workflow->load('steps');

            // Create new workflow
            $newWorkflow = ApprovalWorkflow::create([
                'name' => $newName ?? $workflow->name . ' (Copy)',
                'model_type' => $workflow->model_type,
                'description' => $workflow->description,
                'is_active' => false, // Duplicates are never active by default
            ]);

            // Duplicate steps
            foreach ($workflow->steps as $step) {
                ApprovalStep::create([
                    'approval_workflow_id' => $newWorkflow->id,
                    'name' => $step->name,
                    'description' => $step->description,
                    'step_order' => $step->step_order,
                    'step_type' => $step->step_type,
                    'required_approvals_count' =>
                        $step->required_approvals_count,
                    'approver_type' => $step->approver_type,
                    'approver_identifiers' => $step->approver_identifiers,
                    'conditional_rules' => $step->conditional_rules,
                ]);
            }

            activity('approval')
                ->performedOn($newWorkflow)
                ->log('Workflow duplicated from: ' . $workflow->name);

            return $newWorkflow->fresh('steps');
        });
    }
}
