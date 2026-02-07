import { StatusBadge } from '@/components/dashboard/atoms/StatusBadge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import programRoutes from '@/routes/programs';
import type { FiscalYear, Program } from '@/types';
import { Link, router } from '@inertiajs/react';
import type {
    ColumnDef,
    ColumnFiltersState,
    SortingState,
    VisibilityState,
} from '@tanstack/react-table';
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import {
    ArrowUpDown,
    ChevronDown,
    Edit,
    Eye,
    MoreHorizontal,
    Search,
    Trash,
} from 'lucide-react';
import { useState } from 'react';

interface ProgramsTableProps {
    programs: Program[];
    site_code: string;
    fiscal_years: FiscalYear[];
}

export function ProgramsTable({
    programs,
    site_code,
    fiscal_years,
}: ProgramsTableProps) {
    const [sorting, setSorting] = useState<SortingState>([]);
    const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
    const [columnVisibility, setColumnVisibility] = useState<VisibilityState>(
        {},
    );
    const [globalFilter, setGlobalFilter] = useState('');
    const [selectedFiscalYear, setSelectedFiscalYear] = useState<string>('all');

    // Create a map of fiscal years for quick lookup
    const fiscalYearMap = new Map(fiscal_years.map((fy) => [fy.year, fy]));

    const columns: ColumnDef<Program>[] = [
        {
            accessorKey: 'classification',
            header: 'Type',
            cell: ({ row }) => (
                <div className="px-4">
                    {row.getValue('classification') === 'PROGRAM'
                        ? 'Research'
                        : 'Activity'}
                </div>
            ),
        },
        {
            accessorKey: 'program_code',
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === 'asc')
                    }
                >
                    Code
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => (
                <div className="px-4 font-medium">
                    {row.getValue('program_code')}
                </div>
            ),
        },
        {
            accessorKey: 'program_name',
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === 'asc')
                    }
                >
                    Name
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => (
                <div className="px-4">{row.getValue('program_name')}</div>
            ),
        },
        {
            accessorKey: 'fiscal_year',
            header: 'Fiscal Year',
            cell: ({ row }) => {
                const year = row.getValue('fiscal_year') as number;
                const fiscalYear = fiscalYearMap.get(year);
                return (
                    <div className="flex items-center justify-center gap-2">
                        <span>FY{year}</span>
                        {fiscalYear &&
                            (fiscalYear.is_closed ? (
                                <Badge variant="secondary" className="text-xs">
                                    Closed
                                </Badge>
                            ) : (
                                <Badge variant="default" className="text-xs">
                                    Open
                                </Badge>
                            ))}
                    </div>
                );
            },
        },
        {
            accessorKey: 'total_budget',
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    className="w-full text-right"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === 'asc')
                    }
                >
                    Budget
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const amount = parseFloat(row.getValue('total_budget'));
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(amount);
                return (
                    <div className="px-4 text-right font-medium">
                        {formatted}
                    </div>
                );
            },
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
                const status = row.getValue('status') as string;
                return <StatusBadge status={status} />;
            },
        },
        {
            id: 'actions',
            header: 'Actions',
            enableHiding: false,
            cell: ({ row }) => {
                const program = row.original;

                return (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <Link
                                href={programRoutes.show.url({
                                    site: site_code,
                                    program: program.id,
                                })}
                            >
                                <DropdownMenuItem className="cursor-pointer">
                                    <Eye className="mr-2 h-4 w-4" /> View
                                </DropdownMenuItem>
                            </Link>
                            <Link
                                href={programRoutes.edit.url({
                                    site: site_code,
                                    program: program.id,
                                })}
                            >
                                <DropdownMenuItem className="cursor-pointer">
                                    <Edit className="mr-2 h-4 w-4" /> Edit
                                </DropdownMenuItem>
                            </Link>
                            <DropdownMenuItem
                                className="cursor-pointer text-red-600 focus:text-red-600"
                                onClick={() => {
                                    if (
                                        confirm(
                                            'Are you sure you want to delete this program? This action cannot be undone.',
                                        )
                                    ) {
                                        router.delete(
                                            programRoutes.destroy.url({
                                                site: site_code,
                                                program: program.id,
                                            }),
                                        );
                                    }
                                }}
                            >
                                <Trash className="mr-2 h-4 w-4" /> Delete
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                );
            },
        },
    ];

    // Filter programs by fiscal year before passing to table
    const filteredPrograms =
        selectedFiscalYear === 'all'
            ? programs
            : programs.filter(
                  (p) => p.fiscal_year.toString() === selectedFiscalYear,
              );

    const table = useReactTable({
        data: filteredPrograms,
        columns,
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onGlobalFilterChange: setGlobalFilter,
        state: {
            sorting,
            columnFilters,
            columnVisibility,
            globalFilter,
        },
    });

    return (
        <div className="w-full">
            <div className="flex items-center gap-4 py-4">
                <div className="relative max-w-sm flex-1">
                    <Search className="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        placeholder="Filter programs..."
                        value={globalFilter ?? ''}
                        onChange={(event) =>
                            setGlobalFilter(event.target.value)
                        }
                        className="pl-8"
                    />
                </div>
                <Select
                    value={selectedFiscalYear}
                    onValueChange={setSelectedFiscalYear}
                >
                    <SelectTrigger className="w-[180px]">
                        <SelectValue placeholder="Fiscal Year" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Fiscal Years</SelectItem>
                        {fiscal_years.map((fy) => (
                            <SelectItem key={fy.id} value={fy.year.toString()}>
                                FY{fy.year}{' '}
                                {fy.is_closed ? '(Closed)' : '(Open)'}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" className="ml-auto">
                            Columns <ChevronDown className="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        {table
                            .getAllColumns()
                            .filter((column) => column.getCanHide())
                            .map((column) => {
                                return (
                                    <DropdownMenuCheckboxItem
                                        key={column.id}
                                        className="capitalize"
                                        checked={column.getIsVisible()}
                                        onCheckedChange={(value) =>
                                            column.toggleVisibility(!!value)
                                        }
                                    >
                                        {column.id}
                                    </DropdownMenuCheckboxItem>
                                );
                            })}
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    return (
                                        <TableHead key={header.id}>
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                      header.column.columnDef
                                                          .header,
                                                      header.getContext(),
                                                  )}
                                        </TableHead>
                                    );
                                })}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows?.length ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow
                                    key={row.id}
                                    data-state={
                                        row.getIsSelected() && 'selected'
                                    }
                                >
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell key={cell.id}>
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-24 text-center"
                                >
                                    No results.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>
            <div className="flex items-center justify-end space-x-2 py-4">
                <div className="flex-1 text-sm text-muted-foreground">
                    {table.getFilteredRowModel().rows.length} row(s) total.
                </div>
                <div className="flex items-center space-x-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => table.previousPage()}
                        disabled={!table.getCanPreviousPage()}
                    >
                        Previous
                    </Button>
                    <div className="text-sm font-medium">
                        Page {table.getState().pagination.pageIndex + 1} of{' '}
                        {table.getPageCount()}
                    </div>
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => table.nextPage()}
                        disabled={!table.getCanNextPage()}
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
    );
}
