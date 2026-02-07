import { Alert, AlertDescription } from '@/components/ui/alert';
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
import MainLayout from '@/layouts/main-layout';
import { index, update } from '@/routes/admin/fiscal-years';
import type { BreadcrumbItem, FiscalYear } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import { AlertTriangle, ArrowLeft } from 'lucide-react';

export default function EditFiscalYear({
    fiscalYear,
    programCount,
}: {
    fiscalYear: FiscalYear;
    programCount: number;
}) {
    const { data, setData, put, processing, errors } = useForm({
        start_date: format(fiscalYear.start_date, 'yyyy-MM-dd'),
        end_date: format(fiscalYear.end_date, 'yyyy-MM-dd'),
        year: fiscalYear.year.toString(),
    });

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Administration',
            href: '#',
        },
        {
            title: 'Fiscal Years',
            href: index().url,
        },
        {
            title: `Edit FY${fiscalYear.year}`,
            href: '#',
        },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update(fiscalYear.id).url);
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Fiscal Year ${fiscalYear.year}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => router.visit(index().url)}
                    >
                        <ArrowLeft className="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 className="text-3xl font-bold">
                            Edit Fiscal Year {fiscalYear.year}
                        </h1>
                        <p className="text-muted-foreground">
                            Modify fiscal year period dates
                        </p>
                    </div>
                </div>

                {/* Warning if programs exist */}
                {programCount > 0 && (
                    <Alert variant="destructive" className="max-w-2xl">
                        <AlertTriangle className="h-4 w-4" />
                        <AlertDescription>
                            This fiscal year has {programCount} associated
                            program{programCount !== 1 ? 's' : ''}. Changing
                            dates may affect program reporting and budgets.
                            Proceed with caution.
                        </AlertDescription>
                    </Alert>
                )}

                {/* Form */}
                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Fiscal Year Details</CardTitle>
                        <CardDescription>
                            Update the start and end dates for the fiscal year.
                            The year code cannot be changed.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="space-y-2">
                                <Label htmlFor="year">Year Code</Label>
                                <Input
                                    id="year"
                                    type="text"
                                    value={data.year}
                                    disabled
                                    className="bg-muted"
                                />
                                <p className="text-sm text-muted-foreground">
                                    Year code cannot be modified
                                </p>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="start_date">Start Date</Label>
                                <Input
                                    id="start_date"
                                    type="date"
                                    value={data.start_date}
                                    onChange={(e) =>
                                        setData('start_date', e.target.value)
                                    }
                                    required
                                />
                                {errors.start_date && (
                                    <p className="text-sm text-destructive">
                                        {errors.start_date}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="end_date">End Date</Label>
                                <Input
                                    id="end_date"
                                    type="date"
                                    value={data.end_date}
                                    onChange={(e) =>
                                        setData('end_date', e.target.value)
                                    }
                                    required
                                />
                                {errors.end_date && (
                                    <p className="text-sm text-destructive">
                                        {errors.end_date}
                                    </p>
                                )}
                                <p className="text-sm text-muted-foreground">
                                    End date must be after the start date
                                </p>
                            </div>

                            <div className="flex gap-4">
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    className="flex-1"
                                >
                                    {processing
                                        ? 'Updating...'
                                        : 'Update Fiscal Year'}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => router.visit(index().url)}
                                >
                                    Cancel
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}
