<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseFiscalYearRequest;
use App\Http\Requests\StoreFiscalYearRequest;
use App\Http\Requests\UpdateFiscalYearRequest;
use App\Models\FiscalYear;
use App\Services\FiscalYearReportGenerator;
use App\Services\FiscalYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FiscalYearController extends Controller
{
    public function __construct(
        protected FiscalYearService $fiscalYearService,
        protected FiscalYearReportGenerator $reportGenerator,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FiscalYear::query()->withCount(['programs', 'coaAccounts']);

        if ($request->filled('search')) {
            $query->where('year', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $isClosed = $request->status === 'closed';
            $query->where('is_closed', $isClosed);
        }

        $fiscalYears = $query->orderByDesc('year')->get();

        return Inertia::render('admin/fiscal-years/index', [
            'fiscalYears' => $fiscalYears,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/fiscal-years/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFiscalYearRequest $request)
    {
        $fiscalYear = FiscalYear::create($request->validated());

        return redirect()
            ->route('admin.fiscal-years.show', $fiscalYear)
            ->with('success', 'Fiscal year created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FiscalYear $fiscalYear)
    {
        $fiscalYear->loadCount(['programs', 'coaAccounts']);
        $fiscalYear->load([
            'programs' => function ($query) {
                $query->select(
                    'id',
                    'fiscal_year_id',
                    'program_code',
                    'program_name',
                    'status',
                );
            },
        ]);

        $budgetMetrics = $this->fiscalYearService->calculateBudgetMetrics(
            $fiscalYear,
        );
        $reports = $this->reportGenerator->getReportsForFiscalYear($fiscalYear);

        return Inertia::render('admin/fiscal-years/show', [
            'fiscalYear' => $fiscalYear,
            'programCount' => $fiscalYear->programs_count ?? $fiscalYear->programs->count(),
            'coaAccountCount' => $fiscalYear->coa_accounts_count ?? 0,
            'budgetMetrics' => $budgetMetrics,
            'reports' => $reports,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FiscalYear $fiscalYear)
    {
        return Inertia::render('admin/fiscal-years/edit', [
            'fiscalYear' => $fiscalYear,
            'programCount' => $fiscalYear->programs()->count(),
            'coaAccountCount' => $fiscalYear->coaAccounts()->count(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateFiscalYearRequest $request,
        FiscalYear $fiscalYear,
    ) {
        $fiscalYear->update($request->validated());

        return redirect()
            ->route('admin.fiscal-years.show', $fiscalYear)
            ->with('success', 'Fiscal year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FiscalYear $fiscalYear)
    {
        $programCount = $fiscalYear->programs()->count();
        $coaAccountCount = $fiscalYear->coaAccounts()->count();

        if ($programCount > 0 || $coaAccountCount > 0) {
            $errors = [];
            if ($programCount > 0) {
                $errors[] = "{$programCount} associated programs";
            }
            if ($coaAccountCount > 0) {
                $errors[] = "{$coaAccountCount} COA accounts";
            }

            return redirect()
                ->back()
                ->with(
                    'error',
                    "Cannot delete fiscal year {$fiscalYear->year}. It has ".
                        implode(' and ', $errors).
                        '.',
                );
        }

        $fiscalYear->delete();

        return redirect()
            ->route('admin.fiscal-years.index')
            ->with('success', 'Fiscal year deleted successfully.');
    }

    /**
     * Close the fiscal year.
     */
    public function close(
        CloseFiscalYearRequest $request,
        FiscalYear $fiscalYear,
    ) {
        $this->fiscalYearService->close($fiscalYear, $request->validated());

        return redirect()
            ->route('admin.fiscal-years.show', $fiscalYear)
            ->with(
                'success',
                "Fiscal year {$fiscalYear->year} has been closed successfully.",
            );
    }

    /**
     * Reopen a closed fiscal year.
     */
    public function reopen(Request $request, FiscalYear $fiscalYear)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->fiscalYearService->reopen($fiscalYear, $request->reason);

        return redirect()
            ->route('admin.fiscal-years.show', $fiscalYear)
            ->with(
                'success',
                "Fiscal year {$fiscalYear->year} has been reopened.",
            );
    }

    /**
     * Download a year-end report for the fiscal year.
     */
    public function downloadReport(FiscalYear $fiscalYear)
    {
        $reportPath = $this->reportGenerator->generateYearEndReport(
            $fiscalYear,
        );

        return Storage::download($reportPath, basename($reportPath));
    }

    /**
     * Copy COA structure from another fiscal year.
     */
    public function copyCoa(Request $request, FiscalYear $fiscalYear)
    {
        $request->validate([
            'source_fiscal_year_id' => 'required|exists:fiscal_years,id',
        ]);

        $sourceFiscalYear = FiscalYear::findOrFail(
            $request->source_fiscal_year_id,
        );

        try {
            $this->fiscalYearService->copyCoaStructure(
                $sourceFiscalYear,
                $fiscalYear,
            );

            return redirect()
                ->route('admin.fiscal-years.show', $fiscalYear)
                ->with(
                    'success',
                    "COA structure copied from fiscal year {$sourceFiscalYear->year} successfully.",
                );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Failed to copy COA structure: '.$e->getMessage(),
                );
        }
    }

    /**
     * Get budget metrics for the fiscal year (API endpoint).
     */
    public function budgetMetrics(FiscalYear $fiscalYear)
    {
        $metrics = $this->fiscalYearService->calculateBudgetMetrics(
            $fiscalYear,
        );

        return response()->json($metrics);
    }
}
