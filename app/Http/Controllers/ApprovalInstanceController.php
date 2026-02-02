<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApprovalInstance;
use App\Models\Site;
use App\Services\Approval\WorkflowEngine;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApprovalInstanceController extends Controller
{
    public function __construct(protected WorkflowEngine $workflowEngine) {}

    /**
     * Display a listing of pending approvals for the user.
     */
    public function index(Request $request, Site $site): Response
    {
        // Get instances where the user is an eligible approver for the current step
        // This logic needs to be robust to check user ID, roles, and permissions against the step configuration

        $user = $request->user();

        // We'll use a service method or scope to get actionable instances for the user
        // For now, let's implement a basic query based on the logic we saw in the archive

        $instances = $this->getPendingInstancesForUser($user);

        Inertia::share('site_code', $site->site_code);

        return Inertia::render('approvals/index', [
            'approvals' => $instances,
        ]);
    }

    /**
     * Display the specified approval instance.
     */
    public function show(
        Site $site,
        ApprovalInstance $approvalInstance,
    ): Response {
        // Ensure user is authorized to view this instance
        // Gate::authorize('view', $approvalInstance);
        // For now we skip strict Gate check as we haven't implemented the Policy fully yet,
        // but we should ensure they are related to it (submitter or approver)

        $approvalInstance->load([
            'approvable',
            'workflow',
            'currentStep',
            'actions.actor',
            'actions.step',
            'submittedBy',
        ]);

        Inertia::share('site_code', $site->site_code);
        return Inertia::render('approvals/show', [
            'approval' => $approvalInstance,
        ]);
    }

    /**
     * Process an approval action (approve/reject/request_changes).
     */
    public function action(
        Request $request,
        Site $site,
        ApprovalInstance $approvalInstance,
    ) {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,request_changes',
            'comment' => 'nullable|string',
        ]);

        $action = $validated['action'];
        $comment = $validated['comment'];
        $user = $request->user();

        try {
            if (!$approvalInstance->currentStep) {
                return back()->with(
                    'error',
                    'No active step found for this approval.',
                );
            }

            $actionEnum = \App\Enums\ApprovalActionType::tryFrom($action);

            if (!$actionEnum) {
                return back()->with('error', 'Invalid action type.');
            }

            $success = $this->workflowEngine->processAction(
                instance: $approvalInstance,
                step: $approvalInstance->currentStep,
                actionType: $actionEnum,
                actor: $user,
                comments: $comment,
            );

            if ($success) {
                return back()->with(
                    'success',
                    'Action processed successfully.',
                );
            } else {
                return back()->with(
                    'error',
                    'Failed to process action. You may not be authorized or the state is invalid.',
                );
            }
        } catch (\Exception $e) {
            return back()->with(
                'error',
                'Error processing action: ' . $e->getMessage(),
            );
        }
    }

    protected function getPendingInstancesForUser($user)
    {
        // Basic implementation based on what we saw in the archive
        // In a real scenario, this should likely be in a reusable service method
        // or a scope on the ApprovalInstance model.

        return ApprovalInstance::query()
            ->with(['approvable', 'currentStep', 'submittedBy', 'workflow'])
            ->where(
                'status',
                \App\Enums\ApprovalInstanceStatus::PendingApproval,
            )
            ->whereHas('currentStep', function ($query) use ($user) {
                $query
                    ->where(function ($q) use ($user) {
                        // User-based
                        $q->where('approver_type', 'user')->whereJsonContains(
                            'approver_identifiers',
                            $user->id,
                        );
                    })
                    ->orWhere(
                        function ($q) use ($user) {
                            // Role-based
                            $q->where('approver_type', 'role')->where(function (
                                $subQ,
                            ) use ($user) {
                                foreach ($user->roles as $role) {
                                    $subQ->orWhereJsonContains(
                                        'approver_identifiers',
                                        $role->id,
                                    ); // assuming stored by ID
                                    // Use name if stored by name: $role->name
                                }
                            });
                        },
                        // Add permission based if needed
                    );
            })
            ->latest()
            ->paginate(10);
    }
}
