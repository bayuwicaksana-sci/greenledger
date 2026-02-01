import { Head, Link, router, usePage } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import {
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import {
    Copy,
    Edit,
    Filter,
    GitBranch,
    MoreHorizontal,
    Plus,
    Power,
    PowerOff,
    Search,
    Trash2,
} from 'lucide-react';
import { useMemo, useState } from 'react';

import {
    create,
    deactivate,
    destroy,
    duplicate,
    edit,
    index,
    setActive,
} from '@/actions/App/Http/Controllers/ApprovalWorkflowController';
import { ConfirmationDialog } from '@/components/approval-workflows/ConfirmationDialog';
import { DuplicateDialog } from '@/components/approval-workflows/DuplicateDialog';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import type {
    ApprovalWorkflow,
    BreadcrumbItem,
    PaginatedResponse,
    SharedData,
} from '@/types';

interface Props {
    workflows: PaginatedResponse<ApprovalWorkflow>;
    filters: {
        search?: string;
        model_type?: string;
        is_active?: boolean;
    };
    modelTypes: string[];
}

export default function ApprovalWorkflowsIndex({
    workflows,
    filters,
    modelTypes,
}: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const { site_code } = usePage<SharedData>().props;

    // Dialog state
    const [selectedWorkflow, setSelectedWorkflow] =
        useState<ApprovalWorkflow | null>(null);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [duplicateDialogOpen, setDuplicateDialogOpen] = useState(false);
    const [activateDialogOpen, setActivateDialogOpen] = useState(false);
    const [deactivateDialogOpen, setDeactivateDialogOpen] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Administration', href: '#' },
        { title: 'Approval Workflows', href: '#' },
    ];

    const handleSearch = () => {
        router.get(index.url(site_code!), { search }, { preserveState: true });
    };

    const handleDelete = (workflow: ApprovalWorkflow) => {
        setSelectedWorkflow(workflow);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (selectedWorkflow) {
            router.delete(
                destroy.url({
                    approvalWorkflow: selectedWorkflow.id,
                    site: site_code!,
                }),
                {
                    preserveScroll: true,
                },
            );
        }
    };

    const handleDuplicate = (workflow: ApprovalWorkflow) => {
        setSelectedWorkflow(workflow);
        setDuplicateDialogOpen(true);
    };

    const confirmDuplicate = (newName: string) => {
        if (selectedWorkflow) {
            router.post(
                duplicate.url({
                    approvalWorkflow: selectedWorkflow.id,
                    site: site_code!,
                }),
                { name: newName },
                { preserveScroll: true },
            );
        }
    };

    const handleSetActive = (workflow: ApprovalWorkflow) => {
        setSelectedWorkflow(workflow);
        setActivateDialogOpen(true);
    };

    const confirmSetActive = () => {
        if (selectedWorkflow) {
            router.post(
                setActive.url({
                    approvalWorkflow: selectedWorkflow.id,
                    site: site_code!,
                }),
                {},
                { preserveScroll: true },
            );
        }
    };

    const handleDeactivate = (workflow: ApprovalWorkflow) => {
        setSelectedWorkflow(workflow);
        setDeactivateDialogOpen(true);
    };

    const confirmDeactivate = () => {
        if (selectedWorkflow) {
            router.post(
                deactivate.url({
                    approvalWorkflow: selectedWorkflow.id,
                    site: site_code!,
                }),
                {},
                { preserveScroll: true },
            );
        }
    };

    const columns = useMemo<ColumnDef<ApprovalWorkflow>[]>(
        () => [
            {
                accessorKey: 'name',
                header: 'Name',
                cell: ({ row }) => (
                    <div className="font-medium">{row.original.name}</div>
                ),
            },
            {
                accessorKey: 'model_type',
                header: 'Model Type',
                cell: ({ row }) => (
                    <code className="rounded bg-muted px-2 py-1 text-xs">
                        {row.original.model_type.split('\\').pop()}
                    </code>
                ),
            },
            {
                accessorKey: 'is_active',
                header: 'Status',
                cell: ({ row }) => (
                    <Badge
                        variant={
                            row.original.is_active ? 'default' : 'secondary'
                        }
                    >
                        {row.original.is_active ? 'Active' : 'Inactive'}
                    </Badge>
                ),
            },
            {
                accessorKey: 'steps_count',
                header: 'Steps',
                cell: ({ row }) => (
                    <div className="text-center">
                        {row.original.steps_count || 0}
                    </div>
                ),
            },
            {
                accessorKey: 'instances_count',
                header: 'Instances',
                cell: ({ row }) => (
                    <div className="text-center">
                        {row.original.instances_count || 0}
                    </div>
                ),
            },
            {
                accessorKey: 'created_at',
                header: 'Created',
                cell: ({ row }) =>
                    new Date(row.original.created_at).toLocaleDateString(),
            },
            {
                id: 'actions',
                header: () => <div className="text-right">Actions</div>,
                cell: ({ row }) => {
                    const workflow = row.original;
                    return (
                        <div className="flex justify-end">
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="ghost" size="sm">
                                        <MoreHorizontal className="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuLabel>
                                        Actions
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem asChild>
                                        <Link
                                            href={edit.url({
                                                approvalWorkflow: workflow.id,
                                                site: site_code!,
                                            })}
                                        >
                                            <Edit className="mr-2 h-4 w-4" />
                                            Edit
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        onClick={() =>
                                            handleDuplicate(workflow)
                                        }
                                    >
                                        <Copy className="mr-2 h-4 w-4" />
                                        Duplicate
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    {!workflow.is_active && (
                                        <DropdownMenuItem
                                            onClick={() =>
                                                handleSetActive(workflow)
                                            }
                                        >
                                            <Power className="mr-2 h-4 w-4" />
                                            Set as Active
                                        </DropdownMenuItem>
                                    )}
                                    {workflow.is_active && (
                                        <DropdownMenuItem
                                            onClick={() =>
                                                handleDeactivate(workflow)
                                            }
                                        >
                                            <PowerOff className="mr-2 h-4 w-4" />
                                            Deactivate
                                        </DropdownMenuItem>
                                    )}
                                    {!workflow.is_active && (
                                        <>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                onClick={() =>
                                                    handleDelete(workflow)
                                                }
                                                className="text-destructive"
                                            >
                                                <Trash2 className="mr-2 h-4 w-4" />
                                                Delete
                                            </DropdownMenuItem>
                                        </>
                                    )}
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    );
                },
            },
        ],
        [site_code],
    );

    const table = useReactTable({
        data: workflows.data,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Approval Workflows" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">
                            Approval Workflows
                        </h1>
                        <p className="text-muted-foreground">
                            Configure approval workflows for different models
                        </p>
                    </div>
                    <Link href={create.url(site_code!)}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Workflow
                        </Button>
                    </Link>
                </div>

                {/* Filters */}
                <div className="flex items-center gap-2">
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            placeholder="Search workflows..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) =>
                                e.key === 'Enter' && handleSearch()
                            }
                            className="pl-9"
                        />
                    </div>
                    <Button variant="outline" onClick={handleSearch}>
                        <Filter className="mr-2 h-4 w-4" />
                        Filter
                    </Button>
                </div>

                {/* Table */}
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
                            {table.getRowModel().rows.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={columns.length}
                                        className="h-24 text-center"
                                    >
                                        <div className="flex flex-col items-center justify-center gap-2">
                                            <GitBranch className="h-8 w-8 text-muted-foreground" />
                                            <p className="text-sm text-muted-foreground">
                                                No approval workflows found.
                                                Create your first workflow to
                                                get started.
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

            {/* Delete Confirmation Dialog */}
            <ConfirmationDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                title="Delete Workflow"
                description={`Are you sure you want to delete "${selectedWorkflow?.name}"? This action cannot be undone.`}
                onConfirm={confirmDelete}
                confirmText="Delete"
                variant="destructive"
            />

            {/* Duplicate Dialog */}
            <DuplicateDialog
                open={duplicateDialogOpen}
                onOpenChange={setDuplicateDialogOpen}
                defaultName={`${selectedWorkflow?.name || ''} (Copy)`}
                onConfirm={confirmDuplicate}
            />

            {/* Activate Confirmation Dialog */}
            <ConfirmationDialog
                open={activateDialogOpen}
                onOpenChange={setActivateDialogOpen}
                title="Set Active Workflow"
                description={`Set "${selectedWorkflow?.name}" as the active workflow for ${selectedWorkflow?.model_type?.split('\\').pop()}? This will deactivate any other workflow for this model type.`}
                onConfirm={confirmSetActive}
                confirmText="Set Active"
            />

            {/* Deactivate Confirmation Dialog */}
            <ConfirmationDialog
                open={deactivateDialogOpen}
                onOpenChange={setDeactivateDialogOpen}
                title="Deactivate Workflow"
                description={`Deactivate "${selectedWorkflow?.name}"? Models of this type will no longer use this workflow.`}
                onConfirm={confirmDeactivate}
                confirmText="Deactivate"
            />
        </AppLayout>
    );
}
