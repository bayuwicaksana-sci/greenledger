<?php

namespace App\Services\Approval;

use Illuminate\Database\Eloquent\Model;

/**
 * Service for evaluating conditional rules in approval workflows.
 *
 * Supports a simple JSON-based DSL for defining rules.
 */
class ApprovalRuleEvaluator
{
    /**
     * Evaluate if a set of rules passes for a given model.
     *
     * @param  array|null  $rules  The conditional rules in DSL format
     * @param  Model  $model  The model to evaluate against
     * @return bool True if rules pass or no rules defined
     */
    public function evaluate(?array $rules, Model $model): bool
    {
        if (empty($rules)) {
            return true; // No rules means always pass
        }

        return $this->evaluateCondition($rules, $model);
    }

    /**
     * Evaluate a single condition or group of conditions.
     */
    protected function evaluateCondition(array $condition, Model $model): bool
    {
        // Handle logical operators (AND/OR)
        if (isset($condition['operator'])) {
            return $this->evaluateLogicalOperator($condition, $model);
        }

        // Handle single comparison
        if (
            isset(
                $condition['field'],
                $condition['comparison'],
                $condition['value'],
            )
        ) {
            return $this->evaluateComparison($condition, $model);
        }

        // Invalid condition format
        return false;
    }

    /**
     * Evaluate logical operators (AND/OR).
     */
    protected function evaluateLogicalOperator(
        array $condition,
        Model $model,
    ): bool {
        $operator = strtoupper($condition['operator']);
        $conditions = $condition['conditions'] ?? [];

        if ($operator === 'AND') {
            foreach ($conditions as $subCondition) {
                if (! $this->evaluateCondition($subCondition, $model)) {
                    return false;
                }
            }

            return true;
        }

        if ($operator === 'OR') {
            foreach ($conditions as $subCondition) {
                if ($this->evaluateCondition($subCondition, $model)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    /**
     * Evaluate a single comparison.
     */
    protected function evaluateComparison(array $condition, Model $model): bool
    {
        $field = $condition['field'];
        $comparison = $condition['comparison'];
        $expectedValue = $condition['value'];

        // Get the actual value from the model (supports dot notation for relationships)
        $actualValue = $this->getFieldValue($model, $field);

        return match ($comparison) {
            '=', '==' => $actualValue == $expectedValue,
            '===' => $actualValue === $expectedValue,
            '!=' => $actualValue != $expectedValue,
            '!==' => $actualValue !== $expectedValue,
            '>' => $actualValue > $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            '<' => $actualValue < $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            'in' => in_array($actualValue, (array) $expectedValue),
            'not_in' => ! in_array($actualValue, (array) $expectedValue),
            'contains' => str_contains(
                (string) $actualValue,
                (string) $expectedValue,
            ),
            'starts_with' => str_starts_with(
                (string) $actualValue,
                (string) $expectedValue,
            ),
            'ends_with' => str_ends_with(
                (string) $actualValue,
                (string) $expectedValue,
            ),
            'is_null' => is_null($actualValue),
            'is_not_null' => ! is_null($actualValue),
            default => false,
        };
    }

    /**
     * Get a field value from the model, supporting dot notation.
     */
    protected function getFieldValue(Model $model, string $field): mixed
    {
        // Support dot notation for relationships
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $value = $model;

            foreach ($parts as $part) {
                if ($value instanceof Model) {
                    $value = $value->{$part};
                } elseif (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return null;
                }
            }

            return $value;
        }

        // Direct attribute access
        return $model->{$field};
    }
}
