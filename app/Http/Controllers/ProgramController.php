<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Models\Commodity;
use App\Models\Program;
use App\Models\ProgramAssignment;
use App\Models\ProgramBudgetCategory;
use App\Models\ProgramBudgetItem;
use App\Models\ProgramBudgetPhase;
use App\Models\ProgramTreatment;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Site $site)
    {
        $query = Program::query()
            ->where('site_id', $site->id)
            ->where('status', '!=', Program::STATUS_ARCHIVED)
            ->with(['createdBy', 'updatedBy']);

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('classification')) {
            $query->where('classification', $request->classification);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")->orWhere(
                    'program_name',
                    'like',
                    "%{$search}%",
                );
            });
        }

        return Inertia::render('programs/index', [
            'programs' => $query->latest()->get(),
            'site_code' => $site->site_code,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Site $site)
    {
        return Inertia::render('programs/create', [
            'site_code' => $site->site_code,
            'current_year' => now()->year,
            'users' => User::select('id', 'name', 'email')->get(),
            'commodities' => Commodity::where('is_active', true)->get(),
            'active_programs' => Program::where('site_id', $site->id)
                ->whereIn('status', [Program::STATUS_DRAFT, Program::STATUS_ACTIVE])
                ->select('id', 'program_code', 'program_name')->get(),
            'budget_categories' => ProgramBudgetCategory::orderBy('sort_order')->get(),
            'budget_phases' => ProgramBudgetPhase::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramRequest $request, Site $site)
    {
        $data = $request->validated();

        // Extract nested / file data
        $treatments = $data['treatments'] ?? [];
        $budgetItems = $data['budget_items'] ?? [];
        $activities = $data['activities'] ?? [];
        $supportTeamIds = $data['support_team_member_ids'] ?? [];
        unset($data['treatments'], $data['budget_items'], $data['activities'], $data['support_team_member_ids'],
            $data['plot_map'], $data['reference_files'], $data['existing_reference_file_ids'], $data['remove_plot_map']);

        $data['status'] = Program::STATUS_DRAFT;
        $data['site_id'] = $site->id;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $program = Program::create($data);

        // Plot map upload
        if ($request->hasFile('plot_map')) {
            $program->addMedia($request->file('plot_map'))->toMediaCollection('plot_map');
        }

        // Reference files upload
        if ($request->hasFile('reference_files')) {
            foreach ($request->file('reference_files') as $file) {
                $program->addMedia($file)->toMediaCollection('reference_files');
            }
        }

        // Create activities
        foreach ($activities as $index => $activity) {
            $program->activities()->create([
                'activity_name' => $activity['activity_name'],
                'description' => $activity['description'] ?? null,
                'budget_allocation' => $activity['budget_allocation'],
                'planned_start_date' => $activity['planned_start_date'] ?? null,
                'planned_end_date' => $activity['planned_end_date'] ?? null,
                'status' => 'PLANNED',
                'sort_order' => $index,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Create treatments
        foreach ($treatments as $index => $treatment) {
            ProgramTreatment::create([
                'program_id' => $program->id,
                'treatment_code' => $treatment['treatment_code'],
                'treatment_description' => $treatment['treatment_description'] ?? null,
                'specification' => $treatment['specification'] ?? null,
                'sort_order' => $index,
            ]);
        }

        // Create budget items (subtotal recalculated server-side)
        foreach ($budgetItems as $index => $item) {
            ProgramBudgetItem::create([
                'program_id' => $program->id,
                'category_id' => $item['category_id'],
                'phase_id' => $item['phase_id'],
                'item_description' => $item['item_description'],
                'specification' => $item['specification'] ?? null,
                'unit' => $item['unit'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => (float) $item['qty'] * (float) $item['unit_price'],
                'notes' => $item['notes'] ?? null,
                'sort_order' => $index,
            ]);
        }

        // Create support team member assignments
        foreach ($supportTeamIds as $userId) {
            ProgramAssignment::create([
                'program_id' => $program->id,
                'user_id' => $userId,
                'role_in_program' => ProgramAssignment::ROLE_MEMBER,
                'assigned_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('programs.index', $site->site_code)
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site, Program $program)
    {
        if ($program->site_id !== $site->id) {
            abort(404);
        }

        return Inertia::render('programs/show', [
            'program' => $program->load(['activities', 'createdBy', 'budgetItems.category', 'budgetItems.phase', 'treatments']),
            'site_code' => $site->site_code,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site, Program $program)
    {
        if ($program->site_id !== $site->id) {
            abort(404);
        }

        $program->load(['treatments', 'budgetItems', 'activities']);

        // Serialize media for frontend
        $referenceFiles = $program->getMedia('reference_files')->map(fn ($media) => [
            'id' => $media->id,
            'original_name' => $media->file_name,
            'url' => $media->getUrl(),
        ])->toArray();

        $plotMap = $program->getFirstMedia('plot_map');
        $plotMapData = $plotMap ? [
            'id' => $plotMap->id,
            'original_name' => $plotMap->file_name,
            'url' => $plotMap->getUrl(),
        ] : null;

        // Extract active support team member IDs
        $supportTeamMemberIds = $program->assignments()
            ->where('role_in_program', ProgramAssignment::ROLE_MEMBER)
            ->whereNull('removed_at')
            ->pluck('user_id')
            ->toArray();

        return Inertia::render('programs/edit', [
            'program' => $program,
            'site_code' => $site->site_code,
            'statuses' => match ($program->status) {
                Program::STATUS_DRAFT => [Program::STATUS_DRAFT, Program::STATUS_ACTIVE],
                Program::STATUS_ACTIVE => [Program::STATUS_ACTIVE, Program::STATUS_COMPLETED],
                Program::STATUS_COMPLETED => [Program::STATUS_COMPLETED, Program::STATUS_ARCHIVED],
                default => [$program->status],
            },
            'users' => User::select('id', 'name', 'email')->get(),
            'commodities' => Commodity::where('is_active', true)->get(),
            'active_programs' => Program::where('site_id', $site->id)
                ->where('id', '!=', $program->id)
                ->whereIn('status', [Program::STATUS_DRAFT, Program::STATUS_ACTIVE])
                ->select('id', 'program_code', 'program_name')->get(),
            'budget_categories' => ProgramBudgetCategory::orderBy('sort_order')->get(),
            'budget_phases' => ProgramBudgetPhase::orderBy('sort_order')->get(),
            'reference_files' => $referenceFiles,
            'plot_map' => $plotMapData,
            'support_team_member_ids' => $supportTeamMemberIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        StoreProgramRequest $request,
        Site $site,
        Program $program,
    ) {
        if ($program->site_id !== $site->id) {
            abort(404);
        }

        $data = $request->validated();

        $allowedTransitions = match ($program->status) {
            Program::STATUS_DRAFT => [Program::STATUS_DRAFT, Program::STATUS_ACTIVE],
            Program::STATUS_ACTIVE => [Program::STATUS_ACTIVE, Program::STATUS_COMPLETED],
            Program::STATUS_COMPLETED => [Program::STATUS_COMPLETED, Program::STATUS_ARCHIVED],
            default => [$program->status],
        };

        if (! in_array($data['status'], $allowedTransitions)) {
            return redirect()->back()->withErrors([
                'status' => 'Invalid status transition.',
            ]);
        }

        // Extract nested / file data
        $treatments = $data['treatments'] ?? [];
        $budgetItems = $data['budget_items'] ?? [];
        $activities = $data['activities'] ?? [];
        $supportTeamIds = $data['support_team_member_ids'] ?? [];
        $existingReferenceFileIds = $data['existing_reference_file_ids'] ?? [];
        $removePlotMap = $data['remove_plot_map'] ?? false;
        unset($data['treatments'], $data['budget_items'], $data['activities'], $data['support_team_member_ids'],
            $data['plot_map'], $data['reference_files'], $data['existing_reference_file_ids'], $data['remove_plot_map']);

        $data['updated_by'] = Auth::id();
        $program->update($data);

        // Sync activities: delete all, re-create
        $program->activities()->delete();
        foreach ($activities as $index => $activity) {
            $program->activities()->create([
                'activity_name' => $activity['activity_name'],
                'description' => $activity['description'] ?? null,
                'budget_allocation' => $activity['budget_allocation'],
                'planned_start_date' => $activity['planned_start_date'] ?? null,
                'planned_end_date' => $activity['planned_end_date'] ?? null,
                'status' => 'PLANNED',
                'sort_order' => $index,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Sync treatments: delete all, re-create
        $program->treatments()->delete();
        foreach ($treatments as $index => $treatment) {
            ProgramTreatment::create([
                'program_id' => $program->id,
                'treatment_code' => $treatment['treatment_code'],
                'treatment_description' => $treatment['treatment_description'] ?? null,
                'specification' => $treatment['specification'] ?? null,
                'sort_order' => $index,
            ]);
        }

        // Sync budget items: delete all, re-create
        $program->budgetItems()->delete();
        foreach ($budgetItems as $index => $item) {
            ProgramBudgetItem::create([
                'program_id' => $program->id,
                'category_id' => $item['category_id'],
                'phase_id' => $item['phase_id'],
                'item_description' => $item['item_description'],
                'specification' => $item['specification'] ?? null,
                'unit' => $item['unit'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => (float) $item['qty'] * (float) $item['unit_price'],
                'notes' => $item['notes'] ?? null,
                'sort_order' => $index,
            ]);
        }

        // Sync reference files: remove deleted, upload new
        $currentMedia = $program->getMedia('reference_files');
        foreach ($currentMedia as $media) {
            if (! in_array($media->id, $existingReferenceFileIds)) {
                $media->delete();
            }
        }
        if ($request->hasFile('reference_files')) {
            foreach ($request->file('reference_files') as $file) {
                $program->addMedia($file)->toMediaCollection('reference_files');
            }
        }

        // Sync plot map
        if ($removePlotMap) {
            $program->clearMediaCollection('plot_map');
        } elseif ($request->hasFile('plot_map')) {
            $program->clearMediaCollection('plot_map');
            $program->addMedia($request->file('plot_map'))->toMediaCollection('plot_map');
        }

        // Sync support team members
        $currentMembers = $program->assignments()
            ->where('role_in_program', ProgramAssignment::ROLE_MEMBER)
            ->whereNull('removed_at')
            ->get();

        $currentMemberIds = $currentMembers->pluck('user_id')->toArray();

        // Soft-remove users no longer in the list
        $toRemove = array_diff($currentMemberIds, $supportTeamIds);
        if (! empty($toRemove)) {
            $program->assignments()
                ->where('role_in_program', ProgramAssignment::ROLE_MEMBER)
                ->whereNull('removed_at')
                ->whereIn('user_id', $toRemove)
                ->update([
                    'removed_at' => now(),
                    'removed_by' => Auth::id(),
                ]);
        }

        // Add new members
        $toAdd = array_diff($supportTeamIds, $currentMemberIds);
        foreach ($toAdd as $userId) {
            ProgramAssignment::create([
                'program_id' => $program->id,
                'user_id' => $userId,
                'role_in_program' => ProgramAssignment::ROLE_MEMBER,
                'assigned_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('programs.index', $site->site_code)
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site, Program $program)
    {
        if ($program->site_id !== $site->id) {
            abort(404);
        }

        $program->delete();

        return redirect()
            ->route('programs.index', $site->site_code)
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Display programs assigned to the current user.
     */
    public function myPrograms(Request $request, Site $site)
    {
        $user = Auth::user();

        $query = Program::query()
            ->where('site_id', $site->id)
            ->where('status', '!=', Program::STATUS_ARCHIVED)
            ->whereHas('assignments', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereNull('removed_at');
            })
            ->with(['createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")->orWhere(
                    'program_name',
                    'like',
                    "%{$search}%",
                );
            });
        }

        return Inertia::render('programs/my', [
            'programs' => $query->latest()->get(),
            'site_code' => $site->site_code,
        ]);
    }

    /**
     * Display archived programs.
     */
    public function archived(Request $request, Site $site)
    {
        $query = Program::query()
            ->where('site_id', $site->id)
            ->where('status', Program::STATUS_ARCHIVED)
            ->with(['createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                    ->orWhere('program_name', 'like', "%{$search}%");
            });
        }

        return Inertia::render('programs/archived', [
            'programs' => $query->latest()->get(),
            'site_code' => $site->site_code,
        ]);
    }
}
