<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseFiscalYearRequest;
use App\Http\Requests\StoreFiscalYearRequest;
use App\Http\Requests\UpdateFiscalYearRequest;
use App\Models\FiscalYear;
use App\Services\FiscalYearService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FiscalYearController extends Controller
{
    public function __construct(protected FiscalYearService $fiscalYearService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FiscalYear::query()
            ->withCount('programs');

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

        return redirect()->route('admin.fiscal-years.show', $fiscalYear)
            ->with('success', 'Fiscal year created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FiscalYear $fiscalYear)
    {
        $fiscalYear->load(['programs' => function ($query) {
            $query->select('id', 'fiscal_year', 'program_code', 'program_name', 'status');
        }]);

        return Inertia::render('admin/fiscal-years/show', [
            'fiscalYear' => $fiscalYear,
            'programCount' => $fiscalYear->programs_count ?? $fiscalYear->programs->count(),
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFiscalYearRequest $request, FiscalYear $fiscalYear)
    {
        $fiscalYear->update($request->validated());

        return redirect()->route('admin.fiscal-years.show', $fiscalYear)
            ->with('success', 'Fiscal year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FiscalYear $fiscalYear)
    {
        $programCount = $fiscalYear->programs()->count();

        if ($programCount > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete fiscal year {$fiscalYear->year}. It has {$programCount} associated programs.");
        }

        $fiscalYear->delete();

        return redirect()->route('admin.fiscal-years.index')
            ->with('success', 'Fiscal year deleted successfully.');
    }

    /**
     * Close the fiscal year.
     */
    public function close(CloseFiscalYearRequest $request, FiscalYear $fiscalYear)
    {
        $this->fiscalYearService->close($fiscalYear, $request->validated());

        return redirect()->route('admin.fiscal-years.show', $fiscalYear)
            ->with('success', "Fiscal year {$fiscalYear->year} has been closed successfully.");
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

        return redirect()->route('admin.fiscal-years.show', $fiscalYear)
            ->with('success', "Fiscal year {$fiscalYear->year} has been reopened.");
    }
}
