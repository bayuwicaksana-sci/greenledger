<?php

namespace App\Services\Approval;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

/**
 * Service for discovering fields from Eloquent models for use in conditional rules.
 *
 * Introspects models to extract database columns, relationships, and field types.
 */
class ModelFieldDiscoveryService
{
    /**
     * Cache TTL in minutes.
     */
    protected int $cacheTtl = 5;

    /**
     * Maximum relationship depth to traverse.
     */
    protected int $maxRelationshipDepth = 1;

    /**
     * Discover all fields for a given model class.
     *
     * @param  string  $modelClass  Fully qualified model class name
     * @return array{fields: array<int, array{value: string, label: string, type: string, nullable: bool, relationship?: bool}>}
     */
    public function discoverFields(string $modelClass): array
    {
        // Use cache to avoid repeated introspection
        $cacheKey = $this->getCacheKey($modelClass);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($modelClass) {
            if (! class_exists($modelClass)) {
                return ['fields' => []];
            }

            try {
                $model = app($modelClass);

                if (! $model instanceof Model) {
                    return ['fields' => []];
                }

                $fields = [];

                // Get direct model attributes
                $fields = array_merge($fields, $this->getModelAttributes($model));

                // Get relationship fields (BelongsTo/HasOne only)
                $fields = array_merge($fields, $this->getRelationshipFields($model));

                // Sort fields alphabetically by label
                usort($fields, fn ($a, $b) => strcmp($a['label'], $b['label']));

                return ['fields' => $fields];
            } catch (\Exception $e) {
                // Log error for debugging
                logger()->error('ModelFieldDiscoveryService error', [
                    'model' => $modelClass,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Return empty on error
                return ['fields' => []];
            }
        });
    }

    /**
     * Get direct model attributes from database table.
     *
     * @return array<int, array{value: string, label: string, type: string, nullable: bool}>
     */
    protected function getModelAttributes(Model $model): array
    {
        $table = $model->getTable();
        $columns = Schema::getColumns($table);
        $fields = [];

        foreach ($columns as $column) {
            $columnName = $column['name'];

            // Skip internal Laravel columns
            if (in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'])) {
                continue;
            }

            // Skip foreign keys (they're represented via relationships)
            if (Str::endsWith($columnName, ['_id', '_by'])) {
                continue;
            }

            $fields[] = [
                'value' => $columnName,
                'label' => $this->formatLabel($columnName),
                'type' => $this->getFieldType($columnName, $column, $model),
                'nullable' => $column['nullable'] ?? false,
            ];
        }

        return $fields;
    }

    /**
     * Get fields from BelongsTo and HasOne relationships.
     *
     * @return array<int, array{value: string, label: string, type: string, nullable: bool, relationship: bool}>
     */
    protected function getRelationshipFields(Model $model): array
    {
        $fields = [];
        $relationships = $this->getModelRelationships($model);

        foreach ($relationships as $relationName => $relationType) {
            // Only support single-record relationships
            if (! in_array($relationType, [BelongsTo::class, HasOne::class])) {
                continue;
            }

            try {
                // Get related model instance
                $relation = $model->{$relationName}();
                $relatedModel = $relation->getRelated();
                $relatedTable = $relatedModel->getTable();
                $relatedColumns = Schema::getColumns($relatedTable);

                // Add commonly useful fields from related model
                foreach ($relatedColumns as $column) {
                    $columnName = $column['name'];

                    // Skip internal columns
                    if (in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'])) {
                        continue;
                    }

                    // Skip foreign keys
                    if (Str::endsWith($columnName, ['_id', '_by'])) {
                        continue;
                    }

                    // Only include common identifier/descriptor fields
                    // Match: name, title, code, email, status, OR fields containing 'name', 'code', 'title'
                    $isCommonField = in_array($columnName, ['name', 'title', 'code', 'email', 'status']) ||
                        Str::contains($columnName, ['name', 'code', 'title', 'email', 'number']);

                    if (! $isCommonField) {
                        continue;
                    }

                    $dotNotation = "{$relationName}.{$columnName}";

                    $fields[] = [
                        'value' => $dotNotation,
                        'label' => $this->formatLabel($dotNotation),
                        'type' => $this->getFieldType($columnName, $column, $relatedModel),
                        'nullable' => $column['nullable'] ?? false,
                        'relationship' => true,
                    ];
                }
            } catch (\Exception $e) {
                // Skip this relationship if it can't be resolved
                continue;
            }
        }

        return $fields;
    }

    /**
     * Get all relationships defined on a model.
     *
     * @return array<string, string> Map of relationship name to relationship class
     */
    protected function getModelRelationships(Model $model): array
    {
        $relationships = [];
        $reflection = new ReflectionClass($model);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip magic methods and inherited methods
            if (
                $method->isStatic() ||
                $method->isAbstract() ||
                $method->getDeclaringClass()->getName() !== get_class($model) ||
                Str::startsWith($method->getName(), '__')
            ) {
                continue;
            }

            // Check if method returns a relationship
            $returnType = $method->getReturnType();

            if (! $returnType) {
                continue;
            }

            $returnTypeName = $returnType->getName();

            // Check for Eloquent relationship types
            if (
                is_subclass_of($returnTypeName, \Illuminate\Database\Eloquent\Relations\Relation::class) ||
                in_array($returnTypeName, [
                    BelongsTo::class,
                    HasOne::class,
                    \Illuminate\Database\Eloquent\Relations\HasMany::class,
                    \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
                    \Illuminate\Database\Eloquent\Relations\MorphTo::class,
                ])
            ) {
                $relationships[$method->getName()] = $returnTypeName;
            }
        }

        return $relationships;
    }

    /**
     * Determine the field type based on model casts and schema.
     *
     * @param  array  $columnInfo  Schema column information
     * @return string One of: string, number, date, boolean
     */
    protected function getFieldType(string $columnName, array $columnInfo, Model $model): string
    {
        // Priority 1: Check model's getCasts() method (uses protected casts() method)
        $casts = $model->getCasts();

        if (isset($casts[$columnName])) {
            return $this->mapCastToType($casts[$columnName]);
        }

        // Priority 2: Check schema type
        $schemaType = $columnInfo['type_name'] ?? $columnInfo['type'] ?? 'string';

        return $this->mapSchemaTypeToFieldType($schemaType);
    }

    /**
     * Map Laravel cast type to field type.
     */
    protected function mapCastToType(string $castType): string
    {
        // Handle enum casts (EnumName::class)
        if (class_exists($castType)) {
            return 'string';
        }

        // Handle casts with parameters (e.g., "decimal:2")
        $baseCast = Str::before($castType, ':');

        return match ($baseCast) {
            'int', 'integer', 'float', 'double', 'decimal', 'real' => 'number',
            'bool', 'boolean' => 'boolean',
            'date', 'datetime', 'immutable_date', 'immutable_datetime', 'timestamp' => 'date',
            default => 'string',
        };
    }

    /**
     * Map database schema type to field type.
     */
    protected function mapSchemaTypeToFieldType(string $schemaType): string
    {
        $schemaType = strtolower($schemaType);

        if (Str::contains($schemaType, ['int', 'float', 'double', 'decimal', 'numeric'])) {
            return 'number';
        }

        if (Str::contains($schemaType, ['bool', 'boolean'])) {
            return 'boolean';
        }

        if (Str::contains($schemaType, ['date', 'time', 'timestamp'])) {
            return 'date';
        }

        return 'string';
    }

    /**
     * Convert snake_case or dot notation to human-readable label.
     *
     * Examples:
     * - total_amount → Total Amount
     * - site.name → Site Name
     * - fiscal_year → Fiscal Year
     */
    protected function formatLabel(string $field): string
    {
        // Replace dots with spaces and convert to title case
        $label = str_replace('.', ' ', $field);

        return Str::title(str_replace('_', ' ', $label));
    }

    /**
     * Get cache key for a model class.
     */
    protected function getCacheKey(string $modelClass): string
    {
        return 'approval.model_fields.'.str_replace('\\', '_', $modelClass);
    }

    /**
     * Clear cached fields for a specific model or all models.
     *
     * @param  string|null  $modelClass  Model class to clear, or null for all
     */
    public function clearCache(?string $modelClass = null): void
    {
        if ($modelClass) {
            Cache::forget($this->getCacheKey($modelClass));
        } else {
            // Clear all approval field caches
            // Note: This is a simple approach; for production you might want to track cache keys
            Cache::flush();
        }
    }
}
