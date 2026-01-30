import { ArrowDown, ArrowUp } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { TrendData } from '@/types/dashboard';

interface TrendIndicatorProps {
    trend: TrendData;
    className?: string;
}

export function TrendIndicator({ trend, className }: TrendIndicatorProps) {
    const { direction, value, positive } = trend;

    const isPositive = positive;
    const Icon = direction === 'up' ? ArrowUp : ArrowDown;

    return (
        <div
            className={cn(
                'flex items-center gap-1 text-sm font-medium',
                isPositive
                    ? 'text-green-600 dark:text-green-400'
                    : 'text-red-600 dark:text-red-400',
                className,
            )}
        >
            <Icon className="size-4" />
            <span>{value}</span>
        </div>
    );
}
