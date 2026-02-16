import type { UseFormReturn } from '@inertiajs/react';
import type {
    ColumnDef,
    ExpandedState,
    SortingState,
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
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { EditableDateCell } from '@/components/editable-cells/EditableDateCell';
import { EditableTextCell } from '@/components/editable-cells/EditableTextCell';
import { EditableTextareaCell } from '@/components/editable-cells/EditableTextareaCell';
import type { Activity } from '@/pages/programs/utils/budgetCalculations';

interface ActivitiesTableProps {
    form: UseFormReturn<any>;
}

export function ActivitiesTable({ form }: ActivitiesTableProps) {
    const { data, setData, errors } = form;
    const activities = Array.isArray(data.activities) ? data.activities : [];

    const [sorting, setSorting] = useState<SortingState>([]);
    const [expanded, setExpanded] = useState<ExpandedState>({});

    const columns = useMemo<ColumnDef<Activity>[]>(
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
            },
            {
                accessorKey: 'activity_name',
                header: 'Activity Name',
                cell: (props) => (
                    <EditableTextCell
                        {...props}
                        maxLength={200}
                        placeholder="e.g., Field Preparation, Planting"
                    />
                ),
                size: 400,
                minSize: 300,
            },
            {
                accessorKey: 'planned_start_date',
                header: 'Planned Start',
                cell: (props) => <EditableDateCell {...props} />,
                size: 180,
                minSize: 150,
            },
            {
                accessorKey: 'planned_end_date',
                header: 'Planned End',
                cell: (props) => {
                    const activity = props.row.original;
                    return (
                        <EditableDateCell
                            {...props}
                            min={activity.planned_start_date || undefined}
                        />
                    );
                },
                size: 180,
                minSize: 150,
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
            },
        ],
        [],
    );

    const table = useReactTable({
        data: activities,
        columns,
        state: {
            sorting,
            expanded,
        },
        onSortingChange: setSorting,
        onExpandedChange: setExpanded,
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
            dataKey: 'activities',
            errors,
            updateData: (rowIndex: number, columnId: string, value: any) => {
                const updated = activities.map((row, index) =>
                    index === rowIndex ? { ...row, [columnId]: value } : row,
                );
                setData('activities', updated);
            },
            addRow: () => {
                const newActivity: Activity = {
                    activity_name: '',
                    description: '',
                    planned_start_date: '',
                    planned_end_date: '',
                };
                const updated = [...activities, newActivity];
                setData('activities', updated);

                // Navigate to last page
                const lastPage = Math.floor((updated.length - 1) / 10);
                setTimeout(() => table.setPageIndex(lastPage), 0);
            },
            removeRow: (rowIndex: number) => {
                const updated = activities.filter((_, i) => i !== rowIndex);
                setData('activities', updated);
            },
        },
    });

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="rounded-lg border border-muted bg-muted/50 p-4">
                <h4 className="mb-2 font-medium">Program Activities</h4>
                <p className="text-sm text-muted-foreground">
                    Define the organizational structure of your program. Budget
                    for each activity will be calculated from Budget Items (BoQ)
                    assigned to it in the next section.
                </p>
            </div>

            {/* Table */}
            {activities.length === 0 ? (
                <div className="rounded-lg border-2 border-dashed border-muted p-8 text-center">
                    <p className="text-sm text-muted-foreground">
                        No activities yet. Click "Add Activity" to create the
                        first one.
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
                                        {/* Expanded Row - Description */}
                                        {row.getIsExpanded() && (
                                            <TableRow>
                                                <TableCell colSpan={columns.length}>
                                                    <div className="space-y-2 py-4">
                                                        <label className="text-sm font-medium">
                                                            Description
                                                        </label>
                                                        <EditableTextareaCell
                                                            getValue={() =>
                                                                row.original.description
                                                            }
                                                            row={row}
                                                            column={{ id: 'description' } as any}
                                                            table={table}
                                                            rows={2}
                                                            placeholder="Describe this activity..."
                                                        />
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

            {/* Add Activity Button */}
            <Button
                type="button"
                variant="outline"
                onClick={() => (table.options.meta as any)?.addRow?.()}
                className="w-full"
            >
                <Plus className="mr-2 h-4 w-4" />
                Add Activity
            </Button>

            {/* Global Error */}
            {errors.activities && typeof errors.activities === 'string' && (
                <p className="text-sm text-red-500">{errors.activities}</p>
            )}
        </div>
    );
}
