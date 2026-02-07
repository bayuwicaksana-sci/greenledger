import { router, usePage } from '@inertiajs/react';
import { Alert } from '@/components/ui/alert';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import type { SharedData } from '@/types';
import type { KpiCardData } from '@/types/dashboard';
import { MetricValue } from '../atoms/MetricValue';
import { TrendIndicator } from '../atoms/TrendIndicator';

interface KpiCardProps extends KpiCardData {
    format?: 'number' | 'currency' | 'percentage';
}

export function KpiCard({
    title,
    value,
    trend,
    subtitle,
    alert,
    icon,
    onClick,
    loading = false,
    format = 'number',
}: KpiCardProps) {
    const { site_code } = usePage<SharedData>().props;

    const handleClick = () => {
        if (onClick && site_code) {
            // Import route dynamically based on onClick string
            import(`@/routes`)
                .then((routes: any) => {
                    const routeFunc = onClick
                        .split('.')
                        .reduce((obj, key) => obj[key], routes);
                    if (typeof routeFunc === 'function') {
                        router.visit(routeFunc({ site_code }).url);
                    }
                })
                .catch(() => {
                    // Fallback if route doesn't exist
                    console.warn(`Route ${onClick} not found`);
                });
        }
    };

    if (loading) {
        return (
            <Card className="h-full">
                <CardHeader>
                    <Skeleton className="h-4 w-24" />
                </CardHeader>
                <CardContent className="space-y-2">
                    <Skeleton className="h-8 w-16" />
                    <Skeleton className="h-3 w-32" />
                </CardContent>
            </Card>
        );
    }

    const isClickable = Boolean(onClick);
    const hasAlert = Boolean(alert);

    return (
        <Card
            className={cn(
                'h-full transition-all',
                isClickable && 'cursor-pointer hover:bg-accent hover:shadow-md',
                hasAlert &&
                    alert === 'error' &&
                    'border-red-500 dark:border-red-400',
                hasAlert &&
                    alert === 'warning' &&
                    'border-yellow-500 dark:border-yellow-400',
                hasAlert &&
                    alert === 'success' &&
                    'border-green-500 dark:border-green-400',
            )}
            onClick={handleClick}
        >
            <CardHeader>
                <div className="flex items-start justify-between">
                    <CardTitle className="text-sm font-medium text-muted-foreground">
                        {title}
                    </CardTitle>
                    {icon && (
                        <div className="text-muted-foreground opacity-50">
                            {/* Icon component would go here */}
                        </div>
                    )}
                </div>
            </CardHeader>
            <CardContent className="space-y-2">
                <div className="text-3xl font-bold">
                    <MetricValue value={value} format={format} />
                </div>
                {trend && <TrendIndicator trend={trend} />}
                {subtitle && (
                    <p className="text-xs text-muted-foreground">{subtitle}</p>
                )}
                {hasAlert && alert === 'error' && (
                    <Alert variant="destructive" className="px-2 py-1 text-xs">
                        Attention Required
                    </Alert>
                )}
            </CardContent>
        </Card>
    );
}
