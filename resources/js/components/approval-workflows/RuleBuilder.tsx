import { Plus, Trash2 } from 'lucide-react';
import { useCallback, useId } from 'react';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import type { ModelField } from '@/types/approval';

// Types for the rule DSL
export interface SingleCondition {
    field: string;
    comparison: string;
    value: string | number | boolean | null;
}

export interface LogicalCondition {
    operator: 'AND' | 'OR';
    conditions: SingleCondition[];
}

export type ConditionalRules = SingleCondition | LogicalCondition | null;

// Available comparison operators
const COMPARISON_OPERATORS = [
    { value: '=', label: 'equals' },
    { value: '!=', label: 'not equals' },
    { value: '>', label: 'greater than' },
    { value: '>=', label: 'greater or equal' },
    { value: '<', label: 'less than' },
    { value: '<=', label: 'less or equal' },
    { value: 'contains', label: 'contains' },
    { value: 'starts_with', label: 'starts with' },
    { value: 'ends_with', label: 'ends with' },
    { value: 'in', label: 'is one of' },
    { value: 'is_null', label: 'is empty' },
    { value: 'is_not_null', label: 'has value' },
];

// Common fields for approvable models
const COMMON_FIELDS = [
    { value: 'amount', label: 'Amount', type: 'number' },
    { value: 'total_amount', label: 'Total Amount', type: 'number' },
    { value: 'status', label: 'Status', type: 'string' },
    { value: 'type', label: 'Type', type: 'string' },
    { value: 'priority', label: 'Priority', type: 'string' },
    { value: 'created_at', label: 'Created Date', type: 'date' },
    { value: 'description', label: 'Description', type: 'string' },
    { value: 'notes', label: 'Notes', type: 'string' },
    { value: 'requester.name', label: 'Requester Name', type: 'string' },
    { value: 'program.name', label: 'Program Name', type: 'string' },
    { value: 'program.budget', label: 'Program Budget', type: 'number' },
];

interface RuleBuilderProps {
    value: ConditionalRules;
    onChange: (rules: ConditionalRules) => void;
    availableFields?: ModelField[];
    disabled?: boolean;
}

export function RuleBuilder({
    value,
    onChange,
    availableFields = [],
    disabled,
}: RuleBuilderProps) {
    const id = useId();

    // Use availableFields if provided, otherwise fall back to COMMON_FIELDS
    const fields = availableFields.length > 0 ? availableFields : COMMON_FIELDS;

    // Check if rules are enabled
    const isEnabled = value !== null && value !== undefined;

    // Get conditions array from value
    const getConditions = useCallback((): SingleCondition[] => {
        if (!value) return [];
        if ('operator' in value) {
            return value.conditions || [];
        }
        if ('field' in value) {
            return [value];
        }
        return [];
    }, [value]);

    // Get logical operator
    const getOperator = useCallback((): 'AND' | 'OR' => {
        if (value && 'operator' in value) {
            return value.operator;
        }
        return 'AND';
    }, [value]);

    // Build the output structure
    const buildOutput = useCallback(
        (
            conditions: SingleCondition[],
            operator: 'AND' | 'OR',
        ): ConditionalRules => {
            if (conditions.length === 0) return null;
            if (conditions.length === 1) return conditions[0];
            return { operator, conditions };
        },
        [],
    );

    // Enable/disable rules
    const handleToggle = useCallback(
        (enabled: boolean) => {
            if (enabled) {
                onChange({ field: '', comparison: '=', value: '' });
            } else {
                onChange(null);
            }
        },
        [onChange],
    );

    // Add a new condition
    const handleAddCondition = useCallback(() => {
        const conditions = getConditions();
        const newConditions = [
            ...conditions,
            { field: '', comparison: '=', value: '' },
        ];
        onChange(buildOutput(newConditions, getOperator()));
    }, [getConditions, getOperator, buildOutput, onChange]);

    // Remove a condition
    const handleRemoveCondition = useCallback(
        (index: number) => {
            const conditions = getConditions();
            const newConditions = conditions.filter((_, i) => i !== index);
            onChange(buildOutput(newConditions, getOperator()));
        },
        [getConditions, getOperator, buildOutput, onChange],
    );

    // Update a condition
    const handleUpdateCondition = useCallback(
        (index: number, updates: Partial<SingleCondition>) => {
            const conditions = getConditions();
            const newConditions = conditions.map((c, i) =>
                i === index ? { ...c, ...updates } : c,
            );
            onChange(buildOutput(newConditions, getOperator()));
        },
        [getConditions, getOperator, buildOutput, onChange],
    );

    // Update the logical operator
    const handleOperatorChange = useCallback(
        (operator: 'AND' | 'OR') => {
            const conditions = getConditions();
            onChange(buildOutput(conditions, operator));
        },
        [getConditions, buildOutput, onChange],
    );

    const conditions = getConditions();
    const operator = getOperator();

    return (
        <div className="space-y-3">
            <div className="flex items-center justify-between">
                <Label htmlFor={`${id}-toggle`} className="text-sm font-medium">
                    Conditional Rules
                </Label>
                <Switch
                    id={`${id}-toggle`}
                    checked={isEnabled}
                    onCheckedChange={handleToggle}
                    disabled={disabled || fields.length === 0}
                />
            </div>

            {/* Show message if no fields available */}
            {fields.length === 0 && (
                <p className="text-xs text-muted-foreground">
                    Select a model type above to configure conditional rules
                </p>
            )}

            {isEnabled && fields.length > 0 && (
                <div className="space-y-3 rounded-md border bg-muted/30 p-3">
                    <p className="text-xs text-muted-foreground">
                        This step will only execute when the following
                        conditions are met.
                    </p>

                    {/* Conditions List */}
                    <div className="space-y-2">
                        {conditions.map((condition, index) => (
                            <div
                                key={index}
                                className="flex items-center gap-2 rounded-md bg-background p-2"
                            >
                                {/* Field Select */}
                                <Select
                                    value={condition.field}
                                    onValueChange={(val) =>
                                        handleUpdateCondition(index, {
                                            field: val,
                                        })
                                    }
                                    disabled={disabled}
                                >
                                    <SelectTrigger className="w-40">
                                        <SelectValue placeholder="Field..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {fields.map((f) => (
                                            <SelectItem
                                                key={f.value}
                                                value={f.value}
                                            >
                                                {f.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                {/* Comparison Operator */}
                                <Select
                                    value={condition.comparison}
                                    onValueChange={(val) =>
                                        handleUpdateCondition(index, {
                                            comparison: val,
                                        })
                                    }
                                    disabled={disabled}
                                >
                                    <SelectTrigger className="w-36">
                                        <SelectValue placeholder="Operator..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {COMPARISON_OPERATORS.map((op) => (
                                            <SelectItem
                                                key={op.value}
                                                value={op.value}
                                            >
                                                {op.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                {/* Value Input (hidden for is_null/is_not_null) */}
                                {!['is_null', 'is_not_null'].includes(
                                    condition.comparison,
                                ) && (
                                    <Input
                                        type={
                                            fields.find(
                                                (f) =>
                                                    f.value === condition.field,
                                            )?.type === 'number'
                                                ? 'number'
                                                : 'text'
                                        }
                                        value={String(condition.value ?? '')}
                                        onChange={(e) => {
                                            const fieldType = fields.find(
                                                (f) =>
                                                    f.value ===
                                                    condition.field,
                                            )?.type;
                                            const val =
                                                fieldType === 'number'
                                                    ? parseFloat(
                                                          e.target.value,
                                                      ) || 0
                                                    : e.target.value;
                                            handleUpdateCondition(index, {
                                                value: val,
                                            });
                                        }}
                                        placeholder="Value..."
                                        className="flex-1"
                                        disabled={disabled}
                                    />
                                )}

                                {/* Remove Button */}
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => handleRemoveCondition(index)}
                                    disabled={
                                        disabled || conditions.length <= 1
                                    }
                                    className="shrink-0 text-destructive hover:text-destructive"
                                >
                                    <Trash2 className="h-4 w-4" />
                                </Button>
                            </div>
                        ))}
                    </div>

                    {/* Add Condition Button */}
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={handleAddCondition}
                        disabled={disabled}
                        className="w-full"
                    >
                        <Plus className="mr-2 h-4 w-4" />
                        Add Condition
                    </Button>

                    {/* Logical Operator Selection (only show if multiple conditions) */}
                    {conditions.length > 1 && (
                        <div className="flex items-center gap-4 border-t pt-2">
                            <span className="text-xs text-muted-foreground">
                                Match:
                            </span>
                            <label className="flex cursor-pointer items-center gap-2">
                                <input
                                    type="radio"
                                    name={`${id}-operator`}
                                    value="AND"
                                    checked={operator === 'AND'}
                                    onChange={() => handleOperatorChange('AND')}
                                    disabled={disabled}
                                    className="h-4 w-4"
                                />
                                <span className="text-sm">
                                    ALL conditions (AND)
                                </span>
                            </label>
                            <label className="flex cursor-pointer items-center gap-2">
                                <input
                                    type="radio"
                                    name={`${id}-operator`}
                                    value="OR"
                                    checked={operator === 'OR'}
                                    onChange={() => handleOperatorChange('OR')}
                                    disabled={disabled}
                                    className="h-4 w-4"
                                />
                                <span className="text-sm">
                                    ANY condition (OR)
                                </span>
                            </label>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
