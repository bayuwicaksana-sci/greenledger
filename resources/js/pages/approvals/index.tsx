import { Head, Link, usePage } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import {
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { format } from 'date-fns';
import { FileCheck } from 'lucide-react';
import { useMemo } from 'react';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { index as approvalsIndex, show } from '@/routes/approvals';
import type { BreadcrumbItem, PaginatedResponse, SharedData } from '@/types';

interface ApprovalInstance {
    id: number;
    approvable_type: string;
    approvable_id: number;
    status_value: string;
    status_label: string;
    status_color: string;
    submitted_at: string;
    submitted_by: {
        id: number;
        name: string;
    };
    current_step: {
        id: number;
        name: string;
    } | null;
    approvable: {
        id: number;
        name?: string;
        title?: string;
        reference_number?: string;
        amount?: number;
    } | null;
    workflow: {
        name: string;
    };
}

interface PageProps {
    approvals: PaginatedResponse<ApprovalInstance>;
}

const statusVariantMap: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    pending: 'outline',
    pending_approval: 'secondary',
    approved: 'default',
    rejected: 'destructive',
    changes_requested: 'outline',
    cancelled: 'secondary',
};

export default function PendingApprovals({ approvals }: PageProps) {
    const { site_code } = usePage<SharedData>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pending Approvals',
            href: approvalsIndex.url({ site: site_code ?? '' }),
        },
    ];

    const getApprovableName = (instance: ApprovalInstance) => {
        if (!instance.approvable) return 'Unknown Item';
        return (
            instance.approvable.title ||
            instance.approvable.name ||
            instance.approvable.reference_number ||
            `ID: ${instance.approvable.id}`
        );
    };

    const getApprovableTypeLabel = (type: string) => {
        const parts = type.split('\\');
        return parts[parts.length - 1];
    };

    const columns = useMemo<ColumnDef<ApprovalInstance>[]>(
        () => [
            {
                accessorKey: 'approvable_type',
                header: 'Type',
                cell: ({ row }) => (
                    <Badge variant="outline">
                        {getApprovableTypeLabel(row.original.approvable_type)}
                    </Badge>
                ),
            },
            {
                accessorKey: 'approvable',
                header: 'Item',
                cell: ({ row }) => (
                    <div className="font-medium">{getApprovableName(row.original)}</div>
                ),
            },
            {
                accessorKey: 'submitted_by',
                header: 'Submitted By',
                cell: ({ row }) => (
                    <div>{row.original.submitted_by?.name || 'Unknown'}</div>
                ),
            },
            {
                accessorKey: 'current_step',
                header: 'Step',
                cell: ({ row }) => (
                    <div>{row.original.current_step?.name || 'Processing'}</div>
                ),
            },
            {
                accessorKey: 'status_value',
                header: 'Status',
                cell: ({ row }) => (
                    <Badge variant={statusVariantMap[row.original.status_value] || 'outline'}>
                        {row.original.status_label}
                    </Badge>
                ),
            },
            {
                accessorKey: 'submitted_at',
                header: 'Submitted At',
                cell: ({ row }) =>
                    row.original.submitted_at
                        ? format(new Date(row.original.submitted_at), 'MMM dd, yyyy')
                        : '—',
            },
            {
                id: 'actions',
                header: () => <div className="text-right">Action</div>,
                cell: ({ row }) => (
                    <div className="flex justify-end">
                        <Link
                            href={show.url({
                                site: site_code ?? '',
                                approvalInstance: row.original.id,
                            })}
                            className="rounded-md border bg-background px-3 py-1 text-sm hover:bg-muted transition-colors"
                        >
                            Review
                        </Link>
                    </div>
                ),
            },
        ],
        [site_code],
    );

    const table = useReactTable({
        data: approvals.data,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pending Approvals" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Pending Approvals</h1>
                        <p className="text-muted-foreground">
                            Items requiring your action
                        </p>
                    </div>
                </div>

                <div className="rounded-md border">
                    <Table>
                        <TableHeader>
                            {table.getHeaderGroups().map((headerGroup) => (
                                <TableRow key={headerGroup.id}>
                                    {headerGroup.headers.map((header) => (
                                        <TableHead key={header.id}>
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                      header.column.columnDef.header,
                                                      header.getContext(),
                                                  )}
                                        </TableHead>
                                    ))}
                                </TableRow>
                            ))}
                        </TableHeader>
                        <TableBody>
                            {table.getRowModel().rows.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={columns.length}
                                        className="h-24 text-center"
                                    >
                                        <div className="flex flex-col items-center justify-center gap-2">
                                            <FileCheck className="h-8 w-8 text-muted-foreground" />
                                            <p className="text-sm text-muted-foreground">
                                                You have no pending approvals at this time.
                                            </p>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ) : (
                                table.getRowModel().rows.map((row) => (
                                    <TableRow key={row.id}>
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
                            )}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </AppLayout>
    );
}
