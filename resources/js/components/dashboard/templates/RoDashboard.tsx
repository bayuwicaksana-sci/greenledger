import type { RoDashboardData } from '@/types/dashboard';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';
import { Calendar } from 'lucide-react';
import { BudgetChartWidget } from '../organisms/BudgetChartWidget';
import { ProgramsWidget } from '../organisms/ProgramsWidget';
import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { RecentActivityWidget } from '../organisms/RecentActivityWidget';
import { SettlementAlertsWidget } from '../organisms/SettlementAlertsWidget';
import { Widget } from '../organisms/Widget';

interface RoDashboardProps {
    data: RoDashboardData;
}

export function RoDashboard({ data }: RoDashboardProps) {
    return (
        <div className="flex flex-col gap-6 p-6">
            {/* Quick Stats Row */}
            <QuickStatsRow kpis={data.kpis} />

            {/* Main Content Grid */}
            <div className="grid gap-6 md:grid-cols-2">
                {/* Priority 1: Settlement Alerts - Full Width */}
                <SettlementAlertsWidget settlements={data.settlementAlerts} />

                {/* Priority 2: My Programs Summary */}
                <ProgramsWidget programs={data.programs} title="My Programs" />

                {/* Priority 2: Recent Transactions */}
                <RecentActivityWidget activities={data.recentActivity} />

                {/* Priority 2: Budget Utilization Chart */}
                <BudgetChartWidget data={data.budgetUtilization} />

                {/* Priority 3: Upcoming Harvest Schedule */}
                <Widget
                    title="Upcoming Harvests"
                    subtitle="Next 3 harvests within 30 days"
                    icon={<Calendar className="size-5" />}
                >
                    <div className="space-y-3">
                        {data.upcomingHarvests.map((harvest) => (
                            <div
                                key={harvest.id}
                                className="flex items-center justify-between rounded-lg border border-border p-3 transition-colors hover:bg-accent"
                            >
                                <div>
                                    <div className="font-medium">
                                        {harvest.program_name}
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        {harvest.crop_type} • Expected{' '}
                                        {harvest.expected_quantity}kg
                                    </div>
                                </div>
                                <div className="text-sm font-medium text-muted-foreground">
                                    {format(new Date(harvest.date), 'MMM dd', {
                                        locale: id,
                                    })}
                                </div>
                            </div>
                        ))}
                    </div>
                </Widget>
            </div>
        </div>
    );
}
