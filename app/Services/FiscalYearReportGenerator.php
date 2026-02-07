<?php

namespace App\Services;

use App\Models\FiscalYear;
use App\Models\Program;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class FiscalYearReportGenerator
{
    /**
     * Generate year-end report for a fiscal year
     */
    public function generateYearEndReport(FiscalYear $fiscalYear): string
    {
        $data = $this->prepareReportData($fiscalYear);

        $pdf = Pdf::loadView('reports.fiscal-year-end', $data)->setPaper(
            'a4',
            'portrait',
        );

        $filename =
            "fiscal-year-{$fiscalYear->year}-report-".
            now()->format('Y-m-d-His').
            '.pdf';
        $path = "fiscal-year-reports/{$filename}";

        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Prepare data for the report
     */
    protected function prepareReportData(FiscalYear $fiscalYear): array
    {
        $programs = Program::where('fiscal_year', $fiscalYear->year)
            ->with(['site', 'createdBy'])
            ->get();

        $totalPrograms = $programs->count();
        $programsByStatus = $programs->groupBy('status')->map->count();
        $totalBudget = $programs->sum('total_budget');

        // Calculate budget by classification
        $budgetByClassification = $programs
            ->groupBy('classification')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'budget' => $group->sum('total_budget'),
                ];
            });

        return [
            'fiscal_year' => $fiscalYear,
            'generated_at' => now(),
            'total_programs' => $totalPrograms,
            'programs_by_status' => $programsByStatus,
            'total_budget' => $totalBudget,
            'budget_by_classification' => $budgetByClassification,
            'programs' => $programs,
        ];
    }

    /**
     * Get all generated reports for a fiscal year
     */
    public function getReportsForFiscalYear(FiscalYear $fiscalYear): array
    {
        $files = Storage::files('fiscal-year-reports');
        $pattern = "fiscal-year-{$fiscalYear->year}-report-";

        return collect($files)
            ->filter(fn ($file) => str_contains($file, $pattern))
            ->map(function ($file) {
                return [
                    'path' => $file,
                    'name' => basename($file),
                    'size' => Storage::size($file),
                    'created_at' => Storage::lastModified($file),
                    'url' => Storage::url($file),
                ];
            })
            ->sortByDesc('created_at')
            ->values()
            ->toArray();
    }
}
