<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use App\Services\Approval\ApprovalWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalWorkflowController extends Controller
{
    public function __construct(
        protected ApprovalWorkflowService $workflowService,
    ) {}

    /**
     * Display a listing of workflows.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ApprovalWorkflow::class);

        $query = ApprovalWorkflow::with(['currentVersion.steps']);

        // Filter by model type
        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $workflows = $query->paginate($request->input('per_page', 15));

        return response()->json($workflows);
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', ApprovalWorkflow::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model_type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workflow = $this->workflowService->createWorkflow(
            name: $validated['name'],
            modelType: $validated['model_type'],
            description: $validated['description'] ?? null,
        );

        return response()->json($workflow->load('currentVersion'), 201);
    }

    /**
     * Display the specified workflow.
     */
    public function show(ApprovalWorkflow $approvalWorkflow): JsonResponse
    {
        Gate::authorize('view', $approvalWorkflow);

        $approvalWorkflow->load(['currentVersion.steps', 'versions']);

        return response()->json($approvalWorkflow);
    }

    /**
     * Update the specified workflow.
     */
    public function update(
        Request $request,
        ApprovalWorkflow $approvalWorkflow,
    ): JsonResponse {
        Gate::authorize('update', $approvalWorkflow);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $approvalWorkflow->update($validated);

        return response()->json($approvalWorkflow->load('currentVersion'));
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(ApprovalWorkflow $approvalWorkflow): JsonResponse
    {
        Gate::authorize('delete', $approvalWorkflow);

        $approvalWorkflow->delete();

        return response()->json(['message' => 'Workflow deleted successfully']);
    }

    /**
     * Create a new version for the workflow.
     */
    public function createVersion(
        Request $request,
        ApprovalWorkflow $approvalWorkflow,
    ): JsonResponse {
        Gate::authorize('update', $approvalWorkflow);

        $validated = $request->validate([
            'configuration' => 'nullable|array',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.step_order' => 'nullable|integer|min:0',
            'steps.*.step_type' => 'required|in:sequential,parallel',
            'steps.*.required_approvals_count' => 'nullable|integer|min:1',
            'steps.*.approver_type' => 'required|in:user,role,permission',
            'steps.*.approver_identifiers' => 'required|array|min:1',
            'steps.*.conditional_rules' => 'nullable|array',
        ]);

        $version = $this->workflowService->createVersion(
            workflow: $approvalWorkflow,
            steps: $validated['steps'],
            configuration: $validated['configuration'] ?? null,
        );

        return response()->json($version->load('steps'), 201);
    }

    /**
     * Activate a specific version.
     */
    public function activateVersion(
        ApprovalWorkflow $approvalWorkflow,
        int $versionId,
    ): JsonResponse {
        Gate::authorize('activate', $approvalWorkflow);

        $version = $approvalWorkflow->versions()->findOrFail($versionId);

        $this->workflowService->activateVersion($version);

        return response()->json([
            'message' => 'Version activated successfully',
            'workflow' => $approvalWorkflow
                ->fresh()
                ->load('currentVersion.steps'),
        ]);
    }

    /**
     * Deactivate the workflow.
     */
    public function deactivate(ApprovalWorkflow $approvalWorkflow): JsonResponse
    {
        Gate::authorize('activate', $approvalWorkflow);

        $this->workflowService->deactivateWorkflow($approvalWorkflow);

        return response()->json([
            'message' => 'Workflow deactivated successfully',
            'workflow' => $approvalWorkflow->fresh(),
        ]);
    }
}
