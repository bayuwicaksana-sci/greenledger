import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import reports from '@/routes/reports';
import type { BreadcrumbItem, FiscalYear, SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import {
    ArrowDown,
    ArrowUp,
    Download,
    TrendingDown,
    TrendingUp,
} from 'lucide-react';
import { useMemo, useState } from 'react';

interface YearSummary {
    year: number;
    total_programs: number;
    total_revenue: number;
    total_expenses: number;
    net_income: number;
}

interface ProgramCounts {
    year: number;
    draft: number;
    active: number;
    completed: number;
    archived: number;
}

interface AccountBreakdown {
    account_name: string;
    total: number;
}

interface RevenueExpenseData {
    year: number;
    revenue_by_account: AccountBreakdown[];
    expenses_by_account: AccountBreakdown[];
}

interface BudgetUtilization {
    year: number;
    total_budget: number;
    total_actual: number;
    utilization_percent: number;
}

interface Props {
    fiscal_years: FiscalYear[];
    selected_years: number[];
    summary_data: YearSummary[];
    program_counts: ProgramCounts[];
    revenue_expense_data: RevenueExpenseData[];
    budget_utilization: BudgetUtilization[];
}

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
    }).format(value);
}

function formatNumber(value: number): string {
    return new Intl.NumberFormat('en-US').format(value);
}

function calculatePercentChange(current: number, previous: number): number {
    if (previous === 0) return 0;
    return ((current - previous) / previous) * 100;
}

function PercentageChange({
    current,
    previous,
}: {
    current: number;
    previous: number;
}) {
    const change = calculatePercentChange(current, previous);
    const isPositive = change > 0;
    const isNegative = change < 0;

    if (change === 0) {
        return <span className="text-muted-foreground">—</span>;
    }

    return (
        <div className="flex items-center gap-1">
            {isPositive && <ArrowUp className="h-4 w-4 text-green-600" />}
            {isNegative && <ArrowDown className="h-4 w-4 text-red-600" />}
            <span
                className={
                    isPositive
                        ? 'text-green-600'
                        : isNegative
                          ? 'text-red-600'
                          : 'text-muted-foreground'
                }
            >
                {Math.abs(change).toFixed(1)}%
            </span>
        </div>
    );
}

export default function HistoricalReport({
    fiscal_years,
    selected_years,
    summary_data,
    program_counts,
    revenue_expense_data,
    budget_utilization,
}: Props) {
    const { site_code } = usePage<SharedData>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Reports',
            href: '#',
        },
        {
            title: 'Historical Report',
            href: reports.historical({ site_code: site_code! }).url,
        },
    ];

    const [selectedYears, setSelectedYears] = useState<number[]>(
        selected_years.length > 0
            ? selected_years
            : fiscal_years.slice(0, 3).map((fy) => fy.year),
    );

    const handleYearChange = (index: number, year: string) => {
        const newYears = [...selectedYears];
        newYears[index] = parseInt(year);
        setSelectedYears(newYears);

        // Reload with new years
        router.get(
            reports.historical({ site_code: site_code! }).url,
            { years: newYears },
            { preserveScroll: true },
        );
    };

    const handleExport = () => {
        const baseUrl = reports.historical({ site_code: site_code! }).url;
        window.location.href = `${baseUrl}/export?years=${selectedYears.join(',')}`;
    };

    // Calculate year-over-year changes
    const summaryWithChanges = useMemo(() => {
        return summary_data.map((data, index) => {
            const previousData = summary_data[index + 1];
            return {
                ...data,
                revenue_change: previousData
                    ? calculatePercentChange(
                          data.total_revenue,
                          previousData.total_revenue,
                      )
                    : null,
                expense_change: previousData
                    ? calculatePercentChange(
                          data.total_expenses,
                          previousData.total_expenses,
                      )
                    : null,
                net_income_change: previousData
                    ? calculatePercentChange(
                          data.net_income,
                          previousData.net_income,
                      )
                    : null,
            };
        });
    }, [summary_data]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Historical Report" />
            <PageLayout>
                <PageHeader
                    pageTitle="Historical Report"
                    pageSubtitle="Year-over-year financial comparison and analysis"
                >
                    <Button onClick={handleExport} variant="outline">
                        <Download className="mr-2 h-4 w-4" />
                        Export to Excel
                    </Button>
                </PageHeader>

                {/* Fiscal Year Selector */}
                <Card>
                    <CardHeader>
                        <CardTitle>Select Fiscal Years to Compare</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="flex gap-4">
                            {[0, 1, 2].map((index) => (
                                <Select
                                    key={index}
                                    value={selectedYears[index]?.toString()}
                                    onValueChange={(value) =>
                                        handleYearChange(index, value)
                                    }
                                >
                                    <SelectTrigger className="w-[180px]">
                                        <SelectValue
                                            placeholder={`Year ${index + 1}`}
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {fiscal_years.map((fy) => (
                                            <SelectItem
                                                key={fy.id}
                                                value={fy.year.toString()}
                                            >
                                                FY {fy.year}
                                                {fy.is_closed && ' (Closed)'}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                {/* Summary Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {summaryWithChanges.map((data, index) => {
                        const previousData = summaryWithChanges[index + 1];
                        return (
                            <Card key={data.year}>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-sm font-medium">
                                        FY {data.year}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <div>
                                            <p className="text-xs text-muted-foreground">
                                                Net Income
                                            </p>
                                            <p
                                                className={`text-2xl font-bold ${
                                                    data.net_income >= 0
                                                        ? 'text-green-600'
                                                        : 'text-red-600'
                                                }`}
                                            >
                                                {formatCurrency(
                                                    data.net_income,
                                                )}
                                            </p>
                                            {previousData && (
                                                <PercentageChange
                                                    current={data.net_income}
                                                    previous={
                                                        previousData.net_income
                                                    }
                                                />
                                            )}
                                        </div>
                                        <div className="pt-2 text-xs text-muted-foreground">
                                            <div className="flex justify-between">
                                                <span>Revenue:</span>
                                                <span className="font-medium text-green-600">
                                                    {formatCurrency(
                                                        data.total_revenue,
                                                    )}
                                                </span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span>Expenses:</span>
                                                <span className="font-medium text-red-600">
                                                    {formatCurrency(
                                                        data.total_expenses,
                                                    )}
                                                </span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span>Programs:</span>
                                                <span className="font-medium">
                                                    {formatNumber(
                                                        data.total_programs,
                                                    )}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        );
                    })}
                </div>

                {/* Year-over-Year Summary Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Year-over-Year Summary</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Fiscal Year</TableHead>
                                    <TableHead className="text-right">
                                        Programs
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Revenue
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Expenses
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Net Income
                                    </TableHead>
                                    <TableHead className="text-right">
                                        YoY Change
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {summaryWithChanges.map((data, index) => {
                                    const previousData =
                                        summaryWithChanges[index + 1];
                                    return (
                                        <TableRow key={data.year}>
                                            <TableCell className="font-medium">
                                                FY {data.year}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {formatNumber(
                                                    data.total_programs,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right text-green-600">
                                                {formatCurrency(
                                                    data.total_revenue,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right text-red-600">
                                                {formatCurrency(
                                                    data.total_expenses,
                                                )}
                                            </TableCell>
                                            <TableCell
                                                className={`text-right font-medium ${
                                                    data.net_income >= 0
                                                        ? 'text-green-600'
                                                        : 'text-red-600'
                                                }`}
                                            >
                                                {formatCurrency(
                                                    data.net_income,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {previousData ? (
                                                    <PercentageChange
                                                        current={
                                                            data.net_income
                                                        }
                                                        previous={
                                                            previousData.net_income
                                                        }
                                                    />
                                                ) : (
                                                    <span className="text-muted-foreground">
                                                        —
                                                    </span>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Program Status Breakdown */}
                <Card>
                    <CardHeader>
                        <CardTitle>Program Status Breakdown</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Fiscal Year</TableHead>
                                    <TableHead className="text-right">
                                        Draft
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Active
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Completed
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Archived
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Total
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {program_counts.map((data) => {
                                    const total =
                                        data.draft +
                                        data.active +
                                        data.completed +
                                        data.archived;
                                    return (
                                        <TableRow key={data.year}>
                                            <TableCell className="font-medium">
                                                FY {data.year}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Badge variant="secondary">
                                                    {data.draft}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Badge variant="default">
                                                    {data.active}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Badge variant="outline">
                                                    {data.completed}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Badge variant="secondary">
                                                    {data.archived}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right font-medium">
                                                {total}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Budget Utilization */}
                <Card>
                    <CardHeader>
                        <CardTitle>Budget Utilization</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Fiscal Year</TableHead>
                                    <TableHead className="text-right">
                                        Total Budget
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Actual Spending
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Utilization
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Status
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {budget_utilization.map((data) => {
                                    const isOverBudget =
                                        data.utilization_percent > 100;
                                    const isNearLimit =
                                        data.utilization_percent > 90 &&
                                        data.utilization_percent <= 100;

                                    return (
                                        <TableRow key={data.year}>
                                            <TableCell className="font-medium">
                                                FY {data.year}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {formatCurrency(
                                                    data.total_budget,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {formatCurrency(
                                                    data.total_actual,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <div className="h-2 w-24 overflow-hidden rounded-full bg-muted">
                                                        <div
                                                            className={`h-full ${
                                                                isOverBudget
                                                                    ? 'bg-red-500'
                                                                    : isNearLimit
                                                                      ? 'bg-amber-500'
                                                                      : 'bg-green-500'
                                                            }`}
                                                            style={{
                                                                width: `${Math.min(data.utilization_percent, 100)}%`,
                                                            }}
                                                        />
                                                    </div>
                                                    <span className="text-sm font-medium">
                                                        {data.utilization_percent.toFixed(
                                                            1,
                                                        )}
                                                        %
                                                    </span>
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {isOverBudget ? (
                                                    <Badge
                                                        variant="destructive"
                                                        className="gap-1"
                                                    >
                                                        <TrendingUp className="h-3 w-3" />
                                                        Over Budget
                                                    </Badge>
                                                ) : isNearLimit ? (
                                                    <Badge
                                                        variant="outline"
                                                        className="gap-1 border-amber-500 text-amber-600"
                                                    >
                                                        <TrendingUp className="h-3 w-3" />
                                                        Near Limit
                                                    </Badge>
                                                ) : (
                                                    <Badge
                                                        variant="outline"
                                                        className="gap-1 border-green-500 text-green-600"
                                                    >
                                                        <TrendingDown className="h-3 w-3" />
                                                        On Track
                                                    </Badge>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </PageLayout>
        </AppLayout>
    );
}
