<?php

namespace App\Helpers;

use App\Models\PaymentRequest;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NavigationHelper
{
    /**
     * Get filtered navigation based on user permissions.
     */
    public static function getFilteredNavigation(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $config = self::getNavigationConfig();
        $filtered = [];

        foreach ($config as $group) {
            $visibleItems = [];

            foreach ($group['items'] as $item) {
                if (self::userCanSeeItem($user, $item['permission'] ?? null)) {
                    // Add badge counts if specified
                    if (isset($item['badge'])) {
                        $item['badgeCount'] = self::getBadgeCount(
                            $item['badge'],
                            $user,
                        );
                    }

                    $visibleItems[] = $item;
                }
            }

            // Only include group if it has visible items
            if (! empty($visibleItems)) {
                $filtered[] = [
                    'title' => $group['title'],
                    'items' => $visibleItems,
                ];
            }
        }

        return $filtered;
    }

    /**
     * Check if user can see a navigation item.
     */
    private static function userCanSeeItem(
        User $user,
        string|array|null $permission,
    ): bool {
        if ($permission === null) {
            return true; // No permission required
        }

        if (is_array($permission)) {
            // User needs ANY of these permissions
            return $user->hasAnyPermission($permission);
        }

        return $user->hasPermissionTo($permission);
    }

    /**
     * Get badge count for a specific badge type.
     */
    private static function getBadgeCount(string $badgeType, User $user): int
    {
        return match ($badgeType) {
            'pending_approvals' => PaymentRequest::query()
                ->where('status', 'pending')
                ->when(
                    ! $user->can('payment-requests.approve.all'),
                    fn ($q) => $q->whereHas(
                        'program',
                        fn ($q2) => $q2->where(
                            'site_id',
                            $user->primary_site_id,
                        ),
                    ),
                )
                ->count(),

            'overdue_settlements' => Settlement::query()
                ->where('status', 'pending')
                // ->where('deadline', '<', now())
                ->when(
                    ! $user->can('settlements.view.all'),
                    fn ($q) => $q->where('submitted_by', $user->id),
                )
                ->count(),

            default => 0,
        };
    }

    /**
     * Get navigation configuration with permission requirements.
     */
    private static function getNavigationConfig(): array
    {
        return [
            // Dashboard
            [
                'title' => null,
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'route' => 'dashboard',
                        'icon' => 'LayoutGrid',
                        'permission' => [
                            'dashboard.view.own',
                            'dashboard.view.assigned',
                            'dashboard.view.site',
                            'dashboard.view.all',
                        ],
                    ],
                ],
            ],

            // Financial Management - Programs & Budgets
            [
                'title' => 'Programs & Budgets',
                'items' => [
                    [
                        'title' => 'My Programs',
                        'route' => 'programs.my',
                        'icon' => 'Sprout',
                        'permission' => [
                            'programs.view.own',
                            'programs.view.assigned',
                        ],
                    ],
                    [
                        'title' => 'All Programs',
                        'route' => 'programs.index',
                        'icon' => 'FolderTree',
                        'permission' => [
                            'programs.view.site',
                            'programs.view.all',
                        ],
                    ],
                    [
                        'title' => 'Create New Program',
                        'route' => 'programs.create',
                        'icon' => 'Plus',
                        'permission' => [
                            'programs.create.site',
                            'programs.create.all',
                        ],
                    ],
                    [
                        'title' => 'Archived Programs',
                        'route' => 'programs.archived',
                        'icon' => 'Archive',
                        'permission' => ['programs.archive.view'],
                    ],
                ],
            ],

            // Financial Management - Transactions
            [
                'title' => 'Transactions',
                'items' => [
                    [
                        'title' => 'All Payment Requests',
                        'route' => 'payment-requests.index',
                        'icon' => 'FileText',
                        'permission' => [
                            'payment-requests.view.all',
                            'payment-requests.view.site',
                        ],
                    ],
                    [
                        'title' => 'My Requests',
                        'route' => 'payment-requests.my',
                        'icon' => 'FilePlus',
                        'permission' => ['payment-requests.view.own'],
                    ],
                    [
                        'title' => 'Create Request',
                        'route' => 'payment-requests.create',
                        'icon' => 'Plus',
                        'permission' => [
                            'payment-requests.create.own',
                            'payment-requests.create.assigned',
                            'payment-requests.create.all',
                        ],
                    ],
                    [
                        'title' => 'Pending Approvals',
                        'route' => 'payment-requests.approvals',
                        'icon' => 'FileCheck',
                        'permission' => ['payment-requests.approve.all'],
                        'badge' => 'pending_approvals',
                    ],
                    [
                        'title' => 'Payment Queue',
                        'route' => 'payment-requests.queue',
                        'icon' => 'Banknote',
                        'permission' => ['payment-requests.process'],
                    ],
                    [
                        'title' => 'Settlements',
                        'route' => 'settlements.index',
                        'icon' => 'Receipt',
                        'permission' => [
                            'settlements.view.own',
                            'settlements.view.assigned',
                            'settlements.view.site',
                            'settlements.view.all',
                        ],
                        'badge' => 'overdue_settlements',
                    ],
                ],
            ],

            // Financial Management - Revenue
            [
                'title' => 'Revenue',
                'items' => [
                    [
                        'title' => 'Record Harvest',
                        'route' => 'revenue.harvest.create',
                        'icon' => 'Wheat',
                        'permission' => [
                            'programs.view.assigned',
                            'programs.view.all',
                        ],
                    ],
                    [
                        'title' => 'Harvest History',
                        'route' => 'revenue.harvest.index',
                        'icon' => 'History',
                        'permission' => [
                            'programs.view.assigned',
                            'programs.view.all',
                        ],
                    ],
                    [
                        'title' => 'Record Testing Service',
                        'route' => 'revenue.testing.create',
                        'icon' => 'FlaskConical',
                        'permission' => [
                            'programs.view.assigned',
                            'programs.view.all',
                        ],
                    ],
                    [
                        'title' => 'Service History',
                        'route' => 'revenue.testing.index',
                        'icon' => 'History',
                        'permission' => [
                            'programs.view.assigned',
                            'programs.view.all',
                        ],
                    ],
                ],
            ],

            // Activities
            [
                'title' => 'Activities',
                'items' => [
                    [
                        'title' => 'Activity Planning',
                        'route' => 'activities.index',
                        'icon' => 'Calendar',
                        'permission' => [
                            'activities.view.assigned',
                            'activities.view.site',
                            'activities.view.all',
                        ],
                    ],
                    [
                        'title' => 'Activity Tracking',
                        'route' => 'activities.tracking',
                        'icon' => 'TrendingUp',
                        'permission' => [
                            'activities.view.assigned',
                            'activities.view.site',
                            'activities.view.all',
                        ],
                    ],
                ],
            ],

            // Subsidi
            [
                'title' => 'Employee Benefit',
                'items' => [
                    [
                        'title' => 'My Benefit',
                        'route' => 'subsidi.my',
                        'icon' => 'Wallet',
                        'permission' => ['profile.view.own'],
                    ],
                    [
                        'title' => 'Submit Claim',
                        'route' => 'subsidi.claim',
                        'icon' => 'FilePlus',
                        'permission' => ['profile.view.own'],
                    ],
                    [
                        'title' => 'Claim History',
                        'route' => 'subsidi.history',
                        'icon' => 'History',
                        'permission' => ['profile.view.own'],
                    ],
                    [
                        'title' => 'Administration',
                        'route' => 'subsidi.admin',
                        'icon' => 'Settings',
                        'permission' => ['users.view.all'],
                    ],
                ],
            ],

            // Reports & Analytics
            [
                'title' => 'Reports & Analytics',
                'items' => [
                    [
                        'title' => 'Program P&L',
                        'route' => 'reports.program-pnl',
                        'icon' => 'PieChart',
                        'permission' => ['reports.financial.view'],
                    ],
                    [
                        'title' => 'Budget Reports',
                        'route' => 'reports.budget',
                        'icon' => 'TrendingUp',
                        'permission' => ['reports.financial.view'],
                    ],
                    [
                        'title' => 'Revenue Reports',
                        'route' => 'reports.revenue',
                        'icon' => 'DollarSign',
                        'permission' => ['reports.financial.view'],
                    ],
                    [
                        'title' => 'Expense Reports',
                        'route' => 'reports.expense',
                        'icon' => 'Receipt',
                        'permission' => ['reports.financial.view'],
                    ],
                    [
                        'title' => 'Transaction Reports',
                        'route' => 'reports.transactions',
                        'icon' => 'FileText',
                        'permission' => ['reports.operational.view'],
                    ],
                ],
            ],

            // Configuration
            [
                'title' => 'Configuration',
                'items' => [
                    [
                        'title' => 'Chart of Accounts',
                        'route' => 'config.coa',
                        'icon' => 'BookOpen',
                        'permission' => ['coa.view.site', 'coa.view.all'],
                    ],
                    [
                        'title' => 'Sites',
                        'route' => 'config.sites',
                        'icon' => 'MapPin',
                        'permission' => ['sites.view.all'],
                    ],
                    [
                        'title' => 'Buyers & Clients',
                        'route' => 'config.buyers-clients',
                        'icon' => 'Users',
                        'permission' => ['users.view.all'],
                    ],
                    [
                        'title' => 'System Settings',
                        'route' => 'config.settings',
                        'icon' => 'Settings',
                        'permission' => ['users.view.all'],
                    ],
                ],
            ],

            // Administration
            [
                'title' => 'Administration',
                'items' => [
                    [
                        'title' => 'User Management',
                        'route' => 'admin.users',
                        'icon' => 'Users',
                        'permission' => ['users.view.site', 'users.view.all'],
                    ],
                    [
                        'title' => 'Role Configuration',
                        'route' => 'admin.roles',
                        'icon' => 'Shield',
                        'permission' => ['users.assign-roles'],
                    ],
                    [
                        'title' => 'Access Logs',
                        'route' => 'admin.logs',
                        'icon' => 'FileText',
                        'permission' => ['users.view-logs'],
                    ],
                    [
                        'title' => 'System Health',
                        'route' => 'admin.health',
                        'icon' => 'Activity',
                        'permission' => ['users.view.all'],
                    ],
                ],
            ],

            // Notifications
            [
                'title' => null,
                'items' => [
                    [
                        'title' => 'Notifications',
                        'route' => 'notifications.index',
                        'icon' => 'Bell',
                        'permission' => ['notifications.view.own'],
                    ],
                ],
            ],
        ];
    }
}
