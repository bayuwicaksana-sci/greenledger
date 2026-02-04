import { StatusBadge } from '@/components/dashboard/atoms/StatusBadge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/app-layout';
import programRoutes from '@/routes/programs';
import { BreadcrumbItem } from '@/types';
import { ProgramWithRelations } from '@/types/programs';
import { Head, Link } from '@inertiajs/react';
import { Edit } from 'lucide-react';

export default function ProgramShow({
    site_code,
    program,
}: {
    site_code: string;
    program: ProgramWithRelations;
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Programs',
            href: programRoutes.index.url({ site: site_code }),
        },
        {
            title: program.program_code,
            href: programRoutes.show.url({
                site: site_code,
                program: program.id,
            }),
        },
    ];

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const formatDate = (date: string | null) => {
        if (!date) return '-';
        return new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Program ${program.program_code}`} />
            <div className="flex flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {program.program_name}
                        </h1>
                        <p className="text-muted-foreground">
                            {program.program_code} • {program.fiscal_year}
                        </p>
                    </div>
                    <Link
                        href={programRoutes.edit.url({
                            site: site_code,
                            program: program.id,
                        })}
                    >
                        <Button>
                            <Edit className="mr-2 h-4 w-4" /> Edit Program
                        </Button>
                    </Link>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card className="col-span-2">
                        <CardHeader>
                            <CardTitle>Program Details</CardTitle>
                            <CardDescription>
                                Overview of the program information.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div>
                                <h3 className="text-sm font-medium text-muted-foreground">
                                    Description
                                </h3>
                                <p className="mt-1 text-sm">
                                    {program.description ||
                                        'No description provided.'}
                                </p>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">
                                        Total Budget
                                    </h3>
                                    <p className="mt-1 text-lg font-semibold">
                                        {formatCurrency(program.total_budget)}
                                    </p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">
                                        Status
                                    </h3>
                                    <div className="mt-1">
                                        <StatusBadge status={program.status} />
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">
                                        Planning Dates
                                    </h3>
                                    <p className="mt-1 text-sm">
                                        {formatDate(program.start_date)} -{' '}
                                        {formatDate(program.end_date)}
                                    </p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">
                                        Actual Dates
                                    </h3>
                                    <p className="mt-1 text-sm">
                                        {formatDate(program.actual_start_date)}{' '}
                                        - {formatDate(program.actual_end_date)}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div>
                                <h3 className="text-sm font-medium text-muted-foreground">
                                    Created By
                                </h3>
                                <p className="mt-1 text-sm">
                                    {program.created_by.name}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Activities</CardTitle>
                            <CardDescription>
                                Activities linked to this program.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {program.activities && program.activities.length > 0 ? (
                                <ul className="space-y-2">
                                    {program.activities.map((activity) => (
                                        <li
                                            key={activity.id}
                                            className="flex items-center justify-between rounded-md border p-2 text-sm"
                                        >
                                            <span>
                                                {activity.activity_name}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    No activities found.
                                </p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
