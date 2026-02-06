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
import { UseFormReturn } from '@inertiajs/react';

interface Props {
    form: UseFormReturn<any>;
}

export default function NonProgramForm({ form }: Props) {
    const { data, setData, errors } = form;

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-medium">Non-Program Details</h3>

            <div className="space-y-2">
                <Label htmlFor="non_program_category">Category</Label>
                <Select
                    value={data.non_program_category || ''}
                    onValueChange={(value) => setData('non_program_category', value)}
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select category" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="Infrastructure & Facilities">Infrastructure & Facilities</SelectItem>
                        <SelectItem value="Equipment & Machinery">Equipment & Machinery</SelectItem>
                        <SelectItem value="Maintenance & Repairs">Maintenance & Repairs</SelectItem>
                        <SelectItem value="Training & Development">Training & Development</SelectItem>
                        <SelectItem value="Administrative & General Affairs">Administrative & General Affairs</SelectItem>
                        <SelectItem value="Transportation & Logistics">Transportation & Logistics</SelectItem>
                        <SelectItem value="External Services">External Services</SelectItem>
                        <SelectItem value="Amenities">Amenities</SelectItem>
                        <SelectItem value="Other">Other</SelectItem>
                    </SelectContent>
                </Select>
                {errors.non_program_category && (
                    <p className="text-sm text-red-500">{errors.non_program_category}</p>
                )}
            </div>

            <div className="space-y-2">
                <Label htmlFor="description">Activity Description / Justification</Label>
                <Textarea
                    id="description"
                    value={data.description || ''}
                    onChange={(e) => setData('description', e.target.value)}
                    placeholder="Describe the activity, purpose, and justification..."
                    className="min-h-[150px]"
                />
                {errors.description && (
                    <p className="text-sm text-red-500">{errors.description}</p>
                )}
            </div>
        </div>
    );
}
