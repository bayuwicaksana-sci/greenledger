<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        foreach ($this->definePermissions() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles and assign permissions
        foreach ($this->defineRoles() as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($permissions);
        }

        // Create test users
        $this->createTestUsers();
    }

    /**
     * Define all permissions (~100 total for MVP).
     */
    private function definePermissions(): array
    {
        return [
            // Dashboard (4 permissions)
            'dashboard.view.own',
            'dashboard.view.assigned',
            'dashboard.view.site',
            'dashboard.view.all',

            // Programs (16 permissions)
            'programs.view.own',
            'programs.view.assigned',
            'programs.view.site',
            'programs.view.all',
            'programs.create.site',
            'programs.create.all',
            'programs.update.own',
            'programs.update.assigned',
            'programs.update.all',
            'programs.delete.assigned',
            'programs.delete.all',
            'programs.approve.all',
            'programs.reject.all',
            'programs.complete.all',
            'programs.archive.view',
            'programs.archive.manage',

            // Payment Requests (14 permissions)
            'payment-requests.view.own',
            'payment-requests.view.assigned',
            'payment-requests.view.site',
            'payment-requests.view.all',
            'payment-requests.create.own',
            'payment-requests.create.assigned',
            'payment-requests.create.all',
            'payment-requests.update.own',
            'payment-requests.update.assigned',
            'payment-requests.update.all',
            'payment-requests.delete.own',
            'payment-requests.delete.assigned',
            'payment-requests.approve.all',
            'payment-requests.process',

            // Settlements (12 permissions)
            'settlements.view.own',
            'settlements.view.assigned',
            'settlements.view.site',
            'settlements.view.all',
            'settlements.submit.own',
            'settlements.submit.assigned',
            'settlements.submit.all',
            'settlements.update.own',
            'settlements.update.assigned',
            'settlements.delete.own',
            'settlements.review.site',
            'settlements.approve.all',

            // Activities (8 permissions)
            'activities.view.own',
            'activities.view.assigned',
            'activities.view.site',
            'activities.view.all',
            'activities.create.assigned',
            'activities.create.all',
            'activities.update.assigned',
            'activities.update.all',

            // Users & Access Management (9 permissions)
            'users.view.site',
            'users.view.all',
            'users.create',
            'users.update',
            'users.deactivate',
            'users.assign-roles',
            'users.assign-sites',
            'users.reset-password',
            'users.view-logs',

            // Chart of Accounts (9 permissions)
            'coa.view.site',
            'coa.view.all',
            'coa.create',
            'coa.update',
            'coa.deactivate',
            'coa.view-usage',
            'coa.view-transactions',
            'coa.import',
            'coa.export',

            // Site Management (6 permissions)
            'sites.view.assigned',
            'sites.view.all',
            'sites.create',
            'sites.update',
            'sites.activate',
            'sites.compare-performance',

            // Program Assignments (4 permissions)
            'program-assignments.view.assigned',
            'program-assignments.view.all',
            'program-assignments.assign',
            'program-assignments.remove',

            // Notifications (4 permissions)
            'notifications.view.own',
            'notifications.manage.own',
            'notifications.manage.all',
            'notifications.configure',

            // Profile & Auth (4 permissions)
            'profile.view.own',
            'profile.update.own',
            'profile.change-password.own',
            'profile.view-activity',

            // Reports (placeholder for future - 3 permissions)
            'reports.financial.view',
            'reports.operational.view',
            'reports.export',
        ];
    }

    /**
     * Define roles and their permissions.
     */
    private function defineRoles(): array
    {
        return [
            'Research Officer' => [
                // Dashboard
                'dashboard.view.own',

                // Programs
                'programs.view.own',

                // Payment Requests
                'payment-requests.view.own',
                'payment-requests.create.own',
                'payment-requests.update.own',
                'payment-requests.delete.own',

                // Settlements
                'settlements.view.own',
                'settlements.submit.own',
                'settlements.update.own',
                'settlements.delete.own',

                // Activities
                'activities.view.assigned',
                'activities.create.assigned',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',
                'profile.view-activity',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',

                // Reports (limited)
                'reports.export',
            ],

            'Research Associate' => [
                // Dashboard
                'dashboard.view.assigned',

                // Programs
                'programs.view.assigned',
                'programs.create.site',
                'programs.update.assigned',

                // Payment Requests
                'payment-requests.view.assigned',
                'payment-requests.create.assigned',
                'payment-requests.update.assigned',
                'payment-requests.delete.assigned',

                // Settlements
                'settlements.view.assigned',
                'settlements.submit.assigned',
                'settlements.update.assigned',

                // Activities
                'activities.view.assigned',
                'activities.create.assigned',
                'activities.update.assigned',

                // Sites
                'sites.view.assigned',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',
                'profile.view-activity',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',

                // COA (view only)
                'coa.view.site',

                // Reports
                'reports.financial.view',
                'reports.export',
            ],

            'Manager' => [
                // Dashboard
                'dashboard.view.all',

                // Programs (full access)
                'programs.view.all',
                'programs.create.all',
                'programs.update.all',
                'programs.delete.all',
                'programs.approve.all',
                'programs.reject.all',
                'programs.complete.all',
                'programs.archive.view',
                'programs.archive.manage',

                // Payment Requests (full access)
                'payment-requests.view.all',
                'payment-requests.create.all',
                'payment-requests.update.all',
                'payment-requests.approve.all',

                // Settlements
                'settlements.view.all',
                'settlements.submit.all',
                'settlements.approve.all',

                // Activities (full access)
                'activities.view.all',
                'activities.create.all',
                'activities.update.all',

                // Users (full management)
                'users.view.all',
                'users.create',
                'users.update',
                'users.deactivate',
                'users.assign-roles',
                'users.assign-sites',
                'users.reset-password',
                'users.view-logs',

                // COA (full management)
                'coa.view.all',
                'coa.create',
                'coa.update',
                'coa.deactivate',
                'coa.view-usage',
                'coa.view-transactions',
                'coa.import',
                'coa.export',

                // Sites (full management)
                'sites.view.all',
                'sites.create',
                'sites.update',
                'sites.activate',
                'sites.compare-performance',

                // Program Assignments
                'program-assignments.view.all',
                'program-assignments.assign',
                'program-assignments.remove',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',
                'notifications.manage.all',
                'notifications.configure',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',
                'profile.view-activity',

                // Reports (full access)
                'reports.financial.view',
                'reports.operational.view',
                'reports.export',
            ],

            'AVP' => [
                // AVP has all Manager permissions + additional executive permissions
                // Dashboard
                'dashboard.view.all',

                // Programs (full access + completion approval)
                'programs.view.all',
                'programs.create.all',
                'programs.update.all',
                'programs.delete.all',
                'programs.approve.all',
                'programs.reject.all',
                'programs.complete.all',
                'programs.archive.view',
                'programs.archive.manage',

                // Payment Requests (full access)
                'payment-requests.view.all',
                'payment-requests.create.all',
                'payment-requests.update.all',
                'payment-requests.approve.all',

                // Settlements (full access)
                'settlements.view.all',
                'settlements.submit.all',
                'settlements.approve.all',

                // Activities (full access)
                'activities.view.all',
                'activities.create.all',
                'activities.update.all',

                // Users (full management)
                'users.view.all',
                'users.create',
                'users.update',
                'users.deactivate',
                'users.assign-roles',
                'users.assign-sites',
                'users.reset-password',
                'users.view-logs',

                // COA (full management)
                'coa.view.all',
                'coa.create',
                'coa.update',
                'coa.deactivate',
                'coa.view-usage',
                'coa.view-transactions',
                'coa.import',
                'coa.export',

                // Sites (full management)
                'sites.view.all',
                'sites.create',
                'sites.update',
                'sites.activate',
                'sites.compare-performance',

                // Program Assignments
                'program-assignments.view.all',
                'program-assignments.assign',
                'program-assignments.remove',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',
                'notifications.manage.all',
                'notifications.configure',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',
                'profile.view-activity',

                // Reports (full access)
                'reports.financial.view',
                'reports.operational.view',
                'reports.export',
            ],

            'Finance Operation' => [
                // Dashboard (financial focus)
                'dashboard.view.all',

                // Payment Requests (process payments)
                'payment-requests.view.all',
                'payment-requests.process',

                // Settlements (review and approve)
                'settlements.view.all',
                'settlements.review.site',
                'settlements.approve.all',

                // COA (full management)
                'coa.view.all',
                'coa.create',
                'coa.update',
                'coa.deactivate',
                'coa.view-usage',
                'coa.view-transactions',
                'coa.import',
                'coa.export',

                // Programs (view only for financial context)
                'programs.view.all',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',

                // Reports (financial reports)
                'reports.financial.view',
                'reports.operational.view',
                'reports.export',
            ],

            'Farm Admin' => [
                // Dashboard (site-specific)
                'dashboard.view.site',

                // Payment Requests (view only for assigned sites)
                'payment-requests.view.site',

                // Settlements (preliminary review)
                'settlements.view.site',
                'settlements.review.site',

                // Programs (view only)
                'programs.view.site',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',

                // Reports (operational)
                'reports.operational.view',
            ],
        ];
    }

    /**
     * Create test users for each role.
     */
    private function createTestUsers(): void
    {
        // Get first site for default assignment
        $defaultSite = Site::first();

        if (!$defaultSite) {
            $this->command->warn(
                'No sites found. Please run SiteSeeder first.',
            );
            return;
        }

        $users = [
            [
                'name' => 'Research Officer',
                'email' => 'ro@greenledger.test',
                'role' => 'Research Officer',
            ],
            [
                'name' => 'Research Associate',
                'email' => 'ra@greenledger.test',
                'role' => 'Research Associate',
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@greenledger.test',
                'role' => 'Manager',
            ],
            [
                'name' => 'AVP User',
                'email' => 'avp@greenledger.test',
                'role' => 'AVP',
            ],
            [
                'name' => 'Finance Operation',
                'email' => 'finance@greenledger.test',
                'role' => 'Finance Operation',
            ],
            [
                'name' => 'Farm Admin',
                'email' => 'farmadmin@greenledger.test',
                'role' => 'Farm Admin',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'primary_site_id' => $defaultSite->id,
                ],
            );

            // Assign role
            $user->assignRole($userData['role']);

            // Attach to default site with granted_at timestamp
            $user->sites()->syncWithoutDetaching([
                $defaultSite->id => ['granted_at' => now()],
            ]);

            $this->command->info(
                "Created test user: {$userData['email']} (password: password)",
            );
        }
    }
}
