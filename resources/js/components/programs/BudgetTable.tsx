import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Plus, Trash2 } from 'lucide-react';
import { useMemo } from 'react';
import type { BudgetItemFormData, ProgramBudgetCategory, ProgramBudgetPhase } from '@/types/programs';

interface BudgetTableProps {
    budgetItems: BudgetItemFormData[];
    onChange: (items: BudgetItemFormData[]) => void;
    errors?: Record<string, string>;
    budgetCategories: ProgramBudgetCategory[];
    budgetPhases: ProgramBudgetPhase[];
    totalBudget: number;
}

const EMPTY_ITEM: BudgetItemFormData = {
    category_id: '',
    phase_id: '',
    item_description: '',
    specification: '',
    unit: '',
    qty: '0',
    unit_price: '0',
    subtotal: 0,
    notes: '',
};

export function BudgetTable({
    budgetItems,
    onChange,
    errors,
    budgetCategories,
    budgetPhases,
    totalBudget,
}: BudgetTableProps) {
    const updateAt = (index: number, field: keyof BudgetItemFormData, value: string) => {
        const updated = [...budgetItems];
        const item = { ...updated[index], [field]: value };

        // Recalculate subtotal when qty or unit_price changes
        if (field === 'qty' || field === 'unit_price') {
            const qty = field === 'qty' ? parseFloat(value) || 0 : parseFloat(item.qty) || 0;
            const price =
                field === 'unit_price'
                    ? parseFloat(value) || 0
                    : parseFloat(item.unit_price) || 0;
            item.subtotal = qty * price;
        }

        updated[index] = item;
        onChange(updated);
    };

    const removeAt = (index: number) => {
        onChange(budgetItems.filter((_, i) => i !== index));
    };

    const addItem = () => {
        onChange([...budgetItems, { ...EMPTY_ITEM }]);
    };

    const formatCurrency = (n: number) =>
        n.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

    // Budget summary grouped by category
    const summary = useMemo(() => {
        const groups: Record<string, { name: string; total: number }> = {};
        budgetItems.forEach((item) => {
            if (!item.category_id) {
                return;
            }
            const cat = budgetCategories.find((c) => c.id.toString() === item.category_id);
            const name = cat ? cat.category_name : 'Uncategorized';
            if (!groups[item.category_id]) {
                groups[item.category_id] = { name, total: 0 };
            }
            groups[item.category_id].total += item.subtotal;
        });
        return Object.values(groups).sort((a, b) => a.name.localeCompare(b.name));
    }, [budgetItems, budgetCategories]);

    const grandTotal = summary.reduce((sum, g) => sum + g.total, 0);

    return (
        <div className="space-y-4">
            {budgetItems.length > 0 && (
                <div className="overflow-x-auto rounded-md border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="w-8 p-2 text-left">#</th>
                                <th className="w-36 p-2 text-left">Category</th>
                                <th className="w-36 p-2 text-left">Phase</th>
                                <th className="p-2 text-left">Description</th>
                                <th className="w-24 p-2 text-left">Unit</th>
                                <th className="w-24 p-2 text-right">Qty</th>
                                <th className="w-28 p-2 text-right">Unit Price</th>
                                <th className="w-28 p-2 text-right">Subtotal</th>
                                <th className="w-10 p-2" />
                            </tr>
                        </thead>
                        <tbody>
                            {budgetItems.map((item, index) => (
                                <tr key={index} className="border-b last:border-0">
                                    <td className="p-2 text-muted-foreground">
                                        {index + 1}
                                    </td>
                                    <td className="p-2">
                                        <Select
                                            value={item.category_id}
                                            onValueChange={(val) =>
                                                updateAt(index, 'category_id', val)
                                            }
                                        >
                                            <SelectTrigger className="h-8">
                                                <SelectValue placeholder="Category" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {budgetCategories.map((cat) => (
                                                    <SelectItem
                                                        key={cat.id}
                                                        value={cat.id.toString()}
                                                    >
                                                        {cat.category_name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </td>
                                    <td className="p-2">
                                        <Select
                                            value={item.phase_id}
                                            onValueChange={(val) =>
                                                updateAt(index, 'phase_id', val)
                                            }
                                        >
                                            <SelectTrigger className="h-8">
                                                <SelectValue placeholder="Phase" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {budgetPhases.map((phase) => (
                                                    <SelectItem
                                                        key={phase.id}
                                                        value={phase.id.toString()}
                                                    >
                                                        {phase.phase_name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </td>
                                    <td className="p-2">
                                        <Input
                                            value={item.item_description}
                                            onChange={(e) =>
                                                updateAt(index, 'item_description', e.target.value)
                                            }
                                            placeholder="Description"
                                            className="h-8"
                                        />
                                    </td>
                                    <td className="p-2">
                                        <Input
                                            value={item.unit}
                                            onChange={(e) =>
                                                updateAt(index, 'unit', e.target.value)
                                            }
                                            placeholder="Kg"
                                            className="h-8"
                                        />
                                    </td>
                                    <td className="p-2">
                                        <Input
                                            type="number"
                                            value={item.qty}
                                            onChange={(e) =>
                                                updateAt(index, 'qty', e.target.value)
                                            }
                                            className="h-8 text-right"
                                            min="0"
                                            step="0.01"
                                        />
                                    </td>
                                    <td className="p-2">
                                        <Input
                                            type="number"
                                            value={item.unit_price}
                                            onChange={(e) =>
                                                updateAt(index, 'unit_price', e.target.value)
                                            }
                                            className="h-8 text-right"
                                            min="0"
                                            step="0.01"
                                        />
                                    </td>
                                    <td className="p-2 text-right font-medium">
                                        {formatCurrency(item.subtotal)}
                                    </td>
                                    <td className="p-2">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            className="h-8 w-8 p-0 text-red-500 hover:text-red-600"
                                            onClick={() => removeAt(index)}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
            <Button type="button" variant="outline" size="sm" onClick={addItem}>
                <Plus className="mr-2 h-4 w-4" />
                Add Budget Item
            </Button>

            {/* Budget Summary */}
            {summary.length > 0 && (
                <div className="rounded-md border">
                    <div className="border-b bg-muted/50 px-4 py-2">
                        <h3 className="text-sm font-semibold">Budget Summary</h3>
                    </div>
                    <div className="divide-y">
                        {summary.map((group) => (
                            <div key={group.name} className="flex items-center justify-between px-4 py-2 text-sm">
                                <span>{group.name}</span>
                                <div className="flex items-center gap-4">
                                    <span className="font-medium">{formatCurrency(group.total)}</span>
                                    <span className="w-16 text-right text-muted-foreground">
                                        {totalBudget > 0
                                            ? `${((group.total / totalBudget) * 100).toFixed(1)}%`
                                            : '—'}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="flex items-center justify-between border-t bg-muted/30 px-4 py-2 text-sm font-semibold">
                        <span>Grand Total</span>
                        <div className="flex items-center gap-4">
                            <span>{formatCurrency(grandTotal)}</span>
                            <span className="w-16 text-right text-muted-foreground">
                                {totalBudget > 0
                                    ? `${((grandTotal / totalBudget) * 100).toFixed(1)}%`
                                    : '—'}
                            </span>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
