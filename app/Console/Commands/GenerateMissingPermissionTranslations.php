<?php

namespace App\Console\Commands;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateMissingPermissionTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:generate-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate translation stubs for permissions missing translations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get all permissions from seeder
        $seeder = new RolePermissionSeeder;
        $reflection = new \ReflectionClass($seeder);
        $method = $reflection->getMethod('definePermissions');
        $method->setAccessible(true);
        $seederPermissions = $method->invoke($seeder);

        // Load existing translations
        $groups = [
            'view',
            'create',
            'update',
            'delete',
            'approval',
            'review',
            'archive',
            'manage',
            'export',
            'reports',
            'subsidi',
            'revenue',
            'audit',
            'settings',
            'emergency',
            'data',
            'personal',
        ];

        $translations = [];

        foreach ($groups as $group) {
            $groupTranslations = __("permissions.{$group}");

            if (is_array($groupTranslations)) {
                $translations = array_merge($translations, $groupTranslations);
            }
        }

        $translatedPermissions = array_keys($translations);
        $missingTranslations = array_diff(
            $seederPermissions,
            $translatedPermissions,
        );

        if (empty($missingTranslations)) {
            $this->info('✓ All permissions have translations!');

            return self::SUCCESS;
        }

        $this->warn(
            '⚠ Found '.
                count($missingTranslations).
                ' permissions without translations:',
        );
        $this->newLine();

        // Group by action
        $grouped = [];

        foreach ($missingTranslations as $permission) {
            $parts = explode('.', $permission);
            $action = $parts[1] ?? 'other';
            $grouped[$action][] = $permission;
        }

        // Generate translation stubs
        foreach ($grouped as $action => $permissions) {
            $this->line("// {$action} permissions");

            foreach ($permissions as $permission) {
                $label = $this->generateLabel($permission);
                $description = $this->generateDescription($permission);

                $this->line("'{$permission}' => [");
                $this->line("    'label' => '{$label}',");
                $this->line("    'description' => '{$description}',");
                $this->line('],');
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info(
            '💡 Copy the generated stubs above to lang/en/permissions.php',
        );
        $this->info('📝 Remember to refine the labels and descriptions!');

        return self::SUCCESS;
    }

    /**
     * Generate a human-readable label from permission name.
     */
    protected function generateLabel(string $permission): string
    {
        $parts = explode('.', $permission);

        // Handle different permission formats
        if (count($parts) === 2) {
            // Format: resource.action (e.g., "users.create")
            $resource = Str::title(str_replace('-', ' ', $parts[0]));
            $action = Str::title(str_replace('-', ' ', $parts[1]));

            return "{$action} {$resource}";
        }

        if (count($parts) === 3) {
            // Format: resource.action.scope (e.g., "programs.view.all")
            $resource = Str::title(str_replace('-', ' ', $parts[0]));
            $action = Str::title(str_replace('-', ' ', $parts[1]));
            $scope = Str::title($parts[2]);

            return "{$action} {$scope} {$resource}";
        }

        if (count($parts) === 4) {
            // Format: resource.subaction.action.scope (e.g., "subsidi.claim.create.own")
            $resource = Str::title(str_replace('-', ' ', $parts[0]));
            $subaction = Str::title(str_replace('-', ' ', $parts[1]));
            $action = Str::title(str_replace('-', ' ', $parts[2]));
            $scope = Str::title($parts[3]);

            return "{$action} {$scope} {$resource} {$subaction}";
        }

        // Fallback: just title case everything
        return Str::title(str_replace(['-', '.'], ' ', $permission));
    }

    /**
     * Generate a description from permission name.
     */
    protected function generateDescription(string $permission): string
    {
        $parts = explode('.', $permission);
        $resource = str_replace('-', ' ', $parts[0]);
        $action = str_replace('-', ' ', $parts[1] ?? '');
        $scope = $parts[2] ?? '';

        // Special scope handling
        $scopeText = match ($scope) {
            'own' => 'for items you created',
            'assigned' => 'for items assigned to you',
            'site' => 'within your assigned sites',
            'all' => 'across all sites',
            default => '',
        };

        // Generate contextual description
        $description = "Ability to {$action} {$resource}";

        if ($scopeText) {
            $description .= " {$scopeText}";
        }

        return trim($description);
    }
}
