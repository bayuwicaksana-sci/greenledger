import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { ProgramData } from '@/types/dashboard';
import { AlertTriangle, Folder } from 'lucide-react';
import { MetricValue } from '../atoms/MetricValue';
import { PercentageBar } from '../atoms/PercentageBar';
import { StatusBadge } from '../atoms/StatusBadge';
import { Widget } from './Widget';

interface ProgramsWidgetProps {
    programs: ProgramData[];
    title?: string;
    loading?: boolean;
    onRefresh?: () => void;
}

export function ProgramsWidget({
    programs,
    title = 'My Programs',
    loading,
    onRefresh,
}: ProgramsWidgetProps) {
    // Sort by budget utilization descending
    const sortedPrograms = [...programs]
        .sort((a, b) => b.budgetUtilization - a.budgetUtilization)
        .slice(0, 3);

    return (
        <Widget
            title={title}
            icon={<Folder className="size-5" />}
            loading={loading}
            onRefresh={onRefresh}
        >
            <div className="space-y-4">
                {sortedPrograms.map((program) => {
                    const isHighUtilization = program.budgetUtilization > 90;
                    const isProfitable = program.netIncome > 0;

                    return (
                        <div
                            key={program.id}
                            className={cn(
                                'space-y-3 rounded-lg border p-4 transition-colors',
                                'hover:bg-accent',
                                isHighUtilization &&
                                    'border-yellow-500 dark:border-yellow-400',
                            )}
                        >
                            <div className="flex items-start justify-between gap-2">
                                <div className="flex-1">
                                    <h4 className="font-semibold">
                                        {program.name}
                                    </h4>
                                    <div className="mt-1 flex items-center gap-2">
                                        <StatusBadge status={program.status} />
                                        {isHighUtilization && (
                                            <div className="flex items-center gap-1 text-xs text-yellow-600 dark:text-yellow-400">
                                                <AlertTriangle className="size-3" />
                                                Budget Alert
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <div>
                                    <span className="text-muted-foreground">
                                        Budget:
                                    </span>{' '}
                                    <MetricValue
                                        value={program.budget}
                                        format="currency"
                                        className="text-sm"
                                    />
                                </div>
                                <div>
                                    <span className="text-muted-foreground">
                                        Spent:
                                    </span>{' '}
                                    <MetricValue
                                        value={program.spent}
                                        format="currency"
                                        className="text-sm"
                                    />
                                </div>
                                <div>
                                    <span className="text-muted-foreground">
                                        Revenue:
                                    </span>{' '}
                                    <MetricValue
                                        value={program.revenue}
                                        format="currency"
                                        className="text-sm"
                                    />
                                </div>
                                <div>
                                    <span className="text-muted-foreground">
                                        Net:
                                    </span>{' '}
                                    <span
                                        className={cn(
                                            'font-semibold',
                                            isProfitable
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400',
                                        )}
                                    >
                                        <MetricValue
                                            value={program.netIncome}
                                            format="currency"
                                            className="text-sm"
                                        />
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div className="mb-1 flex justify-between text-sm">
                                    <span className="text-muted-foreground">
                                        Budget Utilization
                                    </span>
                                    <span className="font-medium">
                                        {program.budgetUtilization}%
                                    </span>
                                </div>
                                <PercentageBar
                                    percentage={program.budgetUtilization}
                                />
                            </div>
                        </div>
                    );
                })}

                {programs.length > 3 && (
                    <div className="flex justify-center pt-2">
                        <Button variant="outline" size="sm">
                            View All Programs →
                        </Button>
                    </div>
                )}
            </div>
        </Widget>
    );
}
