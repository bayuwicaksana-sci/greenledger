import { CartesianGrid, Line, LineChart, XAxis } from 'recharts';
import { Widget } from './Widget';
import {
    ChartContainer,
    ChartTooltip,
    ChartTooltipContent,
    type ChartConfig,
} from '@/components/ui/chart';
import type { TimeSeriesData } from '@/types/dashboard';
import { TrendingUp } from 'lucide-react';

interface RevenueChartWidgetProps {
    data: TimeSeriesData[];
    loading?: boolean;
    onRefresh?: () => void;
}

const chartConfig = {
    value: {
        label: 'Revenue',
        color: 'hsl(var(--chart-1))',
    },
} satisfies ChartConfig;

export function RevenueChartWidget({
    data,
    loading,
    onRefresh,
}: RevenueChartWidgetProps) {
    // Format data for the chart
    const chartData = data.map((item) => ({
        month: item.date,
        revenue: item.value,
    }));

    // Calculate trend
    const firstValue = data[0]?.value || 0;
    const lastValue = data[data.length - 1]?.value || 0;
    const trend =
        firstValue > 0
            ? (((lastValue - firstValue) / firstValue) * 100).toFixed(1)
            : 0;

    return (
        <Widget
            title="Revenue Trends (Last 6 Months)"
            icon={<TrendingUp className="size-5" />}
            loading={loading}
            onRefresh={onRefresh}
        >
            <ChartContainer config={chartConfig} className="h-[300px] w-full">
                <LineChart
                    accessibilityLayer
                    data={chartData}
                    margin={{
                        left: 12,
                        right: 12,
                    }}
                >
                    <CartesianGrid vertical={false} />
                    <XAxis
                        dataKey="month"
                        tickLine={false}
                        axisLine={false}
                        tickMargin={8}
                        tickFormatter={(value) =>
                            value.substring(0, 7)
                        } // Show YYYY-MM
                    />
                    <ChartTooltip
                        cursor={false}
                        content={
                            <ChartTooltipContent
                                hideLabel
                                formatter={(value) =>
                                    new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0,
                                    }).format(value as number)
                                }
                            />
                        }
                    />
                    <Line
                        dataKey="revenue"
                        type="natural"
                        stroke="var(--color-value)"
                        strokeWidth={2}
                        dot={{ r: 4 }}
                    />
                </LineChart>
            </ChartContainer>
            <div className="mt-4 flex items-center gap-2 text-sm">
                <TrendingUp className="size-4 text-green-600 dark:text-green-400" />
                <span className="font-medium">
                    {Number(trend) >= 0 ? '+' : ''}
                    {trend}% from start of period
                </span>
            </div>
        </Widget>
    );
}
