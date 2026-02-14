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
import { Textarea } from '@/components/ui/textarea';
import { MultiSelect } from '@/components/programs/MultiSelect';

interface Props {
    form: UseFormReturn<any>;
    users: any[];
    activePrograms: any[];
}

export default function ProgramStep2Team({
    form,
    users,
    activePrograms,
}: Props) {
    const { data, setData, errors } = form;

    const userOptions = users.map((user) => ({
        value: user.id.toString(),
        label: user.name,
    }));

    return (
        <div className="space-y-6">
            <div className="rounded-lg border border-muted bg-muted/50 p-4">
                <h4 className="mb-2 font-medium">Research Team</h4>
                <p className="text-sm text-muted-foreground">
                    Assign team members responsible for this program
                </p>
            </div>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {/* Research Associate */}
                <div className="space-y-2">
                    <Label>Research Associate</Label>
                    <Select
                        value={data.research_associate_id}
                        onValueChange={(val) =>
                            setData('research_associate_id', val)
                        }
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select research associate" />
                        </SelectTrigger>
                        <SelectContent>
                            {users.map((user) => (
                                <SelectItem
                                    key={user.id}
                                    value={user.id.toString()}
                                >
                                    {user.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    {errors.research_associate_id && (
                        <p className="text-sm text-red-500">
                            {errors.research_associate_id}
                        </p>
                    )}
                </div>

                {/* Research Officer */}
                <div className="space-y-2">
                    <Label>Research Officer</Label>
                    <Select
                        value={data.research_officer_id}
                        onValueChange={(val) =>
                            setData('research_officer_id', val)
                        }
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select research officer" />
                        </SelectTrigger>
                        <SelectContent>
                            {users.map((user) => (
                                <SelectItem
                                    key={user.id}
                                    value={user.id.toString()}
                                >
                                    {user.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    {errors.research_officer_id && (
                        <p className="text-sm text-red-500">
                            {errors.research_officer_id}
                        </p>
                    )}
                </div>
            </div>

            {/* Support Team Members */}
            <div className="space-y-2">
                <Label>Support Team Members</Label>
                <MultiSelect
                    options={userOptions}
                    values={data.support_team_member_ids || []}
                    onValuesChange={(values) =>
                        setData('support_team_member_ids', values)
                    }
                    placeholder="Select support team members"
                />
                <p className="text-xs text-muted-foreground">
                    Select one or more team members
                </p>
                {errors.support_team_member_ids && (
                    <p className="text-sm text-red-500">
                        {errors.support_team_member_ids}
                    </p>
                )}
            </div>

            <div className="rounded-lg border border-muted bg-muted/50 p-4">
                <h4 className="mb-2 font-medium">Timeline & Dependencies</h4>
                <p className="text-sm text-muted-foreground">
                    Schedule and program relationships
                </p>
            </div>

            {/* Planting Start Date */}
            <div className="space-y-2">
                <Label>Planting Start Date</Label>
                <Input
                    type="date"
                    value={data.planting_start_date}
                    onChange={(e) =>
                        setData('planting_start_date', e.target.value)
                    }
                />
                <p className="text-xs text-muted-foreground">
                    When will the planting phase begin?
                </p>
                {errors.planting_start_date && (
                    <p className="text-sm text-red-500">
                        {errors.planting_start_date}
                    </p>
                )}
            </div>

            {/* Prerequisite Program */}
            <div className="space-y-2">
                <Label>Prerequisite Program</Label>
                <Select
                    value={data.prerequisite_program_id}
                    onValueChange={(val) =>
                        setData('prerequisite_program_id', val)
                    }
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select prerequisite program (optional)" />
                    </SelectTrigger>
                    <SelectContent>
                        {activePrograms.map((program) => (
                            <SelectItem
                                key={program.id}
                                value={program.id.toString()}
                            >
                                {program.program_code} - {program.program_name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <p className="text-xs text-muted-foreground">
                    Does this program depend on another program?
                </p>
                {errors.prerequisite_program_id && (
                    <p className="text-sm text-red-500">
                        {errors.prerequisite_program_id}
                    </p>
                )}
            </div>

            {/* Dependency Note */}
            {data.prerequisite_program_id && (
                <div className="space-y-2">
                    <Label>Dependency Note</Label>
                    <Textarea
                        value={data.dependency_note}
                        onChange={(e) =>
                            setData('dependency_note', e.target.value)
                        }
                        placeholder="Explain how this program depends on the prerequisite..."
                        rows={3}
                    />
                    {errors.dependency_note && (
                        <p className="text-sm text-red-500">
                            {errors.dependency_note}
                        </p>
                    )}
                </div>
            )}
        </div>
    );
}
