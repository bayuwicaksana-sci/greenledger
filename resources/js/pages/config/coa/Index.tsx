import { CoaTreeView } from '@/components/config/coa/CoaTreeView';
import { ImportDialog } from '@/components/config/coa/ImportDialog';
import { TemplateDialog } from '@/components/config/coa/TemplateDialog';
import PageAction from '@/components/page/page-action';
import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
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
import MainLayout from '@/layouts/main-layout';
import config from '@/routes/config';
import { BreadcrumbItem, Site } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
    type ColumnDef,
    type SortingState,
    type VisibilityState,
} from '@tanstack/react-table';
import {
    ArrowUpDown,
    ChevronDown,
    Edit,
    LayoutTemplate,
    Plus,
    Search,
    Trash2,
    Upload,
} from 'lucide-react';
import { useMemo, useState } from 'react';

interface CoaAccount {
    id: number;
    site_id: number;
    account_code: string;
    account_name: string;
    account_type: 'REVENUE' | 'EXPENSE';
    short_description?: string;
    is_active: boolean;
    hierarchy_level: number;
    parent_account_id: number | null;
    initial_budget: number;
    actual_amount: number;
    balance: number;
    site?: Site;
}

interface Props {
    accounts: CoaAccount[];
    sites: Site[];
}

function formatCurrency(value: number): string {
    if (value === 0) {
        return '—';
    }
    return value.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

export default function ChartOfAccounts({ accounts, sites = [] }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Chart of Accounts',
            href: '#',
        },
    ];

    const [sorting, setSorting] = useState<SortingState>([]);
    const [columnVisibility, setColumnVisibility] = useState<VisibilityState>(
        {},
    );
    const [globalFilter, setGlobalFilter] = useState('');
    const [selectedSiteId, setSelectedSiteId] = useState<string>('all');
    const [viewMode, setViewMode] = useState<'table' | 'tree'>('table');

    const [importDialogOpen, setImportDialogOpen] = useState(false);
    const [templateDialogOpen, setTemplateDialogOpen] = useState(false);

    const handleDialogSuccess = () => {
        router.reload();
    };

    const filteredAccounts = useMemo(() => {
        if (selectedSiteId === 'all') {
            return accounts;
        }
        return accounts.filter((a) => a.site_id.toString() === selectedSiteId);
    }, [accounts, selectedSiteId]);

    const columns: ColumnDef<CoaAccount>[] = [
        {
            accessorKey: 'account_code',
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
                <span className="font-medium">
                    {row.getValue('account_code')}
                </span>
            ),
        },
        {
            accessorKey: 'account_name',
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
        },
        {
            accessorKey: 'account_type',
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === 'asc')
                    }
                >
                    Type
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const type = row.getValue<string>('account_type');
                return (
                    <span
                        className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                            type === 'REVENUE'
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                        }`}
                    >
                        {type}
                    </span>
                );
            },
        },
        {
            id: 'site',
            accessorKey: 'site',
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === 'asc')
                    }
                >
                    Site
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const account = row.original;
                return <span>{account.site?.site_code || '—'}</span>;
            },
            sortingFn: (a, b) => {
                const siteA = a.original.site?.site_code || '';
                const siteB = b.original.site?.site_code || '';
                return siteA.localeCompare(siteB);
            },
        },
        {
            accessorKey: 'is_active',
            header: 'Status',
            cell: ({ row }) => {
                const isActive = row.getValue<boolean>('is_active');
                return isActive ? (
                    <span className="text-green-600">Active</span>
                ) : (
                    <span className="text-gray-500">Inactive</span>
                );
            },
        },
        {
            accessorKey: 'initial_budget',
            header: () => <span className="block text-right">Budget</span>,
            cell: ({ row }) => (
                <span className="block text-right">
                    {formatCurrency(row.getValue<number>('initial_budget'))}
                </span>
            ),
        },
        {
            accessorKey: 'actual_amount',
            header: () => <span className="block text-right">Actual</span>,
            cell: ({ row }) => {
                const amount = row.getValue<number>('actual_amount');
                const type = row.original.account_type;
                const colorClass =
                    amount === 0
                        ? ''
                        : type === 'EXPENSE'
                          ? 'text-red-600'
                          : 'text-green-600';
                return (
                    <span className={`block text-right ${colorClass}`}>
                        {formatCurrency(amount)}
                    </span>
                );
            },
        },
        {
            accessorKey: 'balance',
            header: () => <span className="block text-right">Balance</span>,
            cell: ({ row }) => {
                const amount = row.getValue<number>('balance');
                return (
                    <span className="block text-right">
                        {formatCurrency(amount)}
                    </span>
                );
            },
        },
        {
            id: 'actions',
            header: 'Actions',
            cell: ({ row }) => {
                const account = row.original;
                return (
                    <div className="flex justify-end gap-2">
                        <Link href={config.coa.edit(account.id)}>
                            <Button variant="ghost" size="icon">
                                <Edit className="h-4 w-4" />
                            </Button>
                        </Link>
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => {
                                if (
                                    confirm(
                                        'Are you sure you want to delete this account?',
                                    )
                                ) {
                                    router.delete(
                                        config.coa.destroy(account.id),
                                    );
                                }
                            }}
                        >
                            <Trash2 className="h-4 w-4 text-destructive" />
                        </Button>
                    </div>
                );
            },
        },
    ];

    const table = useReactTable({
        data: filteredAccounts,
        columns,
        onSortingChange: setSorting,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onGlobalFilterChange: setGlobalFilter,
        state: {
            sorting,
            columnVisibility,
            globalFilter,
        },
        initialState: {
            pagination: {
                pageSize: 10,
            },
        },
    });

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Chart of Accounts" />
            <PageLayout>
                <PageHeader
                    pageTitle="Chart of Accounts"
                    pageSubtitle="Manage Chart of Accounts"
                >
                    <PageAction>
                        <Button
                            variant="outline"
                            onClick={() => setTemplateDialogOpen(true)}
                        >
                            <LayoutTemplate className="mr-2 h-4 w-4" />
                            Templates
                        </Button>
                        <Button
                            variant="outline"
                            onClick={() => setImportDialogOpen(true)}
                        >
                            <Upload className="mr-2 h-4 w-4" />
                            Import CSV
                        </Button>
                        <Link href={config.coa.create()}>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Account
                            </Button>
                        </Link>
                    </PageAction>
                </PageHeader>
                <div className="flex flex-col gap-4">
                    <div className="flex items-center gap-4">
                        <div className="relative max-w-sm flex-1">
                            <Search className="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                placeholder="Search accounts..."
                                className="pl-8"
                                value={globalFilter}
                                onChange={(e) =>
                                    setGlobalFilter(e.target.value)
                                }
                            />
                        </div>
                        <Select
                            value={selectedSiteId}
                            onValueChange={setSelectedSiteId}
                        >
                            <SelectTrigger className="w-[180px]">
                                <SelectValue placeholder="All Sites" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Sites</SelectItem>
                                {sites.map((site) => (
                                    <SelectItem
                                        key={site.id}
                                        value={site.id.toString()}
                                    >
                                        {site.site_name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" className="ml-auto">
                                    Columns{' '}
                                    <ChevronDown className="ml-2 h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                {table
                                    .getAllColumns()
                                    .filter((column) => column.getCanHide())
                                    .map((column) => (
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
                                    ))}
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <div className="flex rounded-md border">
                            <Button
                                variant={
                                    viewMode === 'table' ? 'default' : 'ghost'
                                }
                                size="sm"
                                className="rounded-r-none"
                                onClick={() => setViewMode('table')}
                            >
                                Table
                            </Button>
                            <Button
                                variant={
                                    viewMode === 'tree' ? 'default' : 'ghost'
                                }
                                size="sm"
                                className="rounded-l-none"
                                onClick={() => setViewMode('tree')}
                            >
                                Tree
                            </Button>
                        </div>
                    </div>

                    {viewMode === 'tree' ? (
                        <CoaTreeView
                            accounts={filteredAccounts}
                            sites={sites}
                        />
                    ) : (
                        <>
                            <div className="rounded-md border">
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
                                        {table.getRowModel().rows?.length ? (
                                            table
                                                .getRowModel()
                                                .rows.map((row) => (
                                                    <TableRow
                                                        key={row.id}
                                                        className={
                                                            row.original
                                                                .parent_account_id ===
                                                            null
                                                                ? 'font-bold'
                                                                : ''
                                                        }
                                                    >
                                                        {row
                                                            .getVisibleCells()
                                                            .map((cell) => (
                                                                <TableCell
                                                                    key={
                                                                        cell.id
                                                                    }
                                                                >
                                                                    {flexRender(
                                                                        cell
                                                                            .column
                                                                            .columnDef
                                                                            .cell,
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
                                                    No accounts found.
                                                </TableCell>
                                            </TableRow>
                                        )}
                                    </TableBody>
                                </Table>
                            </div>

                            <div className="flex items-center justify-between space-x-2 py-4">
                                <div className="flex-1 text-sm text-muted-foreground">
                                    {table.getFilteredRowModel().rows.length}{' '}
                                    account(s) total
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
                                        Page{' '}
                                        {table.getState().pagination.pageIndex +
                                            1}{' '}
                                        of {table.getPageCount()}
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
                        </>
                    )}
                </div>
            </PageLayout>

            <ImportDialog
                open={importDialogOpen}
                onOpenChange={setImportDialogOpen}
                onSuccess={handleDialogSuccess}
            />

            <TemplateDialog
                open={templateDialogOpen}
                onOpenChange={setTemplateDialogOpen}
                onSuccess={handleDialogSuccess}
                sites={sites}
            />
        </MainLayout>
    );
}
