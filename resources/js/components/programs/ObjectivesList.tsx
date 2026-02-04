import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Plus, Trash2 } from 'lucide-react';

interface ObjectivesListProps {
    values: string[];
    onValuesChange: (values: string[]) => void;
}

export function ObjectivesList({ values, onValuesChange }: ObjectivesListProps) {
    const updateAt = (index: number, value: string) => {
        const updated = [...values];
        updated[index] = value;
        onValuesChange(updated);
    };

    const removeAt = (index: number) => {
        onValuesChange(values.filter((_, i) => i !== index));
    };

    const addObjective = () => {
        onValuesChange([...values, '']);
    };

    return (
        <div className="space-y-2">
            {values.map((val, index) => (
                <div key={index} className="flex items-center gap-2">
                    <span className="min-w-[20px] text-sm text-muted-foreground">
                        {index + 1}.
                    </span>
                    <Input
                        value={val}
                        onChange={(e) => updateAt(index, e.target.value)}
                        placeholder={`Objective ${index + 1}`}
                        className="flex-1"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="h-8 w-8 p-0 text-red-500 hover:text-red-600"
                        onClick={() => removeAt(index)}
                    >
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </div>
            ))}
            <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={addObjective}
                className="w-full"
            >
                <Plus className="mr-2 h-4 w-4" />
                Add Objective
            </Button>
        </div>
    );
}
