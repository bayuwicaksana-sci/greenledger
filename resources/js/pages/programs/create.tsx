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
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import programRoutes from '@/routes/programs';
import { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ProgramCreate({
    site_code,
    current_year,
}: {
    site_code: string;
    current_year: number;
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Programs',
            href: programRoutes.index.url({ site: site_code }),
        },
        {
            title: 'Create',
            href: programRoutes.create.url({ site: site_code }),
        },
    ];
    const { data, setData, post, processing, errors } = useForm({
        program_code: '',
        program_name: '',
        description: '',
        fiscal_year: current_year.toString(),
        total_budget: '',
        start_date: '',
        end_date: '',
        status: 'DRAFT',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(programRoutes.store.url({ site: site_code }));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Program" />
            <div className="flex flex-col gap-4 p-4">
                <div className="mx-auto w-full max-w-2xl">
                    <form onSubmit={submit}>
                        <Card>
                            <CardHeader>
                                <CardTitle>Create New Program</CardTitle>
                                <CardDescription>
                                    Enter the details for the new research
                                    program.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="program_code">
                                            Program Code
                                        </Label>
                                        <Input
                                            id="program_code"
                                            value={data.program_code}
                                            onChange={(e) =>
                                                setData(
                                                    'program_code',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="e.g. KLT-2026-001"
                                            required
                                        />
                                        {errors.program_code && (
                                            <p className="text-sm text-red-500">
                                                {errors.program_code}
                                            </p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="fiscal_year">
                                            Fiscal Year
                                        </Label>
                                        <Input
                                            id="fiscal_year"
                                            type="number"
                                            value={data.fiscal_year}
                                            onChange={(e) =>
                                                setData(
                                                    'fiscal_year',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                        />
                                        {errors.fiscal_year && (
                                            <p className="text-sm text-red-500">
                                                {errors.fiscal_year}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="program_name">
                                        Program Name
                                    </Label>
                                    <Input
                                        id="program_name"
                                        value={data.program_name}
                                        onChange={(e) =>
                                            setData(
                                                'program_name',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g. Rice Yield Improvement Trial"
                                        required
                                    />
                                    {errors.program_name && (
                                        <p className="text-sm text-red-500">
                                            {errors.program_name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="description">
                                        Description (Optional)
                                    </Label>
                                    <Textarea
                                        id="description"
                                        value={data.description}
                                        onChange={(e) =>
                                            setData(
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Brief description of the program goals..."
                                    />
                                    {errors.description && (
                                        <p className="text-sm text-red-500">
                                            {errors.description}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="total_budget">
                                        Total Budget
                                    </Label>
                                    <Input
                                        id="total_budget"
                                        type="number"
                                        step="0.01"
                                        value={data.total_budget}
                                        onChange={(e) =>
                                            setData(
                                                'total_budget',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="0.00"
                                        required
                                    />
                                    {errors.total_budget && (
                                        <p className="text-sm text-red-500">
                                            {errors.total_budget}
                                        </p>
                                    )}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="start_date">
                                            Start Date
                                        </Label>
                                        <Input
                                            id="start_date"
                                            type="date"
                                            value={data.start_date}
                                            onChange={(e) =>
                                                setData(
                                                    'start_date',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {errors.start_date && (
                                            <p className="text-sm text-red-500">
                                                {errors.start_date}
                                            </p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="end_date">
                                            End Date
                                        </Label>
                                        <Input
                                            id="end_date"
                                            type="date"
                                            value={data.end_date}
                                            onChange={(e) =>
                                                setData(
                                                    'end_date',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {errors.end_date && (
                                            <p className="text-sm text-red-500">
                                                {errors.end_date}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </CardContent>
                            <CardFooter className="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    type="button"
                                    onClick={() =>
                                        router.get(
                                            programRoutes.index.url({
                                                site: site_code,
                                            }),
                                        )
                                    }
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    Create Program
                                </Button>
                            </CardFooter>
                        </Card>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
