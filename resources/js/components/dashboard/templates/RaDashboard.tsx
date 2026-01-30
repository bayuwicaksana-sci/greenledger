import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { ProgramsWidget } from '../organisms/ProgramsWidget';
import { RevenueChartWidget } from '../organisms/RevenueChartWidget';
import { Widget } from '../organisms/Widget';
import { MetricValue } from '../atoms/MetricValue';
import { Users } from 'lucide-react';
import type { RaDashboardData } from '@/types/dashboard';

interface RaDashboardProps {
    data: RaDashboardData;
}

export function RaDashboard({ data }: RaDashboardProps) {
    return (
        <div className="flex flex-col gap-6 p-6">
            <QuickStatsRow kpis={data.kpis} />

            <div className="grid gap-6 md:grid-cols-2">
                <ProgramsWidget programs={data.programs} title="Program Performance" />

                <Widget title="Team Activity & Settlements" icon={<Users className="size-5" />}>
                    <div className="space-y-3">
                        {data.teamMembers.map((member) => (
                            <div key={member.id} className="p-3 rounded-lg border border-border space-y-2">
                                <div className="flex justify-between items-start">
                                    <div>
                                        <div className="font-medium">{member.name} ({member.role})</div>
                                        <div className="text-sm text-muted-foreground">{member.program}</div>
                                    </div>
                                    <span className={`text-xs px-2 py-1 rounded ${
                                        member.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' :
                                        member.status === 'warning' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' :
                                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    }`}>
                                        {member.status}
                                    </span>
                                </div>
                                <div className="text-sm">
                                    {member.pendingSettlements} pending settlements
                                    {member.overdueSettlements > 0 && ` (${member.overdueSettlements} overdue)`}
                                </div>
                                <div className="text-xs text-muted-foreground">Last activity: {member.lastActivity}</div>
                            </div>
                        ))}
                    </div>
                </Widget>

                <Widget title="Revenue Breakdown (This Month)" fullWidth>
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <div className="flex justify-between">
                                <span className="font-medium">Harvest Revenue</span>
                                <MetricValue value={data.revenueBreakdown.harvest.amount} format="currency" className="text-sm" />
                            </div>
                            <div className="text-sm text-muted-foreground">
                                {data.revenueBreakdown.harvest.items.length} harvests recorded
                            </div>
                        </div>
                        <div className="space-y-2">
                            <div className="flex justify-between">
                                <span className="font-medium">Testing Services</span>
                                <MetricValue value={data.revenueBreakdown.testing.amount} format="currency" className="text-sm" />
                            </div>
                            <div className="text-sm text-muted-foreground">
                                {data.revenueBreakdown.testing.items.length} services completed
                            </div>
                        </div>
                    </div>
                </Widget>
            </div>
        </div>
    );
}
