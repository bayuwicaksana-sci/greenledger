import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Plus, Trash2 } from 'lucide-react';
import type { TreatmentFormData } from '@/types/programs';

interface TreatmentTableProps {
    treatments: TreatmentFormData[];
    onChange: (treatments: TreatmentFormData[]) => void;
    errors?: Record<string, string>;
}

const EMPTY_TREATMENT: TreatmentFormData = {
    treatment_code: '',
    treatment_description: '',
    specification: '',
};

export function TreatmentTable({ treatments, onChange, errors }: TreatmentTableProps) {
    const updateAt = (index: number, field: keyof TreatmentFormData, value: string) => {
        const updated = [...treatments];
        updated[index] = { ...updated[index], [field]: value };
        onChange(updated);
    };

    const removeAt = (index: number) => {
        onChange(treatments.filter((_, i) => i !== index));
    };

    const addTreatment = () => {
        onChange([...treatments, { ...EMPTY_TREATMENT }]);
    };

    return (
        <div className="space-y-3">
            {treatments.length > 0 && (
                <div className="overflow-x-auto rounded-md border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="w-8 p-2 text-left">#</th>
                                <th className="w-36 p-2 text-left">Code</th>
                                <th className="p-2 text-left">Description</th>
                                <th className="p-2 text-left">Specification</th>
                                <th className="w-10 p-2" />
                            </tr>
                        </thead>
                        <tbody>
                            {treatments.map((treatment, index) => (
                                <tr key={index} className="border-b last:border-0">
                                    <td className="p-2 text-muted-foreground">
                                        {index + 1}
                                    </td>
                                    <td className="p-2">
                                        <Input
                                            value={treatment.treatment_code}
                                            onChange={(e) =>
                                                updateAt(index, 'treatment_code', e.target.value)
                                            }
                                            placeholder="T1"
                                            className="h-8"
                                        />
                                    </td>
                                    <td className="p-2">
                                        <Input
                                            value={treatment.treatment_description}
                                            onChange={(e) =>
                                                updateAt(index, 'treatment_description', e.target.value)
                                            }
                                            placeholder="Description"
                                            className="h-8"
                                        />
                                    </td>
                                    <td className="p-2">
                                        <Textarea
                                            value={treatment.specification}
                                            onChange={(e) =>
                                                updateAt(index, 'specification', e.target.value)
                                            }
                                            placeholder="Specification details"
                                            className="h-16 resize-none"
                                        />
                                    </td>
                                    <td className="p-2">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            className="h-8 w-8 p-0 text-red-500 hover:text-red-600"
                                            onClick={() => removeAt(index)}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
            {errors?.treatments && (
                <p className="text-sm text-red-500">{errors.treatments}</p>
            )}
            <Button type="button" variant="outline" size="sm" onClick={addTreatment}>
                <Plus className="mr-2 h-4 w-4" />
                Add Treatment
            </Button>
        </div>
    );
}
