import type { UseFormReturn } from '@inertiajs/react';
import { useState } from 'react';
import { Plus, Trash2, GripVertical, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { cn } from '@/lib/utils';

interface Props {
    form: UseFormReturn<any>;
}

export default function ActivitiesManager({ form }: Props) {
    const { data, setData, errors } = form;

    const activities = Array.isArray(data.activities) ? data.activities : [];

    // State for collapsed activities
    const [collapsedActivities, setCollapsedActivities] = useState<Set<number>>(
        new Set()
    );

    const toggleActivity = (index: number) => {
        setCollapsedActivities((prev) => {
            const next = new Set(prev);
            if (next.has(index)) {
                next.delete(index);
            } else {
                next.add(index);
            }
            return next;
        });
    };

    const addActivity = () => {
        const newActivity = {
            activity_name: '',
            description: '',
            planned_start_date: '',
            planned_end_date: '',
        };
        setData('activities', [...activities, newActivity]);
    };

    const removeActivity = (index: number) => {
        const newActivities = activities.filter((_: any, i: number) => i !== index);
        setData('activities', newActivities);
    };

    const updateActivity = (index: number, field: string, value: any) => {
        const newActivities = [...activities];
        newActivities[index] = { ...newActivities[index], [field]: value };
        setData('activities', newActivities);
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="rounded-lg border border-muted bg-muted/50 p-4">
                <h4 className="mb-2 font-medium">Program Activities</h4>
                <p className="text-sm text-muted-foreground">
                    Define the organizational structure of your program. Budget for each activity
                    will be calculated from Budget Items (BoQ) assigned to it in the next section.
                </p>
            </div>

            {/* Activities List */}
            <div className="space-y-4">
                {activities.length === 0 ? (
                    <div className="rounded-lg border-2 border-dashed border-muted p-8 text-center">
                        <p className="text-sm text-muted-foreground">
                            No activities yet. Click "Add Activity" to create the first one.
                        </p>
                    </div>
                ) : (
                    activities.map((activity: any, index: number) => (
                        <Collapsible
                            key={index}
                            open={!collapsedActivities.has(index)}
                            onOpenChange={() => toggleActivity(index)}
                        >
                            <Card>
                                <CardHeader className="flex flex-row items-center justify-between pb-3">
                                    <CollapsibleTrigger asChild>
                                        <div className="flex flex-1 cursor-pointer items-center gap-2">
                                            <GripVertical className="h-5 w-5 text-muted-foreground" />
                                            <h5 className="font-medium">
                                                {activity.activity_name || `Activity #${index + 1}`}
                                            </h5>
                                            <ChevronDown
                                                className={cn(
                                                    "h-4 w-4 text-muted-foreground transition-transform",
                                                    !collapsedActivities.has(index) && "rotate-180"
                                                )}
                                            />
                                        </div>
                                    </CollapsibleTrigger>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            removeActivity(index);
                                        }}
                                    >
                                        <Trash2 className="h-4 w-4 text-red-600" />
                                    </Button>
                                </CardHeader>
                                <CollapsibleContent>
                                    <CardContent className="space-y-4">
                                {/* Activity Name */}
                                <div className="space-y-2">
                                    <Label>
                                        Activity Name <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        value={activity.activity_name || ''}
                                        onChange={(e) =>
                                            updateActivity(index, 'activity_name', e.target.value)
                                        }
                                        placeholder="e.g., Field Preparation, Planting, Fertilization"
                                        maxLength={200}
                                    />
                                    {errors[`activities.${index}.activity_name`] && (
                                        <p className="text-sm text-red-500">
                                            {errors[`activities.${index}.activity_name`]}
                                        </p>
                                    )}
                                </div>

                                {/* Description */}
                                <div className="space-y-2">
                                    <Label>Description</Label>
                                    <Textarea
                                        value={activity.description || ''}
                                        onChange={(e) =>
                                            updateActivity(index, 'description', e.target.value)
                                        }
                                        placeholder="Describe this activity..."
                                        rows={2}
                                    />
                                    {errors[`activities.${index}.description`] && (
                                        <p className="text-sm text-red-500">
                                            {errors[`activities.${index}.description`]}
                                        </p>
                                    )}
                                </div>

                                {/* Planned Dates */}
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label>Planned Start Date</Label>
                                        <Input
                                            type="date"
                                            value={activity.planned_start_date || ''}
                                            onChange={(e) =>
                                                updateActivity(
                                                    index,
                                                    'planned_start_date',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {errors[`activities.${index}.planned_start_date`] && (
                                            <p className="text-sm text-red-500">
                                                {errors[`activities.${index}.planned_start_date`]}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label>Planned End Date</Label>
                                        <Input
                                            type="date"
                                            value={activity.planned_end_date || ''}
                                            onChange={(e) =>
                                                updateActivity(
                                                    index,
                                                    'planned_end_date',
                                                    e.target.value,
                                                )
                                            }
                                            min={activity.planned_start_date || ''}
                                        />
                                        {errors[`activities.${index}.planned_end_date`] && (
                                            <p className="text-sm text-red-500">
                                                {errors[`activities.${index}.planned_end_date`]}
                                            </p>
                                        )}
                                    </div>
                                </div>
                                    </CardContent>
                                </CollapsibleContent>
                            </Card>
                        </Collapsible>
                    ))
                )}
            </div>

            {/* Add Activity Button */}
            <Button type="button" variant="outline" onClick={addActivity} className="w-full">
                <Plus className="mr-2 h-4 w-4" />
                Add Activity
            </Button>

            {errors.activities && typeof errors.activities === 'string' && (
                <p className="text-sm text-red-500">{errors.activities}</p>
            )}
        </div>
    );
}
