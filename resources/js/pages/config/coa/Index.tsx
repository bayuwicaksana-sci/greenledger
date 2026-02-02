import PageAction from '@/components/page/page-action';
import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import { Button } from '@/components/ui/button';
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
import { BreadcrumbItem, PaginatedResponse, Site } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Edit, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';

// Declare route globally or locally
declare var route: any;

interface CoaAccount {
    id: number;
    site_id: number;
    account_code: string;
    account_name: string;
    account_type: 'REVENUE' | 'EXPENSE';
    short_description?: string;
    is_active: boolean;
    hierarchy_level: number;
    site?: Site;
}

interface Props {
    accounts: PaginatedResponse<CoaAccount>;
    filters: {
        search?: string;
        site_id?: string;
    };
    sites?: Site[]; // Assuming we pass sites for the filter and form
}

export default function ChartOfAccounts({
    accounts,
    filters,
    sites = [],
}: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Chart of Accounts',
            href: '#',
        },
    ];

    const [openForm, setOpenForm] = useState(false);
    const [selectedAccount, setSelectedAccount] = useState<CoaAccount | null>(
        null,
    );
    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    // Debounce search
    useEffect(() => {
        const timer = setTimeout(() => {
            // Only trigger search if searchTerm has actually changed from the current filter
            // and is not empty when the filter is also empty (to avoid unnecessary requests)
            if (searchTerm !== (filters.search || '')) {
                router.get(
                    config.coa.index(),
                    { ...filters, search: searchTerm || undefined }, // Pass undefined if searchTerm is empty
                    { preserveState: true, preserveScroll: true },
                );
            }
        }, 300);

        return () => clearTimeout(timer);
    }, [searchTerm, filters.search]); // Depend on filters.search to re-evaluate if external filter changes

    const handleSearch = (term: string) => {
        setSearchTerm(term);
    };

    const handleSiteFilter = (siteId: string) => {
        router.get(
            route('config.coa.index'),
            { ...filters, site_id: siteId === 'all' ? undefined : siteId },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Chart of Accounts" />
            <PageLayout>
                <PageHeader
                    pageTitle="Chart of Accounts"
                    pageSubtitle="Manage Chart of Accounts"
                >
                    <PageAction>
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
                                defaultValue={filters.search}
                                onChange={(e) => handleSearch(e.target.value)}
                            />
                        </div>
                        <Select
                            defaultValue={filters.site_id || 'all'}
                            onValueChange={handleSiteFilter}
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
                    </div>

                    <div className="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Code</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Site</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">
                                        Action
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {accounts.data.length > 0 ? (
                                    accounts.data.map((account) => (
                                        <TableRow key={account.id}>
                                            <TableCell className="font-medium">
                                                {account.account_code}
                                            </TableCell>
                                            <TableCell>
                                                <div
                                                    style={{
                                                        paddingLeft: `${(account.hierarchy_level - 1) * 20}px`,
                                                    }}
                                                >
                                                    {account.account_name}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <span
                                                    className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                        account.account_type ===
                                                        'REVENUE'
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                                    }`}
                                                >
                                                    {account.account_type}
                                                </span>
                                            </TableCell>
                                            <TableCell>
                                                {account.site?.site_code || '-'}
                                            </TableCell>
                                            <TableCell>
                                                {account.is_active ? (
                                                    <span className="text-green-600">
                                                        Active
                                                    </span>
                                                ) : (
                                                    <span className="text-gray-500">
                                                        Inactive
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link
                                                        href={config.coa.edit(
                                                            account.id,
                                                        )}
                                                    >
                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                        >
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
                                                                    config.coa.destroy(
                                                                        account.id,
                                                                    ),
                                                                );
                                                            }
                                                        }}
                                                    >
                                                        <Trash2 className="h-4 w-4 text-destructive" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell
                                            colSpan={6}
                                            className="h-24 text-center"
                                        >
                                            No accounts found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </PageLayout>
        </MainLayout>
    );
}
