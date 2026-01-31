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
        [\App\Http\Controllers\SiteController::class, 'index'],
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

    // Access Logs
    Route::middleware('permission:users.view-logs')->get(
        'api/users/access-logs',
        [UserAccessLogController::class, 'index'],
    );

    // Approval System API Routes
    Route::prefix('api/approvals')->group(function () {
        // Workflow Management (Admin)
        Route::middleware('permission:view-approval-workflows')->group(
            function () {
                Route::get('workflows', [
                    \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                    'index',
                ]);
                Route::get('workflows/{approvalWorkflow}', [
                    \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                    'show',
                ]);
            },
        );

        Route::middleware('permission:create-approval-workflows')->post(
            'workflows',
            [
                \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                'store',
            ],
        );

        Route::middleware('permission:edit-approval-workflows')->group(
            function () {
                Route::put('workflows/{approvalWorkflow}', [
                    \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                    'update',
                ]);
                Route::post('workflows/{approvalWorkflow}/versions', [
                    \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                    'createVersion',
                ]);
            },
        );

        Route::middleware('permission:manage-approval-workflows')->group(
            function () {
                Route::post(
                    'workflows/{approvalWorkflow}/versions/{versionId}/activate',
                    [
                        \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                        'activateVersion',
                    ],
                );
                Route::post('workflows/{approvalWorkflow}/deactivate', [
                    \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                    'deactivate',
                ]);
            },
        );

        Route::middleware('permission:delete-approval-workflows')->delete(
            'workflows/{approvalWorkflow}',
            [
                \App\Http\Controllers\Api\ApprovalWorkflowController::class,
                'destroy',
            ],
        );

        // Approval Instances
        Route::get('instances', [
            \App\Http\Controllers\Api\ApprovalInstanceController::class,
            'index',
        ]);
        Route::get('instances/pending', [
            \App\Http\Controllers\Api\ApprovalInstanceController::class,
            'pendingForUser',
        ]);
        Route::get('instances/{approvalInstance}', [
            \App\Http\Controllers\Api\ApprovalInstanceController::class,
            'show',
        ]);
        Route::post('instances/{approvalInstance}/submit', [
            \App\Http\Controllers\Api\ApprovalInstanceController::class,
            'submit',
        ]);
        Route::post('instances/{approvalInstance}/cancel', [
            \App\Http\Controllers\Api\ApprovalInstanceController::class,
            'cancel',
        ]);

        // Approval Actions
        Route::post('instances/{approvalInstance}/actions', [
            \App\Http\Controllers\Api\ApprovalActionController::class,
            'process',
        ]);
        Route::get('instances/{approvalInstance}/history', [
            \App\Http\Controllers\Api\ApprovalActionController::class,
            'history',
        ]);
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

require __DIR__.'/settings.php';
