import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { ChevronsUpDown, X } from 'lucide-react';
import { useState } from 'react';
import { cn } from '@/lib/utils';

interface Option {
    value: string;
    label: string;
}

interface MultiSelectProps {
    options: Option[];
    values: string[];
    onValuesChange: (values: string[]) => void;
    placeholder?: string;
    className?: string;
}

export function MultiSelect({
    options,
    values,
    onValuesChange,
    placeholder = 'Select...',
    className,
}: MultiSelectProps) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');

    const toggleValue = (val: string) => {
        onValuesChange(
            values.includes(val)
                ? values.filter((v) => v !== val)
                : [...values, val],
        );
    };

    const removeValue = (val: string) => {
        onValuesChange(values.filter((v) => v !== val));
    };

    return (
        <div className="space-y-2">
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        className={cn('w-full justify-between', className)}
                    >
                        <span className="text-muted-foreground">
                            {values.length === 0
                                ? placeholder
                                : `${values.length} selected`}
                        </span>
                        <ChevronsUpDown className="h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent className="w-full max-w-[400px] p-0" align="start">
                    <Command>
                        <CommandInput
                            placeholder={`Search ${placeholder.toLowerCase()}...`}
                            value={search}
                            onValueChange={setSearch}
                        />
                        <CommandList>
                            <CommandEmpty>No results found.</CommandEmpty>
                            <CommandGroup>
                                {options
                                    .filter((opt) =>
                                        opt.label
                                            .toLowerCase()
                                            .includes(search.toLowerCase()),
                                    )
                                    .map((opt) => (
                                        <CommandItem
                                            key={opt.value}
                                            value={opt.value}
                                            onSelect={() => toggleValue(opt.value)}
                                        >
                                            <Checkbox
                                                className="mr-2"
                                                checked={values.includes(opt.value)}
                                            />
                                            {opt.label}
                                        </CommandItem>
                                    ))}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>
            {values.length > 0 && (
                <div className="flex flex-wrap gap-1">
                    {values.map((val) => {
                        const opt = options.find((o) => o.value === val);
                        if (!opt) {
                            return null;
                        }
                        return (
                            <Badge key={val} variant="secondary" className="flex items-center gap-1">
                                {opt.label}
                                <X
                                    className="h-3 w-3 cursor-pointer"
                                    onClick={() => removeValue(val)}
                                />
                            </Badge>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
