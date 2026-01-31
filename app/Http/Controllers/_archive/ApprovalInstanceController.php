<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApprovalInstanceStatus;
use App\Http\Controllers\Controller;
use App\Models\ApprovalInstance;
use App\Services\Approval\WorkflowEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalInstanceController extends Controller
{
    public function __construct(protected WorkflowEngine $workflowEngine) {}

    /**
     * Display a listing of approval instances.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ApprovalInstance::class);

        $query = ApprovalInstance::with([
            'workflowVersion.workflow',
            'currentStep',
            'submittedBy',
            'approvable',
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by submitter
        if ($request->has('submitted_by')) {
            $query->where('submitted_by', $request->submitted_by);
        }

        // Filter by approvable type
        if ($request->has('approvable_type')) {
            $query->where('approvable_type', $request->approvable_type);
        }

        // Show only user's instances unless they have permission
        if (! $request->user()->can('view-all-approval-instances')) {
            $query->where('submitted_by', $request->user()->id);
        }

        $instances = $query
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($instances);
    }

    /**
     * Display the specified instance.
     */
    public function show(ApprovalInstance $approvalInstance): JsonResponse
    {
        Gate::authorize('view', $approvalInstance);

        $approvalInstance->load([
            'workflowVersion.workflow',
            'workflowVersion.steps',
            'currentStep',
            'submittedBy',
            'approvable',
            'actions.actor',
            'actions.step',
        ]);

        return response()->json($approvalInstance);
    }

    /**
     * Submit an instance for approval.
     */
    public function submit(
        Request $request,
        ApprovalInstance $approvalInstance,
    ): JsonResponse {
        Gate::authorize('submit', $approvalInstance);

        $success = $this->workflowEngine->submitForApproval(
            instance: $approvalInstance,
            submitter: $request->user(),
        );

        if (! $success) {
            return response()->json(
                [
                    'message' => 'Cannot submit instance for approval',
                ],
                422,
            );
        }

        return response()->json([
            'message' => 'Instance submitted for approval successfully',
            'instance' => $approvalInstance
                ->fresh()
                ->load(['currentStep', 'workflowVersion']),
        ]);
    }

    /**
     * Cancel an approval instance.
     */
    public function cancel(
        Request $request,
        ApprovalInstance $approvalInstance,
    ): JsonResponse {
        Gate::authorize('cancel', $approvalInstance);

        $success = $this->workflowEngine->cancelApproval(
            instance: $approvalInstance,
            actor: $request->user(),
        );

        if (! $success) {
            return response()->json(
                [
                    'message' => 'Cannot cancel this instance',
                ],
                422,
            );
        }

        return response()->json([
            'message' => 'Instance cancelled successfully',
            'instance' => $approvalInstance->fresh(),
        ]);
    }

    /**
     * Get instances pending approval by the current user.
     */
    public function pendingForUser(Request $request): JsonResponse
    {
        $user = $request->user();

        $instances = ApprovalInstance::with([
            'workflowVersion.workflow',
            'currentStep',
            'submittedBy',
            'approvable',
        ])
            ->where('status', ApprovalInstanceStatus::PendingApproval)
            ->whereHas('currentStep', function ($query) use ($user) {
                $query
                    ->where(function ($q) use ($user) {
                        // User-based approvers
                        $q->where('approver_type', 'user')->whereJsonContains(
                            'approver_identifiers',
                            $user->id,
                        );
                    })
                    ->orWhere(function ($q) use ($user) {
                        // Role-based approvers
                        $q->where('approver_type', 'role')->where(function (
                            $subQ,
                        ) use ($user) {
                            foreach ($user->roles->pluck('name') as $role) {
                                $subQ->orWhereJsonContains(
                                    'approver_identifiers',
                                    $role,
                                );
                            }
                        });
                    })
                    ->orWhere(function ($q) use ($user) {
                        // Permission-based approvers
                        $q->where('approver_type', 'permission')->where(
                            function ($subQ) use ($user) {
                                foreach (
                                    $user->getAllPermissions()->pluck('name') as $permission
                                ) {
                                    $subQ->orWhereJsonContains(
                                        'approver_identifiers',
                                        $permission,
                                    );
                                }
                            },
                        );
                    });
            })
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($instances);
    }
}
