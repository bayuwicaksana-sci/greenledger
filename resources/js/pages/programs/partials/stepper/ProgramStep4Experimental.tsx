import type { UseFormReturn } from '@inertiajs/react';
import ExperimentalDesignForm from '../ExperimentalDesignForm';
import HarvestPlanningForm from '../HarvestPlanningForm';

interface Props {
    form: UseFormReturn<any>;
}

export default function ProgramStep4Experimental({ form }: Props) {
    return (
        <div className="space-y-8">
            <div className="space-y-6">
                <div className="rounded-lg border border-muted bg-muted/50 p-4">
                    <h4 className="mb-2 font-medium">Experimental Design</h4>
                    <p className="text-sm text-muted-foreground">
                        Define the trial design, treatments, and plot layout
                    </p>
                </div>

                <ExperimentalDesignForm form={form} />
            </div>

            <div className="space-y-6">
                <div className="rounded-lg border border-muted bg-muted/50 p-4">
                    <h4 className="mb-2 font-medium">Harvest Planning</h4>
                    <p className="text-sm text-muted-foreground">
                        Schedule harvest events and timeline
                    </p>
                </div>

                <HarvestPlanningForm form={form} />
            </div>
        </div>
    );
}
