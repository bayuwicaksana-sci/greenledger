import {
    bulkStore,
    index,
} from '@/actions/App/Http/Controllers/CoaAccountController';
import { Button } from '@/components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
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
import MainLayout from '@/layouts/main-layout';
import { BreadcrumbItem, Site } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, ChevronDown, Plus, Save, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface ParentAccount {
    id: number;
    site_id: number;
    account_code: string;
    account_name: string;
    account_type: string;
    hierarchy_level: number;
}

interface ExistingCode {
    site_id: number;
    account_code: string;
}

interface StandardAccount {
    code: string;
    name: string;
    type: string;
    description: string;
    parent: string | null;
    abbreviation?: string;
}

interface CreateProps {
    sites: Site[];
    parents: ParentAccount[];
    existingCodes: ExistingCode[];
    standardAccounts: StandardAccount[];
}

interface AccountFormData {
    _temp_id: string;
    site_id: string;
    account_code: string;
    abbreviation: string;
    account_name: string;
    account_type: string;
    short_description: string;
    parent_account_id: string;
    parent_temp_id: string;
    is_active: boolean;
    budget_control: boolean;
    initial_budget: string;
    is_standard: boolean;
}

export default function Create({
    sites,
    parents, // eslint-disable-line
    existingCodes, // eslint-disable-line
    standardAccounts, // eslint-disable-line
}: CreateProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Chart of Accounts', href: index.url() },
        { title: 'Create Account', href: '#' },
    ];

    const [accounts, setAccounts] = useState<AccountFormData[]>([
        createEmptyAccount(),
    ]);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [collapsedCards, setCollapsedCards] = useState<Set<string>>(
        new Set(),
    );

    const toggleCard = (tempId: string) => {
        setCollapsedCards((prev) => {
            const next = new Set(prev);
            if (next.has(tempId)) {
                next.delete(tempId);
            } else {
                next.add(tempId);
            }
            return next;
        });
    };

    function createEmptyAccount(): AccountFormData {
        return {
            _temp_id: `temp_${Date.now()}_${Math.random()}`,
            site_id: '',
            account_code: '',
            abbreviation: '',
            account_name: '',
            account_type: 'EXPENSE',
            short_description: '',
            parent_account_id: '',
            parent_temp_id: '',
            is_active: true,
            budget_control: false,
            initial_budget: '0',
            is_standard: false,
        };
    }

    const handleAddAccount = () => {
        setAccounts((prev) => [...prev, createEmptyAccount()]);
    };

    const handleRemoveAccount = (index: number) => {
        if (accounts.length > 1) {
            setAccounts((prev) => prev.filter((_, i) => i !== index));
        }
    };

    const fetchNextCode = async (
        idx: number,
        siteId: string,
        parentId: string,
    ) => {
        if (!siteId) return;
        try {
            const params = new URLSearchParams({
                site_id: siteId,
                parent_id: parentId || '',
            });
            const res = await fetch(
                `/app/config/coa/next-code?${params.toString()}`,
                {
                    headers: { Accept: 'application/json' },
                },
            );
            if (res.ok) {
                const data = await res.json();
                if (data.code) {
                    setAccounts((prev) => {
                        const updated = [...prev];
                        updated[idx] = {
                            ...updated[idx],
                            account_code: data.code,
                        };
                        return updated;
                    });
                }
            }
        } catch (error) {
            console.error('Failed to fetch next code', error);
        }
    };

    const handleUpdateAccount = (
        index: number,
        field: keyof AccountFormData,
        value: string | boolean,
    ) => {
        setAccounts((prev) => {
            const updated = [...prev];
            const oldRow = updated[index];
            let newRow = { ...oldRow, [field]: value };

            if (field === 'parent_account_id') {
                const raw = value as string;

                if (raw.startsWith('temp:')) {
                    // In-batch sibling selected as parent
                    const tempId = raw.slice('temp:'.length);
                    newRow.parent_account_id = '';
                    newRow.parent_temp_id = tempId;

                    // Auto-set account_type from the sibling
                    const sibling = prev.find(
                        (a) => a._temp_id === tempId,
                    );
                    if (sibling) {
                        newRow.account_type = sibling.account_type;
                    }
                } else if (raw === 'root' || raw === '') {
                    // Root / no parent
                    newRow.parent_account_id = '';
                    newRow.parent_temp_id = '';
                } else {
                    // Existing DB parent
                    newRow.parent_account_id = raw;
                    newRow.parent_temp_id = '';

                    const parent = parents.find(
                        (p) => p.id.toString() === raw,
                    );
                    if (parent) {
                        newRow.account_type = parent.account_type;
                    }
                }
            }

            updated[index] = newRow;
            return updated;
        });

        // Trigger code generation if core fields change (skip for temp parents)
        if (field === 'site_id' || field === 'parent_account_id') {
            const acc = accounts[index];
            const siteId =
                field === 'site_id' ? (value as string) : acc.site_id;
            const rawParent =
                field === 'parent_account_id'
                    ? (value as string)
                    : acc.parent_account_id;

            // Don't call nextCode when a temp: parent is selected
            const isTempParent =
                typeof rawParent === 'string' && rawParent.startsWith('temp:');

            if (siteId && !isTempParent) {
                const parentId =
                    rawParent === 'root' ? '' : (rawParent as string);
                fetchNextCode(index, siteId, parentId);
            }
        }
    };

    const handleStandardAccountSelect = (
        index: number,
        standardCode: string,
    ) => {
        const standard = standardAccounts.find((s) => s.code === standardCode);
        if (!standard) return;

        setAccounts((prev) => {
            const updated = [...prev];
            const current = updated[index];
            const newRow = {
                ...current,
                account_code: standard.code,
                account_name: standard.name,
                account_type: standard.type,
                short_description: standard.description,
                abbreviation: standard.abbreviation || '',
                is_standard: true,
            };
            updated[index] = newRow;
            return updated;
        });
    };

    // Kept for potential future use — no current UI wires it
    void handleStandardAccountSelect;

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        router.post(bulkStore.url(), { accounts } as any, {
            onSuccess: () => setProcessing(false),
            onError: (err) => {
                setErrors(err);
                setProcessing(false);
            },
        });
    };

    /**
     * Build parent options for a given sub-card:
     *   - DB parents filtered by site_id
     *   - In-batch siblings with non-empty account_name (prefixed NEW:)
     */
    const getParentOptions = (currentIdx: number, siteId: string) => {
        const dbParents = parents.filter(
            (p) => p.site_id.toString() === siteId,
        );

        const siblingOptions = accounts
            .map((a, i) => ({ ...a, _idx: i }))
            .filter(
                (a) =>
                    a._idx !== currentIdx &&
                    a.site_id === siteId &&
                    a.account_name.trim() !== '',
            )
            .map((a) => ({
                value: `temp:${a._temp_id}`,
                label: `NEW: ${a.account_name}`,
            }));

        return { dbParents, siblingOptions };
    };

    /** Derive the current Select value for parent dropdown */
    const getParentValue = (acc: AccountFormData): string => {
        if (acc.parent_temp_id) return `temp:${acc.parent_temp_id}`;
        if (acc.parent_account_id) return acc.parent_account_id;
        return 'root';
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Create COA Account" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">
                            Create COA Accounts
                        </h1>
                        <p className="text-muted-foreground">
                            Add one or multiple accounts at once
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
                    <div className="rounded-lg border p-6">
                        <h2 className="mb-4 text-lg font-semibold">
                            Accounts
                        </h2>

                        <div className="space-y-4">
                            {accounts.map((acc, idx) => {
                                const { dbParents, siblingOptions } =
                                    getParentOptions(idx, acc.site_id);

                                return (
                                    <Collapsible
                                        key={acc._temp_id}
                                        open={
                                            !collapsedCards.has(acc._temp_id)
                                        }
                                        onOpenChange={() =>
                                            toggleCard(acc._temp_id)
                                        }
                                    >
                                    <div className="rounded-lg border bg-muted/20 p-4">
                                        {/* Header */}
                                        <div className="flex items-center justify-between">
                                            <CollapsibleTrigger asChild>
                                                <button className="flex flex-1 items-center gap-2 text-left">
                                                    <ChevronDown
                                                        className={`h-4 w-4 text-muted-foreground transition-transform ${collapsedCards.has(acc._temp_id) ? '-rotate-90' : ''}`}
                                                    />
                                                    <span className="text-sm font-semibold">
                                                        Account #{idx + 1}
                                                        {acc.account_name.trim() && (
                                                            <span className="font-normal text-muted-foreground">
                                                                {' '}— {acc.account_name}
                                                            </span>
                                                        )}
                                                    </span>
                                                </button>
                                            </CollapsibleTrigger>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                onClick={() =>
                                                    handleRemoveAccount(idx)
                                                }
                                                disabled={
                                                    accounts.length === 1
                                                }
                                            >
                                                <Trash2 className="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>

                                        <CollapsibleContent>
                                        {/* Row 1: Site | Parent Account */}
                                        <div className="mt-3 grid grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <Label>Site</Label>
                                                <Select
                                                    value={acc.site_id}
                                                    onValueChange={(val) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'site_id',
                                                            val,
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select Site" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {sites.map((site) => (
                                                            <SelectItem
                                                                key={site.id}
                                                                value={site.id.toString()}
                                                            >
                                                                {site.site_code}{' '}
                                                                - {site.site_name}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                                {errors[
                                                    `accounts.${idx}.site_id`
                                                ] && (
                                                    <p className="text-xs text-destructive">
                                                        {
                                                            errors[
                                                                `accounts.${idx}.site_id`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label>Parent Account</Label>
                                                <Select
                                                    value={getParentValue(acc)}
                                                    onValueChange={(val) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'parent_account_id',
                                                            val,
                                                        )
                                                    }
                                                    disabled={!acc.site_id}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue
                                                            placeholder={
                                                                acc.site_id
                                                                    ? 'Select Parent'
                                                                    : 'Select Site First'
                                                            }
                                                        />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="root">
                                                            None (Root Account)
                                                        </SelectItem>
                                                        {dbParents.map((p) => (
                                                            <SelectItem
                                                                key={p.id}
                                                                value={p.id.toString()}
                                                            >
                                                                {p.account_code}{' '}
                                                                -{' '}
                                                                {p.account_name}
                                                            </SelectItem>
                                                        ))}
                                                        {siblingOptions.map(
                                                            (s) => (
                                                                <SelectItem
                                                                    key={
                                                                        s.value
                                                                    }
                                                                    value={
                                                                        s.value
                                                                    }
                                                                >
                                                                    {s.label}
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                                {errors[
                                                    `accounts.${idx}.parent_temp_id`
                                                ] && (
                                                    <p className="text-xs text-destructive">
                                                        {
                                                            errors[
                                                                `accounts.${idx}.parent_temp_id`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>
                                        </div>

                                        {/* Row 2: Account Code | Account Type */}
                                        <div className="mt-4 grid grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <Label>Account Code</Label>
                                                <Input
                                                    value={acc.account_code}
                                                    onChange={(e) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'account_code',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Auto"
                                                    readOnly={acc.is_standard}
                                                    className={
                                                        acc.is_standard
                                                            ? 'bg-muted'
                                                            : ''
                                                    }
                                                    maxLength={20}
                                                />
                                                {errors[
                                                    `accounts.${idx}.account_code`
                                                ] && (
                                                    <p className="text-xs text-destructive">
                                                        {
                                                            errors[
                                                                `accounts.${idx}.account_code`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label>Account Type</Label>
                                                <Select
                                                    value={acc.account_type}
                                                    onValueChange={(val) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'account_type',
                                                            val,
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select Type" />
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
                                                {errors[
                                                    `accounts.${idx}.account_type`
                                                ] && (
                                                    <p className="text-xs text-destructive">
                                                        {
                                                            errors[
                                                                `accounts.${idx}.account_type`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>
                                        </div>

                                        {/* Row 3: Account Name | Abbreviation */}
                                        <div className="mt-4 grid grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <Label>Account Name</Label>
                                                <Input
                                                    value={acc.account_name}
                                                    onChange={(e) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'account_name',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Account Name"
                                                />
                                                {errors[
                                                    `accounts.${idx}.account_name`
                                                ] && (
                                                    <p className="text-xs text-destructive">
                                                        {
                                                            errors[
                                                                `accounts.${idx}.account_name`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>

                                            <div className="space-y-2">
                                                <Label>Abbreviation</Label>
                                                <Input
                                                    value={acc.abbreviation}
                                                    onChange={(e) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'abbreviation',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Optional"
                                                />
                                            </div>
                                        </div>

                                        {/* Short Description (full width) */}
                                        <div className="mt-4 space-y-2">
                                            <Label>Short Description</Label>
                                            <Input
                                                value={acc.short_description}
                                                onChange={(e) =>
                                                    handleUpdateAccount(
                                                        idx,
                                                        'short_description',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Short Description"
                                            />
                                        </div>

                                        {/* Budget Control + Initial Budget */}
                                        <div className="mt-4 grid grid-cols-2 gap-4">
                                            <div className="flex items-end space-x-2">
                                                <Switch
                                                    checked={acc.budget_control}
                                                    onCheckedChange={(c) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'budget_control',
                                                            c,
                                                        )
                                                    }
                                                />
                                                <Label className="text-sm">
                                                    Budget Control
                                                </Label>
                                            </div>

                                            <div className="space-y-2">
                                                <Label>Initial Budget</Label>
                                                <Input
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    value={acc.initial_budget}
                                                    onChange={(e) =>
                                                        handleUpdateAccount(
                                                            idx,
                                                            'initial_budget',
                                                            e.target.value,
                                                        )
                                                    }
                                                />
                                                {errors[
                                                    `accounts.${idx}.initial_budget`
                                                ] && (
                                                    <p className="text-xs text-destructive">
                                                        {
                                                            errors[
                                                                `accounts.${idx}.initial_budget`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                        </CollapsibleContent>
                                    </div>
                                    </Collapsible>
                                );
                            })}
                        </div>

                        {/* Add Another Account */}
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleAddAccount}
                            className="mt-4"
                        >
                            <Plus className="mr-2 h-4 w-4" />
                            Add Another Account
                        </Button>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end gap-2">
                        <Link href={index.url()}>
                            <Button type="button" variant="outline">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing}>
                            <Save className="mr-2 h-4 w-4" />
                            {processing
                                ? 'Saving...'
                                : 'Save All Accounts'}
                        </Button>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
