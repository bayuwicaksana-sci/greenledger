import { cn } from '@/lib/utils';

type MetricFormat = 'number' | 'currency' | 'percentage';

interface MetricValueProps {
    value: string | number;
    format?: MetricFormat;
    className?: string;
}

export function MetricValue({
    value,
    format = 'number',
    className,
}: MetricValueProps) {
    const formatValue = (val: string | number): string => {
        const numericValue = typeof val === 'string' ? parseFloat(val) : val;

        switch (format) {
            case 'currency':
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                }).format(numericValue);

            case 'percentage':
                return new Intl.NumberFormat('id-ID', {
                    style: 'percent',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                }).format(numericValue / 100);

            case 'number':
            default:
                return new Intl.NumberFormat('id-ID').format(numericValue);
        }
    };

    return (
        <span
            className={cn(
                'font-semibold tabular-nums text-foreground',
                className,
            )}
        >
            {typeof value === 'number' ? formatValue(value) : value}
        </span>
    );
}
