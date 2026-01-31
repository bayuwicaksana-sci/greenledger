import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import {
    ChevronDown,
    ChevronUp,
    GripVertical,
    Shield,
    Trash2,
    User,
    Users,
} from 'lucide-react';
import { useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
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
import type { Permission, Role, User as UserType } from '@/types';

export interface StepData {
    id: string;
    name: string;
    description?: string;
    step_order: number;
    step_type: 'sequential' | 'parallel';
    step_purpose: 'approval' | 'action';
    approver_type: 'user' | 'role' | 'permission';
    approver_identifiers: number[];
    required_approvals_count?: number;
    conditional_rules?: Record<string, any>;
}

interface StepCardProps {
    step: StepData;
    index: number;
    users: UserType[];
    roles: Role[];
    permissions: Permission[];
    onUpdate: (id: string, updates: Partial<StepData>) => void;
    onDelete: (id: string) => void;
    initialExpanded?: boolean;
    forceCollapsed?: boolean;
}

export function StepCard({
    step,
    index,
    users,
    roles,
    permissions,
    onUpdate,
    onDelete,
    initialExpanded = true,
    forceCollapsed = false,
}: StepCardProps) {
    const [isExpanded, setIsExpanded] = useState(initialExpanded);

    // Force collapsed state (for drag overlay)
    const actualExpanded = forceCollapsed ? false : isExpanded;

    const {
        attributes,
        listeners,
        setNodeRef,
        setActivatorNodeRef,
        transform,
        transition,
        isDragging,
    } = useSortable({ id: step.id });

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isDragging ? 0.5 : 1,
    };

    const getApproverIcon = () => {
        switch (step.approver_type) {
            case 'user':
                return <User className="h-4 w-4" />;
            case 'role':
                return <Users className="h-4 w-4" />;
            case 'permission':
                return <Shield className="h-4 w-4" />;
        }
    };

    const getApproverOptions = () => {
        switch (step.approver_type) {
            case 'user':
                return users;
            case 'role':
                return roles;
            case 'permission':
                return permissions;
            default:
                return [];
        }
    };

    const getApproverLabel = (id: number) => {
        const options = getApproverOptions();
        const option = options.find((o) => o.id === id);
        return option?.name || option?.email || `ID: ${id}`;
    };

    return (
        <Card ref={setNodeRef} style={style} className="relative">
            <CardHeader className="flex flex-row items-center gap-3 space-y-0 pb-3">
                {/* Drag Handle */}
                <button
                    type="button"
                    ref={setActivatorNodeRef}
                    {...attributes}
                    {...listeners}
                    className="cursor-grab touch-none text-muted-foreground hover:text-foreground active:cursor-grabbing"
                    aria-label="Drag to reorder"
                >
                    <GripVertical className="h-5 w-5" />
                </button>

                {/* Step Info - clickable area for dragging when collapsed */}
                <div
                    ref={!actualExpanded ? setActivatorNodeRef : undefined}
                    {...(!actualExpanded ? attributes : {})}
                    {...(!actualExpanded ? listeners : {})}
                    className={`flex flex-1 items-center gap-3 ${
                        !actualExpanded
                            ? 'cursor-grab active:cursor-grabbing'
                            : ''
                    }`}
                >
                    {/* Step Number */}
                    <Badge variant="outline" className="shrink-0">
                        Step {index + 1}
                    </Badge>

                    {/* Step Name */}
                    <div className="flex-1 font-semibold">
                        {step.name || 'Untitled Step'}
                    </div>

                    {/* Approver Type Icon */}
                    <div className="text-muted-foreground">
                        {getApproverIcon()}
                    </div>
                </div>

                {/* Expand/Collapse Button */}
                <button
                    type="button"
                    className="inline-flex h-8 w-8 items-center justify-center rounded-md p-0 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                    onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        setIsExpanded(!isExpanded);
                    }}
                    aria-label={actualExpanded ? 'Collapse' : 'Expand'}
                >
                    {actualExpanded ? (
                        <ChevronUp className="h-4 w-4" />
                    ) : (
                        <ChevronDown className="h-4 w-4" />
                    )}
                </button>

                {/* Delete Button */}
                <button
                    type="button"
                    className="inline-flex h-8 w-8 items-center justify-center rounded-md p-0 text-sm font-medium text-destructive transition-colors hover:bg-accent hover:text-accent-foreground hover:text-destructive"
                    onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        onDelete(step.id);
                    }}
                    aria-label="Delete step"
                >
                    <Trash2 className="h-4 w-4" />
                </button>
            </CardHeader>

            {actualExpanded && (
                <CardContent className="space-y-4">
                    {/* Step Name */}
                    <div className="space-y-2">
                        <Label htmlFor={`step-${step.id}-name`}>
                            Step Name *
                        </Label>
                        <Input
                            id={`step-${step.id}-name`}
                            value={step.name}
                            onChange={(e) =>
                                onUpdate(step.id, { name: e.target.value })
                            }
                            placeholder="e.g., Manager Approval"
                        />
                    </div>

                    {/* Step Description */}
                    <div className="space-y-2">
                        <Label htmlFor={`step-${step.id}-description`}>
                            Description
                        </Label>
                        <Textarea
                            id={`step-${step.id}-description`}
                            value={step.description || ''}
                            onChange={(e) =>
                                onUpdate(step.id, {
                                    description: e.target.value,
                                })
                            }
                            placeholder="Optional description of this approval step"
                            rows={2}
                        />
                    </div>

                    {/* Step Type */}
                    <div className="space-y-2">
                        <Label htmlFor={`step-${step.id}-type`}>
                            Step Type *
                        </Label>
                        <Select
                            value={step.step_type}
                            onValueChange={(value) =>
                                onUpdate(step.id, {
                                    step_type: value as
                                        | 'sequential'
                                        | 'parallel',
                                })
                            }
                        >
                            <SelectTrigger id={`step-${step.id}-type`}>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="sequential">
                                    Sequential (one at a time)
                                </SelectItem>
                                <SelectItem value="parallel">
                                    Parallel (all at once)
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    {/* Step Purpose */}
                    <div className="space-y-2">
                        <Label htmlFor={`step-${step.id}-purpose`}>
                            Step Purpose *
                        </Label>
                        <Select
                            value={step.step_purpose}
                            onValueChange={(value) =>
                                onUpdate(step.id, {
                                    step_purpose: value as
                                        | 'approval'
                                        | 'action',
                                })
                            }
                        >
                            <SelectTrigger id={`step-${step.id}-purpose`}>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="approval">
                                    Approval (can be auto-skipped)
                                </SelectItem>
                                <SelectItem value="action">
                                    Action (always executes)
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p className="text-xs text-muted-foreground">
                            {step.step_purpose === 'approval'
                                ? 'This step requires approval and will be auto-skipped if the requester is in the approvers list.'
                                : 'This step always executes regardless of who the requester is (use for actions like notifications or final processing).'}
                        </p>
                    </div>

                    {/* Approver Type */}
                    <div className="space-y-2">
                        <Label htmlFor={`step-${step.id}-approver-type`}>
                            Approver Type *
                        </Label>
                        <Select
                            value={step.approver_type}
                            onValueChange={(value) =>
                                onUpdate(step.id, {
                                    approver_type: value as
                                        | 'user'
                                        | 'role'
                                        | 'permission',
                                    approver_identifiers: [], // Reset identifiers when type changes
                                })
                            }
                        >
                            <SelectTrigger id={`step-${step.id}-approver-type`}>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="user">
                                    Specific Users
                                </SelectItem>
                                <SelectItem value="role">Roles</SelectItem>
                                <SelectItem value="permission">
                                    Permissions
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    {/* Approver Identifiers */}
                    <div className="space-y-2">
                        <Label>Approvers *</Label>
                        <div className="space-y-2">
                            {getApproverOptions().map((option) => (
                                <label
                                    key={option.id}
                                    className="flex items-center gap-2"
                                >
                                    <input
                                        type="checkbox"
                                        checked={step.approver_identifiers.includes(
                                            option.id,
                                        )}
                                        onChange={(e) => {
                                            const newIdentifiers = e.target
                                                .checked
                                                ? [
                                                      ...step.approver_identifiers,
                                                      option.id,
                                                  ]
                                                : step.approver_identifiers.filter(
                                                      (id) => id !== option.id,
                                                  );
                                            onUpdate(step.id, {
                                                approver_identifiers:
                                                    newIdentifiers,
                                            });
                                        }}
                                        className="h-4 w-4 rounded border-gray-300"
                                    />
                                    <span className="text-sm">
                                        {option.name ||
                                            (option as UserType).email}
                                    </span>
                                </label>
                            ))}
                        </div>
                    </div>

                    {/* Required Approvals Count (for parallel steps) */}
                    {step.step_type === 'parallel' && (
                        <div className="space-y-2">
                            <Label
                                htmlFor={`step-${step.id}-required-approvals`}
                            >
                                Required Approvals
                            </Label>
                            <Input
                                id={`step-${step.id}-required-approvals`}
                                type="number"
                                min={1}
                                max={step.approver_identifiers.length}
                                value={step.required_approvals_count || 1}
                                onChange={(e) =>
                                    onUpdate(step.id, {
                                        required_approvals_count: parseInt(
                                            e.target.value,
                                        ),
                                    })
                                }
                                placeholder="Number of required approvals"
                            />
                            <p className="text-xs text-muted-foreground">
                                How many approvals are needed from the selected
                                approvers?
                            </p>
                        </div>
                    )}

                    {/* Selected Approvers Summary */}
                    {step.approver_identifiers.length > 0 && (
                        <div className="rounded-md bg-muted p-3">
                            <p className="mb-2 text-xs font-medium text-muted-foreground">
                                Selected Approvers:
                            </p>
                            <div className="flex flex-wrap gap-1">
                                {step.approver_identifiers.map((id) => (
                                    <Badge key={id} variant="secondary">
                                        {getApproverLabel(id)}
                                    </Badge>
                                ))}
                            </div>
                        </div>
                    )}
                </CardContent>
            )}
        </Card>
    );
}
