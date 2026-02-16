import type { CellContext } from '@tanstack/react-table';
import { useEffect, useState } from 'react';
import { Textarea } from '@/components/ui/textarea';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import type { TableMeta } from './types';

interface EditableTextareaCellProps extends CellContext<any, any> {
    rows?: number;
    placeholder?: string;
}

export function EditableTextareaCell({
    getValue,
    row: { index },
    column: { id },
    table,
    rows = 3,
    placeholder = '',
}: EditableTextareaCellProps) {
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

    useEffect(() => {
        setValue(initialValue || '');
    }, [initialValue]);

    const textarea = (
        <Textarea
            value={value as string}
            onChange={(e) => setValue(e.target.value)}
            onBlur={onBlur}
            rows={rows}
            placeholder={placeholder}
            className={cn(
                'resize-none',
                error && 'border-red-500 focus-visible:ring-red-500',
            )}
        />
    );

    if (error) {
        return (
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger asChild>{textarea}</TooltipTrigger>
                    <TooltipContent className="bg-red-500 text-white">
                        <p>{error}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        );
    }

    return textarea;
}
