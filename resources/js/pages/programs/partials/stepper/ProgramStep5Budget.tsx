import type { UseFormReturn } from '@inertiajs/react';
import { AlertCircle } from 'lucide-react';
import BoQManager from '../BoQManager';
import ActivitiesManager from '../ActivitiesManager';

interface Props {
    form: UseFormReturn<any>;
    budgetCategories: any[];
    budgetPhases: any[];
    coaAccounts: any[];
}

export default function ProgramStep5Budget({
    form,
    budgetCategories,
    budgetPhases,
    coaAccounts,
}: Props) {
    const { data } = form;

    return (
        <div className="space-y-8">
            {/* Instructional Banner */}
            <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/20">
                <div className="flex items-start gap-3">
                    <AlertCircle className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    <div className="flex-1">
                        <h4 className="mb-1 font-medium text-blue-900 dark:text-blue-100">
                            Budget Planning Workflow
                        </h4>
                        <p className="text-sm text-blue-700 dark:text-blue-300">
                            1. Create Activities below (organizational structure)
                            <br />
                            2. Add Budget Items (BoQ) and assign them to Activities
                            <br />
                            3. Activity budgets will be calculated automatically from assigned BoQ
                            items
                        </p>
                    </div>
                </div>
            </div>

            {/* STEP 1: Activities Manager */}
            <ActivitiesManager form={form} />

            {/* STEP 2: BoQ Manager (with Activity selector) */}
            <div className="space-y-4">
                <div className="rounded-lg border border-muted bg-muted/50 p-4">
                    <h4 className="mb-2 font-medium">Bill of Quantities (BoQ)</h4>
                    <p className="text-sm text-muted-foreground">
                        Itemized budget breakdown with activity assignment and cost tagging
                    </p>
                </div>

                <BoQManager
                    form={form}
                    categories={budgetCategories}
                    phases={budgetPhases}
                    coaAccounts={coaAccounts}
                    activities={data.activities || []}
                />
            </div>
        </div>
    );
}
