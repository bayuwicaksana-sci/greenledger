import type { DraggableAttributes } from '@dnd-kit/core';
import type { SyntheticListenerMap } from '@dnd-kit/core/dist/hooks/utilities';
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
import React, { forwardRef, useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
    conditional_rules?: Record<string, unknown>;
}

interface StepCardContentProps {
    step: StepData;
    index: number;
    users: UserType[];
    roles: Role[];
    permissions: Permission[];
    onUpdate: (id: string, updates: Partial<StepData>) => void;
    onDelete: (id: string) => void;
    // Sortable props (optional - only provided when used in sortable context)
    dragHandleProps?: {
        ref?: React.Ref<HTMLButtonElement>;
        attributes?: DraggableAttributes;
        listeners?: SyntheticListenerMap;
    };
    // Style props for sortable transformation
    style?: React.CSSProperties;
    // Control expand/collapse
    defaultExpanded?: boolean;
    forceCollapsed?: boolean;
}

/**
 * Presentational component for step card content.
 * This component does NOT use useSortable - it receives drag handle props from parent.
 * Used both in sortable list and in DragOverlay.
 */
export const StepCardContent = forwardRef<HTMLDivElement, StepCardContentProps>(
    function StepCardContent(
        {
            step,
            index,
            users,
            roles,
            permissions,
            onUpdate,
            onDelete,
            dragHandleProps,
            style,
            defaultExpanded = true,
            forceCollapsed = false,
        },
        ref,
    ) {
        const [isExpanded, setIsExpanded] = useState(defaultExpanded);

        // Force collapsed state (for drag overlay)
        const actualExpanded = forceCollapsed ? false : isExpanded;

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
            <Card ref={ref} style={style} className="relative">
                <CardHeader className="flex flex-row items-center gap-3 space-y-0">
                    {/* Drag Handle */}
                    <button
                        type="button"
                        ref={
                            dragHandleProps?.ref as React.Ref<HTMLButtonElement>
                        }
                        {...(dragHandleProps?.attributes || {})}
                        {...(dragHandleProps?.listeners || {})}
                        className="cursor-grab touch-none text-muted-foreground hover:text-foreground active:cursor-grabbing"
                        aria-label="Drag to reorder"
                    >
                        <GripVertical className="h-5 w-5" />
                    </button>

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

                    {/* Expand/Collapse Button */}
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
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
                    </Button>

                    {/* Delete Button */}
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="text-destructive hover:text-destructive"
                        onClick={(e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            onDelete(step.id);
                        }}
                        aria-label="Delete step"
                    >
                        <Trash2 className="h-4 w-4" />
                    </Button>
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

                        {/* Description */}
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
                                placeholder="Optional description for this step"
                                rows={2}
                            />
                        </div>

                        {/* Step Type */}
                        <div className="space-y-2">
                            <Label>Step Type</Label>
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
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="sequential">
                                        Sequential (One at a time)
                                    </SelectItem>
                                    <SelectItem value="parallel">
                                        Parallel (All at once)
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Step Purpose */}
                        <div className="space-y-2">
                            <Label>Step Purpose</Label>
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
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="approval">
                                        Approval (can be auto-skipped if
                                        requester is approver)
                                    </SelectItem>
                                    <SelectItem value="action">
                                        Action (Upload transfer receipt, etc.)
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p className="text-xs text-muted-foreground">
                                Approval steps may be auto-skipped when the
                                requester is an approver. Action steps always
                                require manual action.
                            </p>
                        </div>

                        {/* Approver Type */}
                        <div className="space-y-2">
                            <Label>Approver Type</Label>
                            <Select
                                value={step.approver_type}
                                onValueChange={(value) =>
                                    onUpdate(step.id, {
                                        approver_type: value as
                                            | 'user'
                                            | 'role'
                                            | 'permission',
                                        approver_identifiers: [], // Reset when type changes
                                    })
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="user">
                                        Specific Users
                                    </SelectItem>
                                    <SelectItem value="role">
                                        Users with Role
                                    </SelectItem>
                                    <SelectItem value="permission">
                                        Users with Permission
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Approver Selection */}
                        <div className="space-y-2">
                            <Label>
                                Select{' '}
                                {step.approver_type === 'user'
                                    ? 'Users'
                                    : step.approver_type === 'role'
                                      ? 'Roles'
                                      : 'Permissions'}
                            </Label>
                            <div className="max-h-48 space-y-1 overflow-y-auto rounded-md border p-2">
                                {getApproverOptions().map((option) => (
                                    <label
                                        key={option.id}
                                        className="flex cursor-pointer items-center gap-2 rounded p-1 hover:bg-accent"
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
                                                          (id) =>
                                                              id !== option.id,
                                                      );
                                                onUpdate(step.id, {
                                                    approver_identifiers:
                                                        newIdentifiers,
                                                });
                                            }}
                                            className="h-4 w-4 rounded border-gray-300"
                                        />
                                        <span className="text-sm">
                                            {option.name || option.email}
                                        </span>
                                    </label>
                                ))}
                            </div>
                        </div>

                        {/* Required Approvals Count (only for parallel) */}
                        {step.step_type === 'parallel' && (
                            <div className="space-y-2">
                                <Label
                                    htmlFor={`step-${step.id}-required-count`}
                                >
                                    Required Approvals Count
                                </Label>
                                <Input
                                    id={`step-${step.id}-required-count`}
                                    type="number"
                                    min={1}
                                    max={step.approver_identifiers.length || 1}
                                    value={step.required_approvals_count || 1}
                                    onChange={(e) =>
                                        onUpdate(step.id, {
                                            required_approvals_count:
                                                parseInt(e.target.value) || 1,
                                        })
                                    }
                                    placeholder="Number of required approvals"
                                />
                                <p className="text-xs text-muted-foreground">
                                    How many approvals are needed from the
                                    selected approvers?
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
    },
);

interface StepCardProps {
    step: StepData;
    index: number;
    users: UserType[];
    roles: Role[];
    permissions: Permission[];
    onUpdate: (id: string, updates: Partial<StepData>) => void;
    onDelete: (id: string) => void;
}

/**
 * Sortable wrapper for StepCardContent.
 * Uses useSortable hook and passes drag handle props to the presentational component.
 */
export function StepCard({
    step,
    index,
    users,
    roles,
    permissions,
    onUpdate,
    onDelete,
}: StepCardProps) {
    const {
        attributes,
        listeners,
        setNodeRef,
        setActivatorNodeRef,
        transform,
        transition,
        isDragging,
    } = useSortable({ id: step.id });

    const style: React.CSSProperties = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isDragging ? 0.5 : 1,
    };

    return (
        <StepCardContent
            ref={setNodeRef}
            step={step}
            index={index}
            users={users}
            roles={roles}
            permissions={permissions}
            onUpdate={onUpdate}
            onDelete={onDelete}
            style={style}
            dragHandleProps={{
                ref: setActivatorNodeRef,
                attributes,
                listeners,
            }}
        />
    );
}
