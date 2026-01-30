import { cn } from '@/lib/utils';
import type { KpiCardData } from '@/types/dashboard';
import { KpiCard } from '../molecules/KpiCard';

interface QuickStatsRowProps {
    kpis: KpiCardData[];
    className?: string;
}

export function QuickStatsRow({ kpis, className }: QuickStatsRowProps) {
    const getGridCols = (count: number): string => {
        if (count <= 2) return 'md:grid-cols-2';
        if (count <= 3) return 'md:grid-cols-3';
        if (count <= 4) return 'md:grid-cols-2';
        if (count <= 6) return 'md:grid-cols-2 lg:grid-cols-3';
        return 'md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4';
    };

    return (
        <div
            className={cn(
                'grid grid-cols-1 gap-4',
                getGridCols(kpis.length),
                className,
            )}
        >
            {kpis.map((kpi, index) => (
                <KpiCard
                    key={index}
                    {...kpi}
                    format={
                        typeof kpi.value === 'number' && kpi.value > 100000
                            ? 'currency'
                            : 'number'
                    }
                />
            ))}
        </div>
    );
}
