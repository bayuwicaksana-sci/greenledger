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
import MainLayout from '@/layouts/main-layout';
import config from '@/routes/config';
import type { BreadcrumbItem, CoaAccount, FiscalYear, Site } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import {
    getCoreRowModel,
    getFilteredRowModel,
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

interface Props {
    accounts: CoaAccount[];
    sites: Site[];
    fiscalYears: FiscalYear[];
    selectedFiscalYear: number;
    currentFiscalYearId: number;
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

export default function ChartOfAccounts({
    accounts,
    sites = [],
    fiscalYears = [],
    selectedFiscalYear,
    currentFiscalYearId,
}: Props) {
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
    const [selectedCategory, setSelectedCategory] = useState<string>('all');
    // const [viewMode, setViewMode] = useState<'table' | 'tree'>('table');

    const [importDialogOpen, setImportDialogOpen] = useState(false);
    const [templateDialogOpen, setTemplateDialogOpen] = useState(false);

    const handleFiscalYearChange = (value: string) => {
        router.get(
            config.coa.index(),
            { fiscal_year: value },
            { preserveScroll: true },
        );
    };

    const handleDialogSuccess = () => {
        router.reload();
    };

    const filteredAccounts = useMemo(() => {
        return accounts.filter((a) => {
            if (
                selectedSiteId !== 'all' &&
                a.site_id.toString() !== selectedSiteId
            )
                return false;
            if (selectedCategory !== 'all' && a.category !== selectedCategory)
                return false;
            return true;
        });
    }, [accounts, selectedSiteId, selectedCategory]);

    const columns: ColumnDef<CoaAccount>[] = [
        {
            accessorKey: 'account_code',
            enableHiding: false,
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
            accessorKey: 'category',
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === 'asc')
                    }
                >
                    Category
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const category = row.getValue<string>('category');
                return (
                    <span
                        className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                            category === 'PROGRAM'
                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                        }`}
                    >
                        {category === 'PROGRAM' ? 'Program' : 'Non-Program'}
                    </span>
                );
            },
        },
        {
            id: 'fiscal_year',
            accessorKey: 'fiscal_year',
            header: 'Fiscal Year',
            cell: ({ row }) => {
                const account = row.original;
                const fyYear = account.fiscal_year?.year || selectedFiscalYear;
                const isCurrent =
                    account.fiscal_year_id === currentFiscalYearId;
                const isPast = account.fiscal_year_id < currentFiscalYearId;

                return (
                    <div className="flex items-center gap-2">
                        <span>{fyYear}</span>
                        {isCurrent && (
                            <span className="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Current
                            </span>
                        )}
                        {isPast && (
                            <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                Past
                            </span>
                        )}
                    </div>
                );
            },
        },
        {
            accessorKey: 'is_active',
            header: 'Status',
            cell: ({ row }) => {
                const isActive = row.getValue<boolean>('is_active');
                const approvalStatus = row.original.approval_status;

                if (approvalStatus === 'pending_approval') {
                    return (
                        <span className="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                            Pending
                        </span>
                    );
                }
                if (approvalStatus === 'draft') {
                    return (
                        <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                            Draft
                        </span>
                    );
                }
                if (approvalStatus === 'rejected') {
                    return (
                        <span className="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                            Rejected
                        </span>
                    );
                }

                return isActive ? (
                    <span className="text-green-600">Active</span>
                ) : (
                    <span className="text-gray-500">Inactive</span>
                );
            },
        },
        {
            id: 'utilization',
            header: () => <span className="block text-right">Utilization</span>,
            cell: ({ row }) => {
                const budget = row.original.allocated_budget;
                const actual = row.original.actual_amount;
                if (budget <= 0)
                    return (
                        <span className="block text-right text-muted-foreground">
                            —
                        </span>
                    );
                const pct = Math.round((actual / budget) * 100);
                const colorClass =
                    pct > 90
                        ? 'bg-red-500'
                        : pct > 70
                          ? 'bg-amber-500'
                          : 'bg-green-500';
                return (
                    <div className="flex items-center justify-end gap-2">
                        <div className="h-2 w-16 overflow-hidden rounded-full bg-muted">
                            <div
                                className={`h-full ${colorClass}`}
                                style={{ width: `${Math.min(pct, 100)}%` }}
                            />
                        </div>
                        <span className="text-right text-xs">{pct}%</span>
                    </div>
                );
            },
        },
        {
            accessorKey: 'allocated_budget',
            header: () => <span className="block text-right">Budget</span>,
            cell: ({ row }) => (
                <span className="block text-right">
                    {formatCurrency(row.getValue<number>('allocated_budget'))}
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
            enableHiding: false,
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
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onGlobalFilterChange: setGlobalFilter,
        state: {
            sorting,
            columnVisibility,
            globalFilter,
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
                        <Select
                            value={selectedFiscalYear.toString()}
                            onValueChange={handleFiscalYearChange}
                        >
                            <SelectTrigger className="w-[180px]">
                                <SelectValue placeholder="Fiscal Year" />
                            </SelectTrigger>
                            <SelectContent>
                                {fiscalYears.map((fy) => (
                                    <SelectItem
                                        key={fy.id}
                                        value={fy.year.toString()}
                                    >
                                        {fy.year}
                                        {fy.is_closed ? ' (Closed)' : ''}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <Select
                            value={selectedCategory}
                            onValueChange={setSelectedCategory}
                        >
                            <SelectTrigger className="w-[180px]">
                                <SelectValue placeholder="All Categories" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    All Categories
                                </SelectItem>
                                <SelectItem value="PROGRAM">Program</SelectItem>
                                <SelectItem value="NON_PROGRAM">
                                    Non-Program
                                </SelectItem>
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
                        {/* <div className="flex rounded-md border">
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
                        </div> */}
                    </div>

                    {filteredAccounts.length === 0 ? (
                        <div className="rounded-md border bg-muted/50 p-12">
                            <div className="flex flex-col items-center justify-center gap-4 text-center">
                                <div className="rounded-full bg-muted p-3">
                                    <svg
                                        className="h-6 w-6 text-muted-foreground"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold">
                                        No accounts for this fiscal year
                                    </h3>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        Get started by creating accounts or
                                        copying from a previous fiscal year.
                                    </p>
                                </div>
                                <div className="mt-2 flex gap-3">
                                    <Link href={config.coa.create()}>
                                        <Button>
                                            <Plus className="mr-2 h-4 w-4" />
                                            Create Account
                                        </Button>
                                    </Link>
                                    {fiscalYears.length > 1 && (
                                        <Button
                                            variant="outline"
                                            onClick={() =>
                                                setTemplateDialogOpen(true)
                                            }
                                        >
                                            <LayoutTemplate className="mr-2 h-4 w-4" />
                                            Copy from Previous Year
                                        </Button>
                                    )}
                                </div>
                            </div>
                        </div>
                    ) : (
                        <CoaTreeView
                            accounts={filteredAccounts}
                            sites={sites}
                            columnVisibility={columnVisibility}
                            currentFiscalYearId={currentFiscalYearId}
                        />
                    )}
                    {/* {viewMode === 'tree' ? (
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
                    )} */}
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
