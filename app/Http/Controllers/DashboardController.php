<?php

namespace App\Http\Controllers;

use App\Data\DashboardDummyData;
use App\Models\FiscalYear;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the role-based dashboard with site filtering.
     */
    public function index(Request $request, Site $site): Response
    {
        $user = $request->user();

        // Get user's primary role
        $role = $user->roles->first()?->name ?? 'Research Officer';

        // Check if user has permission to view all sites
        $canViewAllSites = $user->hasPermissionTo('dashboard.view.all');

        // Get requested scope from query param or session (default to 'current')
        $requestedScope = $request->input(
            'scope',
            $request->session()->get('dashboard_view_scope', 'current'),
        );

        // Validate scope parameter
        if (! in_array($requestedScope, ['current', 'all'])) {
            $requestedScope = 'current';
        }

        // SECURITY: Force scope to 'current' if user lacks permission
        $viewScope = $canViewAllSites ? $requestedScope : 'current';

        // Log suspicious activity if non-privileged user tries to access all sites
        if ($requestedScope === 'all' && ! $canViewAllSites) {
            Log::warning('Unauthorized attempt to access all sites dashboard', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role' => $role,
                'ip' => $request->ip(),
                'requested_scope' => $requestedScope,
            ]);
        }

        // Save validated scope to session
        $request->session()->put('dashboard_view_scope', $viewScope);

        // Get fiscal year from query param or session
        $selectedFiscalYear = $request->input(
            'fiscal_year',
            $request->session()->get('dashboard_fiscal_year'),
        );

        // If no fiscal year selected, default to current open fiscal year
        if (! $selectedFiscalYear) {
            $currentFiscalYear = FiscalYear::where('is_closed', false)
                ->orderByDesc('year')
                ->first();
            $selectedFiscalYear = $currentFiscalYear?->year;
        }

        // Save fiscal year to session
        if ($selectedFiscalYear) {
            $request
                ->session()
                ->put('dashboard_fiscal_year', $selectedFiscalYear);
        }

        // Get dashboard data based on VALIDATED scope, role, and fiscal year
        $dashboardData = $this->getDashboardData(
            $role,
            $viewScope,
            $site,
            $selectedFiscalYear,
        );

        Inertia::share('site_code', $site->site_code);

        return Inertia::render('dashboard', [
            'role' => $role,
            'dashboardData' => $dashboardData,
            'canViewAllSites' => $canViewAllSites,
            'currentViewScope' => $viewScope,
            'currentSite' => [
                'site_code' => $site->site_code,
                'site_name' => $site->site_name,
            ],
            'fiscal_years' => FiscalYear::orderBy('year', 'desc')->get(),
            'selected_fiscal_year' => $selectedFiscalYear,
        ]);
    }

    /**
     * Get dashboard data for the specified role, scope, site, and fiscal year.
     */
    private function getDashboardData(
        string $role,
        string $scope,
        Site $site,
        ?int $fiscalYear = null,
    ): array {
        // TODO: Update DashboardDummyData methods to accept fiscal year parameter
        // For now, pass fiscal year but dummy data doesn't use it yet
        return match ($role) {
            'Research Officer' => DashboardDummyData::getRoData($scope, $site),
            'Research Associate' => DashboardDummyData::getRaData(
                $scope,
                $site,
            ),
            'Manager' => DashboardDummyData::getManagerData($scope, $site),
            'AVP' => DashboardDummyData::getAvpData($scope, $site),
            'Finance Operation' => DashboardDummyData::getFinanceOpsData(
                $scope,
                $site,
            ),
            'Farm Admin' => DashboardDummyData::getFarmAdminData($scope, $site),
            default => DashboardDummyData::getRoData($scope, $site),
        };
    }
}
