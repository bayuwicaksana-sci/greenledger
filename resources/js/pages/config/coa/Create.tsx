import {
    bulkStore,
    index,
    store,
} from '@/actions/App/Http/Controllers/CoaAccountController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { Textarea } from '@/components/ui/textarea';
import MainLayout from '@/layouts/main-layout';
import { BreadcrumbItem, Site } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface ParentAccount {
    id: number;
    site_id: number;
    account_code: string;
    account_name: string;
    hierarchy_level: number;
}

interface ExistingCode {
    site_id: number;
    account_code: string;
}

interface CreateProps {
    sites: Site[];
    parents: ParentAccount[];
    existingCodes: ExistingCode[];
}

interface AccountFormData {
    _temp_id: string;
    site_id: string;
    account_code: string;
    account_name: string;
    account_type: 'REVENUE' | 'EXPENSE';
    short_description: string;
    parent_account_id: string;
    parent_temp_id: string;
    is_active: boolean;
    initial_budget: string;
}

// Parse the trailing number from a code like "klaten-expense-003" → 3
function parseTrailingNumber(code: string): number {
    const match = code.match(/-(\d+)$/);
    return match ? parseInt(match[1], 10) : 0;
}

// Build the prefix for a site + type: "klaten-expense-"
function buildPrefix(siteCode: string, accountType: string): string {
    return `${siteCode.toLowerCase()}-${accountType.toLowerCase()}-`;
}

export default function Create({ sites, parents, existingCodes }: CreateProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Chart of Accounts',
            href: index.url(),
        },
        {
            title: 'Create Account',
            href: '#',
        },
    ];

    const [bulkMode, setBulkMode] = useState(false);
    const [accounts, setAccounts] = useState<AccountFormData[]>([
        createEmptyAccount(),
    ]);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    function createEmptyAccount(): AccountFormData {
        return {
            _temp_id: `temp_${Date.now()}_${Math.random()}`,
            site_id: '',
            account_code: '',
            account_name: '',
            account_type: 'EXPENSE',
            short_description: '',
            parent_account_id: '',
            parent_temp_id: '',
            is_active: true,
            initial_budget: '0',
        };
    }

    // Compute the next auto-generated code for a given site + type,
    // taking into account both DB codes and codes already in the current batch.
    function generateNextCode(
        siteId: string,
        accountType: string,
        excludeIndex?: number,
    ): string {
        if (!siteId) return '';

        const site = sites.find((s) => s.id.toString() === siteId);
        if (!site) return '';

        const prefix = buildPrefix(site.site_code, accountType);

        // Max from existing DB codes matching this prefix + site
        const dbMax = existingCodes
            .filter(
                (c) =>
                    c.site_id.toString() === siteId &&
                    c.account_code.toLowerCase().startsWith(prefix),
            )
            .reduce(
                (max, c) => Math.max(max, parseTrailingNumber(c.account_code)),
                0,
            );

        // Max from other rows already in the batch
        const batchMax = accounts.reduce((max, acc, idx) => {
            if (idx === excludeIndex) return max;
            if (
                acc.site_id === siteId &&
                acc.account_code.toLowerCase().startsWith(prefix)
            ) {
                return Math.max(max, parseTrailingNumber(acc.account_code));
            }
            return max;
        }, 0);

        const nextNum = Math.max(dbMax, batchMax) + 1;
        return `${prefix}${nextNum.toString().padStart(3, '0')}`;
    }

    const handleAddAccount = () => {
        setAccounts((prev) => [...prev, createEmptyAccount()]);
    };

    const handleRemoveAccount = (index: number) => {
        if (accounts.length > 1) {
            setAccounts((prev) => prev.filter((_, i) => i !== index));
        }
    };

    const handleUpdateAccount = (
        index: number,
        field: keyof AccountFormData,
        value: string | boolean,
    ) => {
        setAccounts((prev) => {
            const updated = [...prev];
            updated[index] = { ...updated[index], [field]: value };
            return updated;
        });
    };

    // When site or type changes, regenerate the account code for that row.
    const handleSiteOrTypeChange = (
        index: number,
        field: 'site_id' | 'account_type',
        value: string,
    ) => {
        setAccounts((prev) => {
            const updated = [...prev];
            updated[index] = { ...updated[index], [field]: value };

            // Regenerate code based on the new site/type
            const siteId = field === 'site_id' ? value : updated[index].site_id;
            const accountType =
                field === 'account_type' ? value : updated[index].account_type;

            if (siteId) {
                const site = sites.find((s) => s.id.toString() === siteId);
                if (site) {
                    const prefix = buildPrefix(site.site_code, accountType);

                    const dbMax = existingCodes
                        .filter(
                            (c) =>
                                c.site_id.toString() === siteId &&
                                c.account_code.toLowerCase().startsWith(prefix),
                        )
                        .reduce(
                            (max, c) =>
                                Math.max(
                                    max,
                                    parseTrailingNumber(c.account_code),
                                ),
                            0,
                        );

                    const batchMax = updated.reduce((max, acc, idx) => {
                        if (idx === index) return max;
                        if (
                            acc.site_id === siteId &&
                            acc.account_code.toLowerCase().startsWith(prefix)
                        ) {
                            return Math.max(
                                max,
                                parseTrailingNumber(acc.account_code),
                            );
                        }
                        return max;
                    }, 0);

                    const nextNum = Math.max(dbMax, batchMax) + 1;
                    updated[index].account_code =
                        `${prefix}${nextNum.toString().padStart(3, '0')}`;
                }
            }

            return updated;
        });
    };

    // Single atomic update for parent selection — avoids stale-state
    // overwrite that happened when calling handleUpdateAccount twice.
    const handleParentChange = (index: number, val: string) => {
        setAccounts((prev) => {
            const updated = [...prev];
            if (val.startsWith('temp_')) {
                updated[index] = {
                    ...updated[index],
                    parent_temp_id: val,
                    parent_account_id: '',
                };
            } else if (val === 'none') {
                updated[index] = {
                    ...updated[index],
                    parent_temp_id: '',
                    parent_account_id: '',
                };
            } else {
                updated[index] = {
                    ...updated[index],
                    parent_account_id: val,
                    parent_temp_id: '',
                };
            }
            return updated;
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        if (bulkMode) {
            router.post(
                bulkStore.url(),
                { accounts },
                {
                    onSuccess: () => {
                        setProcessing(false);
                    },
                    onError: (errors) => {
                        setProcessing(false);
                        setErrors(errors);
                    },
                },
            );
        } else {
            router.post(store.url(), accounts[0], {
                onSuccess: () => {
                    setProcessing(false);
                },
                onError: (errors) => {
                    setProcessing(false);
                    setErrors(errors);
                },
            });
        }
    };

    // Available parents for a given row: existing DB parents for the same site,
    // plus earlier rows in the batch (which will be created first).
    const getAvailableParents = (currentIndex: number) => {
        const currentSiteId = accounts[currentIndex]?.site_id;

        const existingParents = parents
            .filter(
                (p) => !currentSiteId || p.site_id.toString() === currentSiteId,
            )
            .map((p) => ({
                id: p.id.toString(),
                label: `${p.account_code} - ${p.account_name}`,
            }));

        const batchParents = accounts
            .slice(0, currentIndex)
            .map((acc, idx) => ({
                id: acc._temp_id,
                label: acc.account_code
                    ? `${acc.account_code} - ${acc.account_name || 'unnamed'} (in batch)`
                    : `Account ${idx + 1} (in batch)`,
            }))
            .filter(
                (_, idx) =>
                    !currentSiteId || accounts[idx].site_id === currentSiteId,
            );

        return [...existingParents, ...batchParents];
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Account" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold">
                            Create COA Accounts
                        </h2>
                        <p className="text-sm text-muted-foreground">
                            {bulkMode
                                ? 'Create multiple accounts at once'
                                : 'Create a single account'}
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => {
                            setBulkMode(!bulkMode);
                            setAccounts([accounts[0] || createEmptyAccount()]);
                        }}
                    >
                        {bulkMode ? 'Single Mode' : 'Bulk Mode'}
                    </Button>
                </div>

                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                    {bulkMode ? (
                        <Card>
                            <CardHeader className="pb-3">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle>
                                            Accounts to Create
                                        </CardTitle>
                                        <CardDescription>
                                            Create multiple accounts in one
                                            submission. Account codes are
                                            auto-generated when you pick a site.
                                        </CardDescription>
                                    </div>
                                    <Button
                                        type="button"
                                        size="sm"
                                        onClick={handleAddAccount}
                                    >
                                        <Plus className="mr-2 h-4 w-4" />
                                        Add Account
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>#</TableHead>
                                                <TableHead>Site</TableHead>
                                                <TableHead>Code</TableHead>
                                                <TableHead>Name</TableHead>
                                                <TableHead>Type</TableHead>
                                                <TableHead>Parent</TableHead>
                                                <TableHead>
                                                    Description
                                                </TableHead>
                                                <TableHead>Active</TableHead>
                                                <TableHead></TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {accounts.map((account, index) => (
                                                <TableRow
                                                    key={account._temp_id}
                                                >
                                                    <TableCell>
                                                        {index + 1}
                                                    </TableCell>

                                                    {/* Site */}
                                                    <TableCell>
                                                        <Select
                                                            value={
                                                                account.site_id
                                                            }
                                                            onValueChange={(
                                                                val,
                                                            ) =>
                                                                handleSiteOrTypeChange(
                                                                    index,
                                                                    'site_id',
                                                                    val,
                                                                )
                                                            }
                                                        >
                                                            <SelectTrigger className="w-36">
                                                                <SelectValue placeholder="Site" />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                {sites.map(
                                                                    (site) => (
                                                                        <SelectItem
                                                                            key={
                                                                                site.id
                                                                            }
                                                                            value={site.id.toString()}
                                                                        >
                                                                            {
                                                                                site.site_name
                                                                            }
                                                                        </SelectItem>
                                                                    ),
                                                                )}
                                                            </SelectContent>
                                                        </Select>
                                                        {errors[
                                                            `accounts.${index}.site_id`
                                                        ] && (
                                                            <p className="text-xs text-destructive">
                                                                {
                                                                    errors[
                                                                        `accounts.${index}.site_id`
                                                                    ]
                                                                }
                                                            </p>
                                                        )}
                                                    </TableCell>

                                                    {/* Code — auto-generated, still editable */}
                                                    <TableCell>
                                                        <Input
                                                            value={
                                                                account.account_code
                                                            }
                                                            onChange={(e) =>
                                                                handleUpdateAccount(
                                                                    index,
                                                                    'account_code',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder="Auto"
                                                            className="w-36"
                                                        />
                                                        {errors[
                                                            `accounts.${index}.account_code`
                                                        ] && (
                                                            <p className="text-xs text-destructive">
                                                                {
                                                                    errors[
                                                                        `accounts.${index}.account_code`
                                                                    ]
                                                                }
                                                            </p>
                                                        )}
                                                    </TableCell>

                                                    {/* Name */}
                                                    <TableCell>
                                                        <Input
                                                            value={
                                                                account.account_name
                                                            }
                                                            onChange={(e) =>
                                                                handleUpdateAccount(
                                                                    index,
                                                                    'account_name',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder="Name"
                                                            className="w-48"
                                                        />
                                                        {errors[
                                                            `accounts.${index}.account_name`
                                                        ] && (
                                                            <p className="text-xs text-destructive">
                                                                {
                                                                    errors[
                                                                        `accounts.${index}.account_name`
                                                                    ]
                                                                }
                                                            </p>
                                                        )}
                                                    </TableCell>

                                                    {/* Type */}
                                                    <TableCell>
                                                        <Select
                                                            value={
                                                                account.account_type
                                                            }
                                                            onValueChange={(
                                                                val,
                                                            ) =>
                                                                handleSiteOrTypeChange(
                                                                    index,
                                                                    'account_type',
                                                                    val,
                                                                )
                                                            }
                                                        >
                                                            <SelectTrigger className="w-32">
                                                                <SelectValue />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem value="REVENUE">
                                                                    Revenue
                                                                </SelectItem>
                                                                <SelectItem value="EXPENSE">
                                                                    Expense
                                                                </SelectItem>
                                                            </SelectContent>
                                                        </Select>
                                                    </TableCell>

                                                    {/* Parent — uses single atomic handler */}
                                                    <TableCell>
                                                        <Select
                                                            value={
                                                                account.parent_temp_id ||
                                                                account.parent_account_id ||
                                                                'none'
                                                            }
                                                            onValueChange={(
                                                                val,
                                                            ) =>
                                                                handleParentChange(
                                                                    index,
                                                                    val,
                                                                )
                                                            }
                                                        >
                                                            <SelectTrigger className="w-52">
                                                                <SelectValue placeholder="None" />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem value="none">
                                                                    None
                                                                </SelectItem>
                                                                {getAvailableParents(
                                                                    index,
                                                                ).map(
                                                                    (
                                                                        parent,
                                                                    ) => (
                                                                        <SelectItem
                                                                            key={
                                                                                parent.id
                                                                            }
                                                                            value={
                                                                                parent.id
                                                                            }
                                                                        >
                                                                            {
                                                                                parent.label
                                                                            }
                                                                        </SelectItem>
                                                                    ),
                                                                )}
                                                            </SelectContent>
                                                        </Select>
                                                    </TableCell>

                                                    {/* Description */}
                                                    <TableCell>
                                                        <Input
                                                            value={
                                                                account.short_description
                                                            }
                                                            onChange={(e) =>
                                                                handleUpdateAccount(
                                                                    index,
                                                                    'short_description',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder="Description"
                                                            className="w-48"
                                                        />
                                                    </TableCell>

                                                    {/* Active */}
                                                    <TableCell>
                                                        <Select
                                                            value={
                                                                account.is_active
                                                                    ? 'true'
                                                                    : 'false'
                                                            }
                                                            onValueChange={(
                                                                val,
                                                            ) =>
                                                                handleUpdateAccount(
                                                                    index,
                                                                    'is_active',
                                                                    val ===
                                                                        'true',
                                                                )
                                                            }
                                                        >
                                                            <SelectTrigger className="w-24">
                                                                <SelectValue />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem value="true">
                                                                    Active
                                                                </SelectItem>
                                                                <SelectItem value="false">
                                                                    Inactive
                                                                </SelectItem>
                                                            </SelectContent>
                                                        </Select>
                                                    </TableCell>

                                                    {/* Remove */}
                                                    <TableCell>
                                                        {accounts.length >
                                                            1 && (
                                                            <Button
                                                                type="button"
                                                                variant="ghost"
                                                                size="sm"
                                                                onClick={() =>
                                                                    handleRemoveAccount(
                                                                        index,
                                                                    )
                                                                }
                                                            >
                                                                <Trash2 className="h-4 w-4" />
                                                            </Button>
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            </CardContent>
                        </Card>
                    ) : (
                        /* Single account creation form */
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div className="flex flex-col gap-4 md:col-span-2">
                                <Card>
                                    <CardHeader className="pb-3">
                                        <CardTitle>Account Details</CardTitle>
                                        <CardDescription>
                                            Basic information about the account.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="grid gap-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="account_code">
                                                Account Code
                                            </Label>
                                            <Input
                                                id="account_code"
                                                value={accounts[0].account_code}
                                                onChange={(e) =>
                                                    handleUpdateAccount(
                                                        0,
                                                        'account_code',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Auto-generated on site select"
                                            />
                                            {errors.account_code && (
                                                <p className="text-sm text-destructive">
                                                    {errors.account_code}
                                                </p>
                                            )}
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="account_name">
                                                Account Name
                                            </Label>
                                            <Input
                                                id="account_name"
                                                value={accounts[0].account_name}
                                                onChange={(e) =>
                                                    handleUpdateAccount(
                                                        0,
                                                        'account_name',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="e.g. Fertilizer Expenses"
                                            />
                                            {errors.account_name && (
                                                <p className="text-sm text-destructive">
                                                    {errors.account_name}
                                                </p>
                                            )}
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="short_description">
                                                Description
                                            </Label>
                                            <Textarea
                                                id="short_description"
                                                value={
                                                    accounts[0]
                                                        .short_description
                                                }
                                                onChange={(e) =>
                                                    handleUpdateAccount(
                                                        0,
                                                        'short_description',
                                                        e.target.value,
                                                    )
                                                }
                                                rows={4}
                                            />
                                            {errors.short_description && (
                                                <p className="text-sm text-destructive">
                                                    {errors.short_description}
                                                </p>
                                            )}
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="initial_budget">
                                                Initial Budget
                                            </Label>
                                            <Input
                                                id="initial_budget"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                value={
                                                    accounts[0].initial_budget
                                                }
                                                onChange={(e) =>
                                                    handleUpdateAccount(
                                                        0,
                                                        'initial_budget',
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {errors.initial_budget && (
                                                <p className="text-sm text-destructive">
                                                    {errors.initial_budget}
                                                </p>
                                            )}
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>

                            <div className="flex flex-col gap-4">
                                <Card className="h-full">
                                    <CardHeader className="pb-3">
                                        <CardTitle>Classification</CardTitle>
                                        <CardDescription>
                                            Organize this account.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="grid gap-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="site_id">
                                                Site
                                            </Label>
                                            <Select
                                                value={accounts[0].site_id}
                                                onValueChange={(val) =>
                                                    handleSiteOrTypeChange(
                                                        0,
                                                        'site_id',
                                                        val,
                                                    )
                                                }
                                            >
                                                <SelectTrigger id="site_id">
                                                    <SelectValue placeholder="Select site" />
                                                </SelectTrigger>
                                                <SelectContent>
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
                                            {errors.site_id && (
                                                <p className="text-sm text-destructive">
                                                    {errors.site_id}
                                                </p>
                                            )}
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="parent_account_id">
                                                Parent Account
                                            </Label>
                                            <Select
                                                value={
                                                    accounts[0]
                                                        .parent_account_id ||
                                                    'none'
                                                }
                                                onValueChange={(val) =>
                                                    handleUpdateAccount(
                                                        0,
                                                        'parent_account_id',
                                                        val === 'none'
                                                            ? ''
                                                            : val,
                                                    )
                                                }
                                            >
                                                <SelectTrigger id="parent_account_id">
                                                    <SelectValue placeholder="None" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="none">
                                                        None
                                                    </SelectItem>
                                                    {parents
                                                        .filter(
                                                            (p) =>
                                                                !accounts[0]
                                                                    .site_id ||
                                                                p.site_id.toString() ===
                                                                    accounts[0]
                                                                        .site_id,
                                                        )
                                                        .map((parent) => (
                                                            <SelectItem
                                                                key={parent.id}
                                                                value={parent.id.toString()}
                                                            >
                                                                {
                                                                    parent.account_code
                                                                }{' '}
                                                                -{' '}
                                                                {
                                                                    parent.account_name
                                                                }
                                                            </SelectItem>
                                                        ))}
                                                </SelectContent>
                                            </Select>
                                            {errors.parent_account_id && (
                                                <p className="text-sm text-destructive">
                                                    {errors.parent_account_id}
                                                </p>
                                            )}
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="account_type">
                                                Type
                                            </Label>
                                            <Select
                                                value={accounts[0].account_type}
                                                onValueChange={(val) =>
                                                    handleSiteOrTypeChange(
                                                        0,
                                                        'account_type',
                                                        val,
                                                    )
                                                }
                                            >
                                                <SelectTrigger id="account_type">
                                                    <SelectValue placeholder="Select type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="REVENUE">
                                                        Revenue
                                                    </SelectItem>
                                                    <SelectItem value="EXPENSE">
                                                        Expense
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                            {errors.account_type && (
                                                <p className="text-sm text-destructive">
                                                    {errors.account_type}
                                                </p>
                                            )}
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    )}

                    <div className="flex justify-end gap-2">
                        <Link href={index.url()}>
                            <Button type="button" variant="secondary">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing}>
                            {bulkMode
                                ? `Create ${accounts.length} Account(s)`
                                : 'Create Account'}
                        </Button>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
