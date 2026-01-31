<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\Site;
use App\Services\Approval\ApprovalWorkflowService;
use App\Services\Approval\ModelDiscoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ApprovalWorkflowController extends Controller
{
    public function __construct(
        protected ApprovalWorkflowService $workflowService,
        protected ModelDiscoveryService $modelDiscoveryService,
    ) {}

    /**
     * Display a listing of approval workflows.
     */
    public function index(Request $request, Site $site): Response
    {
        Gate::authorize('viewAny', ApprovalWorkflow::class);

        $query = ApprovalWorkflow::query()->withCount(['steps', 'instances']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $workflows = $query->latest()->paginate(15)->withQueryString();

        Inertia::share('site_code', $site->site_code);

        return Inertia::render('admin/approval-workflows/index', [
            'workflows' => $workflows,
            'filters' => $request->only(['search', 'model_type', 'is_active']),
            'modelTypes' => $this->getAvailableModelTypes(),
        ]);
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create(Site $site): Response
    {
        Gate::authorize('create', ApprovalWorkflow::class);

        Inertia::share('site_code', $site->site_code);

        return Inertia::render('admin/approval-workflows/create', [
            'modelTypes' => $this->getAvailableModelTypes(),
            'users' => \App\Models\User::select('id', 'name', 'email')->get(),
            'roles' => Role::select('id', 'name')->get(),
            'permissions' => Permission::select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', ApprovalWorkflow::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:approval_workflows,name',
            'description' => 'nullable|string',
            'model_type' => 'required|string|max:255',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.step_order' => 'nullable|integer|min:0',
            'steps.*.step_type' => 'required|in:sequential,parallel',
            'steps.*.required_approvals_count' => 'nullable|integer|min:1',
            'steps.*.approver_type' => 'required|in:user,role,permission',
            'steps.*.approver_identifiers' => 'required|array|min:1',
            'steps.*.conditional_rules' => 'nullable|array',
            'set_active' => 'nullable|boolean',
        ]);

        // Create workflow with steps
        $this->workflowService->createWorkflow(
            name: $validated['name'],
            modelType: $validated['model_type'],
            steps: $validated['steps'],
            description: $validated['description'] ?? null,
            setActive: $validated['set_active'] ?? false,
        );

        // Inertia::share('site_code', $site->site_code);

        return redirect()
            ->route('admin.approval-workflows.index')
            ->with('success', 'Approval workflow created successfully.');
    }

    /**
     * Show the form for editing the specified workflow.
     */
    public function edit(ApprovalWorkflow $approvalWorkflow): Response
    {
        Gate::authorize('update', $approvalWorkflow);

        $approvalWorkflow->load(['steps']);

        return Inertia::render('admin/approval-workflows/edit', [
            'workflow' => $approvalWorkflow,
            'modelTypes' => $this->getAvailableModelTypes(),
            'users' => \App\Models\User::select('id', 'name', 'email')->get(),
            'roles' => Role::select('id', 'name')->get(),
            'permissions' => Permission::select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified workflow.
     */
    public function update(
        Request $request,
        ApprovalWorkflow $approvalWorkflow,
    ): RedirectResponse {
        Gate::authorize('update', $approvalWorkflow);

        $validated = $request->validate([
            'name' =>
                'required|string|max:255|unique:approval_workflows,name,' .
                $approvalWorkflow->id,
            'description' => 'nullable|string',
            'steps' => 'nullable|array|min:1',
            'steps.*.name' => 'required_with:steps|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.step_order' => 'nullable|integer|min:0',
            'steps.*.step_type' => 'required_with:steps|in:sequential,parallel',
            'steps.*.required_approvals_count' => 'nullable|integer|min:1',
            'steps.*.approver_type' =>
                'required_with:steps|in:user,role,permission',
            'steps.*.approver_identifiers' => 'required_with:steps|array|min:1',
            'steps.*.conditional_rules' => 'nullable|array',
        ]);

        $approvalWorkflow->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Update steps if provided
        if (isset($validated['steps'])) {
            $this->workflowService->updateSteps(
                workflow: $approvalWorkflow,
                steps: $validated['steps'],
            );
        }

        return redirect()
            ->route('admin.approval-workflows.index')
            ->with('success', 'Approval workflow updated successfully.');
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(
        ApprovalWorkflow $approvalWorkflow,
    ): RedirectResponse {
        Gate::authorize('delete', $approvalWorkflow);

        $approvalWorkflow->delete();

        return redirect()
            ->route('admin.approval-workflows.index')
            ->with('success', 'Approval workflow deleted successfully.');
    }

    /**
     * Duplicate a workflow with all its steps.
     */
    public function duplicate(
        Request $request,
        ApprovalWorkflow $approvalWorkflow,
    ): RedirectResponse {
        Gate::authorize('duplicate', $approvalWorkflow);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255|unique:approval_workflows,name',
        ]);

        $this->workflowService->duplicateWorkflow(
            workflow: $approvalWorkflow,
            newName: $validated['name'] ?? null,
        );

        return redirect()
            ->route('admin.approval-workflows.index')
            ->with('success', 'Workflow duplicated successfully.');
    }

    /**
     * Set a workflow as the active workflow for its model type.
     */
    public function setActive(
        ApprovalWorkflow $approvalWorkflow,
    ): RedirectResponse {
        Gate::authorize('setActive', $approvalWorkflow);

        $this->workflowService->setActiveWorkflow($approvalWorkflow);

        return redirect()
            ->route('admin.approval-workflows.index')
            ->with('success', 'Workflow activated successfully.');
    }

    /**
     * Deactivate the workflow.
     */
    public function deactivate(
        ApprovalWorkflow $approvalWorkflow,
    ): RedirectResponse {
        Gate::authorize('activate', $approvalWorkflow);

        $this->workflowService->deactivateWorkflow($approvalWorkflow);

        return redirect()
            ->route('admin.approval-workflows.index')
            ->with('success', 'Workflow deactivated successfully.');
    }

    /**
     * Get available model types for workflows using auto-discovery.
     *
     * @return array<string, string> Model class => Display name
     */
    private function getAvailableModelTypes(): array
    {
        return $this->modelDiscoveryService->discoverApprovableModels();
    }
}
