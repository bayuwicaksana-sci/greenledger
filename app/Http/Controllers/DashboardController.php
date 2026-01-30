<?php

namespace App\Http\Controllers;

use App\Data\DashboardDummyData;
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
        $requestedScope = $request->input('scope', $request->session()->get('dashboard_view_scope', 'current'));

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

        // Get dashboard data based on VALIDATED scope and role
        $dashboardData = $this->getDashboardData($role, $viewScope, $site);

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
        ]);
    }

    /**
     * Get dashboard data for the specified role, scope, and site.
     */
    private function getDashboardData(string $role, string $scope, Site $site): array
    {
        return match ($role) {
            'Research Officer' => DashboardDummyData::getRoData($scope, $site),
            'Research Associate' => DashboardDummyData::getRaData($scope, $site),
            'Manager' => DashboardDummyData::getManagerData($scope, $site),
            'AVP' => DashboardDummyData::getAvpData($scope, $site),
            'Finance Operation' => DashboardDummyData::getFinanceOpsData($scope, $site),
            'Farm Admin' => DashboardDummyData::getFarmAdminData($scope, $site),
            default => DashboardDummyData::getRoData($scope, $site),
        };
    }
}
