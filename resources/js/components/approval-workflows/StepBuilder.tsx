import type {
    DragEndEvent,
    DragStartEvent} from '@dnd-kit/core';
import {
    closestCenter,
    DndContext,
    DragOverlay,
    KeyboardSensor,
    PointerSensor,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import {
    arrayMove,
    SortableContext,
    sortableKeyboardCoordinates,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { Plus } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import type { Permission, Role, User } from '@/types';
import type { ModelField } from '@/types/approval';

import { StepCard, StepCardContent, type StepData } from './StepCard';

interface StepBuilderProps {
    steps: StepData[];
    onStepsChange: (steps: StepData[]) => void;
    users: User[];
    roles: Role[];
    permissions: Permission[];
    availableFields?: ModelField[];
}

export function StepBuilder({
    steps,
    onStepsChange,
    users,
    roles,
    permissions,
    availableFields,
}: StepBuilderProps) {
    const [activeId, setActiveId] = useState<string | null>(null);

    const sensors = useSensors(
        useSensor(PointerSensor),
        useSensor(KeyboardSensor, {
            coordinateGetter: sortableKeyboardCoordinates,
        }),
    );

    const handleDragStart = (event: DragStartEvent) => {
        setActiveId(event.active.id as string);
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;

        if (over && active.id !== over.id) {
            const oldIndex = steps.findIndex((step) => step.id === active.id);
            const newIndex = steps.findIndex((step) => step.id === over.id);

            const reorderedSteps = arrayMove(steps, oldIndex, newIndex).map(
                (step, index) => ({
                    ...step,
                    step_order: index,
                }),
            );

            onStepsChange(reorderedSteps);
        }

        setActiveId(null);
    };

    const handleAddStep = () => {
        const newStep: StepData = {
            id: `step-${Date.now()}`,
            name: '',
            description: '',
            step_order: steps.length,
            step_type: 'sequential',
            step_purpose: 'approval',
            approver_type: 'user',
            approver_identifiers: [],
            required_approvals_count: 1,
        };

        onStepsChange([...steps, newStep]);
    };

    const handleUpdateStep = (id: string, updates: Partial<StepData>) => {
        const updatedSteps = steps.map((step) =>
            step.id === id ? { ...step, ...updates } : step,
        );
        onStepsChange(updatedSteps);
    };

    const handleDeleteStep = (id: string) => {
        const filteredSteps = steps
            .filter((step) => step.id !== id)
            .map((step, index) => ({
                ...step,
                step_order: index,
            }));
        onStepsChange(filteredSteps);
    };

    const activeStep = steps.find((step) => step.id === activeId);

    return (
        <div className="space-y-4">
            <DndContext
                sensors={sensors}
                collisionDetection={closestCenter}
                onDragStart={handleDragStart}
                onDragEnd={handleDragEnd}
            >
                <SortableContext
                    items={steps.map((step) => step.id)}
                    strategy={verticalListSortingStrategy}
                >
                    <div className="space-y-3">
                        {steps.length === 0 ? (
                            <div className="flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-12 text-center">
                                <p className="text-sm text-muted-foreground">
                                    No steps added yet. Click the button below
                                    to add your first approval step.
                                </p>
                            </div>
                        ) : (
                            steps.map((step, index) => (
                                <StepCard
                                    key={step.id}
                                    step={step}
                                    index={index}
                                    users={users}
                                    roles={roles}
                                    permissions={permissions}
                                    onUpdate={handleUpdateStep}
                                    onDelete={handleDeleteStep}
                                    availableFields={availableFields}
                                />
                            ))
                        )}
                    </div>
                </SortableContext>

                <DragOverlay>
                    {activeId && activeStep ? (
                        <div className="opacity-80">
                            <StepCardContent
                                step={activeStep}
                                index={steps.findIndex(
                                    (s) => s.id === activeId,
                                )}
                                users={users}
                                roles={roles}
                                permissions={permissions}
                                onUpdate={() => {}}
                                onDelete={() => {}}
                                forceCollapsed={true}
                                availableFields={availableFields}
                            />
                        </div>
                    ) : null}
                </DragOverlay>
            </DndContext>

            <Button
                type="button"
                variant="outline"
                onClick={handleAddStep}
                className="w-full"
            >
                <Plus className="mr-2 h-4 w-4" />
                Add Approval Step
            </Button>
        </div>
    );
}
