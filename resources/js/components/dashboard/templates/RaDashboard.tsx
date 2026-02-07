import { Users } from 'lucide-react';
import type { RaDashboardData } from '@/types/dashboard';
import { MetricValue } from '../atoms/MetricValue';
import { ProgramsWidget } from '../organisms/ProgramsWidget';
import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { Widget } from '../organisms/Widget';

interface RaDashboardProps {
    data: RaDashboardData;
}

export function RaDashboard({ data }: RaDashboardProps) {
    return (
        <div className="flex flex-col gap-6 p-6">
            <QuickStatsRow kpis={data.kpis} />

            <div className="grid gap-6 md:grid-cols-2">
                <ProgramsWidget
                    programs={data.programs}
                    title="Program Performance"
                />

                <Widget
                    title="Team Activity & Settlements"
                    icon={<Users className="size-5" />}
                >
                    <div className="space-y-3">
                        {data.teamMembers.map((member) => (
                            <div
                                key={member.id}
                                className="space-y-2 rounded-lg border border-border p-3"
                            >
                                <div className="flex items-start justify-between">
                                    <div>
                                        <div className="font-medium">
                                            {member.name} ({member.role})
                                        </div>
                                        <div className="text-sm text-muted-foreground">
                                            {member.program}
                                        </div>
                                    </div>
                                    <span
                                        className={`rounded px-2 py-1 text-xs ${
                                            member.status === 'active'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                                : member.status === 'warning'
                                                  ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'
                                                  : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                        }`}
                                    >
                                        {member.status}
                                    </span>
                                </div>
                                <div className="text-sm">
                                    {member.pendingSettlements} pending
                                    settlements
                                    {member.overdueSettlements > 0 &&
                                        ` (${member.overdueSettlements} overdue)`}
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    Last activity: {member.lastActivity}
                                </div>
                            </div>
                        ))}
                    </div>
                </Widget>

                <Widget title="Revenue Breakdown (This Month)" fullWidth>
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <div className="flex justify-between">
                                <span className="font-medium">
                                    Harvest Revenue
                                </span>
                                <MetricValue
                                    value={data.revenueBreakdown.harvest.amount}
                                    format="currency"
                                    className="text-sm"
                                />
                            </div>
                            <div className="text-sm text-muted-foreground">
                                {data.revenueBreakdown.harvest.items.length}{' '}
                                harvests recorded
                            </div>
                        </div>
                        <div className="space-y-2">
                            <div className="flex justify-between">
                                <span className="font-medium">
                                    Testing Services
                                </span>
                                <MetricValue
                                    value={data.revenueBreakdown.testing.amount}
                                    format="currency"
                                    className="text-sm"
                                />
                            </div>
                            <div className="text-sm text-muted-foreground">
                                {data.revenueBreakdown.testing.items.length}{' '}
                                services completed
                            </div>
                        </div>
                    </div>
                </Widget>
            </div>
        </div>
    );
}
