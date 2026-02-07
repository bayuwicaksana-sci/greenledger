import { format } from 'date-fns';
import { useEffect, useState } from 'react';
import { Badge } from '@/components/ui/badge';
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
import type { FiscalYear } from '@/types';

interface FiscalYearSelectorProps {
    fiscalYears: FiscalYear[];
    selectedYear?: number;
    onChange: (year: number) => void;
    className?: string;
}

export function FiscalYearSelector({
    fiscalYears,
    selectedYear,
    onChange,
    className,
}: FiscalYearSelectorProps) {
    const [selected, setSelected] = useState<string>(
        selectedYear?.toString() || '',
    );

    // Load from session storage on mount
    useEffect(() => {
        const stored = sessionStorage.getItem('selected_fiscal_year');
        if (stored && !selectedYear) {
            setSelected(stored);
            onChange(parseInt(stored));
        }
    }, []);

    const handleChange = (value: string) => {
        setSelected(value);
        sessionStorage.setItem('selected_fiscal_year', value);
        onChange(parseInt(value));
    };

    // Sort fiscal years in descending order
    const sortedFiscalYears = [...fiscalYears].sort((a, b) => b.year - a.year);

    return (
        <Select value={selected} onValueChange={handleChange}>
            <SelectTrigger className={className}>
                <SelectValue placeholder="Select fiscal year" />
            </SelectTrigger>
            <SelectContent>
                {sortedFiscalYears.map((fy) => (
                    <SelectItem key={fy.id} value={fy.year.toString()}>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <div className="flex items-center gap-2">
                                        <span>FY{fy.year}</span>
                                        {fy.is_closed ? (
                                            <Badge
                                                variant="secondary"
                                                className="text-xs"
                                            >
                                                Closed
                                            </Badge>
                                        ) : (
                                            <Badge
                                                variant="default"
                                                className="text-xs"
                                            >
                                                Open
                                            </Badge>
                                        )}
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>
                                        FY{fy.year}:{' '}
                                        {format(
                                            new Date(fy.start_date),
                                            'MMM d, yyyy',
                                        )}{' '}
                                        -{' '}
                                        {format(
                                            new Date(fy.end_date),
                                            'MMM d, yyyy',
                                        )}
                                    </p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}
