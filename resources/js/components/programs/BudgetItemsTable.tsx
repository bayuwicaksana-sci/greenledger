import type { UseFormReturn } from '@inertiajs/react';
import type {
    ColumnDef,
    ExpandedState,
    SortingState,
    VisibilityState,
} from '@tanstack/react-table';
import {
    flexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { ChevronDown, ChevronRight, Plus, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { EditableNumberCell } from '@/components/editable-cells/EditableNumberCell';
import { EditableSelectCell } from '@/components/editable-cells/EditableSelectCell';
import { EditableTextCell } from '@/components/editable-cells/EditableTextCell';
import { EditableTextareaCell } from '@/components/editable-cells/EditableTextareaCell';
import { ReadOnlyCell } from '@/components/editable-cells/ReadOnlyCell';
import type { Activity, BudgetItem } from '@/pages/programs/utils/budgetCalculations';
import {
    calculateEstimatedDate,
    calculateSubtotal,
    calculateTotalBudget,
} from '@/pages/programs/utils/budgetCalculations';

interface CoaAccount {
    id: number;
    fiscal_year_id: number;
    account_code: string;
    account_name: string;
    short_description: string | null;
}

interface BudgetItemsTableProps {
    form: UseFormReturn<any>;
    categories: any[];
    phases: any[];
    coaAccounts: CoaAccount[];
    activities: Activity[];
}

export function BudgetItemsTable({
    form,
    categories,
    phases,
    coaAccounts,
    activities,
}: BudgetItemsTableProps) {
    const { data, setData, errors } = form;
    const items = (data.budget_items as BudgetItem[]) || [];

    const [sorting, setSorting] = useState<SortingState>([]);
    const [expanded, setExpanded] = useState<ExpandedState>({});
    const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({
        activity_id: false,
        coa_account_id: false,
        days: false,
        estimated_realization_date: false,
    });

    // Filter COA accounts based on selected fiscal year
    const filteredCoaAccounts = useMemo(() => {
        const fiscalYearId = Number(data.fiscal_year_id);
        if (!fiscalYearId) return coaAccounts;

        return coaAccounts.filter(
            (account) => account.fiscal_year_id === fiscalYearId,
        );
    }, [coaAccounts, data.fiscal_year_id]);

    // Recalculate estimated dates when program start_date changes
    useEffect(() => {
        if (data.start_date && items.length > 0) {
            const updatedItems = items.map((item) => ({
                ...item,
                estimated_realization_date: item.days
                    ? calculateEstimatedDate(data.start_date, item.days)
                    : null,
            }));

            const hasChanges = updatedItems.some(
                (item, index) =>
                    item.estimated_realization_date !==
                    items[index].estimated_realization_date,
            );

            if (hasChanges) {
                setData('budget_items', updatedItems);
            }
        }
    }, [data.start_date]);

    // Prepare select options
    const activityOptions = useMemo(
        () => [
            {
                value: '__unassigned__',
                label: 'Unassigned (General/Overhead)',
            },
            ...activities.map((activity, index) => ({
                value: index.toString(),
                label: activity.activity_name || `Activity #${index + 1}`,
            })),
        ],
        [activities],
    );

    const categoryOptions = useMemo(
        () =>
            categories.map((cat) => ({
                value: cat.id.toString(),
                label: cat.category_name,
            })),
        [categories],
    );

    const phaseOptions = useMemo(
        () =>
            phases.map((phase) => ({
                value: phase.id.toString(),
                label: phase.phase_name,
            })),
        [phases],
    );

    const coaOptions = useMemo(() => {
        if (filteredCoaAccounts.length === 0) {
            return [
                {
                    value: '__no_coa__',
                    label: 'No COA accounts for selected fiscal year',
                    disabled: true,
                },
            ];
        }

        return filteredCoaAccounts.map((account) => ({
            value: account.id.toString(),
            label: `${account.account_code} - ${account.account_name}`,
        }));
    }, [filteredCoaAccounts]);

    const columns = useMemo<ColumnDef<BudgetItem>[]>(
        () => [
            {
                id: 'expand',
                header: () => null,
                cell: ({ row }) => (
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => row.toggleExpanded()}
                        className="h-8 w-8 p-0"
                    >
                        {row.getIsExpanded() ? (
                            <ChevronDown className="h-4 w-4" />
                        ) : (
                            <ChevronRight className="h-4 w-4" />
                        )}
                    </Button>
                ),
                size: 50,
                minSize: 50,
                maxSize: 50,
                enableSorting: false,
                enableHiding: false,
            },
            {
                accessorKey: 'activity_id',
                header: 'Activity',
                cell: (props) => (
                    <EditableSelectCell
                        {...props}
                        options={activityOptions}
                        placeholder="Unassigned"
                    />
                ),
                size: 200,
                minSize: 180,
            },
            {
                accessorKey: 'item_description',
                header: 'Description',
                cell: (props) => (
                    <EditableTextCell
                        {...props}
                        placeholder="What is being purchased?"
                    />
                ),
                size: 250,
                minSize: 200,
                enableHiding: false,
            },
            {
                accessorKey: 'category_id',
                header: 'Category',
                cell: (props) => (
                    <EditableSelectCell
                        {...props}
                        options={categoryOptions}
                        placeholder="Select category"
                    />
                ),
                size: 180,
                minSize: 150,
            },
            {
                accessorKey: 'phase_id',
                header: 'Phase',
                cell: (props) => (
                    <EditableSelectCell
                        {...props}
                        options={phaseOptions}
                        placeholder="Select phase"
                    />
                ),
                size: 180,
                minSize: 150,
            },
            {
                accessorKey: 'coa_account_id',
                header: 'COA Account',
                cell: (props) => (
                    <EditableSelectCell
                        {...props}
                        options={coaOptions}
                        placeholder="Select COA (optional)"
                    />
                ),
                size: 250,
                minSize: 200,
            },
            {
                accessorKey: 'unit',
                header: 'Unit',
                cell: (props) => (
                    <EditableTextCell
                        {...props}
                        placeholder="kg, pcs, box"
                    />
                ),
                size: 120,
                minSize: 100,
            },
            {
                accessorKey: 'qty',
                header: 'Qty',
                cell: (props) => (
                    <EditableNumberCell {...props} placeholder="0" />
                ),
                size: 120,
                minSize: 100,
            },
            {
                accessorKey: 'unit_price',
                header: 'Unit Price (Rp)',
                cell: (props) => (
                    <EditableNumberCell {...props} placeholder="0" />
                ),
                size: 160,
                minSize: 140,
            },
            {
                accessorKey: 'subtotal',
                header: 'Subtotal',
                cell: (props) => <ReadOnlyCell {...props} format="currency" />,
                size: 180,
                minSize: 150,
                enableHiding: false,
            },
            {
                accessorKey: 'days',
                header: 'Days',
                cell: (props) => (
                    <EditableNumberCell {...props} placeholder="0" />
                ),
                size: 120,
                minSize: 100,
            },
            {
                accessorKey: 'estimated_realization_date',
                header: 'Est. Date',
                cell: (props) => <ReadOnlyCell {...props} format="date" />,
                size: 150,
                minSize: 130,
            },
            {
                id: 'actions',
                header: 'Actions',
                cell: ({ row, table }) => {
                    const meta = table.options.meta as any;
                    return (
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => meta?.removeRow?.(row.index)}
                            className="h-8 w-8 p-0 text-red-600 hover:bg-red-50 hover:text-red-700"
                        >
                            <Trash2 className="h-4 w-4" />
                        </Button>
                    );
                },
                size: 80,
                minSize: 80,
                maxSize: 80,
                enableSorting: false,
                enableHiding: false,
            },
        ],
        [activityOptions, categoryOptions, phaseOptions, coaOptions],
    );

    const table = useReactTable({
        data: items,
        columns,
        state: {
            sorting,
            expanded,
            columnVisibility,
        },
        onSortingChange: setSorting,
        onExpandedChange: setExpanded,
        onColumnVisibilityChange: setColumnVisibility,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getExpandedRowModel: getExpandedRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        initialState: {
            pagination: {
                pageSize: 10,
            },
        },
        meta: {
            dataKey: 'budget_items',
            errors,
            updateData: (rowIndex: number, columnId: string, value: any) => {
                const updated = items.map((row, index) => {
                    if (index !== rowIndex) return row;

                    const updatedRow = { ...row, [columnId]: value };

                    // Handle activity_id special case (convert __unassigned__ to empty string)
                    if (columnId === 'activity_id') {
                        updatedRow.activity_id =
                            value === '__unassigned__' ? '' : value;
                    }

                    // Recalculate subtotal if qty or unit_price changed
                    if (columnId === 'qty' || columnId === 'unit_price') {
                        updatedRow.subtotal = calculateSubtotal(updatedRow);
                    }

                    // Recalculate estimated date if days changed
                    if (columnId === 'days') {
                        updatedRow.estimated_realization_date =
                            calculateEstimatedDate(
                                data.start_date,
                                updatedRow.days,
                            );
                    }

                    return updatedRow;
                });

                setData('budget_items', updated);
                setData('total_budget', calculateTotalBudget(updated));
            },
            addRow: () => {
                const newItem: BudgetItem = {
                    activity_id: '',
                    category_id: '',
                    phase_id: '',
                    coa_account_id: '',
                    item_description: '',
                    specification: '',
                    unit: '',
                    qty: '',
                    unit_price: '',
                    days: '',
                    estimated_realization_date: null,
                    subtotal: 0,
                    notes: '',
                };
                const updated = [...items, newItem];
                setData('budget_items', updated);

                // Navigate to last page
                const lastPage = Math.floor((updated.length - 1) / 10);
                setTimeout(() => table.setPageIndex(lastPage), 0);
            },
            removeRow: (rowIndex: number) => {
                const updated = items.filter((_, i) => i !== rowIndex);
                setData('budget_items', updated);
                setData('total_budget', calculateTotalBudget(updated));
            },
        },
    });

    return (
        <div className="space-y-4">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-medium">
                        Bill of Quantities (BoQ)
                    </h3>
                    <p className="text-sm text-muted-foreground">
                        Itemized budget breakdown for this program
                    </p>
                </div>
                <div className="flex items-center gap-2">
                    {/* Column Visibility Toggle */}
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" size="sm">
                                Columns <ChevronDown className="ml-2 h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {table
                                .getAllColumns()
                                .filter((column) => column.getCanHide())
                                .map((column) => {
                                    // Map column IDs to readable names
                                    const columnNames: Record<string, string> = {
                                        activity_id: 'Activity',
                                        item_description: 'Description',
                                        category_id: 'Category',
                                        phase_id: 'Phase',
                                        coa_account_id: 'COA Account',
                                        unit: 'Unit',
                                        qty: 'Quantity',
                                        unit_price: 'Unit Price',
                                        subtotal: 'Subtotal',
                                        days: 'Days',
                                        estimated_realization_date: 'Est. Date',
                                    };

                                    return (
                                        <DropdownMenuCheckboxItem
                                            key={column.id}
                                            className="capitalize"
                                            checked={column.getIsVisible()}
                                            onCheckedChange={(value) =>
                                                column.toggleVisibility(!!value)
                                            }
                                        >
                                            {columnNames[column.id] || column.id}
                                        </DropdownMenuCheckboxItem>
                                    );
                                })}
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <Button
                        type="button"
                        onClick={() => (table.options.meta as any)?.addRow?.()}
                        size="sm"
                    >
                        <Plus className="mr-2 h-4 w-4" /> Add Item
                    </Button>
                </div>
            </div>

            {/* Table or Empty State */}
            {items.length === 0 ? (
                <div className="rounded-lg border-2 border-dashed border-muted p-12 text-center">
                    <p className="text-sm text-muted-foreground">
                        No budget items added yet.
                    </p>
                    <p className="text-xs text-muted-foreground">
                        Click "Add Item" to create your first budget entry
                    </p>
                </div>
            ) : (
                <>
                    <div className="overflow-x-auto rounded-md border">
                        <Table>
                            <TableHeader>
                                {table.getHeaderGroups().map((headerGroup) => (
                                    <TableRow key={headerGroup.id}>
                                        {headerGroup.headers.map((header) => (
                                            <TableHead
                                                key={header.id}
                                                style={{
                                                    width: header.getSize(),
                                                    minWidth: header.column.columnDef.minSize,
                                                    maxWidth: header.column.columnDef.maxSize,
                                                }}
                                            >
                                                {header.isPlaceholder
                                                    ? null
                                                    : flexRender(
                                                          header.column.columnDef
                                                              .header,
                                                          header.getContext(),
                                                      )}
                                            </TableHead>
                                        ))}
                                    </TableRow>
                                ))}
                            </TableHeader>
                            <TableBody>
                                {table.getRowModel().rows.map((row) => (
                                    <>
                                        <TableRow key={row.id}>
                                            {row.getVisibleCells().map((cell) => (
                                                <TableCell
                                                    key={cell.id}
                                                    style={{
                                                        width: cell.column.getSize(),
                                                        minWidth: cell.column.columnDef.minSize,
                                                        maxWidth: cell.column.columnDef.maxSize,
                                                    }}
                                                >
                                                    {flexRender(
                                                        cell.column.columnDef.cell,
                                                        cell.getContext(),
                                                    )}
                                                </TableCell>
                                            ))}
                                        </TableRow>
                                        {/* Expanded Row - Specification & Notes */}
                                        {row.getIsExpanded() && (
                                            <TableRow>
                                                <TableCell colSpan={columns.length}>
                                                    <div className="space-y-4 py-4">
                                                        <div className="space-y-2">
                                                            <label className="text-sm font-medium">
                                                                Specification
                                                            </label>
                                                            <EditableTextareaCell
                                                                getValue={() =>
                                                                    row.original
                                                                        .specification
                                                                }
                                                                row={row}
                                                                column={
                                                                    {
                                                                        id: 'specification',
                                                                    } as any
                                                                }
                                                                table={table}
                                                                rows={3}
                                                                placeholder="Detailed specifications, brand, model, technical requirements..."
                                                            />
                                                        </div>
                                                        <div className="space-y-2">
                                                            <label className="text-sm font-medium">
                                                                Notes
                                                            </label>
                                                            <EditableTextareaCell
                                                                getValue={() =>
                                                                    row.original.notes
                                                                }
                                                                row={row}
                                                                column={
                                                                    { id: 'notes' } as any
                                                                }
                                                                table={table}
                                                                rows={2}
                                                                placeholder="Additional notes or remarks..."
                                                            />
                                                        </div>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        )}
                                    </>
                                ))}
                            </TableBody>
                        </Table>
                    </div>

                    {/* Pagination */}
                    {table.getPageCount() > 1 && (
                        <div className="flex items-center justify-end space-x-2">
                            <div className="text-sm text-muted-foreground">
                                Page {table.getState().pagination.pageIndex + 1} of{' '}
                                {table.getPageCount()}
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => table.previousPage()}
                                disabled={!table.getCanPreviousPage()}
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => table.nextPage()}
                                disabled={!table.getCanNextPage()}
                            >
                                Next
                            </Button>
                        </div>
                    )}
                </>
            )}

            {/* Global Error */}
            {errors.budget_items && typeof errors.budget_items === 'string' && (
                <p className="text-sm text-red-500">{errors.budget_items}</p>
            )}
        </div>
    );
}
