import type { UseFormReturn } from '@inertiajs/react';
import { Plus, Trash2, ChevronDown } from 'lucide-react';
import { useEffect, useMemo } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
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
import { Textarea } from '@/components/ui/textarea';

interface CoaAccount {
    id: number;
    fiscal_year_id: number;
    account_code: string;
    account_name: string;
    short_description: string | null;
}

interface Props {
    form: UseFormReturn<any>;
    categories: any[];
    phases: any[];
    coaAccounts: CoaAccount[];
    activities: any[];
}

export default function BoQManager({
    form,
    categories,
    phases,
    coaAccounts,
    activities,
}: Props) {
    const { data, setData, errors } = form;
    const items = data.budget_items || [];

    // Filter COA accounts based on selected fiscal year
    const filteredCoaAccounts = useMemo(() => {
        const fiscalYearId = Number(data.fiscal_year_id);
        if (!fiscalYearId) return coaAccounts; // Show all if no fiscal year selected

        return coaAccounts.filter(
            (account) => account.fiscal_year_id === fiscalYearId
        );
    }, [coaAccounts, data.fiscal_year_id]);

    const calculateEstimatedDate = (
        programStartDate: string | null,
        days: number | string | null,
    ): string | null => {
        if (!programStartDate || !days || Number(days) <= 0) {
            return null;
        }

        const startDate = new Date(programStartDate);
        const estimatedDate = new Date(startDate);
        estimatedDate.setDate(estimatedDate.getDate() + Number(days));

        return estimatedDate.toISOString().split('T')[0];
    };

    const addItem = () => {
        setData('budget_items', [
            ...items,
            {
                activity_id: '',
                category_id: '',
                phase_id: '',
                coa_account_id: '',
                item_description: '',
                specification: '',
                unit: '',
                qty: '',
                unit_price: '',
                days: '',
                estimated_realization_date: '',
                subtotal: 0,
                notes: '',
            },
        ]);
    };

    const updateItem = (index: number, field: string, value: any) => {
        const newItems = [...items];
        newItems[index] = { ...newItems[index], [field]: value };

        // Auto-calc subtotal
        if (field === 'qty' || field === 'unit_price') {
            const qty = Number(newItems[index].qty) || 0;
            const unitPrice = Number(newItems[index].unit_price) || 0;
            newItems[index].subtotal = qty * unitPrice;

            // Update total budget
            const total = newItems.reduce(
                (sum: number, item: any) => sum + (Number(item.subtotal) || 0),
                0,
            );
            setData('total_budget', total);
        }

        // Auto-calc estimated realization date when days changes
        if (field === 'days') {
            newItems[index].estimated_realization_date = calculateEstimatedDate(
                data.start_date,
                value,
            );
        }

        setData('budget_items', newItems);
    };

    const removeItem = (index: number) => {
        const newItems = items.filter((_: any, i: number) => i !== index);
        setData('budget_items', newItems);

        // Update total budget
        const total = newItems.reduce(
            (sum: number, item: any) => sum + (Number(item.subtotal) || 0),
            0,
        );
        setData('total_budget', total);
    };

    // Watch for program start_date changes and recalculate estimated dates
    useEffect(() => {
        if (data.start_date && items.length > 0) {
            const updatedItems = items.map((item: any) => ({
                ...item,
                estimated_realization_date: item.days
                    ? calculateEstimatedDate(data.start_date, item.days)
                    : null,
            }));

            // Only update if there are actual changes
            const hasChanges = updatedItems.some(
                (item: any, index: number) =>
                    item.estimated_realization_date !==
                    items[index].estimated_realization_date,
            );

            if (hasChanges) {
                setData('budget_items', updatedItems);
            }
        }
    }, [data.start_date]);

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-medium">
                        Bill of Quantities (BoQ)
                    </h3>
                    <p className="text-sm text-muted-foreground">
                        Itemized budget breakdown for this program
                    </p>
                </div>
                <Button type="button" onClick={addItem} size="sm">
                    <Plus className="mr-2 h-4 w-4" /> Add Item
                </Button>
            </div>

            {items.length === 0 && (
                <Card className="border-dashed">
                    <CardContent className="flex flex-col items-center justify-center py-12">
                        <p className="text-sm text-muted-foreground">
                            No budget items added yet.
                        </p>
                        <p className="text-xs text-muted-foreground">
                            Click "Add Item" to create your first budget entry
                        </p>
                    </CardContent>
                </Card>
            )}

            <div className="space-y-4">
                {items.map((item: any, index: number) => (
                    <Card key={index} className="relative">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
                            <h4 className="text-base font-medium">
                                Item #{index + 1}
                            </h4>
                            <Button
                                variant="ghost"
                                size="sm"
                                type="button"
                                onClick={() => removeItem(index)}
                                className="h-8 w-8 p-0"
                            >
                                <Trash2 className="h-4 w-4 text-red-600" />
                            </Button>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {/* Row 0: Activity Assignment */}
                            <div className="space-y-2">
                                <Label>Assign to Activity</Label>
                                <Select
                                    value={item.activity_id || '__unassigned__'}
                                    onValueChange={(val) =>
                                        updateItem(
                                            index,
                                            'activity_id',
                                            val === '__unassigned__' ? '' : val,
                                        )
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Unassigned (General/Overhead)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="__unassigned__">
                                            <span className="text-muted-foreground">
                                                Unassigned (General/Overhead)
                                            </span>
                                        </SelectItem>
                                        {activities.length > 0 &&
                                            activities.map(
                                                (activity: any, actIndex: number) => (
                                                    <SelectItem
                                                        key={actIndex}
                                                        value={actIndex.toString()}
                                                    >
                                                        {activity.activity_name ||
                                                            `Activity #${actIndex + 1}`}
                                                    </SelectItem>
                                                ),
                                            )}
                                    </SelectContent>
                                </Select>
                                <p className="text-xs text-muted-foreground">
                                    {activities.length === 0
                                        ? 'Create activities in the section above to assign budget items'
                                        : 'Leave unassigned for overhead or general expenses'}
                                </p>
                                {errors[`budget_items.${index}.activity_id`] && (
                                    <p className="text-sm text-red-500">
                                        {errors[`budget_items.${index}.activity_id`]}
                                    </p>
                                )}
                            </div>

                            {/* Row 1: Category + Phase */}
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>
                                        Category{' '}
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Select
                                        value={item.category_id.toString()}
                                        onValueChange={(val) =>
                                            updateItem(
                                                index,
                                                'category_id',
                                                val,
                                            )
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categories.map((cat) => (
                                                <SelectItem
                                                    key={cat.id}
                                                    value={cat.id.toString()}
                                                >
                                                    {cat.category_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div className="space-y-2">
                                    <Label>
                                        Phase{' '}
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Select
                                        value={item.phase_id.toString()}
                                        onValueChange={(val) =>
                                            updateItem(index, 'phase_id', val)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select phase" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {phases.map((phase) => (
                                                <SelectItem
                                                    key={phase.id}
                                                    value={phase.id.toString()}
                                                >
                                                    {phase.phase_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            {/* Row 2: COA Account (full width) */}
                            <div className="space-y-2">
                                <Label>Cost Tagging (COA Account)</Label>
                                <Select
                                    value={
                                        item.coa_account_id
                                            ? item.coa_account_id.toString()
                                            : ''
                                    }
                                    onValueChange={(val) =>
                                        updateItem(index, 'coa_account_id', val)
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select COA account (optional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {filteredCoaAccounts.length > 0 ? (
                                            filteredCoaAccounts.map((account) => (
                                                <SelectItem
                                                    key={account.id}
                                                    value={account.id.toString()}
                                                >
                                                    <div className="flex flex-col">
                                                        <span className="font-mono text-xs">
                                                            {account.account_code}
                                                        </span>
                                                        <span className="text-sm">
                                                            {account.account_name}
                                                        </span>
                                                    </div>
                                                </SelectItem>
                                            ))
                                        ) : (
                                            <SelectItem value="__no_coa__" disabled>
                                                <span className="text-muted-foreground">
                                                    No COA accounts for selected fiscal year
                                                </span>
                                            </SelectItem>
                                        )}
                                    </SelectContent>
                                </Select>
                                <p className="text-xs text-muted-foreground">
                                    {filteredCoaAccounts.length === 0 ? (
                                        <span className="text-orange-600">
                                            No COA accounts available for the selected fiscal year.
                                            Please check fiscal year selection in Step 1.
                                        </span>
                                    ) : (
                                        <>
                                            Link this expense to a specific chart of accounts entry for
                                            financial tracking ({filteredCoaAccounts.length} accounts available)
                                        </>
                                    )}
                                </p>
                            </div>

                            {/* Row 3: Description */}
                            <div className="space-y-2">
                                <Label>
                                    Item Description{' '}
                                    <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    value={item.item_description}
                                    onChange={(e) =>
                                        updateItem(
                                            index,
                                            'item_description',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="What is being purchased?"
                                />
                            </div>

                            {/* Row 4: Specification (collapsible) */}
                            <Collapsible>
                                <CollapsibleTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        type="button"
                                        className="flex w-full justify-between"
                                    >
                                        <span className="text-sm font-medium">
                                            Specification{' '}
                                            {item.specification &&
                                                item.specification.trim() !==
                                                    '' &&
                                                '(added)'}
                                        </span>
                                        <ChevronDown className="h-4 w-4" />
                                    </Button>
                                </CollapsibleTrigger>
                                <CollapsibleContent className="pt-2">
                                    <Textarea
                                        value={item.specification || ''}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'specification',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Detailed specifications, brand, model, technical requirements..."
                                        rows={3}
                                        className="resize-none"
                                    />
                                </CollapsibleContent>
                            </Collapsible>

                            {/* Row 5: Unit, Qty, Unit Price */}
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                                <div className="space-y-2">
                                    <Label>
                                        Unit{' '}
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        value={item.unit}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'unit',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="kg, pcs, box"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label>
                                        Quantity{' '}
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        type="number"
                                        value={item.qty}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'qty',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="0"
                                        min="0"
                                        step="any"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label>
                                        Unit Price (Rp){' '}
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        type="number"
                                        value={item.unit_price}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'unit_price',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="0"
                                        min="0"
                                        step="any"
                                    />
                                </div>
                            </div>

                            {/* Row 6: Days + Est. Realization Date */}
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Days from Program Start</Label>
                                    <Input
                                        type="number"
                                        value={item.days}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'days',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g., 30"
                                        min="0"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        When this item will be
                                        purchased/realized
                                    </p>
                                </div>
                                <div className="space-y-2">
                                    <Label>Estimated Realization Date</Label>
                                    <Input
                                        type="date"
                                        value={
                                            item.estimated_realization_date ||
                                            ''
                                        }
                                        readOnly
                                        className="bg-muted"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        Auto-calculated from start date + days
                                    </p>
                                </div>
                            </div>

                            {/* Row 7: Subtotal (read-only) */}
                            <div className="rounded-lg bg-blue-50 p-3 dark:bg-blue-950/20">
                                <Label className="text-sm">Subtotal</Label>
                                <p className="text-xl font-semibold">
                                    {new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                    }).format(item.subtotal || 0)}
                                </p>
                            </div>

                            {/* Row 8: Notes (collapsible) */}
                            <Collapsible>
                                <CollapsibleTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        type="button"
                                        className="flex w-full justify-between"
                                    >
                                        <span className="text-sm font-medium">
                                            Notes{' '}
                                            {item.notes &&
                                                item.notes.trim() !== '' &&
                                                '(added)'}
                                        </span>
                                        <ChevronDown className="h-4 w-4" />
                                    </Button>
                                </CollapsibleTrigger>
                                <CollapsibleContent className="pt-2">
                                    <Textarea
                                        value={item.notes || ''}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'notes',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Additional notes or remarks..."
                                        rows={2}
                                        className="resize-none"
                                    />
                                </CollapsibleContent>
                            </Collapsible>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Budget Summary by Activity */}
            {items.length > 0 && (
                <Card>
                    <CardHeader>
                        <h4 className="font-medium">Budget Summary</h4>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {(() => {
                            const activityBudgets = new Map<string, number>();
                            let unassignedTotal = 0;

                            items.forEach((item: any) => {
                                const subtotal = item.subtotal || 0;
                                if (item.activity_id && item.activity_id !== '') {
                                    const current =
                                        activityBudgets.get(item.activity_id) || 0;
                                    activityBudgets.set(
                                        item.activity_id,
                                        current + subtotal,
                                    );
                                } else {
                                    unassignedTotal += subtotal;
                                }
                            });

                            return (
                                <>
                                    {/* Per-Activity Budgets */}
                                    {activities.map(
                                        (activity: any, actIndex: number) => {
                                            const budget =
                                                activityBudgets.get(
                                                    actIndex.toString(),
                                                ) || 0;
                                            return (
                                                <div
                                                    key={actIndex}
                                                    className="flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-950/20"
                                                >
                                                    <span className="font-medium">
                                                        {activity.activity_name ||
                                                            `Activity #${actIndex + 1}`}
                                                    </span>
                                                    <span className="font-semibold text-blue-700 dark:text-blue-400">
                                                        Rp{' '}
                                                        {budget.toLocaleString(
                                                            'id-ID',
                                                        )}
                                                    </span>
                                                </div>
                                            );
                                        },
                                    )}

                                    {/* Unassigned Total */}
                                    {unassignedTotal > 0 && (
                                        <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                                            <span className="font-medium text-muted-foreground">
                                                Unassigned (General/Overhead)
                                            </span>
                                            <span className="font-semibold">
                                                Rp{' '}
                                                {unassignedTotal.toLocaleString(
                                                    'id-ID',
                                                )}
                                            </span>
                                        </div>
                                    )}

                                    {/* Total Program Budget */}
                                    <div className="flex items-center justify-between rounded-lg bg-primary p-4 text-primary-foreground">
                                        <span className="text-lg font-bold">
                                            Total Program Budget
                                        </span>
                                        <span className="text-2xl font-bold">
                                            Rp{' '}
                                            {Number(data.total_budget).toLocaleString(
                                                'id-ID',
                                            )}
                                        </span>
                                    </div>
                                </>
                            );
                        })()}
                    </CardContent>
                </Card>
            )}

            {errors.budget_items && (
                <p className="text-sm text-red-500">{errors.budget_items}</p>
            )}
        </div>
    );
}
