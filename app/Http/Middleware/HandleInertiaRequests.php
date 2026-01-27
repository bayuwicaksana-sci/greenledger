<?php

namespace App\Http\Middleware;

use App\Helpers\NavigationHelper;
use App\Models\Site;
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
                'primarySite' =>
                    $request->user()?->primarySite->site_code ?? null,
                'roles' => $request->user()?->roles->pluck('name') ?? [],
                'allPermissions' =>
                    $request->user()?->getAllPermissions()->pluck('name') ?? [],
            ],
            'navigation' => $request->user()
                ? NavigationHelper::getFilteredNavigation()
                : [],
            'sites' => $request->user()?->sites->toArray() ?? null,
            'sidebarOpen' =>
                !$request->hasCookie('sidebar_state') ||
                $request->cookie('sidebar_state') === 'true',
        ];
    }
}
