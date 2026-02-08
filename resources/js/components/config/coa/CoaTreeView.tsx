import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import config from '@/routes/config';
import type { CoaAccount, Site } from '@/types';
import { Link, router } from '@inertiajs/react';
import { type VisibilityState } from '@tanstack/react-table';
import { ChevronDown, ChevronRight, Edit, Trash2 } from 'lucide-react';
import { useMemo, useState } from 'react';

interface CoaTreeViewProps {
    accounts: CoaAccount[];
    sites: Site[];
    columnVisibility: VisibilityState;
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

function TreeRow({
    account,
    childrenMap,
    expandedIds,
    onToggle,
    depth,
    columnVisibility,
    currentFiscalYearId,
}: {
    account: CoaAccount;
    childrenMap: Map<number | null, CoaAccount[]>;
    expandedIds: Set<number>;
    onToggle: (id: number) => void;
    depth: number;
    columnVisibility: VisibilityState;
    currentFiscalYearId: number;
}) {
    const children = childrenMap.get(account.id) || [];
    const hasChildren = children.length > 0;
    const isExpanded = expandedIds.has(account.id);
    const isVisible = (id: string) => columnVisibility[id] !== false;

    return (
        <>
            <TableRow
                className={
                    account.parent_account_id === null ? 'font-bold' : ''
                }
            >
                <TableCell>
                    <div
                        className="flex items-center"
                        style={{ paddingLeft: `${depth * 20}px` }}
                    >
                        {hasChildren ? (
                            <button
                                type="button"
                                onClick={() => onToggle(account.id)}
                                className="mr-1 text-muted-foreground hover:text-foreground"
                            >
                                {isExpanded ? (
                                    <ChevronDown className="h-4 w-4" />
                                ) : (
                                    <ChevronRight className="h-4 w-4" />
                                )}
                            </button>
                        ) : (
                            <span className="mr-1 w-4" />
                        )}
                        <span className="font-medium">
                            {account.account_code}
                        </span>
                    </div>
                </TableCell>
                {isVisible('account_name') && (
                    <TableCell>{account.account_name}</TableCell>
                )}
                {isVisible('account_type') && (
                    <TableCell>
                        <span
                            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                account.account_type === 'REVENUE'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                            }`}
                        >
                            {account.account_type}
                        </span>
                    </TableCell>
                )}
                {isVisible('site') && (
                    <TableCell className="uppercase">
                        {account.site?.site_name || '—'}
                    </TableCell>
                )}
                {isVisible('category') && (
                    <TableCell>
                        <span
                            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                account.category === 'PROGRAM'
                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                    : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                            }`}
                        >
                            {account.category === 'PROGRAM'
                                ? 'Program'
                                : 'Non-Program'}
                        </span>
                    </TableCell>
                )}
                {isVisible('is_active') && (
                    <TableCell>
                        {account.approval_status === 'pending_approval' ? (
                            <span className="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                                Pending
                            </span>
                        ) : account.approval_status === 'draft' ? (
                            <span className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                Draft
                            </span>
                        ) : account.approval_status === 'rejected' ? (
                            <span className="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                                Rejected
                            </span>
                        ) : account.is_active ? (
                            <span className="text-green-600">Active</span>
                        ) : (
                            <span className="text-gray-500">Inactive</span>
                        )}
                    </TableCell>
                )}
                {isVisible('utilization') && (
                    <TableCell>
                        {account.allocated_budget > 0 ? (
                            (() => {
                                const pct = Math.round(
                                    (account.actual_amount /
                                        account.allocated_budget) *
                                        100,
                                );
                                const colorClass =
                                    pct > 90
                                        ? 'bg-red-500'
                                        : pct > 70
                                          ? 'bg-amber-500'
                                          : 'bg-green-500';
                                return (
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-16 overflow-hidden rounded-full bg-muted">
                                            <div
                                                className={`h-full ${colorClass}`}
                                                style={{
                                                    width: `${Math.min(pct, 100)}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-xs">{pct}%</span>
                                    </div>
                                );
                            })()
                        ) : (
                            <span className="text-muted-foreground">—</span>
                        )}
                    </TableCell>
                )}
                {isVisible('allocated_budget') && (
                    <TableCell className="text-right">
                        {formatCurrency(account.allocated_budget)}
                    </TableCell>
                )}
                {isVisible('actual_amount') && (
                    <TableCell className="text-right">
                        <span
                            className={
                                account.actual_amount === 0
                                    ? ''
                                    : account.account_type === 'EXPENSE'
                                      ? 'text-red-600'
                                      : 'text-green-600'
                            }
                        >
                            {formatCurrency(account.actual_amount)}
                        </span>
                    </TableCell>
                )}
                {isVisible('balance') && (
                    <TableCell className="text-right">
                        {formatCurrency(account.balance)}
                    </TableCell>
                )}
                {isVisible('fiscal_year') && (
                    <TableCell>
                        <div className="flex items-center gap-2">
                            <span>{account.fiscal_year?.year || ''}</span>
                            {account.fiscal_year_id === currentFiscalYearId && (
                                <span className="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    Current
                                </span>
                            )}
                            {account.fiscal_year_id < currentFiscalYearId && (
                                <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    Past
                                </span>
                            )}
                        </div>
                    </TableCell>
                )}
                <TableCell>
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
                </TableCell>
            </TableRow>
            {isExpanded &&
                children.map((child) => (
                    <TreeRow
                        key={child.id}
                        account={child}
                        childrenMap={childrenMap}
                        expandedIds={expandedIds}
                        onToggle={onToggle}
                        depth={depth + 1}
                        columnVisibility={columnVisibility}
                        currentFiscalYearId={currentFiscalYearId}
                    />
                ))}
        </>
    );
}

const HIDEABLE_COLUMNS = [
    'account_name',
    'account_type',
    'site',
    'category',
    'is_active',
    'utilization',
    'allocated_budget',
    'actual_amount',
    'balance',
    'fiscal_year',
] as const;

export function CoaTreeView({
    accounts,
    columnVisibility,
    currentFiscalYearId,
}: CoaTreeViewProps) {
    const childrenMap = useMemo(() => {
        const map = new Map<number | null, CoaAccount[]>();
        for (const account of accounts) {
            const key = account.parent_account_id;
            const existing = map.get(key) || [];
            existing.push(account);
            map.set(key, existing);
        }
        return map;
    }, [accounts]);

    const rootAccounts = useMemo(
        () => childrenMap.get(null) || [],
        [childrenMap],
    );

    const [expandedIds, setExpandedIds] = useState<Set<number>>(() => {
        return new Set(rootAccounts.map((a) => a.id));
    });

    const handleToggle = (id: number) => {
        setExpandedIds((prev) => {
            const next = new Set(prev);
            if (next.has(id)) {
                next.delete(id);
            } else {
                next.add(id);
            }
            return next;
        });
    };

    const isVisible = (id: string) => columnVisibility[id] !== false;

    // 2 always-visible (Code + Actions) + however many hideable columns are on
    const visibleCount =
        2 + HIDEABLE_COLUMNS.filter((id) => isVisible(id)).length;

    return (
        <div className="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Code</TableHead>
                        {isVisible('account_name') && (
                            <TableHead>Name</TableHead>
                        )}
                        {isVisible('account_type') && (
                            <TableHead>Type</TableHead>
                        )}
                        {isVisible('site') && <TableHead>Site</TableHead>}
                        {isVisible('category') && (
                            <TableHead>Category</TableHead>
                        )}
                        {isVisible('is_active') && (
                            <TableHead>Status</TableHead>
                        )}
                        {isVisible('utilization') && (
                            <TableHead className="text-right">
                                Utilization
                            </TableHead>
                        )}
                        {isVisible('allocated_budget') && (
                            <TableHead className="text-right">Budget</TableHead>
                        )}
                        {isVisible('actual_amount') && (
                            <TableHead className="text-right">Actual</TableHead>
                        )}
                        {isVisible('balance') && (
                            <TableHead className="text-right">
                                Balance
                            </TableHead>
                        )}
                        {isVisible('fiscal_year') && (
                            <TableHead>Fiscal Year</TableHead>
                        )}
                        <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {rootAccounts.length > 0 ? (
                        rootAccounts.map((account) => (
                            <TreeRow
                                key={account.id}
                                account={account}
                                childrenMap={childrenMap}
                                expandedIds={expandedIds}
                                onToggle={handleToggle}
                                depth={0}
                                columnVisibility={columnVisibility}
                                currentFiscalYearId={currentFiscalYearId}
                            />
                        ))
                    ) : (
                        <TableRow>
                            <TableCell
                                colSpan={visibleCount}
                                className="h-24 text-center"
                            >
                                No accounts found.
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        </div>
    );
}
