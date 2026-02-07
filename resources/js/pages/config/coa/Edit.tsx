import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import {
    index,
    update,
} from '@/actions/App/Http/Controllers/CoaAccountController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import MainLayout from '@/layouts/main-layout';
import type { BreadcrumbItem, Site } from '@/types';

interface ParentAccount {
    id: number;
    account_code: string;
    account_name: string;
    hierarchy_level: number;
}

interface CoaAccount {
    id: number;
    site_id: number;
    account_code: string;
    account_name: string;
    abbreviation?: string;
    account_type: 'ASSET' | 'LIABILITY' | 'EQUITY' | 'REVENUE' | 'EXPENSE';
    short_description?: string;
    parent_account_id?: number | null;
    is_active: boolean;
    budget_control: boolean;
    initial_budget: number;
    first_transaction_at?: string | null;
    category?: string;
    sub_category?: string | null;
    typical_usage?: string | null;
    tax_applicable?: boolean;
    approval_status?: string | null;
}

const SUB_CATEGORY_OPTIONS: Record<string, string[]> = {
    PROGRAM: ['Research', 'Knowledge'],
    NON_PROGRAM: ['Administrative', 'Financial', 'Operational', 'Research', 'Knowledge'],
};

interface EditProps {
    account: CoaAccount;
    sites: Site[];
    parents: ParentAccount[];
}

export default function Edit({ account, sites, parents }: EditProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Chart of Accounts',
            href: index.url(),
        },
        {
            title: 'Edit Account',
            href: '#',
        },
    ];

    const isLocked = !!account.first_transaction_at;

    const { data, setData, put, processing, errors } = useForm({
        site_id: account.site_id.toString(),
        account_code: account.account_code,
        account_name: account.account_name,
        abbreviation: account.abbreviation || '',
        account_type: account.account_type,
        short_description: account.short_description || '',
        parent_account_id: account.parent_account_id
            ? account.parent_account_id.toString()
            : '',
        is_active: account.is_active,
        budget_control: account.budget_control,
        initial_budget: account.initial_budget.toString(),
        category: account.category || 'NON_PROGRAM',
        sub_category: account.sub_category || '',
        typical_usage: account.typical_usage || '',
        tax_applicable: account.tax_applicable || false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update.url(account.id));
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Account" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <div className="flex items-center gap-3">
                            <h1 className="text-3xl font-bold">Edit Account</h1>
                            {account.approval_status && (
                                <span
                                    className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                        account.approval_status === 'approved'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                            : account.approval_status === 'pending_approval'
                                              ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300'
                                              : account.approval_status === 'rejected'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                                : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                    }`}
                                >
                                    {account.approval_status === 'approved' ? 'Approved' :
                                     account.approval_status === 'pending_approval' ? 'Pending Approval' :
                                     account.approval_status === 'rejected' ? 'Rejected' : 'Draft'}
                                </span>
                            )}
                        </div>
                        <p className="text-muted-foreground">
                            Update account details and classification
                        </p>
                    </div>
                    <Link href={index.url()}>
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back
                        </Button>
                    </Link>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Section 1: Account Details */}
                    <div className="rounded-lg border p-6">
                        <h2 className="mb-4 text-lg font-semibold">
                            Account Details
                        </h2>
                        <div className="space-y-4">
                            {/* Account Code */}
                            <div className="space-y-2">
                                <div className="flex items-center gap-2">
                                    <Label htmlFor="account_code">
                                        Account Code
                                    </Label>
                                    {isLocked && (
                                        <span className="rounded-md bg-muted px-2 py-0.5 text-xs text-muted-foreground">
                                            Locked
                                        </span>
                                    )}
                                </div>
                                <Input
                                    id="account_code"
                                    value={data.account_code}
                                    onChange={(e) =>
                                        setData(
                                            'account_code',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="e.g. 5211"
                                    disabled={isLocked}
                                />
                                {errors.account_code && (
                                    <p className="text-sm text-destructive">
                                        {errors.account_code}
                                    </p>
                                )}
                                {isLocked && (
                                    <p className="text-xs text-muted-foreground">
                                        Account code cannot be changed after
                                        transactions have been recorded.
                                    </p>
                                )}
                            </div>

                            {/* Account Name */}
                            <div className="space-y-2">
                                <Label htmlFor="account_name">
                                    Account Name
                                </Label>
                                <Input
                                    id="account_name"
                                    value={data.account_name}
                                    onChange={(e) =>
                                        setData(
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

                            {/* Abbreviation */}
                            <div className="space-y-2">
                                <Label htmlFor="abbreviation">
                                    Abbreviation
                                </Label>
                                <Input
                                    id="abbreviation"
                                    value={data.abbreviation}
                                    onChange={(e) =>
                                        setData(
                                            'abbreviation',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Optional"
                                />
                                {errors.abbreviation && (
                                    <p className="text-sm text-destructive">
                                        {errors.abbreviation}
                                    </p>
                                )}
                            </div>

                            {/* Description */}
                            <div className="space-y-2">
                                <Label htmlFor="short_description">
                                    Description
                                </Label>
                                <Textarea
                                    id="short_description"
                                    value={data.short_description}
                                    onChange={(e) =>
                                        setData(
                                            'short_description',
                                            e.target.value,
                                        )
                                    }
                                    rows={3}
                                />
                                {errors.short_description && (
                                    <p className="text-sm text-destructive">
                                        {errors.short_description}
                                    </p>
                                )}
                            </div>

                            {/* Initial Budget */}
                            <div className="space-y-2">
                                <Label htmlFor="initial_budget">
                                    Initial Budget
                                </Label>
                                <Input
                                    id="initial_budget"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={data.initial_budget}
                                    onChange={(e) =>
                                        setData(
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
                        </div>
                    </div>

                    {/* Section 2: Classification */}
                    <div className="rounded-lg border p-6">
                        <h2 className="mb-4 text-lg font-semibold">
                            Classification
                        </h2>
                        <div className="space-y-4">
                            {/* Site (always disabled) */}
                            <div className="space-y-2">
                                <Label htmlFor="site_id">Site</Label>
                                <Select
                                    value={data.site_id}
                                    onValueChange={(val) =>
                                        setData('site_id', val)
                                    }
                                    disabled={true}
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
                            </div>

                            {/* Parent Account */}
                            <div className="space-y-2">
                                <Label htmlFor="parent_account_id">
                                    Parent Account
                                </Label>
                                <Select
                                    value={
                                        data.parent_account_id || 'none'
                                    }
                                    onValueChange={(val) =>
                                        setData(
                                            'parent_account_id',
                                            val === 'none' ? '' : val,
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
                                        {parents.map((parent) => (
                                            <SelectItem
                                                key={parent.id}
                                                value={parent.id.toString()}
                                            >
                                                {parent.account_code} -{' '}
                                                {parent.account_name}
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

                            {/* Account Type */}
                            <div className="space-y-2">
                                <div className="flex items-center gap-2">
                                    <Label htmlFor="account_type">
                                        Account Type
                                    </Label>
                                    {isLocked && (
                                        <span className="rounded-md bg-muted px-2 py-0.5 text-xs text-muted-foreground">
                                            Locked
                                        </span>
                                    )}
                                </div>
                                <Select
                                    value={data.account_type}
                                    onValueChange={(val) =>
                                        setData(
                                            'account_type',
                                            val as
                                                | 'ASSET'
                                                | 'LIABILITY'
                                                | 'EQUITY'
                                                | 'REVENUE'
                                                | 'EXPENSE',
                                        )
                                    }
                                    disabled={isLocked}
                                >
                                    <SelectTrigger id="account_type">
                                        <SelectValue placeholder="Select type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="ASSET">
                                            Asset
                                        </SelectItem>
                                        <SelectItem value="LIABILITY">
                                            Liability
                                        </SelectItem>
                                        <SelectItem value="EQUITY">
                                            Equity
                                        </SelectItem>
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
                                {isLocked && (
                                    <p className="text-xs text-muted-foreground">
                                        Account type cannot be changed after
                                        transactions have been recorded.
                                    </p>
                                )}
                            </div>

                            {/* Status */}
                            <div className="space-y-2">
                                <Label htmlFor="is_active">Status</Label>
                                <Select
                                    value={
                                        data.is_active ? 'true' : 'false'
                                    }
                                    onValueChange={(val) =>
                                        setData('is_active', val === 'true')
                                    }
                                >
                                    <SelectTrigger id="is_active">
                                        <SelectValue placeholder="Select status" />
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
                                {errors.is_active && (
                                    <p className="text-sm text-destructive">
                                        {errors.is_active}
                                    </p>
                                )}
                            </div>

                            {/* Budget Control */}
                            <div className="space-y-2">
                                <Label htmlFor="budget_control">
                                    Budget Control
                                </Label>
                                <div className="flex items-center space-x-2">
                                    <Switch
                                        id="budget_control"
                                        checked={data.budget_control}
                                        onCheckedChange={(val) =>
                                            setData('budget_control', val)
                                        }
                                    />
                                    <Label htmlFor="budget_control">
                                        {data.budget_control
                                            ? 'Enabled'
                                            : 'Disabled'}
                                    </Label>
                                </div>
                            </div>

                            {/* Category */}
                            <div className="space-y-2">
                                <Label htmlFor="category">Category</Label>
                                <Select
                                    value={data.category}
                                    onValueChange={(val) => {
                                        setData('category', val);
                                        setData('sub_category', '');
                                    }}
                                >
                                    <SelectTrigger id="category">
                                        <SelectValue placeholder="Select category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="PROGRAM">Program</SelectItem>
                                        <SelectItem value="NON_PROGRAM">Non-Program</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.category && (
                                    <p className="text-sm text-destructive">{errors.category}</p>
                                )}
                            </div>

                            {/* Sub-Category */}
                            <div className="space-y-2">
                                <Label htmlFor="sub_category">Sub-Category</Label>
                                <Select
                                    value={data.sub_category || ''}
                                    onValueChange={(val) =>
                                        setData('sub_category', val)
                                    }
                                >
                                    <SelectTrigger id="sub_category">
                                        <SelectValue placeholder="Select sub-category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {(SUB_CATEGORY_OPTIONS[data.category] || []).map((opt) => (
                                            <SelectItem key={opt} value={opt}>{opt}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* Typical Usage */}
                            <div className="space-y-2">
                                <Label htmlFor="typical_usage">Typical Usage</Label>
                                <Input
                                    id="typical_usage"
                                    value={data.typical_usage}
                                    onChange={(e) =>
                                        setData('typical_usage', e.target.value)
                                    }
                                    placeholder="Optional"
                                />
                            </div>

                            {/* Tax Applicable */}
                            <div className="space-y-2">
                                <Label htmlFor="tax_applicable">Tax Applicable</Label>
                                <div className="flex items-center space-x-2">
                                    <Switch
                                        id="tax_applicable"
                                        checked={data.tax_applicable}
                                        onCheckedChange={(val) =>
                                            setData('tax_applicable', val)
                                        }
                                    />
                                    <Label htmlFor="tax_applicable">
                                        {data.tax_applicable ? 'Yes' : 'No'}
                                    </Label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end gap-2">
                        <Link href={index.url()}>
                            <Button type="button" variant="outline">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing}>
                            Update Account
                        </Button>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
