<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserAccessLogController;
use App\Http\Controllers\UserController;
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

// Public invitation acceptance routes
Route::get('invitation/{token}', [
    AcceptInvitationController::class,
    'show',
])->name('invitation.show');
Route::post('invitation/{token}', [
    AcceptInvitationController::class,
    'accept',
])->name('invitation.accept');

Route::middleware(['auth', 'verified'])->group(function () {
    // Site API (JSON endpoints)
    Route::middleware('permission:users.view.site|users.view.all')->get(
        'api/sites',
        [\App\Http\Controllers\SiteController::class, 'apiIndex'],
    );

    // Role Management API (JSON endpoints)
    Route::middleware('permission:users.assign-roles')
        ->prefix('api')
        ->group(function () {
            // Get all permissions
            Route::get('permissions', [
                \App\Http\Controllers\RoleController::class,
                'allPermissions',
            ]);

            // Role endpoints
            Route::prefix('roles')->group(function () {
                Route::get('/', [
                    \App\Http\Controllers\RoleController::class,
                    'index',
                ]);
                Route::post('/', [
                    \App\Http\Controllers\RoleController::class,
                    'store',
                ]);
                Route::get('{role}', [
                    \App\Http\Controllers\RoleController::class,
                    'show',
                ]);
                Route::put('{role}', [
                    \App\Http\Controllers\RoleController::class,
                    'update',
                ]);
                Route::delete('{role}', [
                    \App\Http\Controllers\RoleController::class,
                    'destroy',
                ]);
                Route::put('{role}/permissions', [
                    \App\Http\Controllers\RoleController::class,
                    'syncPermissions',
                ]);
            });
        });

    // Access Logs
    Route::middleware('permission:users.view-logs')->get(
        'api/users/access-logs',
        [UserAccessLogController::class, 'index'],
    );

    // User Management API (JSON endpoints)
    Route::middleware('permission:users.view.site|users.view.all')
        ->prefix('api/users')
        ->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('{user}', [UserController::class, 'show']);

            // Invitations
            Route::middleware('permission:users.create')->group(function () {
                Route::post('invite', [UserController::class, 'invite']);
                Route::post('bulk-invite', [
                    UserController::class,
                    'bulkInvite',
                ]);
                Route::get('`invitations`', [
                    UserController::class,
                    'invitations',
                ]);
                Route::post('invitations/{invitation}/resend', [
                    UserController::class,
                    'resendInvitation',
                ]);
                Route::delete('invitations/{invitation}', [
                    UserController::class,
                    'cancelInvitation',
                ]);
            });

            // User updates
            Route::middleware('permission:users.update')->group(function () {
                Route::put('{user}', [UserController::class, 'update']);
                Route::post('{user}/toggle-status', [
                    UserController::class,
                    'toggleStatus',
                ]);
            });

            // Bulk operations
            Route::middleware('permission:users.deactivate')->group(
                function () {
                    Route::post('bulk-toggle-status', [
                        UserController::class,
                        'bulkToggleStatus',
                    ]);
                },
            );

            Route::middleware('permission:users.assign-roles')->group(
                function () {
                    Route::put('{user}/roles', [
                        UserController::class,
                        'assignRoles',
                    ]);
                    Route::post('bulk-assign-roles', [
                        UserController::class,
                        'bulkAssignRoles',
                    ]);
                },
            );

            Route::middleware('permission:users.assign-sites')->group(
                function () {
                    Route::put('{user}/sites', [
                        UserController::class,
                        'assignSites',
                    ]);
                },
            );

            Route::middleware('permission:users.reset-password')->group(
                function () {
                    Route::post('{user}/reset-password', [
                        UserController::class,
                        'resetPassword',
                    ]);
                },
            );
        });

    Route::prefix('app')->group(function () {
        Route::get('dashboard', [
            \App\Http\Controllers\MainDashboardController::class,
            'index',
        ])->name('main-dashboard');

        // Research Stations (Sites Management)
        Route::prefix('research-stations')
            ->name('research-stations.')
            ->group(function () {
                Route::get('/', [
                    \App\Http\Controllers\SiteController::class,
                    'index',
                ])->name('index');

                Route::get('/create', [
                    \App\Http\Controllers\SiteController::class,
                    'create',
                ])
                    ->name('create')
                    ->middleware('permission:sites.create');

                Route::post('/', [
                    \App\Http\Controllers\SiteController::class,
                    'store',
                ])
                    ->name('store')
                    ->middleware('permission:sites.create');

                Route::get('/suggest-code', [
                    \App\Http\Controllers\SiteController::class,
                    'suggestCode',
                ])
                    ->name('suggest-code')
                    ->middleware('permission:sites.create');
            });

        // Global Configuration Routes
        Route::prefix('config')
            ->name('config.')
            ->group(function () {
                // COA auxiliary routes (must come before resource routes)
                Route::post('coa/bulk', [
                    \App\Http\Controllers\CoaAccountController::class,
                    'bulkStore',
                ])
                    ->name('coa.bulk-store')
                    ->middleware('permission:coa.view.all|coa.view.site');

                Route::post('coa/import/validate', [
                    \App\Http\Controllers\CoaAccountImportController::class,
                    'validate',
                ])
                    ->name('coa.import.validate')
                    ->middleware('permission:coa.view.all|coa.view.site');

                Route::post('coa/import', [
                    \App\Http\Controllers\CoaAccountImportController::class,
                    'import',
                ])
                    ->name('coa.import')
                    ->middleware('permission:coa.view.all|coa.view.site');

                Route::get('coa/templates', [
                    \App\Http\Controllers\CoaAccountTemplateController::class,
                    'index',
                ])
                    ->name('coa.templates.index')
                    ->middleware('permission:coa.view.all|coa.view.site');

                Route::post('coa/templates/apply', [
                    \App\Http\Controllers\CoaAccountTemplateController::class,
                    'apply',
                ])
                    ->name('coa.templates.apply')
                    ->middleware('permission:coa.view.all|coa.view.site');

                Route::get('coa/next-code', [
                    \App\Http\Controllers\CoaAccountController::class,
                    'nextCode',
                ])
                    ->name('coa.next-code')
                    ->middleware('permission:coa.view.all|coa.view.site');

                Route::resource(
                    'coa',
                    \App\Http\Controllers\CoaAccountController::class,
                )->middleware('permission:coa.view.all|coa.view.site');
            });

        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get('users', function () {
                    return Inertia::render('admin/users');
                })
                    ->name('users')
                    ->middleware('permission:users.view.site|users.view.all');

                Route::get('roles', function (Site $site) {
                    Inertia::share('site_code', $site->site_code);

                    return Inertia::render('admin/roles');
                })
                    ->name('roles')
                    ->middleware('permission:users.assign-roles');

                Route::get('logs', function () {
                    return Inertia::render('admin/logs');
                })
                    ->name('logs')
                    ->middleware('permission:users.view-logs');

                Route::get('health', function () {
                    return Inertia::render('admin/health');
                })
                    ->name('health')
                    ->middleware('permission:users.view.all');

                // Approval Workflows
                Route::prefix('approval-workflows')
                    ->name('approval-workflows.')
                    ->middleware('permission:approval-workflows.view.all')
                    ->group(function () {
                        Route::get('/', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'index',
                        ])->name('index');

                        Route::get('/create', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'create',
                        ])
                            ->name('create')
                            ->middleware(
                                'permission:approval-workflows.create',
                            );

                        Route::post('/', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'store',
                        ])
                            ->name('store')
                            ->middleware(
                                'permission:approval-workflows.create',
                            );

                        Route::get('/{approvalWorkflow}/edit', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'edit',
                        ])
                            ->name('edit')
                            ->middleware(
                                'permission:approval-workflows.update',
                            );

                        Route::put('/{approvalWorkflow}', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'update',
                        ])
                            ->name('update')
                            ->middleware(
                                'permission:approval-workflows.update',
                            );

                        Route::delete('/{approvalWorkflow}', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'destroy',
                        ])
                            ->name('destroy')
                            ->middleware(
                                'permission:approval-workflows.delete',
                            );

                        // Workflow Actions
                        Route::post('/{approvalWorkflow}/duplicate', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'duplicate',
                        ])
                            ->name('duplicate')
                            ->middleware(
                                'permission:approval-workflows.duplicate',
                            );

                        Route::post('/{approvalWorkflow}/set-active', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'setActive',
                        ])
                            ->name('set-active')
                            ->middleware(
                                'permission:approval-workflows.activate',
                            );

                        Route::post('/{approvalWorkflow}/deactivate', [
                            \App\Http\Controllers\ApprovalWorkflowController::class,
                            'deactivate',
                        ])
                            ->name('deactivate')
                            ->middleware(
                                'permission:approval-workflows.activate',
                            );
                    });

                // Fiscal Years
                Route::resource(
                    'fiscal-years',
                    \App\Http\Controllers\FiscalYearController::class,
                )->middleware('permission:fiscal-year.view|fiscal-year.manage');

                Route::post('fiscal-years/{fiscalYear}/close', [
                    \App\Http\Controllers\FiscalYearController::class,
                    'close',
                ])
                    ->name('fiscal-years.close')
                    ->middleware('permission:fiscal-year.close');

                Route::post('fiscal-years/{fiscalYear}/reopen', [
                    \App\Http\Controllers\FiscalYearController::class,
                    'reopen',
                ])
                    ->name('fiscal-years.reopen')
                    ->middleware('permission:fiscal-year.reopen');

                Route::get('fiscal-years/{fiscalYear}/download-report', [
                    \App\Http\Controllers\FiscalYearController::class,
                    'downloadReport',
                ])
                    ->name('fiscal-years.download-report')
                    ->middleware(
                        'permission:fiscal-year.view|fiscal-year.manage',
                    );
            });

        Route::prefix('notifications')
            ->name('notifications.')
            ->group(function () {
                Route::get('/', [
                    \App\Http\Controllers\NotificationController::class,
                    'index',
                ])
                    ->name('index')
                    ->middleware('permission:notifications.view.own');

                Route::post('mark-all-read', [
                    \App\Http\Controllers\NotificationController::class,
                    'markAllAsRead',
                ])->name('mark-all-read');

                Route::post('{notificationId}/mark-read', [
                    \App\Http\Controllers\NotificationController::class,
                    'markAsRead',
                ])->name('mark-read');
            });

        Route::prefix('/site/{site:site_code}')
            ->middleware(EnsureSiteIsValid::class)
            ->group(function () {
                // Dashboard
                Route::get('dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard')
                    ->middleware(
                        'permission:dashboard.view.own|dashboard.view.assigned|dashboard.view.site|dashboard.view.all',
                    );

                // Programs
                Route::prefix('programs')
                    ->name('programs.')
                    ->group(function () {
                        Route::get('my', [
                            \App\Http\Controllers\ProgramController::class,
                            'myPrograms',
                        ])
                            ->name('my')
                            ->middleware([
                                'permission:programs.view.own|programs.view.assigned',
                            ]);

                        Route::get('/', [
                            \App\Http\Controllers\ProgramController::class,
                            'index',
                        ])
                            ->name('index')
                            ->middleware(
                                'permission:programs.view.site|programs.view.all',
                            );

                        Route::get('create', [
                            \App\Http\Controllers\ProgramController::class,
                            'create',
                        ])
                            ->name('create')
                            ->middleware(
                                'permission:programs.create.site|programs.create.all',
                            );

                        Route::post('/', [
                            \App\Http\Controllers\ProgramController::class,
                            'store',
                        ])
                            ->name('store')
                            ->middleware(
                                'permission:programs.create.site|programs.create.all',
                            );

                        Route::get('{program}/edit', [
                            \App\Http\Controllers\ProgramController::class,
                            'edit',
                        ])
                            ->name('edit')
                            ->middleware(
                                'permission:programs.update.site|programs.update.all',
                            );

                        Route::put('{program}', [
                            \App\Http\Controllers\ProgramController::class,
                            'update',
                        ])
                            ->name('update')
                            ->middleware(
                                'permission:programs.update.site|programs.update.all',
                            );

                        Route::delete('{program}', [
                            \App\Http\Controllers\ProgramController::class,
                            'destroy',
                        ])
                            ->name('destroy')
                            ->middleware(
                                'permission:programs.delete.site|programs.delete.all',
                            );

                        Route::get('archived', [
                            \App\Http\Controllers\ProgramController::class,
                            'archived',
                        ])
                            ->name('archived')
                            ->middleware('permission:programs.archive.view');

                        Route::get('{program}', [
                            \App\Http\Controllers\ProgramController::class,
                            'show',
                        ])
                            ->name('show')
                            ->middleware(
                                'permission:programs.view.site|programs.view.all|programs.view.own|programs.view.assigned',
                            );
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
                            ->middleware(
                                'permission:payment-requests.view.own',
                            );

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

                            return Inertia::render(
                                'payment-requests/approvals',
                            );
                        })
                            ->name('approvals')
                            ->middleware(
                                'permission:payment-requests.approve.all',
                            );

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
                                Route::get('create', [
                                    \App\Http\Controllers\RevenueController::class,
                                    'createHarvest',
                                ])
                                    ->name('create')
                                    ->middleware(
                                        'permission:programs.view.assigned|programs.view.all',
                                    );

                                Route::post('/', [
                                    \App\Http\Controllers\RevenueController::class,
                                    'storeHarvest',
                                ])
                                    ->name('store')
                                    ->middleware(
                                        'permission:programs.view.assigned|programs.view.all',
                                    );

                                Route::get('/', function (Site $site) {
                                    Inertia::share(
                                        'site_code',
                                        $site->site_code,
                                    );

                                    return Inertia::render(
                                        'revenue/harvest/index',
                                    );
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
                                Route::get('create', [
                                    \App\Http\Controllers\RevenueController::class,
                                    'createTesting',
                                ])
                                    ->name('create')
                                    ->middleware(
                                        'permission:programs.view.assigned|programs.view.all',
                                    );

                                Route::post('/', [
                                    \App\Http\Controllers\RevenueController::class,
                                    'storeTesting',
                                ])
                                    ->name('store')
                                    ->middleware(
                                        'permission:programs.view.assigned|programs.view.all',
                                    );

                                Route::get('/', function (Site $site) {
                                    Inertia::share(
                                        'site_code',
                                        $site->site_code,
                                    );

                                    return Inertia::render(
                                        'revenue/testing/index',
                                    );
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

                        Route::get('historical', [
                            \App\Http\Controllers\HistoricalReportController::class,
                            'index',
                        ])
                            ->name('historical')
                            ->middleware('permission:reports.historical');

                        Route::get('historical/export', [
                            \App\Http\Controllers\HistoricalReportController::class,
                            'export',
                        ])
                            ->name('historical.export')
                            ->middleware('permission:reports.historical');
                    });

                // Configuration
                Route::prefix('config')
                    ->name('config.')
                    ->group(function () {
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

                // Notifications
                // Route::prefix('notifications')
                //     ->name('notifications.')
                //     ->group(function () {
                //         Route::get('/', [
                //             \App\Http\Controllers\NotificationController::class,
                //             'index',
                //         ])
                //             ->name('index')
                //             ->middleware('permission:notifications.view.own');

                //         Route::post('mark-all-read', [
                //             \App\Http\Controllers\NotificationController::class,
                //             'markAllAsRead',
                //         ])->name('mark-all-read');

                //         Route::post('{notificationId}/mark-read', [
                //             \App\Http\Controllers\NotificationController::class,
                //             'markAsRead',
                //         ])->name('mark-read');
                //     });

                // Approvals
                Route::prefix('approvals')
                    ->name('approvals.')
                    ->group(function () {
                        Route::get('/', [
                            \App\Http\Controllers\ApprovalInstanceController::class,
                            'index',
                        ])->name('index');

                        Route::get('/{approvalInstance}', [
                            \App\Http\Controllers\ApprovalInstanceController::class,
                            'show',
                        ])->name('show');

                        Route::post('/{approvalInstance}/action', [
                            \App\Http\Controllers\ApprovalInstanceController::class,
                            'action',
                        ])->name('action');

                        Route::post('/{approvalInstance}/resubmit', [
                            \App\Http\Controllers\ApprovalInstanceController::class,
                            'resubmit',
                        ])->name('resubmit');
                    });
            });
        require __DIR__.'/settings.php';
    });
});
