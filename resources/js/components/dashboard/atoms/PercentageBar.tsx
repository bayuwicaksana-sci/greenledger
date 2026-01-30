import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';

interface PercentageBarProps {
    percentage: number;
    showLabel?: boolean;
    className?: string;
}

export function PercentageBar({
    percentage,
    showLabel = false,
    className,
}: PercentageBarProps) {
    // Determine color based on percentage thresholds
    const getColor = (value: number): string => {
        if (value < 70) return 'bg-green-500';
        if (value < 90) return 'bg-yellow-500';
        return 'bg-red-500';
    };

    return (
        <div className={cn('flex items-center gap-3', className)}>
            <div className="flex-1">
                <Progress
                    value={percentage}
                    className="h-2"
                    indicatorClassName={getColor(percentage)}
                />
            </div>
            {showLabel && (
                <span className="text-sm font-medium text-muted-foreground tabular-nums min-w-[3ch]">
                    {percentage}%
                </span>
            )}
        </div>
    );
}
