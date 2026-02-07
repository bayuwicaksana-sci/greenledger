import type { UseFormReturn } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface Props {
    form: UseFormReturn<any>;
}

export default function ScientificBackgroundForm({ form }: Props) {
    const { data, setData, errors } = form;

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-medium">Scientific Background</h3>
            
            <div className="space-y-2">
                <Label htmlFor="background_text">Background</Label>
                <Textarea
                    id="background_text"
                    value={data.background_text || ''}
                    onChange={(e) => setData('background_text', e.target.value)}
                    placeholder="Research background..."
                    className="min-h-[100px]"
                />
                {errors.background_text && (
                    <p className="text-sm text-red-500">{errors.background_text}</p>
                )}
            </div>

            <div className="space-y-2">
                <Label htmlFor="problem_statement">Problem Statement</Label>
                <Textarea
                    id="problem_statement"
                    value={data.problem_statement || ''}
                    onChange={(e) => setData('problem_statement', e.target.value)}
                    placeholder="Problem formulation..."
                />
                {errors.problem_statement && (
                    <p className="text-sm text-red-500">{errors.problem_statement}</p>
                )}
            </div>

            <div className="space-y-2">
                <Label htmlFor="hypothesis">Hypothesis</Label>
                <Input
                    id="hypothesis"
                    value={data.hypothesis || ''}
                    onChange={(e) => setData('hypothesis', e.target.value)}
                    placeholder="Research hypothesis"
                />
                {errors.hypothesis && (
                    <p className="text-sm text-red-500">{errors.hypothesis}</p>
                )}
            </div>

            <div className="space-y-2">
                <Label htmlFor="journal_references">References</Label>
                <Textarea
                    id="journal_references"
                    value={data.journal_references || ''}
                    onChange={(e) => setData('journal_references', e.target.value)}
                    placeholder="List key references..."
                />
            </div>
        </div>
    );
}
