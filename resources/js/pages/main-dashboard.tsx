import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import MainLayout from '@/layouts/main-layout';
import { mainDashboard } from '@/routes';
import { show } from '@/routes/approvals';
import type { BreadcrumbItem, SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import {
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { format } from 'date-fns';
import { ClipboardCheck } from 'lucide-react';
import { useMemo } from 'react';

interface ApprovalInstance {
    id: number;
    approvable_type: string;
    status_value: string;
    status_label: string;
    submitted_at: string | null;
    submitted_by: {
        id: number;
        name: string;
    } | null;
    current_step: {
        id: number;
        name: string;
    } | null;
    approvable: {
        id: number;
        account_name?: string;
        name?: string;
        title?: string;
        site?: {
            site_code: string;
            site_name: string;
        };
    } | null;
    workflow: {
        id: number;
        name: string;
    };
}

interface PageProps {
    pendingApprovals: ApprovalInstance[];
}

const statusVariantMap: Record<
    string,
    'default' | 'secondary' | 'destructive' | 'outline'
> = {
    pending: 'outline',
    pending_approval: 'secondary',
    approved: 'default',
    rejected: 'destructive',
    changes_requested: 'outline',
    cancelled: 'secondary',
};

export default function MainDashboard({ pendingApprovals }: PageProps) {
    const { auth } = usePage<SharedData>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: mainDashboard().url,
        },
    ];

    const getApprovableName = (instance: ApprovalInstance): string => {
        if (!instance.approvable) return 'Unknown Item';

        return (
            instance.approvable.account_name ||
            instance.approvable.name ||
            instance.approvable.title ||
            `ID: ${instance.approvable.id}`
        );
    };

    const columns = useMemo<ColumnDef<ApprovalInstance>[]>(
        () => [
            {
                accessorKey: 'workflow',
                header: 'Type',
                cell: ({ row }) => (
                    <div className="font-medium">
                        {row.original.workflow.name}
                    </div>
                ),
            },
            {
                accessorKey: 'approvable',
                header: 'Item',
                cell: ({ row }) => (
                    <div className="font-medium">
                        {getApprovableName(row.original)}
                    </div>
                ),
            },
            {
                id: 'site',
                header: 'Site',
                cell: ({ row }) => (
                    <div className="text-muted-foreground">
                        {row.original.approvable?.site?.site_name ?? '—'}
                    </div>
                ),
            },
            {
                accessorKey: 'submitted_by',
                header: 'Submitted By',
                cell: ({ row }) => (
                    <div>{row.original.submitted_by?.name ?? 'Unknown'}</div>
                ),
            },
            {
                accessorKey: 'status_value',
                header: 'Status',
                cell: ({ row }) => (
                    <Badge
                        variant={
                            statusVariantMap[row.original.status_value] ??
                            'outline'
                        }
                    >
                        {row.original.status_label}
                    </Badge>
                ),
            },
            {
                accessorKey: 'submitted_at',
                header: 'Submitted',
                cell: ({ row }) =>
                    row.original.submitted_at
                        ? format(
                              new Date(row.original.submitted_at),
                              'MMM dd, yyyy',
                          )
                        : '—',
            },
            {
                id: 'actions',
                header: () => <div className="text-right">Action</div>,
                cell: ({ row }) => {
                    const siteCode = row.original.approvable?.site?.site_code;
                    if (!siteCode) return null;

                    return (
                        <div className="flex justify-end">
                            <Link
                                href={show.url({
                                    site: siteCode,
                                    approvalInstance: row.original.id,
                                })}
                                className="rounded-md border bg-background px-3 py-1 text-sm transition-colors hover:bg-muted"
                            >
                                Review
                            </Link>
                        </div>
                    );
                },
            },
        ],
        [],
    );

    const table = useReactTable({
        data: pendingApprovals,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <PageLayout>
                <PageHeader
                    pageTitle={`Halo 👋, ${auth.user.name}`}
                    pageSubtitle="Welcome to GreenLedger"
                />

                <Card>
                    <CardHeader className="flex flex-row items-center justify-between pb-2">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <ClipboardCheck className="h-5 w-5 text-muted-foreground" />
                            Pending Approvals
                        </CardTitle>
                        {pendingApprovals.length > 0 && (
                            <Badge variant="secondary">
                                {pendingApprovals.length}
                            </Badge>
                        )}
                    </CardHeader>
                    <CardContent className="p-0">
                        {table.getRowModel().rows.length === 0 ? (
                            <div className="flex h-48 flex-col items-center justify-center gap-2 px-4">
                                <ClipboardCheck className="h-10 w-10 text-muted-foreground opacity-50" />
                                <p className="text-sm text-muted-foreground">
                                    No pending approvals — you're all caught up.
                                </p>
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    {table
                                        .getHeaderGroups()
                                        .map((headerGroup) => (
                                            <TableRow key={headerGroup.id}>
                                                {headerGroup.headers.map(
                                                    (header) => (
                                                        <TableHead
                                                            key={header.id}
                                                        >
                                                            {header.isPlaceholder
                                                                ? null
                                                                : flexRender(
                                                                      header
                                                                          .column
                                                                          .columnDef
                                                                          .header,
                                                                      header.getContext(),
                                                                  )}
                                                        </TableHead>
                                                    ),
                                                )}
                                            </TableRow>
                                        ))}
                                </TableHeader>
                                <TableBody>
                                    {table.getRowModel().rows.map((row) => (
                                        <TableRow key={row.id}>
                                            {row
                                                .getVisibleCells()
                                                .map((cell) => (
                                                    <TableCell key={cell.id}>
                                                        {flexRender(
                                                            cell.column
                                                                .columnDef.cell,
                                                            cell.getContext(),
                                                        )}
                                                    </TableCell>
                                                ))}
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </PageLayout>
        </MainLayout>
    );
}
