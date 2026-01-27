<?php

use App\Http\Middleware\EnsureSiteIsValid;
use App\Models\Site;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('/site/{site:site_code}')
        ->middleware(EnsureSiteIsValid::class)
        ->group(function () {
            // Dashboard
            Route::get('dashboard', function (Site $site) {
                Inertia::share('site_code', $site->site_code);
                return Inertia::render('dashboard');
            })
                ->name('dashboard')
                ->middleware(
                    'permission:dashboard.view.own|dashboard.view.assigned|dashboard.view.site|dashboard.view.all',
                );

            // Programs
            Route::prefix('programs')
                ->name('programs.')
                ->group(function () {
                    Route::get('my', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('programs/my');
                    })
                        ->name('my')
                        ->middleware([
                            'permission:programs.view.own|programs.view.assigned',
                        ]);

                    Route::get('/', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('programs/index');
                    })
                        ->name('index')
                        ->middleware(
                            'permission:programs.view.site|programs.view.all',
                        );

                    Route::get('create', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('programs/create');
                    })
                        ->name('create')
                        ->middleware(
                            'permission:programs.create.site|programs.create.all',
                        );

                    Route::get('archived', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('programs/archived');
                    })
                        ->name('archived')
                        ->middleware('permission:programs.archive.view');
                });

            // Payment Requests
            Route::prefix('payment-requests')
                ->name('payment-requests.')
                ->group(function () {
                    Route::get('/', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('payment-requests/index');
                    })
                        ->name('index')
                        ->middleware(
                            'permission:payment-requests.view.all|payment-requests.view.site',
                        );

                    Route::get('my', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('payment-requests/my');
                    })
                        ->name('my')
                        ->middleware('permission:payment-requests.view.own');

                    Route::get('create', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('payment-requests/create');
                    })
                        ->name('create')
                        ->middleware(
                            'permission:payment-requests.create.own|payment-requests.create.assigned|payment-requests.create.all',
                        );

                    Route::get('approvals', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('payment-requests/approvals');
                    })
                        ->name('approvals')
                        ->middleware('permission:payment-requests.approve.all');

                    Route::get('queue', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('payment-requests/queue');
                    })
                        ->name('queue')
                        ->middleware('permission:payment-requests.process');
                });

            // Settlements
            Route::get('settlements', function (Site $site) {
                Inertia::share('site_code', $site->site_code);
                return Inertia::render('settlements/index');
            })
                ->name('settlements.index')
                ->middleware(
                    'permission:settlements.view.own|settlements.view.assigned|settlements.view.site|settlements.view.all',
                );

            // Activities
            Route::prefix('activities')
                ->name('activities.')
                ->group(function () {
                    Route::get('/', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('activities/index');
                    })
                        ->name('index')
                        ->middleware(
                            'permission:activities.view.assigned|activities.view.site|activities.view.all',
                        );

                    Route::get('tracking', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('activities/tracking');
                    })
                        ->name('tracking')
                        ->middleware(
                            'permission:activities.view.assigned|activities.view.site|activities.view.all',
                        );
                });

            // Revenue
            Route::prefix('revenue')
                ->name('revenue.')
                ->group(function () {
                    // Harvest
                    Route::prefix('harvest')
                        ->name('harvest.')
                        ->group(function () {
                            Route::get('create', function (Site $site) {
                                Inertia::share('site_code', $site->site_code);
                                return Inertia::render(
                                    'revenue/harvest/create',
                                );
                            })
                                ->name('create')
                                ->middleware(
                                    'permission:programs.view.assigned|programs.view.all',
                                );

                            Route::get('/', function (Site $site) {
                                Inertia::share('site_code', $site->site_code);
                                return Inertia::render('revenue/harvest/index');
                            })
                                ->name('index')
                                ->middleware(
                                    'permission:programs.view.assigned|programs.view.all',
                                );
                        });

                    // Testing Services
                    Route::prefix('testing')
                        ->name('testing.')
                        ->group(function () {
                            Route::get('create', function (Site $site) {
                                Inertia::share('site_code', $site->site_code);
                                return Inertia::render(
                                    'revenue/testing/create',
                                );
                            })
                                ->name('create')
                                ->middleware(
                                    'permission:programs.view.assigned|programs.view.all',
                                );

                            Route::get('/', function (Site $site) {
                                Inertia::share('site_code', $site->site_code);
                                return Inertia::render('revenue/testing/index');
                            })
                                ->name('index')
                                ->middleware(
                                    'permission:programs.view.assigned|programs.view.all',
                                );
                        });
                });

            // Subsidi
            Route::prefix('subsidi')
                ->name('subsidi.')
                ->group(function () {
                    Route::get('my', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('subsidi/my');
                    })
                        ->name('my')
                        ->middleware('permission:profile.view.own');

                    Route::get('claim', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('subsidi/claim');
                    })
                        ->name('claim')
                        ->middleware('permission:profile.view.own');

                    Route::get('history', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('subsidi/history');
                    })
                        ->name('history')
                        ->middleware('permission:profile.view.own');

                    Route::get('admin', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('subsidi/admin');
                    })
                        ->name('admin')
                        ->middleware('permission:users.view.all');
                });

            // Reports
            Route::prefix('reports')
                ->name('reports.')
                ->group(function () {
                    Route::get('program-pnl', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('reports/program-pnl');
                    })
                        ->name('program-pnl')
                        ->middleware('permission:reports.financial.view');

                    Route::get('budget', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('reports/budget');
                    })
                        ->name('budget')
                        ->middleware('permission:reports.financial.view');

                    Route::get('revenue', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('reports/revenue');
                    })
                        ->name('revenue')
                        ->middleware('permission:reports.financial.view');

                    Route::get('expense', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('reports/expense');
                    })
                        ->name('expense')
                        ->middleware('permission:reports.financial.view');

                    Route::get('transactions', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('reports/transactions');
                    })
                        ->name('transactions')
                        ->middleware('permission:reports.operational.view');
                });

            // Configuration
            Route::prefix('config')
                ->name('config.')
                ->group(function () {
                    Route::get('coa', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('config/coa');
                    })
                        ->name('coa')
                        ->middleware('permission:coa.view.site|coa.view.all');

                    Route::get('sites', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('config/sites');
                    })
                        ->name('sites')
                        ->middleware('permission:sites.view.all');

                    Route::get('buyers-clients', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('config/buyers-clients');
                    })
                        ->name('buyers-clients')
                        ->middleware('permission:users.view.all');

                    Route::get('settings', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('config/settings');
                    })
                        ->name('settings')
                        ->middleware('permission:users.view.all');
                });

            // Administration
            Route::prefix('admin')
                ->name('admin.')
                ->group(function () {
                    Route::get('users', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('admin/users');
                    })
                        ->name('users')
                        ->middleware(
                            'permission:users.view.site|users.view.all',
                        );

                    Route::get('roles', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('admin/roles');
                    })
                        ->name('roles')
                        ->middleware('permission:users.assign-roles');

                    Route::get('logs', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('admin/logs');
                    })
                        ->name('logs')
                        ->middleware('permission:users.view-logs');

                    Route::get('health', function (Site $site) {
                        Inertia::share('site_code', $site->site_code);
                        return Inertia::render('admin/health');
                    })
                        ->name('health')
                        ->middleware('permission:users.view.all');
                });

            // Notifications
            Route::get('notifications', function (Site $site) {
                Inertia::share('site_code', $site->site_code);
                return Inertia::render('notifications/index');
            })
                ->name('notifications.index')
                ->middleware('permission:notifications.view.own');
        });
});

require __DIR__ . '/settings.php';
