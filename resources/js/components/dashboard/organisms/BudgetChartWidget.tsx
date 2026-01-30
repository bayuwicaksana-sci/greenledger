import { Bar, BarChart, XAxis, YAxis } from 'recharts';
import { Widget } from './Widget';
import {
    ChartContainer,
    ChartTooltip,
    ChartTooltipContent,
    type ChartConfig,
} from '@/components/ui/chart';
import type { ChartDataPoint } from '@/types/dashboard';
import { TrendingDown } from 'lucide-react';

interface BudgetChartWidgetProps {
    data: ChartDataPoint[];
    loading?: boolean;
    onRefresh?: () => void;
}

const chartConfig = {
    value: {
        label: 'Budget Utilization',
        color: 'hsl(var(--chart-1))',
    },
} satisfies ChartConfig;

export function BudgetChartWidget({
    data,
    loading,
    onRefresh,
}: BudgetChartWidgetProps) {
    // Transform data for the chart
    const chartData = data.map((item) => ({
        name: item.label,
        value: item.value,
        fill:
            item.value < 70
                ? 'hsl(142, 71%, 45%)' // green
                : item.value < 90
                  ? 'hsl(48, 96%, 53%)' // yellow
                  : 'hsl(0, 84%, 60%)', // red
    }));

    return (
        <Widget
            title="Budget Utilization by Program"
            icon={<TrendingDown className="size-5" />}
            loading={loading}
            onRefresh={onRefresh}
        >
            <ChartContainer config={chartConfig} className="h-[300px] w-full">
                <BarChart
                    accessibilityLayer
                    data={chartData}
                    layout="vertical"
                    margin={{
                        left: 0,
                    }}
                >
                    <XAxis type="number" dataKey="value" hide />
                    <YAxis
                        dataKey="name"
                        type="category"
                        tickLine={false}
                        tickMargin={10}
                        axisLine={false}
                        tickFormatter={(value) =>
                            value.length > 15
                                ? value.substring(0, 15) + '...'
                                : value
                        }
                        width={120}
                    />
                    <ChartTooltip
                        cursor={false}
                        content={
                            <ChartTooltipContent
                                hideLabel
                                formatter={(value) => `${value}%`}
                            />
                        }
                    />
                    <Bar dataKey="value" radius={5} />
                </BarChart>
            </ChartContainer>
            <div className="mt-4 text-sm text-muted-foreground">
                <p>
                    <span className="text-green-600 dark:text-green-400">
                        Green
                    </span>
                    : &lt;70% |{' '}
                    <span className="text-yellow-600 dark:text-yellow-400">
                        Yellow
                    </span>
                    : 70-90% |{' '}
                    <span className="text-red-600 dark:text-red-400">Red</span>
                    : &gt;90%
                </p>
            </div>
        </Widget>
    );
}
