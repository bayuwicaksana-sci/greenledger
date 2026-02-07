<?php

namespace App\Http\Controllers;

use App\Models\FiscalYear;
use App\Models\Program;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HistoricalReportController extends Controller
{
    /**
     * Display historical year-over-year comparison report.
     */
    public function index(Request $request, Site $site)
    {
        // Get selected years from query params (default to last 3 years)
        $currentYear = (int) date('Y');
        $selectedYears = $request->input('years', [
            $currentYear - 2,
            $currentYear - 1,
            $currentYear,
        ]);

        // Ensure we have valid fiscal years
        $fiscalYears = FiscalYear::whereIn('year', $selectedYears)
            ->orderBy('year', 'desc')
            ->get();

        // Get all fiscal years for selector
        $allFiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        // Get year-over-year summary data
        $summaryData = $this->getYearOverYearSummary($selectedYears);

        // Get program counts by year
        $programCounts = $this->getProgramCountsByYear($selectedYears);

        // Get revenue and expense breakdown by year
        $revenueExpenseData = $this->getRevenueExpenseByYear($selectedYears);

        // Get budget utilization by year
        $budgetUtilization = $this->getBudgetUtilizationByYear($selectedYears);

        Inertia::share('site_code', $site->site_code);

        return Inertia::render('reports/historical', [
            'fiscal_years' => $allFiscalYears,
            'selected_years' => $selectedYears,
            'summary_data' => $summaryData,
            'program_counts' => $programCounts,
            'revenue_expense_data' => $revenueExpenseData,
            'budget_utilization' => $budgetUtilization,
        ]);
    }

    /**
     * Get year-over-year summary statistics.
     */
    private function getYearOverYearSummary(array $years): array
    {
        $data = [];

        foreach ($years as $year) {
            // Get total programs
            $totalPrograms = Program::where('fiscal_year', $year)->count();

            // Get total revenue (harvest + testing services)
            $totalRevenue =
                DB::table('revenue_harvest')
                    ->join(
                        'programs',
                        'revenue_harvest.program_id',
                        '=',
                        'programs.id',
                    )
                    ->where('programs.fiscal_year', $year)
                    ->sum('revenue_harvest.total_revenue') +
                DB::table('revenue_testing_services')
                    ->join(
                        'programs',
                        'revenue_testing_services.program_id',
                        '=',
                        'programs.id',
                    )
                    ->where('programs.fiscal_year', $year)
                    ->sum('revenue_testing_services.contract_value');

            // Get total expenses
            $totalExpenses = DB::table('payment_request_splits')
                ->join(
                    'programs',
                    'payment_request_splits.program_id',
                    '=',
                    'programs.id',
                )
                ->where('programs.fiscal_year', $year)
                ->sum('payment_request_splits.split_amount');

            // Calculate net income
            $netIncome = $totalRevenue - $totalExpenses;

            $data[] = [
                'year' => $year,
                'total_programs' => $totalPrograms,
                'total_revenue' => round($totalRevenue, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_income' => round($netIncome, 2),
            ];
        }

        return $data;
    }

    /**
     * Get program counts by status for each year.
     */
    private function getProgramCountsByYear(array $years): array
    {
        $data = [];

        foreach ($years as $year) {
            $counts = Program::where('fiscal_year', $year)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $data[] = [
                'year' => $year,
                'draft' => $counts[Program::STATUS_DRAFT] ?? 0,
                'active' => $counts[Program::STATUS_ACTIVE] ?? 0,
                'completed' => $counts[Program::STATUS_COMPLETED] ?? 0,
                'archived' => $counts[Program::STATUS_ARCHIVED] ?? 0,
            ];
        }

        return $data;
    }

    /**
     * Get revenue and expense breakdown by account type for each year.
     */
    private function getRevenueExpenseByYear(array $years): array
    {
        $data = [];

        foreach ($years as $year) {
            // Revenue by account
            $revenueByAccount = DB::table('revenue_harvest')
                ->join(
                    'programs',
                    'revenue_harvest.program_id',
                    '=',
                    'programs.id',
                )
                ->join(
                    'coa_accounts',
                    'revenue_harvest.coa_account_id',
                    '=',
                    'coa_accounts.id',
                )
                ->where('programs.fiscal_year', $year)
                ->select(
                    'coa_accounts.account_name',
                    DB::raw('SUM(revenue_harvest.total_revenue) as total'),
                )
                ->groupBy('coa_accounts.id', 'coa_accounts.account_name')
                ->get()
                ->toArray();

            // Expenses by account
            $expensesByAccount = DB::table('payment_request_splits')
                ->join(
                    'programs',
                    'payment_request_splits.program_id',
                    '=',
                    'programs.id',
                )
                ->join(
                    'coa_accounts',
                    'payment_request_splits.coa_account_id',
                    '=',
                    'coa_accounts.id',
                )
                ->where('programs.fiscal_year', $year)
                ->select(
                    'coa_accounts.account_name',
                    DB::raw(
                        'SUM(payment_request_splits.split_amount) as total',
                    ),
                )
                ->groupBy('coa_accounts.id', 'coa_accounts.account_name')
                ->get()
                ->toArray();

            $data[] = [
                'year' => $year,
                'revenue_by_account' => $revenueByAccount,
                'expenses_by_account' => $expensesByAccount,
            ];
        }

        return $data;
    }

    /**
     * Get budget utilization statistics by year.
     */
    private function getBudgetUtilizationByYear(array $years): array
    {
        $data = [];

        foreach ($years as $year) {
            $fiscalYear = FiscalYear::where('year', $year)->first();

            if (! $fiscalYear) {
                continue;
            }

            // Get total budget allocated
            $totalBudget = DB::table('coa_budget_allocations')
                ->where('fiscal_year_id', $fiscalYear->id)
                ->sum('budget_amount');

            // Get total actual spending
            $totalActual = DB::table('payment_request_splits')
                ->join(
                    'programs',
                    'payment_request_splits.program_id',
                    '=',
                    'programs.id',
                )
                ->where('programs.fiscal_year', $year)
                ->sum('payment_request_splits.split_amount');

            $utilizationPercent =
                $totalBudget > 0
                    ? round(($totalActual / $totalBudget) * 100, 2)
                    : 0;

            $data[] = [
                'year' => $year,
                'total_budget' => round($totalBudget, 2),
                'total_actual' => round($totalActual, 2),
                'utilization_percent' => $utilizationPercent,
            ];
        }

        return $data;
    }

    /**
     * Export historical report to Excel.
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export using Laravel Excel package
        // This will be implemented in a future phase

        return response()->json(
            [
                'message' => 'Excel export feature coming soon',
            ],
            501,
        );
    }
}
