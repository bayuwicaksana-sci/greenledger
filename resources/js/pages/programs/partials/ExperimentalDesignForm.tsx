import type { UseFormReturn } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Props {
    form: UseFormReturn<any>;
}

export default function ExperimentalDesignForm({ form }: Props) {
    const { data, setData, errors } = form;

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-medium">Experimental Design</h3>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="trial_design">Trial Design</Label>
                    <Select
                        value={data.trial_design || ''}
                        onValueChange={(value) => setData('trial_design', value)}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select design" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="RAL">RAL</SelectItem>
                            <SelectItem value="RCBD">RCBD</SelectItem>
                            <SelectItem value="Split Plot">Split Plot</SelectItem>
                            <SelectItem value="Factorial">Factorial</SelectItem>
                            <SelectItem value="Strip Plot">Strip Plot</SelectItem>
                            <SelectItem value="Lattice">Lattice</SelectItem>
                            <SelectItem value="Other">Other</SelectItem>
                        </SelectContent>
                    </Select>
                    {errors.trial_design && (
                        <p className="text-sm text-red-500">{errors.trial_design}</p>
                    )}
                </div>

                {data.trial_design === 'Other' && (
                    <div className="space-y-2">
                        <Label htmlFor="trial_design_other">Specify Design</Label>
                        <Input
                            id="trial_design_other"
                            value={data.trial_design_other || ''}
                            onChange={(e) => setData('trial_design_other', e.target.value)}
                        />
                    </div>
                )}
            </div>

            <div className="grid grid-cols-3 gap-4">
                <div className="space-y-2">
                    <Label htmlFor="num_treatments">Treatments</Label>
                    <Input
                        id="num_treatments"
                        type="number"
                        value={data.num_treatments || ''}
                        onChange={(e) => setData('num_treatments', e.target.value)}
                    />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="num_replications">Replications</Label>
                    <Input
                        id="num_replications"
                        type="number"
                        value={data.num_replications || ''}
                        onChange={(e) => setData('num_replications', e.target.value)}
                    />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="num_samples_per_replication">Samples/Rep</Label>
                    <Input
                        id="num_samples_per_replication"
                        type="number"
                        value={data.num_samples_per_replication || ''}
                        onChange={(e) => setData('num_samples_per_replication', e.target.value)}
                    />
                </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                    <Label htmlFor="plot_width_m">Plot Width (m)</Label>
                    <Input
                        id="plot_width_m"
                        type="number"
                        step="0.01"
                        value={data.plot_width_m || ''}
                        onChange={(e) => setData('plot_width_m', e.target.value)}
                    />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="plot_length_m">Plot Length (m)</Label>
                    <Input
                        id="plot_length_m"
                        type="number"
                        step="0.01"
                        value={data.plot_length_m || ''}
                        onChange={(e) => setData('plot_length_m', e.target.value)}
                    />
                </div>
            </div>
            
             <div className="space-y-2">
                <Label htmlFor="google_maps_url">Google Maps URL</Label>
                <Input
                    id="google_maps_url"
                    value={data.google_maps_url || ''}
                    onChange={(e) => setData('google_maps_url', e.target.value)}
                    placeholder="https://maps.google.com/..."
                />
            </div>
        </div>
    );
}
