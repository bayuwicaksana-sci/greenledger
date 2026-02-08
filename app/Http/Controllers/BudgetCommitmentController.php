<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetCommitmentRequest;
use App\Http\Requests\UpdateBudgetCommitmentRequest;
use App\Models\BudgetCommitment;
use App\Models\CoaAccount;
use App\Models\FiscalYear;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BudgetCommitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BudgetCommitment::query()->with([
            'fiscalYear',
            'program',
            'coaAccount',
            'committedBy',
        ]);

        // Filter by fiscal year
        if ($request->filled('fiscal_year_id')) {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        // Filter by program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commitments = $query->orderByDesc('commitment_date')->paginate(15);

        return Inertia::render('budget-commitments/index', [
            'commitments' => $commitments,
            'fiscalYears' => FiscalYear::orderByDesc('year')->get([
                'id',
                'year',
                'is_closed',
            ]),
            'filters' => $request->only([
                'fiscal_year_id',
                'program_id',
                'status',
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get current fiscal year or requested one
        $fiscalYearId =
            $request->get('fiscal_year_id') ??
            FiscalYear::where('is_closed', false)
                ->orderByDesc('year')
                ->value('id');

        return Inertia::render('budget-commitments/create', [
            'fiscalYears' => FiscalYear::where('is_closed', false)
                ->orderByDesc('year')
                ->get(['id', 'year']),
            'programs' => Program::where('fiscal_year_id', $fiscalYearId)->get([
                'id',
                'program_code',
                'program_name',
            ]),
            'coaAccounts' => CoaAccount::where('fiscal_year_id', $fiscalYearId)
                ->where('is_active', true)
                ->get(['id', 'account_code', 'account_name']),
            'selectedFiscalYearId' => $fiscalYearId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetCommitmentRequest $request)
    {
        $data = $request->validated();
        $data['committed_by'] = $request->user()->id;
        $data['status'] = BudgetCommitment::STATUS_PENDING;

        $commitment = BudgetCommitment::create($data);

        return redirect()
            ->route('budget-commitments.index')
            ->with('success', 'Budget commitment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BudgetCommitment $budgetCommitment)
    {
        $budgetCommitment->load([
            'fiscalYear',
            'program',
            'coaAccount',
            'committedBy',
        ]);

        return Inertia::render('budget-commitments/show', [
            'commitment' => $budgetCommitment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BudgetCommitment $budgetCommitment)
    {
        $budgetCommitment->load(['fiscalYear', 'program', 'coaAccount']);

        return Inertia::render('budget-commitments/edit', [
            'commitment' => $budgetCommitment,
            'programs' => Program::where(
                'fiscal_year_id',
                $budgetCommitment->fiscal_year_id,
            )->get(['id', 'program_code', 'program_name']),
            'coaAccounts' => CoaAccount::where(
                'fiscal_year_id',
                $budgetCommitment->fiscal_year_id,
            )
                ->where('is_active', true)
                ->get(['id', 'account_code', 'account_name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateBudgetCommitmentRequest $request,
        BudgetCommitment $budgetCommitment,
    ) {
        $budgetCommitment->update($request->validated());

        return redirect()
            ->route('budget-commitments.show', $budgetCommitment)
            ->with('success', 'Budget commitment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BudgetCommitment $budgetCommitment)
    {
        // Only allow deletion of pending commitments
        if ($budgetCommitment->status !== BudgetCommitment::STATUS_PENDING) {
            return redirect()
                ->back()
                ->with('error', 'Only pending commitments can be deleted.');
        }

        $budgetCommitment->delete();

        return redirect()
            ->route('budget-commitments.index')
            ->with('success', 'Budget commitment deleted successfully.');
    }
}
