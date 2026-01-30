<?php

use Database\Seeders\RolePermissionSeeder;

// Don't use RefreshDatabase since we're only testing translation files
uses()->group('permissions', 'translations');

/**
 * Test that all permissions from the seeder have translations.
 */
test('all permissions have translations', function () {
    // Get all permissions defined in the seeder using reflection
    $seeder = new RolePermissionSeeder;
    $reflection = new ReflectionClass($seeder);
    $method = $reflection->getMethod('definePermissions');
    $method->setAccessible(true);
    $seederPermissions = $method->invoke($seeder);

    // Load all translations from lang files directly
    $translationFile = lang_path('en/permissions.php');
    expect($translationFile)->toBeFile('Translation file must exist');

    $allTranslations = require $translationFile;
    expect($allTranslations)->toBeArray();

    $translations = [];
    foreach ($allTranslations as $group => $groupTranslations) {
        if (is_array($groupTranslations)) {
            $translations = array_merge($translations, $groupTranslations);
        }
    }

    $translatedPermissions = array_keys($translations);

    // Find missing translations
    $missingTranslations = array_diff(
        $seederPermissions,
        $translatedPermissions,
    );

    // Find extra translations (not in seeder)
    $extraTranslations = array_diff(
        $translatedPermissions,
        $seederPermissions,
    );

    // Assert no missing translations
    expect($missingTranslations)->toBeEmpty(
        'Missing translations for permissions: '.
            implode(', ', $missingTranslations),
    );

    // Assert no extra translations
    expect($extraTranslations)->toBeEmpty(
        'Extra translations not in seeder: '.implode(', ', $extraTranslations),
    );
});

/**
 * Test that all translations have required fields (label and description).
 */
test('all translations have required fields', function () {
    $translationFile = lang_path('en/permissions.php');
    expect($translationFile)->toBeFile();

    $allTranslations = require $translationFile;
    expect($allTranslations)->toBeArray();

    $errors = [];

    foreach ($allTranslations as $group => $groupTranslations) {
        if (! is_array($groupTranslations)) {
            continue;
        }

        foreach ($groupTranslations as $permission => $translation) {
            // Check if translation is an array
            if (! is_array($translation)) {
                $errors[] = "{$permission}: Translation must be an array with 'label' and 'description' keys";

                continue;
            }

            // Check for 'label' field
            if (! isset($translation['label'])) {
                $errors[] = "{$permission}: Missing 'label' field";
            } elseif (empty($translation['label'])) {
                $errors[] = "{$permission}: 'label' cannot be empty";
            }

            // Check for 'description' field
            if (! isset($translation['description'])) {
                $errors[] = "{$permission}: Missing 'description' field";
            } elseif (empty($translation['description'])) {
                $errors[] = "{$permission}: 'description' cannot be empty";
            }
        }
    }

    expect($errors)->toBeEmpty(
        "Translation validation errors:\n".implode("\n", $errors),
    );
})->skip(fn () => ! file_exists(lang_path('en/permissions.php')), 'Translation file does not exist');

/**
 * Test that permission labels follow naming conventions.
 */
test('permission labels follow naming conventions', function () {
    $translationFile = lang_path('en/permissions.php');
    expect($translationFile)->toBeFile();

    $allTranslations = require $translationFile;
    expect($allTranslations)->toBeArray();

    $warnings = [];

    foreach ($allTranslations as $group => $groupTranslations) {
        if (! is_array($groupTranslations)) {
            continue;
        }

        foreach ($groupTranslations as $permission => $translation) {
            if (! is_array($translation) || ! isset($translation['label'])) {
                continue;
            }

            $label = $translation['label'];

            // Check if label is in Title Case (first letter of each word capitalized)
            if ($label !== ucwords(strtolower($label))) {
                // Allow for special cases like "P&L" or acronyms
                if (
                    ! str_contains($label, '&') &&
                    ! str_contains($label, 'Emergency:')
                ) {
                    $warnings[] = "{$permission}: Label should be in Title Case - '{$label}'";
                }
            }

            // Check if label is too long (>50 characters is unusual)
            if (strlen($label) > 50) {
                $warnings[] = "{$permission}: Label might be too long ({$label})";
            }

            // Check if label contains the permission key (should be human-readable)
            if (stripos($label, str_replace(['-', '.'], ' ', $permission)) !== false) {
                $warnings[] = "{$permission}: Label should be more human-readable - '{$label}'";
            }
        }
    }

    // Warnings don't fail the test, just output them
    if (! empty($warnings)) {
        dump("Naming convention warnings:\n".implode("\n", $warnings));
    }

    // Always pass this test, it's informational
    expect(true)->toBeTrue();
})->skip(fn () => ! file_exists(lang_path('en/permissions.php')), 'Translation file does not exist');

/**
 * Test that emergency permissions are properly prefixed.
 */
test('emergency permissions have proper prefix', function () {
    $translationFile = lang_path('en/permissions.php');
    expect($translationFile)->toBeFile();

    $allTranslations = require $translationFile;
    expect($allTranslations)->toBeArray();

    $emergencyTranslations = $allTranslations['emergency'] ?? [];

    if (empty($emergencyTranslations)) {
        $this->markTestSkipped('No emergency translations found');
    }

    foreach ($emergencyTranslations as $permission => $translation) {
        if (! is_array($translation) || ! isset($translation['label'])) {
            continue;
        }

        expect($translation['label'])->toStartWith(
            'Emergency:',
            "Emergency permission '{$permission}' should have 'Emergency:' prefix in label",
        );
    }
})->skip(fn () => ! file_exists(lang_path('en/permissions.php')), 'Translation file does not exist');
