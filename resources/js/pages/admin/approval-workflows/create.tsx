import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { useState } from 'react';

import {
    index,
    store,
} from '@/actions/App/Http/Controllers/ApprovalWorkflowController';
import { StepBuilder } from '@/components/approval-workflows/StepBuilder';
import type { StepData } from '@/components/approval-workflows/StepCard';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import MainLayout from '@/layouts/main-layout';
import type {
    BreadcrumbItem,
    Permission,
    Role,
    SharedData,
    User,
} from '@/types';
import type { ModelField } from '@/types/approval';

interface Props {
    modelTypes: Record<string, string>; // class => display name
    modelFieldsMap: Record<string, ModelField[]>;
    users: User[];
    roles: Role[];
    permissions: Permission[];
}

export default function CreateApprovalWorkflow({
    modelTypes,
    modelFieldsMap,
    users,
    roles,
    permissions,
}: Props) {
    const { site_code } = usePage<SharedData>().props;

    const [formData, setFormData] = useState({
        name: '',
        description: '',
        model_type: '',
        set_active: false,
    });

    const [steps, setSteps] = useState<StepData[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Get fields for selected model
    const selectedModelFields = formData.model_type
        ? modelFieldsMap[formData.model_type] || []
        : [];

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Administration', href: '#' },
        {
            title: 'Approval Workflows',
            href: index().url,
        },
        { title: 'Create Workflow', href: '#' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        // Validation
        const newErrors: Record<string, string> = {};

        if (!formData.name.trim()) {
            newErrors.name = 'Workflow name is required';
        }

        if (!formData.model_type) {
            newErrors.model_type = 'Model type is required';
        }

        if (steps.length === 0) {
            newErrors.steps = 'At least one approval step is required';
        }

        // Validate each step
        steps.forEach((step, index) => {
            if (!step.name.trim()) {
                newErrors[`step_${index}_name`] =
                    `Step ${index + 1} name is required`;
            }
            if (step.approver_identifiers.length === 0) {
                newErrors[`step_${index}_approvers`] =
                    `Step ${index + 1} must have at least one approver`;
            }
        });

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setIsSubmitting(true);

        // Prepare steps data (remove temporary IDs)
        const stepsData = steps.map((step) => ({
            name: step.name,
            description: step.description || null,
            step_order: step.step_order,
            step_type: step.step_type,
            approver_type: step.approver_type,
            approver_identifiers: step.approver_identifiers,
            required_approvals_count: step.required_approvals_count || null,
            conditional_rules: step.conditional_rules || null,
        }));

        router.post(
            store(),
            {
                ...formData,
                steps: stepsData,
            },
            {
                onError: (errors) => {
                    setErrors(errors as Record<string, string>);
                    setIsSubmitting(false);
                },
                onSuccess: () => {
                    setIsSubmitting(false);
                },
            },
        );
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Approval Workflow" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">
                            Create Approval Workflow
                        </h1>
                        <p className="text-muted-foreground">
                            Configure a new approval workflow for your models
                        </p>
                    </div>
                    <Button
                        variant="outline"
                        onClick={() => window.history.back()}
                    >
                        <ArrowLeft className="mr-2 h-4 w-4" />
                        Back
                    </Button>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Basic Information */}
                    <div className="rounded-lg border p-6">
                        <h2 className="mb-4 text-lg font-semibold">
                            Basic Information
                        </h2>
                        <div className="space-y-4">
                            {/* Workflow Name */}
                            <div className="space-y-2">
                                <Label htmlFor="name">Workflow Name *</Label>
                                <Input
                                    id="name"
                                    value={formData.name}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            name: e.target.value,
                                        })
                                    }
                                    placeholder="e.g., Payment Request Approval"
                                    className={
                                        errors.name ? 'border-destructive' : ''
                                    }
                                />
                                {errors.name && (
                                    <p className="text-sm text-destructive">
                                        {errors.name}
                                    </p>
                                )}
                            </div>

                            {/* Description */}
                            <div className="space-y-2">
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={formData.description}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            description: e.target.value,
                                        })
                                    }
                                    placeholder="Optional description of this workflow"
                                    rows={3}
                                />
                            </div>

                            {/* Model Type */}
                            <div className="space-y-2">
                                <Label htmlFor="model_type">Model Type *</Label>
                                <Select
                                    value={formData.model_type}
                                    onValueChange={(value) =>
                                        setFormData({
                                            ...formData,
                                            model_type: value,
                                        })
                                    }
                                >
                                    <SelectTrigger
                                        id="model_type"
                                        className={
                                            errors.model_type
                                                ? 'border-destructive'
                                                : ''
                                        }
                                    >
                                        <SelectValue placeholder="Select a model type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(modelTypes).map(
                                            ([className, displayName]) => (
                                                <SelectItem
                                                    key={className}
                                                    value={className}
                                                >
                                                    {displayName}
                                                </SelectItem>
                                            ),
                                        )}
                                    </SelectContent>
                                </Select>
                                {errors.model_type && (
                                    <p className="text-sm text-destructive">
                                        {errors.model_type}
                                    </p>
                                )}
                            </div>

                            {/* Set as Active */}
                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="set_active"
                                    checked={formData.set_active}
                                    onCheckedChange={(checked) =>
                                        setFormData({
                                            ...formData,
                                            set_active: checked === true,
                                        })
                                    }
                                />
                                <Label
                                    htmlFor="set_active"
                                    className="cursor-pointer font-normal"
                                >
                                    Set as active workflow for this model type
                                </Label>
                            </div>
                        </div>
                    </div>

                    {/* Approval Steps */}
                    <div className="rounded-lg border p-6">
                        <div className="mb-4">
                            <h2 className="text-lg font-semibold">
                                Approval Steps *
                            </h2>
                            <p className="text-sm text-muted-foreground">
                                Define the approval steps in order. Drag to
                                reorder.
                            </p>
                        </div>

                        <StepBuilder
                            steps={steps}
                            onStepsChange={setSteps}
                            users={users}
                            roles={roles}
                            permissions={permissions}
                            availableFields={selectedModelFields}
                        />

                        {errors.steps && (
                            <p className="mt-2 text-sm text-destructive">
                                {errors.steps}
                            </p>
                        )}

                        {/* Step-specific errors */}
                        {Object.entries(errors)
                            .filter(([key]) => key.startsWith('step_'))
                            .map(([key, message]) => (
                                <p
                                    key={key}
                                    className="mt-1 text-sm text-destructive"
                                >
                                    {message}
                                </p>
                            ))}
                    </div>

                    {/* Submit Button */}
                    <div className="flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => window.history.back()}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isSubmitting}>
                            <Save className="mr-2 h-4 w-4" />
                            {isSubmitting ? 'Creating...' : 'Create Workflow'}
                        </Button>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
