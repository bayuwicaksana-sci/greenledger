import type { CellContext } from '@tanstack/react-table';
import { useEffect, useState } from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import type { TableMeta } from './types';

interface SelectOption {
    value: string;
    label: string;
    disabled?: boolean;
}

interface EditableSelectCellProps extends CellContext<any, any> {
    options: SelectOption[];
    placeholder?: string;
}

export function EditableSelectCell({
    getValue,
    row: { index },
    column: { id },
    table,
    options,
    placeholder = 'Select...',
}: EditableSelectCellProps) {
    const initialValue = getValue();
    const [value, setValue] = useState(initialValue || '');

    const meta = table.options.meta as TableMeta | undefined;
    const errors = meta?.errors || {};
    const dataKey = meta?.dataKey || 'items';
    const errorKey = `${dataKey}.${index}.${id}`;
    const error = errors[errorKey];

    const handleValueChange = (newValue: string) => {
        setValue(newValue);
        if (meta?.updateData) {
            meta.updateData(index, id, newValue);
        }
    };

    useEffect(() => {
        setValue(initialValue || '');
    }, [initialValue]);

    const select = (
        <Select value={value as string} onValueChange={handleValueChange}>
            <SelectTrigger
                className={cn(error && 'border-red-500 focus:ring-red-500')}
            >
                <SelectValue placeholder={placeholder} />
            </SelectTrigger>
            <SelectContent>
                {options.map((option) => (
                    <SelectItem
                        key={option.value}
                        value={option.value}
                        disabled={option.disabled}
                    >
                        {option.label}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );

    if (error) {
        return (
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger asChild>{select}</TooltipTrigger>
                    <TooltipContent className="bg-red-500 text-white">
                        <p>{error}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        );
    }

    return select;
}
