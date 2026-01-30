import { useState } from 'react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ArrowUp, ArrowDown } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Column<T> {
    key: keyof T | string;
    label: string;
    sortable?: boolean;
    render?: (value: any, row: T) => React.ReactNode;
    className?: string;
}

interface DataTableProps<T> {
    columns: Column<T>[];
    data: T[];
    sortable?: boolean;
    className?: string;
}

export function DataTable<T extends Record<string, any>>({
    columns,
    data,
    sortable = true,
    className,
}: DataTableProps<T>) {
    const [sortKey, setSortKey] = useState<keyof T | string | null>(null);
    const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc');

    const handleSort = (key: keyof T | string) => {
        if (sortKey === key) {
            setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
        } else {
            setSortKey(key);
            setSortDirection('asc');
        }
    };

    const sortedData = sortKey
        ? [...data].sort((a, b) => {
              const aValue = a[sortKey];
              const bValue = b[sortKey];

              if (aValue === bValue) return 0;

              const comparison = aValue > bValue ? 1 : -1;
              return sortDirection === 'asc' ? comparison : -comparison;
          })
        : data;

    return (
        <div className={cn('w-full overflow-auto', className)}>
            <Table>
                <TableHeader>
                    <TableRow>
                        {columns.map((column) => (
                            <TableHead
                                key={String(column.key)}
                                className={cn(
                                    column.className,
                                    sortable &&
                                        column.sortable !== false &&
                                        'cursor-pointer select-none',
                                )}
                                onClick={() =>
                                    sortable &&
                                    column.sortable !== false &&
                                    handleSort(column.key)
                                }
                            >
                                <div className="flex items-center gap-2">
                                    {column.label}
                                    {sortable &&
                                        column.sortable !== false &&
                                        sortKey === column.key && (
                                            <>
                                                {sortDirection === 'asc' ? (
                                                    <ArrowUp className="size-4" />
                                                ) : (
                                                    <ArrowDown className="size-4" />
                                                )}
                                            </>
                                        )}
                                </div>
                            </TableHead>
                        ))}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {sortedData.length === 0 ? (
                        <TableRow>
                            <TableCell
                                colSpan={columns.length}
                                className="text-center text-muted-foreground py-8"
                            >
                                No data available
                            </TableCell>
                        </TableRow>
                    ) : (
                        sortedData.map((row, idx) => (
                            <TableRow key={idx}>
                                {columns.map((column) => (
                                    <TableCell
                                        key={String(column.key)}
                                        className={column.className}
                                    >
                                        {column.render
                                            ? column.render(
                                                  row[column.key],
                                                  row,
                                              )
                                            : row[column.key]}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))
                    )}
                </TableBody>
            </Table>
        </div>
    );
}
