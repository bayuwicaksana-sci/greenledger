<?php

namespace App\Services\Approval;

use App\Models\Traits\RequiresApproval;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class ModelDiscoveryService
{
    /**
     * Discover all models that use the RequiresApproval trait.
     *
     * @return array<string, string> Model class => Display name
     */
    public function discoverApprovableModels(): array
    {
        $models = [];
        $modelPath = app_path('Models');

        // Get all PHP files in the Models directory
        $files = File::allFiles($modelPath);

        foreach ($files as $file) {
            // Skip trait files
            if (str_contains($file->getPath(), 'Traits')) {
                continue;
            }

            // Get the class name from the file
            $className = $this->getClassFromFile($file->getPathname());

            if (! $className) {
                continue;
            }

            // Check if the class uses the RequiresApproval trait
            if ($this->usesRequiresApprovalTrait($className)) {
                // Get the display name from the model
                $displayName = $className::getApprovalDisplayName();

                // Check config for overrides
                $configOverride = config(
                    "approval.model_display_names.{$className}",
                );

                if ($configOverride) {
                    $displayName = $configOverride;
                }

                $models[$className] = $displayName;
            }
        }

        return $models;
    }

    /**
     * Get the fully qualified class name from a file.
     */
    protected function getClassFromFile(string $filePath): ?string
    {
        $namespace = null;
        $class = null;

        $tokens = token_get_all(file_get_contents($filePath));

        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_NAME_QUALIFIED) {
                        $namespace = $tokens[$j][1];
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $class = $tokens[$j][1];
                        break;
                    }
                }
            }

            if ($namespace && $class) {
                break;
            }
        }

        if (! $namespace || ! $class) {
            return null;
        }

        $fullClassName = $namespace.'\\'.$class;

        // Verify the class exists
        if (! class_exists($fullClassName)) {
            return null;
        }

        return $fullClassName;
    }

    /**
     * Check if a class uses the RequiresApproval trait.
     */
    protected function usesRequiresApprovalTrait(string $className): bool
    {
        try {
            $reflection = new ReflectionClass($className);
            $traits = $reflection->getTraitNames();

            return in_array(RequiresApproval::class, $traits);
        } catch (\Exception $e) {
            return false;
        }
    }
}
