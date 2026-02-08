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
import AppLayout from '@/layouts/app-layout';
import programRoutes from '@/routes/programs';
import type { BreadcrumbItem, FiscalYear } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import type { FormEventHandler } from 'react';
import BoQManager from './partials/BoQManager';
import ExperimentalDesignForm from './partials/ExperimentalDesignForm';
import HarvestPlanningForm from './partials/HarvestPlanningForm';
import NonProgramForm from './partials/NonProgramForm';
import ScientificBackgroundForm from './partials/ScientificBackgroundForm';

export default function ProgramCreate({
    site_code,
    fiscalYears,
    selectedFiscalYearId,
    users,
    commodities,
    active_programs,
    budget_categories,
    budget_phases,
}: {
    site_code: string;
    fiscalYears: FiscalYear[];
    selectedFiscalYearId: number;
    users: any[];
    commodities: any[];
    active_programs: any[];
    budget_categories: any[];
    budget_phases: any[];
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
        classification: 'PROGRAM', // Default to Research Program
        program_code: '',
        program_name: '',
        description: '',
        fiscal_year_id: selectedFiscalYearId.toString(),
        total_budget: '0',
        start_date: '',
        end_date: '',
        status: 'DRAFT',

        // Research specific
        program_category: '',
        commodity_id: '',
        research_associate_id: '',
        research_officer_id: '',

        // Non-Program specific
        non_program_category: '',

        // Nested data
        scientific_background: {},
        experimental_design: {},
        harvest_planning: {},
        budget_items: [],
        treatments: [],

        // Flattened fields mapped to nested forms
        background_text: '',
        problem_statement: '',
        hypothesis: '',
        journal_references: '',

        trial_design: '',
        trial_design_other: '',
        num_treatments: '',
        num_replications: '',
        num_samples_per_replication: '',
        plot_width_m: '',
        plot_length_m: '',
        google_maps_url: '',

        harvest_type: '',
        estimated_harvest_date: '',
        harvest_frequency_value: '',
        harvest_frequency_unit: '',
        harvest_event_count: '',
        first_harvest_date: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(programRoutes.store.url({ site: site_code }));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Program" />
            <div className="flex flex-col gap-4 p-4">
                <div className="mx-auto w-full max-w-4xl">
                    <form onSubmit={submit}>
                        <Card>
                            <CardHeader>
                                <CardTitle>Create New Program</CardTitle>
                                <CardDescription>
                                    Create a new Research Program or Non-Program
                                    Activity.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {/* Classification Selection */}
                                <div className="space-y-2 rounded-lg bg-muted p-4">
                                    <Label htmlFor="classification">
                                        Classification
                                    </Label>
                                    <Select
                                        value={data.classification}
                                        onValueChange={(value) =>
                                            setData('classification', value)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select Classification" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="PROGRAM">
                                                Research Program
                                            </SelectItem>
                                            <SelectItem value="NON_PROGRAM">
                                                Non-Program Activity
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Core Info */}
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="program_code">
                                            Code
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
                                        <Label htmlFor="fiscal_year_id">
                                            Fiscal Year
                                        </Label>
                                        <Select
                                            value={data.fiscal_year_id}
                                            onValueChange={(value) =>
                                                setData('fiscal_year_id', value)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select Fiscal Year" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {fiscalYears.map((fy) => (
                                                    <SelectItem
                                                        key={fy.id}
                                                        value={fy.id.toString()}
                                                        disabled={fy.is_closed}
                                                    >
                                                        {fy.year}
                                                        {fy.is_closed &&
                                                            ' (Closed)'}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.fiscal_year_id && (
                                            <p className="text-sm text-red-500">
                                                {errors.fiscal_year_id}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="program_name">
                                        {data.classification === 'PROGRAM'
                                            ? 'Research Title'
                                            : 'Activity Title'}
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
                                        required
                                    />
                                    {errors.program_name && (
                                        <p className="text-sm text-red-500">
                                            {errors.program_name}
                                        </p>
                                    )}
                                </div>

                                {/* Type Specific Forms */}
                                {data.classification === 'PROGRAM' ? (
                                    <>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="space-y-2">
                                                <Label htmlFor="program_category">
                                                    Category
                                                </Label>
                                                <Select
                                                    value={
                                                        data.program_category
                                                    }
                                                    onValueChange={(value) =>
                                                        setData(
                                                            'program_category',
                                                            value,
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select Category" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="RESEARCH">
                                                            Research
                                                        </SelectItem>
                                                        <SelectItem value="TRIAL">
                                                            Trial
                                                        </SelectItem>
                                                        <SelectItem value="PRODUCTION">
                                                            Production
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div className="space-y-2">
                                                <Label htmlFor="commodity_id">
                                                    Commodity
                                                </Label>
                                                <Select
                                                    value={data.commodity_id?.toString()}
                                                    onValueChange={(value) =>
                                                        setData(
                                                            'commodity_id',
                                                            value,
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select Commodity" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {commodities.map(
                                                            (c) => (
                                                                <SelectItem
                                                                    key={c.id}
                                                                    value={c.id.toString()}
                                                                >
                                                                    {c.name}
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>

                                        <ScientificBackgroundForm
                                            form={
                                                { data, setData, errors } as any
                                            }
                                        />
                                        <ExperimentalDesignForm
                                            form={
                                                { data, setData, errors } as any
                                            }
                                        />
                                        <HarvestPlanningForm
                                            form={
                                                { data, setData, errors } as any
                                            }
                                        />
                                    </>
                                ) : (
                                    <NonProgramForm
                                        form={{ data, setData, errors } as any}
                                    />
                                )}

                                {/* Budget (Shared) */}
                                <div className="space-y-2">
                                    <Label htmlFor="total_budget">
                                        Total Budget (Auto-calculated from BoQ)
                                    </Label>
                                    <Input
                                        id="total_budget"
                                        value={data.total_budget}
                                        readOnly
                                        className="bg-muted"
                                    />
                                </div>
                                <BoQManager
                                    form={{ data, setData, errors } as any}
                                    categories={budget_categories}
                                    phases={budget_phases}
                                />
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
                                    {processing
                                        ? 'Creating...'
                                        : 'Create Program'}
                                </Button>
                            </CardFooter>
                        </Card>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
