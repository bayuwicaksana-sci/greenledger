import { AvpDashboard } from '@/components/dashboard/templates/AvpDashboard';
import { FarmAdminDashboard } from '@/components/dashboard/templates/FarmAdminDashboard';
import { FinanceOpsDashboard } from '@/components/dashboard/templates/FinanceOpsDashboard';
import { ManagerDashboard } from '@/components/dashboard/templates/ManagerDashboard';
import { RaDashboard } from '@/components/dashboard/templates/RaDashboard';
import { RoDashboard } from '@/components/dashboard/templates/RoDashboard';
import { FiscalYearSelector } from '@/components/FiscalYearSelector';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, FiscalYear, SharedData } from '@/types';
import type { DashboardPageProps } from '@/types/dashboard';
import { Head, router, usePage } from '@inertiajs/react';
import { Building2, Filter } from 'lucide-react';

export default function Dashboard({
    role,
    dashboardData,
    canViewAllSites = false,
    currentViewScope = 'current',
    currentSite,
    fiscal_years,
    selected_fiscal_year,
}: DashboardPageProps & {
    fiscal_years: FiscalYear[];
    selected_fiscal_year?: number;
}) {
    const { site_code } = usePage<SharedData>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard({ site_code: site_code! }).url,
        },
    ];

    // Handle scope change (filter toggle)
    const handleScopeChange = (newScope: 'current' | 'all') => {
        if (newScope === currentViewScope) return;

        router.get(
            dashboard({ site_code: site_code! }).url,
            { scope: newScope, fiscal_year: selected_fiscal_year },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    // Handle fiscal year change
    const handleFiscalYearChange = (year: number) => {
        router.get(
            dashboard({ site_code: site_code! }).url,
            { scope: currentViewScope, fiscal_year: year },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    // Role-based dashboard routing
    const renderDashboard = () => {
        switch (role) {
            case 'Research Officer':
                return <RoDashboard data={dashboardData as any} />;
            case 'Research Associate':
                return <RaDashboard data={dashboardData as any} />;
            case 'Manager':
                return <ManagerDashboard data={dashboardData as any} />;
            case 'AVP':
                return <AvpDashboard data={dashboardData as any} />;
            case 'Finance Operation':
                return <FinanceOpsDashboard data={dashboardData as any} />;
            case 'Farm Admin':
                return <FarmAdminDashboard data={dashboardData as any} />;
            default:
                // Fallback to RO dashboard
                return <RoDashboard data={dashboardData as any} />;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            {/* Fiscal Year Selector */}
            <div className="px-6 pt-6">
                <Card>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <Filter className="h-4 w-4 text-muted-foreground" />
                                <span className="font-medium">
                                    Fiscal Year:
                                </span>
                            </div>
                            <FiscalYearSelector
                                fiscalYears={fiscal_years}
                                selectedYear={selected_fiscal_year}
                                onChange={handleFiscalYearChange}
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Site Filter Toggle - Only show for privileged users (Manager, AVP) */}
            {canViewAllSites && (
                <div className="px-6 pt-4">
                    <Card>
                        <CardContent>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Filter className="h-4 w-4 text-muted-foreground" />
                                    <span className="font-medium">
                                        Data Scope:
                                    </span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Button
                                        variant={
                                            currentViewScope === 'current'
                                                ? 'default'
                                                : 'outline'
                                        }
                                        size="sm"
                                        onClick={() =>
                                            handleScopeChange('current')
                                        }
                                        className="gap-2"
                                    >
                                        <Building2 className="h-4 w-4" />
                                        Current Site (
                                        {currentSite?.site_name || 'N/A'})
                                    </Button>
                                    <Button
                                        variant={
                                            currentViewScope === 'all'
                                                ? 'default'
                                                : 'outline'
                                        }
                                        size="sm"
                                        onClick={() => handleScopeChange('all')}
                                        className="gap-2"
                                    >
                                        <Building2 className="h-4 w-4" />
                                        All Sites
                                    </Button>
                                </div>
                            </div>

                            {/* Optional: Show current scope description */}
                            <p className="mt-3 text-sm text-muted-foreground">
                                {currentViewScope === 'current' ? (
                                    <>
                                        Viewing data for{' '}
                                        <span className="font-medium">
                                            {currentSite?.site_name}
                                        </span>{' '}
                                        only. Switch to "All Sites" to see
                                        consolidated data across all research
                                        stations.
                                    </>
                                ) : (
                                    <>
                                        Viewing consolidated data across{' '}
                                        <span className="font-medium">
                                            all research stations
                                        </span>
                                        . Switch to "Current Site" to see{' '}
                                        {currentSite?.site_name}-specific data.
                                    </>
                                )}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            )}

            {renderDashboard()}
        </AppLayout>
    );
}
