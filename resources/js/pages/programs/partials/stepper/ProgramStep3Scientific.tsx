import type { UseFormReturn } from '@inertiajs/react';
import ScientificBackgroundForm from '../ScientificBackgroundForm';

interface Props {
    form: UseFormReturn<any>;
}

export default function ProgramStep3Scientific({ form }: Props) {
    return (
        <div className="space-y-6">
            <div className="rounded-lg border border-muted bg-muted/50 p-4">
                <h4 className="mb-2 font-medium">Scientific Background</h4>
                <p className="text-sm text-muted-foreground">
                    Provide the research context, problem statement, and
                    objectives
                </p>
            </div>

            <ScientificBackgroundForm form={form} />
        </div>
    );
}
