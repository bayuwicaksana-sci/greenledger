import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { Widget } from '../organisms/Widget';
import { MetricValue } from '../atoms/MetricValue';
import { TrendingUp, AlertCircle } from 'lucide-react';
import type { AvpDashboardData } from '@/types/dashboard';

interface AvpDashboardProps {
    data: AvpDashboardData;
}

export function AvpDashboard({ data }: AvpDashboardProps) {
    return (
        <div className="flex flex-col gap-6 p-6">
            <QuickStatsRow kpis={data.kpis} />

            <div className="grid gap-6 md:grid-cols-2">
                <Widget title="Executive Summary" icon={<TrendingUp className="size-5" />} fullWidth>
                    <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <div className="text-sm text-muted-foreground">Total Revenue</div>
                            <MetricValue value={data.executiveSummary.totalRevenue} format="currency" className="text-lg" />
                        </div>
                        <div>
                            <div className="text-sm text-muted-foreground">Total Expenses</div>
                            <MetricValue value={data.executiveSummary.totalExpenses} format="currency" className="text-lg" />
                        </div>
                        <div>
                            <div className="text-sm text-muted-foreground">Net Profit</div>
                            <MetricValue value={data.executiveSummary.netProfit} format="currency" className="text-lg text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <div className="text-sm text-muted-foreground">Profit Margin</div>
                            <span className="text-lg font-semibold">{data.executiveSummary.profitMargin}%</span>
                        </div>
                        <div>
                            <div className="text-sm text-muted-foreground">Active Programs</div>
                            <span className="text-lg font-semibold">{data.executiveSummary.activePrograms}</span>
                        </div>
                        <div>
                            <div className="text-sm text-muted-foreground">Avg Program ROI</div>
                            <span className="text-lg font-semibold text-green-600 dark:text-green-400">+{data.executiveSummary.avgProgramROI}%</span>
                        </div>
                    </div>
                </Widget>

                <Widget title="Strategic Priorities" icon={<AlertCircle className="size-5" />}>
                    <div className="space-y-4">
                        <div>
                            <div className="font-medium mb-2">Budget Approvals Required ({data.strategicPriorities.budgetApprovals.length})</div>
                            {data.strategicPriorities.budgetApprovals.map((approval) => (
                                <div key={approval.id} className="text-sm p-2 rounded bg-accent">
                                    {approval.reference}: <MetricValue value={approval.amount} format="currency" className="text-sm" />
                                </div>
                            ))}
                        </div>
                        <div>
                            <div className="font-medium mb-2">Loss-Making Programs</div>
                            <div className="text-sm text-muted-foreground">
                                {data.strategicPriorities.lossMakingPrograms.length} programs requiring intervention
                            </div>
                        </div>
                    </div>
                </Widget>

                <Widget title="Financial Forecast">
                    <div className="space-y-3">
                        <div className="flex justify-between">
                            <span className="text-sm">Revenue Projection</span>
                            <MetricValue value={data.financialForecast.revenueProjection} format="currency" className="text-sm" />
                        </div>
                        <div className="flex justify-between">
                            <span className="text-sm">Expense Projection</span>
                            <MetricValue value={data.financialForecast.expenseProjection} format="currency" className="text-sm" />
                        </div>
                        <div className="flex justify-between">
                            <span className="text-sm font-medium">Net Profit Projection</span>
                            <MetricValue value={data.financialForecast.netProfitProjection} format="currency" className="text-sm font-semibold text-green-600 dark:text-green-400" />
                        </div>
                        <div className="text-sm text-muted-foreground mt-2">
                            Confidence Level: {data.financialForecast.confidenceLevel}%
                        </div>
                    </div>
                </Widget>
            </div>
        </div>
    );
}
