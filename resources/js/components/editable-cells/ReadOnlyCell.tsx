import type { CellContext } from '@tanstack/react-table';
import { cn } from '@/lib/utils';

interface ReadOnlyCellProps extends CellContext<any, any> {
    className?: string;
    format?: 'currency' | 'date' | 'text';
}

export function ReadOnlyCell({
    getValue,
    className,
    format = 'text',
}: ReadOnlyCellProps) {
    const value = getValue();

    const formatValue = (val: any): string => {
        if (val === null || val === undefined || val === '') {
            return '-';
        }

        switch (format) {
            case 'currency':
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(Number(val));
            case 'date':
                return val;
            case 'text':
            default:
                return String(val);
        }
    };

    return (
        <div
            className={cn(
                'rounded-md bg-muted px-3 py-2 text-sm text-muted-foreground',
                className,
            )}
        >
            {formatValue(value)}
        </div>
    );
}
