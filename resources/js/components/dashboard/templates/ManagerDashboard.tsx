import { Building2, PieChart } from 'lucide-react';
import type { ManagerDashboardData } from '@/types/dashboard';
import { MetricValue } from '../atoms/MetricValue';
import { DataTable } from '../molecules/DataTable';
import { ApprovalQueueWidget } from '../organisms/ApprovalQueueWidget';
import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { RevenueChartWidget } from '../organisms/RevenueChartWidget';
import { Widget } from '../organisms/Widget';

interface ManagerDashboardProps {
    data: ManagerDashboardData;
}

export function ManagerDashboard({ data }: ManagerDashboardProps) {
    // Check if we have data for both sites (multi-site view) or just one (single-site view)
    const hasKlaten = data.siteMetrics?.klaten !== undefined;
    const hasMagelang = data.siteMetrics?.magelang !== undefined;
    const isMultiSiteView = hasKlaten && hasMagelang;

    // Get the single site data if viewing single site
    const singleSiteData = isMultiSiteView
        ? null
        : Object.values(data.siteMetrics || {})[0];
    const singleSiteName = singleSiteData?.site_name || 'Current Site';

    const siteComparisonColumns = isMultiSiteView
        ? [
              { key: 'metric', label: 'Metric', sortable: false },
              {
                  key: 'klaten',
                  label: 'Klaten',
                  sortable: true,
                  render: (val: any) => (
                      <MetricValue value={val} className="text-sm" />
                  ),
              },
              {
                  key: 'magelang',
                  label: 'Magelang',
                  sortable: true,
                  render: (val: any) => (
                      <MetricValue value={val} className="text-sm" />
                  ),
              },
          ]
        : [
              { key: 'metric', label: 'Metric', sortable: false },
              {
                  key: 'value',
                  label: singleSiteName,
                  sortable: false,
                  render: (val: any) => (
                      <MetricValue value={val} className="text-sm" />
                  ),
              },
          ];

    const siteComparisonData: any[] = isMultiSiteView
        ? [
              {
                  metric: 'Active Programs',
                  klaten: data.siteMetrics.klaten?.activePrograms || 0,
                  magelang: data.siteMetrics.magelang?.activePrograms || 0,
              },
              {
                  metric: 'Budget Utilized',
                  klaten: `${data.siteMetrics.klaten?.budgetUtilizationPercent || 0}%`,
                  magelang: `${data.siteMetrics.magelang?.budgetUtilizationPercent || 0}%`,
              },
              {
                  metric: 'Revenue (YTD)',
                  klaten: data.siteMetrics.klaten?.revenueYTD || 0,
                  magelang: data.siteMetrics.magelang?.revenueYTD || 0,
              },
              {
                  metric: 'Net Income',
                  klaten: data.siteMetrics.klaten?.netIncome || 0,
                  magelang: data.siteMetrics.magelang?.netIncome || 0,
              },
              {
                  metric: 'Avg ROI',
                  klaten: `${data.siteMetrics.klaten?.avgProgramROI || 0}%`,
                  magelang: `${data.siteMetrics.magelang?.avgProgramROI || 0}%`,
              },
          ]
        : [
              {
                  metric: 'Active Programs',
                  value: singleSiteData?.activePrograms || 0,
              },
              {
                  metric: 'Budget Utilized',
                  value: `${singleSiteData?.budgetUtilizationPercent || 0}%`,
              },
              {
                  metric: 'Revenue (YTD)',
                  value: singleSiteData?.revenueYTD || 0,
              },
              { metric: 'Net Income', value: singleSiteData?.netIncome || 0 },
              {
                  metric: 'Avg ROI',
                  value: `${singleSiteData?.avgProgramROI || 0}%`,
              },
          ];

    return (
        <div className="flex flex-col gap-6 p-6">
            <QuickStatsRow kpis={data.kpis} />

            <div className="grid gap-6 md:grid-cols-2">
                {/* Priority 1: Approval Queue */}
                <ApprovalQueueWidget approvals={data.approvalQueue} />

                {/* Site Performance Comparison */}
                <Widget
                    title={
                        isMultiSiteView
                            ? 'Site Performance Comparison'
                            : `Site Performance - ${singleSiteName}`
                    }
                    icon={<Building2 className="size-5" />}
                    fullWidth
                >
                    <DataTable
                        columns={siteComparisonColumns}
                        data={siteComparisonData}
                        sortable={false}
                    />
                </Widget>

                {/* Programs by Status */}
                <Widget
                    title="Programs by Status"
                    icon={<PieChart className="size-5" />}
                >
                    <div className="space-y-3">
                        <div className="flex items-center justify-between">
                            <span className="text-sm">Draft</span>
                            <span className="font-medium">
                                {data.programsByStatus.draft} programs
                            </span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="text-sm">Active</span>
                            <span className="font-medium text-green-600 dark:text-green-400">
                                {data.programsByStatus.active} programs
                            </span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="text-sm">Completed</span>
                            <span className="font-medium">
                                {data.programsByStatus.completed} programs
                            </span>
                        </div>
                    </div>
                </Widget>

                {/* Revenue Trends */}
                <RevenueChartWidget data={data.revenueTrends} />
            </div>
        </div>
    );
}
