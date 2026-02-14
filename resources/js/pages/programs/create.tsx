import { useState, type FormEventHandler } from 'react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import programRoutes from '@/routes/programs';
import type { BreadcrumbItem, FiscalYear } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { Stepper, type Step } from '@/components/ui/stepper';
import {
    validateStep1,
    validateStep2,
    validateStep3,
    validateStep4,
    validateStep5,
    validateStep6,
} from './utils/stepValidation';

// Step Components
import ProgramStep1BasicInfo from './partials/stepper/ProgramStep1BasicInfo';
import ProgramStep2Team from './partials/stepper/ProgramStep2Team';
import ProgramStep3Scientific from './partials/stepper/ProgramStep3Scientific';
import ProgramStep4Experimental from './partials/stepper/ProgramStep4Experimental';
import ProgramStep5Budget from './partials/stepper/ProgramStep5Budget';
import ProgramStep6Documents from './partials/stepper/ProgramStep6Documents';

interface CoaAccount {
    id: number;
    account_code: string;
    account_name: string;
    short_description: string | null;
}

export default function ProgramCreate({
    site_code,
    fiscalYears,
    selectedFiscalYearId,
    users,
    commodities,
    active_programs,
    budget_categories,
    budget_phases,
    coa_accounts,
}: {
    site_code: string;
    fiscalYears: FiscalYear[];
    selectedFiscalYearId: number;
    users: any[];
    commodities: any[];
    active_programs: any[];
    budget_categories: any[];
    budget_phases: any[];
    coa_accounts: CoaAccount[];
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
        classification: 'PROGRAM',
        program_code: '',
        program_name: '',
        description: '',
        fiscal_year_id: selectedFiscalYearId.toString(),
        total_budget: 0,
        start_date: '',
        end_date: '',
        status: 'DRAFT',

        program_type: 'SINGLE_YEAR',

        program_category: '',
        commodity_id: '',
        research_associate_id: '',
        research_officer_id: '',
        support_team_member_ids: [],

        planting_start_date: '',
        estimated_duration_days: '',

        prerequisite_program_id: '',
        dependency_note: '',

        non_program_category: '',

        scientific_background: {},
        experimental_design: {},
        harvest_planning: {},
        budget_items: [],
        treatments: [],
        activities: [],

        background_text: '',
        problem_statement: '',
        hypothesis: '',
        objectives: [],
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

        plot_map: null,
        reference_files: [],
    });

    const [currentStep, setCurrentStep] = useState(0);
    const [completedSteps, setCompletedSteps] = useState<Set<number>>(
        new Set(),
    );

    // Define steps based on classification
    const programSteps: Step[] = [
        {
            id: 'basic',
            title: 'Basic Information',
            description: 'Program details',
        },
        { id: 'team', title: 'Team & Timeline', description: 'Research team' },
        {
            id: 'scientific',
            title: 'Scientific Background',
            description: 'Research context',
        },
        {
            id: 'experimental',
            title: 'Experimental Design',
            description: 'Trial setup',
        },
        {
            id: 'budget',
            title: 'Budget & Activities',
            description: 'Financial planning',
        },
        {
            id: 'documents',
            title: 'Supporting Documents',
            description: 'File uploads',
        },
    ];

    const nonProgramSteps: Step[] = [
        {
            id: 'basic',
            title: 'Basic Information',
            description: 'Activity details',
        },
        {
            id: 'budget',
            title: 'Budget & Activities',
            description: 'Financial planning',
        },
        {
            id: 'documents',
            title: 'Supporting Documents',
            description: 'File uploads',
        },
    ];

    const steps =
        data.classification === 'PROGRAM' ? programSteps : nonProgramSteps;

    // Step validation
    const canProceed = (step: number): boolean => {
        if (data.classification === 'PROGRAM') {
            switch (step) {
                case 0:
                    return validateStep1(data).isValid;
                case 1:
                    return validateStep2(data).isValid;
                case 2:
                    return validateStep3(data).isValid;
                case 3:
                    return validateStep4(data).isValid;
                case 4:
                    return validateStep5(data).isValid;
                case 5:
                    return validateStep6(data).isValid;
                default:
                    return true;
            }
        } else {
            // NON_PROGRAM
            switch (step) {
                case 0:
                    return validateStep1(data).isValid;
                case 1:
                    return validateStep5(data).isValid;
                case 2:
                    return validateStep6(data).isValid;
                default:
                    return true;
            }
        }
    };

    const handleNext = () => {
        if (canProceed(currentStep)) {
            setCompletedSteps((prev) => new Set([...prev, currentStep]));
            setCurrentStep((prev) => Math.min(prev + 1, steps.length - 1));
        }
    };

    const handlePrevious = () => {
        setCurrentStep((prev) => Math.max(prev - 1, 0));
    };

    const handleStepClick = (stepIndex: number) => {
        // Only allow clicking on completed steps or current step
        if (completedSteps.has(stepIndex) || stepIndex === currentStep) {
            setCurrentStep(stepIndex);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(programRoutes.store.url({ site: site_code }));
    };

    // Render current step
    const renderStep = () => {
        const formProps = { data, setData, errors };

        if (data.classification === 'PROGRAM') {
            switch (currentStep) {
                case 0:
                    return (
                        <ProgramStep1BasicInfo
                            form={formProps}
                            fiscalYears={fiscalYears}
                            commodities={commodities}
                        />
                    );
                case 1:
                    return (
                        <ProgramStep2Team
                            form={formProps}
                            users={users}
                            activePrograms={active_programs}
                        />
                    );
                case 2:
                    return <ProgramStep3Scientific form={formProps} />;
                case 3:
                    return <ProgramStep4Experimental form={formProps} />;
                case 4:
                    return (
                        <ProgramStep5Budget
                            form={formProps}
                            budgetCategories={budget_categories}
                            budgetPhases={budget_phases}
                            coaAccounts={coa_accounts}
                        />
                    );
                case 5:
                    return <ProgramStep6Documents form={formProps} />;
                default:
                    return null;
            }
        } else {
            // NON_PROGRAM
            switch (currentStep) {
                case 0:
                    return (
                        <ProgramStep1BasicInfo
                            form={formProps}
                            fiscalYears={fiscalYears}
                            commodities={commodities}
                        />
                    );
                case 1:
                    return (
                        <ProgramStep5Budget
                            form={formProps}
                            budgetCategories={budget_categories}
                            budgetPhases={budget_phases}
                            coaAccounts={coa_accounts}
                        />
                    );
                case 2:
                    return <ProgramStep6Documents form={formProps} />;
                default:
                    return null;
            }
        }
    };

    // Reset step when classification changes
    const handleClassificationChange = (value: string) => {
        setData('classification', value);
        setCurrentStep(0);
        setCompletedSteps(new Set());
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Program" />
            <div className="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <form onSubmit={submit}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Create New Program</CardTitle>
                            <CardDescription>
                                {data.classification === 'PROGRAM'
                                    ? 'Complete all 6 steps to create a research program'
                                    : 'Complete all 3 steps to create a non-program activity'}
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="space-y-8">
                            {/* Stepper Progress Indicator */}
                            <Stepper
                                steps={steps}
                                currentStep={currentStep}
                                completedSteps={completedSteps}
                                onStepClick={handleStepClick}
                            />

                            {/* Step Content */}
                            <div className="min-h-[400px]">{renderStep()}</div>
                        </CardContent>

                        <CardFooter className="flex justify-between border-t pt-6">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={handlePrevious}
                                disabled={currentStep === 0}
                            >
                                Previous
                            </Button>

                            <div className="flex gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
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

                                {currentStep === steps.length - 1 ? (
                                    <Button type="submit" disabled={processing}>
                                        {processing
                                            ? 'Creating...'
                                            : 'Create Program'}
                                    </Button>
                                ) : (
                                    <Button
                                        type="button"
                                        onClick={handleNext}
                                        disabled={!canProceed(currentStep)}
                                    >
                                        Next
                                    </Button>
                                )}
                            </div>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
