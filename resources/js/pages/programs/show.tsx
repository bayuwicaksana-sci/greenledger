import { Head, Link } from '@inertiajs/react';
import { Edit } from 'lucide-react';
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import programRoutes from '@/routes/programs';
import type { BreadcrumbItem } from '@/types';
import { ProgramWithRelations } from '@/types/programs';

export default function ProgramShow({
    site_code,
    program,
}: {
    site_code: string;
    program: any; // Using any for extended relations
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
                            {program.program_code} • {program.fiscal_year} • {program.classification === 'PROGRAM' ? 'Research Program' : 'Non-Program Activity'}
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

                <Tabs defaultValue="overview" className="space-y-4">
                    <TabsList>
                        <TabsTrigger value="overview">Overview</TabsTrigger>
                        {program.classification === 'PROGRAM' && (
                            <TabsTrigger value="scientific">Scientific & Design</TabsTrigger>
                        )}
                        <TabsTrigger value="budget">Budget (BoQ)</TabsTrigger>
                    </TabsList>

                    <TabsContent value="overview" className="space-y-4">
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
                                        <h3 className="text-sm font-medium text-muted-foreground">Description</h3>
                                        <p className="mt-1 text-sm whitespace-pre-wrap">{program.description || 'No description provided.'}</p>
                                    </div>

                                    {program.classification === 'NON_PROGRAM' && (
                                        <div>
                                            <h3 className="text-sm font-medium text-muted-foreground">Category</h3>
                                            <p className="mt-1 text-sm">{program.non_program_category}</p>
                                        </div>
                                    )}

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <h3 className="text-sm font-medium text-muted-foreground">Total Budget</h3>
                                            <p className="mt-1 text-lg font-semibold">{formatCurrency(program.total_budget)}</p>
                                        </div>
                                        <div>
                                            <h3 className="text-sm font-medium text-muted-foreground">Status</h3>
                                            <div className="mt-1">
                                                <StatusBadge status={program.status} />
                                            </div>
                                        </div>
                                    </div>

                                    <Separator />

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <h3 className="text-sm font-medium text-muted-foreground">Planning Dates</h3>
                                            <p className="mt-1 text-sm">{formatDate(program.start_date)} - {formatDate(program.end_date)}</p>
                                        </div>
                                        <div>
                                            <h3 className="text-sm font-medium text-muted-foreground">Actual Dates</h3>
                                            <p className="mt-1 text-sm">{formatDate(program.actual_start_date)} - {formatDate(program.actual_end_date)}</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Activities</CardTitle>
                                    <CardDescription>Activities linked to this program.</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {program.activities && program.activities.length > 0 ? (
                                        <ul className="space-y-2">
                                            {program.activities.map((activity: any) => (
                                                <li key={activity.id} className="flex flex-col gap-1 rounded-md border p-2 text-sm">
                                                    <div className="font-medium">{activity.activity_name}</div>
                                                    <div className="flex justify-between text-xs text-muted-foreground">
                                                        <span>{formatCurrency(activity.budget_allocation)}</span>
                                                        <span>{activity.status}</span>
                                                    </div>
                                                </li>
                                            ))}
                                        </ul>
                                    ) : (
                                        <p className="text-sm text-muted-foreground">No activities found.</p>
                                    )}
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <TabsContent value="scientific" className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>Scientific Background</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h3 className="font-medium">Background</h3>
                                        <p className="text-sm text-muted-foreground mt-1">{program.background_text || '-'}</p>
                                    </div>
                                    <div>
                                        <h3 className="font-medium">Problem Statement</h3>
                                        <p className="text-sm text-muted-foreground mt-1">{program.problem_statement || '-'}</p>
                                    </div>
                                    <div>
                                        <h3 className="font-medium">Hypothesis</h3>
                                        <p className="text-sm text-muted-foreground mt-1">{program.hypothesis || '-'}</p>
                                    </div>
                                    <div>
                                        <h3 className="font-medium">References</h3>
                                        <p className="text-sm text-muted-foreground mt-1">{program.journal_references || '-'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                         <Card>
                            <CardHeader>
                                <CardTitle>Experimental Design</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <h3 className="font-medium text-sm text-muted-foreground">Design</h3>
                                        <p>{program.trial_design || '-'}</p>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-sm text-muted-foreground">Treatments</h3>
                                        <p>{program.num_treatments || '-'}</p>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-sm text-muted-foreground">Replications</h3>
                                        <p>{program.num_replications || '-'}</p>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-sm text-muted-foreground">Plot Size</h3>
                                        <p>{program.plot_width_m || 0}m x {program.plot_length_m || 0}m</p>
                                    </div>
                                </div>
                                
                                {program.treatments && program.treatments.length > 0 && (
                                    <div className="mt-4">
                                        <h3 className="font-medium mb-2">Treatments List</h3>
                                        <div className="rounded-md border">
                                            <Table>
                                                <TableHeader>
                                                    <TableRow>
                                                        <TableHead>Code</TableHead>
                                                        <TableHead>Description</TableHead>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody>
                                                    {program.treatments.map((t: any) => (
                                                        <TableRow key={t.id}>
                                                            <TableCell className="font-medium">{t.treatment_code}</TableCell>
                                                            <TableCell>{t.treatment_description}</TableCell>
                                                        </TableRow>
                                                    ))}
                                                </TableBody>
                                            </Table>
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="budget" className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>Bill of Quantities (BoQ)</CardTitle>
                                <CardDescription>Detailed budget breakdown.</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="rounded-md border">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Category</TableHead>
                                                <TableHead>Phase</TableHead>
                                                <TableHead>Description</TableHead>
                                                <TableHead>Unit</TableHead>
                                                <TableHead className="text-right">Qty</TableHead>
                                                <TableHead className="text-right">Price</TableHead>
                                                <TableHead className="text-right">Subtotal</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                             {program.budget_items && program.budget_items.length > 0 ? (
                                                program.budget_items.map((item: any) => (
                                                    <TableRow key={item.id}>
                                                        <TableCell>{item.category?.category_name}</TableCell>
                                                        <TableCell>{item.phase?.phase_name}</TableCell>
                                                        <TableCell>{item.item_description}</TableCell>
                                                        <TableCell>{item.unit}</TableCell>
                                                        <TableCell className="text-right">{item.qty}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(item.unit_price)}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(item.subtotal)}</TableCell>
                                                    </TableRow>
                                                ))
                                            ) : (
                                                 <TableRow>
                                                    <TableCell colSpan={7} className="text-center text-muted-foreground">
                                                        No budget items found.
                                                    </TableCell>
                                                </TableRow>
                                            )}
                                        </TableBody>
                                    </Table>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}