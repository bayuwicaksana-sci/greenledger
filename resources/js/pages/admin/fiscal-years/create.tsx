import { Head, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useEffect } from 'react';
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
import type { BreadcrumbItem } from '@/types';

export default function CreateFiscalYear() {
    const { data, setData, post, processing, errors } = useForm({
        start_date: '',
        end_date: '',
        year: '',
    });

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Administration',
            href: '#',
        },
        {
            title: 'Fiscal Years',
            href: '/admin/fiscal-years',
        },
        {
            title: 'Create',
            href: '#',
        },
    ];

    // Auto-calculate year from start_date
    useEffect(() => {
        if (data.start_date) {
            const year = new Date(data.start_date).getFullYear();
            setData('year', year.toString());
        }
    }, [data.start_date]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/admin/fiscal-years');
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Fiscal Year" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => window.history.back()}
                    >
                        <ArrowLeft className="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 className="text-3xl font-bold">
                            Create Fiscal Year
                        </h1>
                        <p className="text-muted-foreground">
                            Define a new fiscal year period
                        </p>
                    </div>
                </div>

                {/* Form */}
                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Fiscal Year Details</CardTitle>
                        <CardDescription>
                            Enter the start and end dates for the fiscal year.
                            The year code will be automatically calculated from
                            the start date.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
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

                            <div className="space-y-2">
                                <Label htmlFor="year">
                                    Year Code (Auto-calculated)
                                </Label>
                                <Input
                                    id="year"
                                    type="text"
                                    value={data.year}
                                    disabled
                                    className="bg-muted"
                                />
                                <p className="text-sm text-muted-foreground">
                                    Year code is automatically derived from the
                                    start date
                                </p>
                            </div>

                            <div className="flex gap-4">
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    className="flex-1"
                                >
                                    {processing
                                        ? 'Creating...'
                                        : 'Create Fiscal Year'}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => window.history.back()}
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
