import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
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
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import programRoutes from '@/routes/programs';
import { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect } from 'react';

export default function RevenueHarvestCreate({
    site_code,
    programs,
    buyers,
    coa_accounts,
}: {
    site_code: string;
    programs: any[];
    buyers: any[];
    coa_accounts: any[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Revenue',
            href: '#', // TODO: Add revenue index route
        },
        {
            title: 'Record Harvest',
            href: '#',
        },
    ];

    const { data, setData, post, processing, errors } = useForm({
        program_id: '',
        harvest_date: '',
        harvest_cycle: '1',
        crop_type: '',
        quantity_kg: '',
        price_per_kg: '',
        total_revenue: 0,
        buyer_id: '',
        payment_method: 'BANK_TRANSFER',
        payment_date: '',
        bank_reference: '',
        coa_account_id: '',
        notes: '',
    });

    // Auto-calc total
    useEffect(() => {
        const qty = parseFloat(data.quantity_kg) || 0;
        const price = parseFloat(data.price_per_kg) || 0;
        setData('total_revenue', qty * price);
    }, [data.quantity_kg, data.price_per_kg]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('revenue.harvest.store', { site: site_code }));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Record Harvest Revenue" />
            <div className="flex flex-col gap-4 p-4">
                <div className="mx-auto w-full max-w-2xl">
                    <form onSubmit={submit}>
                        <Card>
                            <CardHeader>
                                <CardTitle>Record Harvest Revenue</CardTitle>
                                <CardDescription>
                                    Record revenue from crop harvest sales.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="program_id">Program</Label>
                                    <Select
                                        value={data.program_id}
                                        onValueChange={(value) => setData('program_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Program" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {programs.map((p) => (
                                                <SelectItem key={p.id} value={p.id.toString()}>
                                                    {p.program_code} - {p.program_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.program_id && <p className="text-sm text-red-500">{errors.program_id}</p>}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="harvest_date">Harvest Date</Label>
                                        <Input
                                            type="date"
                                            value={data.harvest_date}
                                            onChange={(e) => setData('harvest_date', e.target.value)}
                                        />
                                        {errors.harvest_date && <p className="text-sm text-red-500">{errors.harvest_date}</p>}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="harvest_cycle">Harvest Cycle #</Label>
                                        <Input
                                            type="number"
                                            min="1"
                                            value={data.harvest_cycle}
                                            onChange={(e) => setData('harvest_cycle', e.target.value)}
                                        />
                                        {errors.harvest_cycle && <p className="text-sm text-red-500">{errors.harvest_cycle}</p>}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="crop_type">Crop Type</Label>
                                    <Input
                                        value={data.crop_type}
                                        onChange={(e) => setData('crop_type', e.target.value)}
                                        placeholder="e.g. Chili Rawit"
                                    />
                                    {errors.crop_type && <p className="text-sm text-red-500">{errors.crop_type}</p>}
                                </div>

                                <div className="grid grid-cols-3 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="quantity_kg">Qty (kg)</Label>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            value={data.quantity_kg}
                                            onChange={(e) => setData('quantity_kg', e.target.value)}
                                        />
                                        {errors.quantity_kg && <p className="text-sm text-red-500">{errors.quantity_kg}</p>}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="price_per_kg">Price/kg</Label>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            value={data.price_per_kg}
                                            onChange={(e) => setData('price_per_kg', e.target.value)}
                                        />
                                        {errors.price_per_kg && <p className="text-sm text-red-500">{errors.price_per_kg}</p>}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="total_revenue">Total</Label>
                                        <Input
                                            value={new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.total_revenue || 0)}
                                            readOnly
                                            className="bg-muted"
                                        />
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="buyer_id">Buyer</Label>
                                    <Select
                                        value={data.buyer_id}
                                        onValueChange={(value) => setData('buyer_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Buyer" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {buyers.map((b) => (
                                                <SelectItem key={b.id} value={b.id.toString()}>
                                                    {b.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.buyer_id && <p className="text-sm text-red-500">{errors.buyer_id}</p>}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="payment_method">Payment Method</Label>
                                        <Select
                                            value={data.payment_method}
                                            onValueChange={(value) => setData('payment_method', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Method" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="CASH">Cash</SelectItem>
                                                <SelectItem value="BANK_TRANSFER">Bank Transfer</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="payment_date">Payment Date</Label>
                                        <Input
                                            type="date"
                                            value={data.payment_date}
                                            onChange={(e) => setData('payment_date', e.target.value)}
                                        />
                                        {errors.payment_date && <p className="text-sm text-red-500">{errors.payment_date}</p>}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="coa_account_id">COA Account (Revenue)</Label>
                                    <Select
                                        value={data.coa_account_id}
                                        onValueChange={(value) => setData('coa_account_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Account" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {coa_accounts.map((c) => (
                                                <SelectItem key={c.id} value={c.id.toString()}>
                                                    {c.account_code} - {c.account_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.coa_account_id && <p className="text-sm text-red-500">{errors.coa_account_id}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="notes">Notes</Label>
                                    <Textarea
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        placeholder="Additional notes..."
                                    />
                                </div>
                            </CardContent>
                            <CardFooter className="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    type="button"
                                    onClick={() => router.visit(programRoutes.index.url({ site: site_code }))}
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    Record Revenue
                                </Button>
                            </CardFooter>
                        </Card>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}