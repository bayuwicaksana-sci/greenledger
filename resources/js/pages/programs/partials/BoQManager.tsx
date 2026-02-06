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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { UseFormReturn } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';

interface Props {
    form: UseFormReturn<any>;
    categories: any[];
    phases: any[];
}

export default function BoQManager({ form, categories, phases }: Props) {
    const { data, setData, errors } = form;
    const items = data.budget_items || [];

    const addItem = () => {
        setData('budget_items', [
            ...items,
            {
                category_id: '',
                phase_id: '',
                item_description: '',
                specification: '',
                unit: '',
                qty: 0,
                unit_price: 0,
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
             newItems[index].subtotal = Number(newItems[index].qty) * Number(newItems[index].unit_price);
             // Update total budget?
             const total = newItems.reduce((sum: number, item: any) => sum + (Number(item.subtotal) || 0), 0);
             setData('total_budget', total.toString()); // Sync total budget
        }

        setData('budget_items', newItems);
    };

    const removeItem = (index: number) => {
        const newItems = items.filter((_: any, i: number) => i !== index);
        setData('budget_items', newItems);
         // Update total budget
         const total = newItems.reduce((sum: number, item: any) => sum + (Number(item.subtotal) || 0), 0);
         setData('total_budget', total.toString());
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h3 className="text-lg font-medium">Bill of Quantities (BoQ)</h3>
                <Button type="button" onClick={addItem} size="sm">
                    <Plus className="mr-2 h-4 w-4" /> Add Item
                </Button>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-[150px]">Category</TableHead>
                            <TableHead className="w-[150px]">Phase</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead className="w-[80px]">Unit</TableHead>
                            <TableHead className="w-[80px]">Qty</TableHead>
                            <TableHead className="w-[120px]">Price</TableHead>
                            <TableHead className="w-[120px]">Subtotal</TableHead>
                            <TableHead className="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {items.length === 0 && (
                             <TableRow>
                                <TableCell colSpan={8} className="text-center text-muted-foreground">
                                    No budget items added.
                                </TableCell>
                            </TableRow>
                        )}
                        {items.map((item: any, index: number) => (
                            <TableRow key={index}>
                                <TableCell>
                                    <Select
                                        value={item.category_id.toString()}
                                        onValueChange={(val) => updateItem(index, 'category_id', val)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categories.map((cat) => (
                                                <SelectItem key={cat.id} value={cat.id.toString()}>
                                                    {cat.category_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </TableCell>
                                <TableCell>
                                    <Select
                                        value={item.phase_id.toString()}
                                        onValueChange={(val) => updateItem(index, 'phase_id', val)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Phase" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {phases.map((phase) => (
                                                <SelectItem key={phase.id} value={phase.id.toString()}>
                                                    {phase.phase_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </TableCell>
                                <TableCell>
                                    <Input
                                        value={item.item_description}
                                        onChange={(e) => updateItem(index, 'item_description', e.target.value)}
                                        placeholder="Item details..."
                                    />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        value={item.unit}
                                        onChange={(e) => updateItem(index, 'unit', e.target.value)}
                                    />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        type="number"
                                        value={item.qty}
                                        onChange={(e) => updateItem(index, 'qty', e.target.value)}
                                    />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        type="number"
                                        value={item.unit_price}
                                        onChange={(e) => updateItem(index, 'unit_price', e.target.value)}
                                    />
                                </TableCell>
                                <TableCell>
                                    {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.subtotal || 0)}
                                </TableCell>
                                <TableCell>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        type="button"
                                        onClick={() => removeItem(index)}
                                    >
                                        <Trash2 className="h-4 w-4 text-red-500" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
            {errors.budget_items && (
                 <p className="text-sm text-red-500">{errors.budget_items}</p>
            )}
        </div>
    );
}
