<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApprovalActionType;
use App\Http\Controllers\Controller;
use App\Models\ApprovalInstance;
use App\Services\Approval\WorkflowEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ApprovalActionController extends Controller
{
    public function __construct(protected WorkflowEngine $workflowEngine) {}

    /**
     * Process an approval action (approve, reject, request changes).
     */
    public function process(
        Request $request,
        ApprovalInstance $approvalInstance,
    ): JsonResponse {
        $validated = $request->validate([
            'action_type' => [
                'required',
                Rule::enum(ApprovalActionType::class),
            ],
            'comments' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        $actionType = ApprovalActionType::from($validated['action_type']);

        // Check authorization based on action type
        $authorized = match ($actionType) {
            ApprovalActionType::Approve => Gate::allows(
                'approve',
                $approvalInstance,
            ),
            ApprovalActionType::Reject => Gate::allows(
                'reject',
                $approvalInstance,
            ),
            ApprovalActionType::RequestChanges => Gate::allows(
                'requestChanges',
                $approvalInstance,
            ),
        };

        if (! $authorized) {
            return response()->json(
                [
                    'message' => 'You are not authorized to perform this action',
                ],
                403,
            );
        }

        $success = $this->workflowEngine->processAction(
            instance: $approvalInstance,
            step: $approvalInstance->currentStep,
            actionType: $actionType,
            actor: $request->user(),
            comments: $validated['comments'] ?? null,
            metadata: $validated['metadata'] ?? null,
        );

        if (! $success) {
            return response()->json(
                [
                    'message' => 'Failed to process approval action',
                ],
                422,
            );
        }

        return response()->json([
            'message' => 'Action processed successfully',
            'instance' => $approvalInstance
                ->fresh()
                ->load(['currentStep', 'actions.actor', 'workflowVersion']),
        ]);
    }

    /**
     * Get approval history for an instance.
     */
    public function history(ApprovalInstance $approvalInstance): JsonResponse
    {
        Gate::authorize('view', $approvalInstance);

        $actions = $approvalInstance
            ->actions()
            ->with(['actor', 'step'])
            ->orderBy('created_at')
            ->get();

        return response()->json($actions);
    }
}
