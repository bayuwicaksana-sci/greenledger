import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import { useEffect, useState } from 'react';

import {
    index,
    update,
} from '@/actions/App/Http/Controllers/ApprovalWorkflowController';
import { StepBuilder } from '@/components/approval-workflows/StepBuilder';
import type { StepData } from '@/components/approval-workflows/StepCard';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import type {
    ApprovalWorkflow,
    BreadcrumbItem,
    Permission,
    Role,
    SharedData,
    User,
} from '@/types';

interface Props {
    workflow: ApprovalWorkflow;
    modelTypes: Record<string, string>; // class => display name (not used in edit)
    users: User[];
    roles: Role[];
    permissions: Permission[];
}

export default function EditApprovalWorkflow({
    workflow,
    modelTypes,
    users,
    roles,
    permissions,
}: Props) {
    const { site_code } = usePage<SharedData>().props;

    const [formData, setFormData] = useState({
        name: workflow.name,
        description: workflow.description || '',
    });

    const [steps, setSteps] = useState<StepData[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Administration', href: '#' },
        {
            title: 'Approval Workflows',
            href: index.url(site_code!),
        },
        { title: workflow.name, href: '#' },
    ];

    // Load existing steps on mount
    useEffect(() => {
        if (workflow.steps && workflow.steps.length > 0) {
            const loadedSteps: StepData[] = workflow.steps.map((step) => ({
                id: `step-${step.id}`,
                name: step.name,
                description: step.description || '',
                step_order: step.step_order,
                step_type: step.step_type as 'sequential' | 'parallel',
                approver_type: step.approver_type as
                    | 'user'
                    | 'role'
                    | 'permission',
                approver_identifiers: step.approver_identifiers || [],
                required_approvals_count: step.required_approvals_count || 1,
                conditional_rules: step.conditional_rules || undefined,
            }));
            setSteps(loadedSteps);
        }
    }, [workflow]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        // Validation
        const newErrors: Record<string, string> = {};

        if (!formData.name.trim()) {
            newErrors.name = 'Workflow name is required';
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

        router.put(
            update.url({
                site: site_code!,
                approvalWorkflow: workflow.id,
            }),
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
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit ${workflow.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">
                            Edit Workflow: {workflow.name}
                        </h1>
                        <p className="text-muted-foreground">
                            Modify workflow settings and approval steps
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

                {/* Info Alert */}
                <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950">
                    <p className="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Model Type:</strong>{' '}
                        {workflow.model_type.split('\\').pop()} •{' '}
                        <strong>Status:</strong>{' '}
                        {workflow.is_active ? 'Active' : 'Inactive'}
                    </p>
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
                            {isSubmitting ? 'Saving...' : 'Save Changes'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
