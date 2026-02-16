import type { CellContext } from '@tanstack/react-table';
import { useEffect, useState } from 'react';
import { Input } from '@/components/ui/input';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import type { TableMeta } from './types';

interface EditableDateCellProps extends CellContext<any, any> {
    min?: string;
    max?: string;
}

export function EditableDateCell({
    getValue,
    row: { index },
    column: { id },
    table,
    min,
    max,
}: EditableDateCellProps) {
    const initialValue = getValue();
    const [value, setValue] = useState(initialValue || '');

    const meta = table.options.meta as TableMeta | undefined;
    const errors = meta?.errors || {};
    const dataKey = meta?.dataKey || 'items';
    const errorKey = `${dataKey}.${index}.${id}`;
    const error = errors[errorKey];

    const onBlur = () => {
        if (value !== initialValue && meta?.updateData) {
            meta.updateData(index, id, value);
        }
    };

    const onKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter') {
            e.currentTarget.blur();
        }
        if (e.key === 'Escape') {
            setValue(initialValue || '');
            e.currentTarget.blur();
        }
    };

    useEffect(() => {
        setValue(initialValue || '');
    }, [initialValue]);

    const input = (
        <Input
            type="date"
            value={value as string}
            onChange={(e) => setValue(e.target.value)}
            onBlur={onBlur}
            onKeyDown={onKeyDown}
            min={min}
            max={max}
            className={cn(error && 'border-red-500 focus-visible:ring-red-500')}
        />
    );

    if (error) {
        return (
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger asChild>{input}</TooltipTrigger>
                    <TooltipContent className="bg-red-500 text-white">
                        <p>{error}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        );
    }

    return input;
}
