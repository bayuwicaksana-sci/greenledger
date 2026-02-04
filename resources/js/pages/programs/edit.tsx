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
import { BreadcrumbItem, Program } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ProgramEdit({
    site_code,
    program,
    statuses,
}: {
    site_code: string;
    program: Program;
    statuses: string[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Programs',
            href: programRoutes.index.url({ site: site_code }),
        },
        {
            title: 'Edit',
            href: programRoutes.edit.url({
                site: site_code,
                program: program.id,
            }),
        },
    ];

    const { data, setData, put, processing, errors } = useForm({
        program_code: program.program_code,
        program_name: program.program_name,
        description: program.description || '',
        fiscal_year: program.fiscal_year.toString(),
        total_budget: program.total_budget.toString(),
        start_date: program.start_date || '',
        end_date: program.end_date || '',
        status: program.status,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        put(programRoutes.update.url({ site: site_code, program: program.id }));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit ${program.program_code}`} />
            <div className="flex flex-col gap-4 p-4">
                <div className="mx-auto w-full max-w-2xl">
                    <form onSubmit={submit}>
                        <Card>
                            <CardHeader>
                                <CardTitle>Edit Program</CardTitle>
                                <CardDescription>
                                    Update the details for this research
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

                                <div className="grid grid-cols-2 gap-4">
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

                                    <div className="space-y-2">
                                        <Label htmlFor="status">Status</Label>
                                        <Select
                                            value={data.status}
                                            onValueChange={(value) =>
                                                setData('status', value)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select status" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {statuses.map((status) => (
                                                    <SelectItem
                                                        key={status}
                                                        value={status}
                                                    >
                                                        {status}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.status && (
                                            <p className="text-sm text-red-500">
                                                {errors.status}
                                            </p>
                                        )}
                                    </div>
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
                                    Save Changes
                                </Button>
                            </CardFooter>
                        </Card>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
