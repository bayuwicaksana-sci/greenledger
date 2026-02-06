import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { UseFormReturn } from '@inertiajs/react';

interface Props {
    form: UseFormReturn<any>;
}

export default function HarvestPlanningForm({ form }: Props) {
    const { data, setData, errors } = form;

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-medium">Harvest Planning</h3>

            <div className="space-y-2">
                <Label htmlFor="harvest_type">Harvest Type</Label>
                <Select
                    value={data.harvest_type || ''}
                    onValueChange={(value) => setData('harvest_type', value)}
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="single">Single Harvest</SelectItem>
                        <SelectItem value="multiple">Multiple Harvests</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            {data.harvest_type === 'single' && (
                <div className="space-y-2">
                    <Label htmlFor="estimated_harvest_date">Estimated Harvest Date</Label>
                    <Input
                        id="estimated_harvest_date"
                        type="date"
                        value={data.estimated_harvest_date || ''}
                        onChange={(e) => setData('estimated_harvest_date', e.target.value)}
                    />
                </div>
            )}

            {data.harvest_type === 'multiple' && (
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div className="space-y-2">
                        <Label htmlFor="harvest_frequency_value">Frequency Value</Label>
                        <Input
                            id="harvest_frequency_value"
                            type="number"
                            min="1"
                            value={data.harvest_frequency_value || ''}
                            onChange={(e) => setData('harvest_frequency_value', e.target.value)}
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="harvest_frequency_unit">Frequency Unit</Label>
                        <Select
                            value={data.harvest_frequency_unit || ''}
                            onValueChange={(value) => setData('harvest_frequency_unit', value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Unit" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="days">Days</SelectItem>
                                <SelectItem value="weeks">Weeks</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="harvest_event_count">Number of Harvests</Label>
                        <Input
                            id="harvest_event_count"
                            type="number"
                            min="1"
                            value={data.harvest_event_count || ''}
                            onChange={(e) => setData('harvest_event_count', e.target.value)}
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="first_harvest_date">First Harvest Date</Label>
                        <Input
                            id="first_harvest_date"
                            type="date"
                            value={data.first_harvest_date || ''}
                            onChange={(e) => setData('first_harvest_date', e.target.value)}
                        />
                    </div>
                </div>
            )}
        </div>
    );
}
