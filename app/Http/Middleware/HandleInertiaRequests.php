<?php

namespace App\Http\Middleware;

use App\Helpers\NavigationHelper;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'primarySite' => $request->user()?->primarySite->site_code ?? null,
                'roles' => $request->user()?->roles->pluck('name') ?? [],
                'allPermissions' => $request->user()?->getAllPermissions()->pluck('name') ?? [],
            ],
            'permissionTranslations' => $this->getPermissionTranslations($request),
            'navigation' => $request->user()
                ? NavigationHelper::getFilteredNavigation()
                : [],
            'sites' => $request->user()?->sites->toArray() ?? null,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') ||
                $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Get permission translations for the authenticated user.
     * Only returns translations for permissions the user actually has.
     */
    protected function getPermissionTranslations(Request $request): array
    {
        if (! $request->user()) {
            return [];
        }

        // Get user's permissions
        $userPermissions = $request
            ->user()
            ->getAllPermissions()
            ->pluck('name')
            ->toArray();

        // Load all translations once (cached by Laravel)
        $allTranslations = $this->loadAllPermissionTranslations();

        // Filter to only user's permissions
        return array_filter(
            $allTranslations,
            fn ($key) => in_array($key, $userPermissions),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * Load all permission translations from lang files.
     * Laravel automatically caches __() function results.
     */
    protected function loadAllPermissionTranslations(): array
    {
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

        return $translations;
    }
}
