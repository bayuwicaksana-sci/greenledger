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

            // Programs (19 permissions)
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
            'programs.submit-for-approval.assigned',
            'programs.submit-for-approval.all',
            'programs.approve.all',
            'programs.reject.all',
            'programs.complete.all',
            'programs.archive.view',
            'programs.archive.manage',
            'program.override-closed-fy',

            // Payment Requests (17 permissions)
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
            'payment-requests.delete.all',
            'payment-requests.approve.all',
            'payment-requests.reject.all',
            'payment-requests.cancel.all',
            'payment-requests.process',

            // Settlements (16 permissions)
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
            'settlements.delete.assigned',
            'settlements.delete.all',
            'settlements.review.site',
            'settlements.review.all',
            'settlements.request-revision',
            'settlements.approve.all',
            'settlements.reject.all',

            // Activities (10 permissions)
            'activities.view.own',
            'activities.view.assigned',
            'activities.view.site',
            'activities.view.all',
            'activities.create.assigned',
            'activities.create.all',
            'activities.update.assigned',
            'activities.update.all',
            'activities.delete.assigned',
            'activities.delete.all',

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

            // Financial Reports (22 permissions - assigned + all variants)
            'reports.program-pl.view.assigned',
            'reports.program-pl.view.all',
            'reports.budget-vs-actual.view.assigned',
            'reports.budget-vs-actual.view.all',
            'reports.budget-utilization.view.assigned',
            'reports.budget-utilization.view.all',
            'reports.variance-analysis.view.assigned',
            'reports.variance-analysis.view.all',
            'reports.revenue-harvest.view.assigned',
            'reports.revenue-harvest.view.all',
            'reports.revenue-testing.view.assigned',
            'reports.revenue-testing.view.all',
            'reports.expense-by-coa.view.assigned',
            'reports.expense-by-coa.view.all',
            'reports.expense-by-program.view.assigned',
            'reports.expense-by-program.view.all',
            'reports.expense-by-activity.view.assigned',
            'reports.expense-by-activity.view.all',
            'reports.site-performance.view.all',
            'reports.consolidated-financial.view.all',

            // Operational Reports (12 permissions)
            'reports.payment-request-register.view.own',
            'reports.payment-request-register.view.assigned',
            'reports.payment-request-register.view.site',
            'reports.payment-request-register.view.all',
            'reports.settlement-status.view.own',
            'reports.settlement-status.view.assigned',
            'reports.settlement-status.view.site',
            'reports.settlement-status.view.all',
            'reports.settlement-compliance.view.site',
            'reports.settlement-compliance.view.all',
            'reports.approval-cycle-time.view.all',
            'reports.transaction-volume.view.all',
            'reports.user-activity.view.all',

            // Compliance & Audit Reports (5 permissions)
            'reports.audit-trail.view.all',
            'reports.digital-signature-log.view.all',
            'reports.admin-activity-log.view.all',
            'reports.critical-actions.view.all',
            'reports.data-change-history.view.all',

            // Historical Reports (1 permission)
            'reports.historical',

            // Export Capabilities (7 permissions)
            'reports.export.excel.assigned',
            'reports.export.excel.all',
            'reports.export.pdf.assigned',
            'reports.export.pdf.all',
            'reports.export.csv.assigned',
            'reports.export.csv.all',
            'reports.schedule-delivery.manage',

            // Subsidi (15 permissions)
            'subsidi.view-eligibility.own',
            'subsidi.view-types.all',
            'subsidi.claim.create.own',
            'subsidi.claim.update.own',
            'subsidi.claim.delete.own',
            'subsidi.view-claims.own',
            'subsidi.view-claims.all',
            'subsidi.approve',
            'subsidi.reject',
            'subsidi.process-payment',
            'subsidi.types.create',
            'subsidi.types.update',
            'subsidi.types.deactivate',
            'subsidi.manage-eligibility',
            'subsidi.export',

            // Fiscal Year Management (4 permissions)
            'fiscal-year.view',
            'fiscal-year.manage',
            'fiscal-year.close',
            'fiscal-year.reopen',

            // Data Management (6 permissions)
            'data.trigger-backup',
            'data.view-backup-history',
            'data.restore-backup',
            'data.run-year-end',
            'data.bulk-archive-programs',
            'data.export-tools',

            // System Settings (9 permissions - Manager/AVP only)
            'settings.view.all',
            'settings.update.budget-ceiling',
            'settings.update.batch-times',
            'settings.update.settlement-deadline',
            'settings.update.fiscal-year',
            'settings.configure.email',
            'settings.configure.notifications',
            'settings.configure.workflows',
            'settings.configure.approval-hierarchies',

            // Audit & Monitoring (6 permissions)
            'audit.view-logs.all',
            'audit.view-admin-activity.all',
            'audit.view-user-access-logs.all',
            'audit.view-critical-alerts.all',
            'audit.view-system-health.all',
            'audit.export.all',

            // Emergency Operations (8 permissions - Manager/AVP only)
            'emergency.unlock-budget',
            'emergency.delete-transaction',
            'emergency.bypass-approval',
            'emergency.modify-locked-data',
            'emergency.manual-journal-entry',
            'emergency.force-archive',
            'emergency.reset-any-password',
            'emergency.override-settlement',

            // Revenue - Testing Services (20 permissions)
            'testing-services.view.assigned',
            'testing-services.view.site',
            'testing-services.view.all',
            'testing-services.create.assigned',
            'testing-services.create.all',
            'testing-services.update.draft.assigned',
            'testing-services.update.draft.all',
            'testing-services.delete.draft.assigned',
            'testing-services.delete.draft.all',
            'testing-services.submit.assigned',
            'testing-services.approve',
            'testing-services.reject',
            'testing-services.update-payment-status.assigned',
            'testing-services.update-payment-status.all',
            'testing-services.upload-contract.assigned',
            'testing-services.upload-contract.all',
            'testing-services.view-by-client.assigned',
            'testing-services.view-by-client.all',
            'testing-services.export.assigned',
            'testing-services.export.all',

            // Revenue - Harvest (22 permissions)
            'harvest.view.assigned',
            'harvest.view.site',
            'harvest.view.all',
            'harvest.create.assigned',
            'harvest.create.all',
            'harvest.update.own',
            'harvest.update.assigned',
            'harvest.update.all',
            'harvest.update.after-48hrs',
            'harvest.delete.draft.own',
            'harvest.delete.draft.assigned',
            'harvest.delete.draft.all',
            'harvest.review.all',
            'harvest.request-correction',
            'harvest.view-by-program.assigned',
            'harvest.view-by-program.all',
            'harvest.view-by-buyer.assigned',
            'harvest.view-by-buyer.all',
            'harvest.view-cycle-report.assigned',
            'harvest.view-cycle-report.all',
            'harvest.export.assigned',
            'harvest.export.all',

            // Buyers & Clients (13 permissions)
            'buyers.view.all',
            'buyers.create',
            'buyers.update',
            'buyers.deactivate',
            'buyers.view-transaction-history.assigned',
            'buyers.view-transaction-history.all',
            'clients.view.all',
            'clients.create',
            'clients.update',
            'clients.deactivate',
            'clients.view-transaction-history.assigned',
            'clients.view-transaction-history.all',
            'buyers-clients.export',
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

                // Reports (assigned scope)
                'reports.program-pl.view.assigned',
                'reports.budget-vs-actual.view.assigned',
                'reports.budget-utilization.view.assigned',
                'reports.revenue-harvest.view.assigned',
                'reports.expense-by-coa.view.assigned',
                'reports.expense-by-program.view.assigned',
                'reports.expense-by-activity.view.assigned',
                'reports.payment-request-register.view.own',
                'reports.settlement-status.view.own',
                'reports.export.excel.assigned',
                'reports.export.pdf.assigned',
                'reports.export.csv.assigned',

                // Subsidi
                'subsidi.view-eligibility.own',
                'subsidi.view-types.all',
                'subsidi.claim.create.own',
                'subsidi.claim.update.own',
                'subsidi.claim.delete.own',
                'subsidi.view-claims.own',

                // Revenue - Harvest (assigned scope)
                'harvest.view.assigned',
                'harvest.create.assigned',
                'harvest.update.own',
                'harvest.delete.draft.own',
                'harvest.view-by-program.assigned',
                'harvest.view-cycle-report.assigned',
                'harvest.export.assigned',

                // Buyers & Clients (limited)
                'buyers.view.all',
                'buyers.create',
                'buyers.view-transaction-history.assigned',
            ],

            'Research Associate' => [
                // Dashboard
                'dashboard.view.assigned',

                // Programs
                'programs.view.assigned',
                'programs.create.site',
                'programs.update.assigned',
                'programs.submit-for-approval.assigned',

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

                // Reports (assigned scope + more operational)
                'reports.program-pl.view.assigned',
                'reports.budget-vs-actual.view.assigned',
                'reports.budget-utilization.view.assigned',
                'reports.variance-analysis.view.assigned',
                'reports.revenue-harvest.view.assigned',
                'reports.revenue-testing.view.assigned',
                'reports.expense-by-coa.view.assigned',
                'reports.expense-by-program.view.assigned',
                'reports.expense-by-activity.view.assigned',
                'reports.payment-request-register.view.assigned',
                'reports.settlement-status.view.assigned',
                'reports.export.excel.assigned',
                'reports.export.pdf.assigned',
                'reports.export.csv.assigned',

                // Subsidi
                'subsidi.view-eligibility.own',
                'subsidi.view-types.all',
                'subsidi.claim.create.own',
                'subsidi.claim.update.own',
                'subsidi.claim.delete.own',
                'subsidi.view-claims.own',

                // Revenue - Harvest
                'harvest.view.assigned',
                'harvest.create.assigned',
                'harvest.update.assigned',
                'harvest.delete.draft.assigned',
                'harvest.view-by-program.assigned',
                'harvest.view-by-buyer.assigned',
                'harvest.view-cycle-report.assigned',
                'harvest.export.assigned',

                // Revenue - Testing Services
                'testing-services.view.assigned',
                'testing-services.create.assigned',
                'testing-services.update.draft.assigned',
                'testing-services.delete.draft.assigned',
                'testing-services.submit.assigned',
                'testing-services.update-payment-status.assigned',
                'testing-services.upload-contract.assigned',
                'testing-services.view-by-client.assigned',
                'testing-services.export.assigned',

                // Buyers & Clients
                'buyers.view.all',
                'buyers.create',
                'buyers.update',
                'buyers.view-transaction-history.assigned',
                'clients.view.all',
                'clients.create',
                'clients.update',
                'clients.view-transaction-history.assigned',
                'buyers-clients.export',
            ],

            'Manager' => [
                // Dashboard
                'dashboard.view.all',

                // Programs (full access)
                'programs.view.all',
                'programs.create.all',
                'programs.update.all',
                'programs.delete.all',
                'programs.submit-for-approval.all',
                'programs.approve.all',
                'programs.reject.all',
                'programs.complete.all',
                'programs.archive.view',
                'programs.archive.manage',
                'program.override-closed-fy',

                // Payment Requests (full access)
                'payment-requests.view.all',
                'payment-requests.create.all',
                'payment-requests.update.all',
                'payment-requests.delete.all',
                'payment-requests.approve.all',
                'payment-requests.reject.all',
                'payment-requests.cancel.all',

                // Settlements (full access)
                'settlements.view.all',
                'settlements.submit.all',
                'settlements.delete.all',
                'settlements.review.all',
                'settlements.approve.all',
                'settlements.reject.all',

                // Activities (full access)
                'activities.view.all',
                'activities.create.all',
                'activities.update.all',
                'activities.delete.all',

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

                // Reports (full access - all scopes)
                'reports.program-pl.view.all',
                'reports.budget-vs-actual.view.all',
                'reports.budget-utilization.view.all',
                'reports.variance-analysis.view.all',
                'reports.revenue-harvest.view.all',
                'reports.revenue-testing.view.all',
                'reports.expense-by-coa.view.all',
                'reports.expense-by-program.view.all',
                'reports.expense-by-activity.view.all',
                'reports.site-performance.view.all',
                'reports.consolidated-financial.view.all',
                'reports.payment-request-register.view.all',
                'reports.settlement-status.view.all',
                'reports.settlement-compliance.view.all',
                'reports.approval-cycle-time.view.all',
                'reports.transaction-volume.view.all',
                'reports.user-activity.view.all',
                'reports.audit-trail.view.all',
                'reports.digital-signature-log.view.all',
                'reports.admin-activity-log.view.all',
                'reports.critical-actions.view.all',
                'reports.data-change-history.view.all',
                'reports.export.excel.all',
                'reports.export.pdf.all',
                'reports.export.csv.all',
                'reports.schedule-delivery.manage',
                'reports.historical',

                // Fiscal Year Management
                'fiscal-year.view',
                'fiscal-year.manage',
                'fiscal-year.close',
                'fiscal-year.reopen',

                // Subsidi (full management)
                'subsidi.view-eligibility.own',
                'subsidi.view-types.all',
                'subsidi.claim.create.own',
                'subsidi.claim.update.own',
                'subsidi.claim.delete.own',
                'subsidi.view-claims.own',
                'subsidi.view-claims.all',
                'subsidi.approve',
                'subsidi.reject',
                'subsidi.types.create',
                'subsidi.types.update',
                'subsidi.types.deactivate',
                'subsidi.manage-eligibility',
                'subsidi.export',

                // Revenue - Harvest (full management)
                'harvest.view.all',
                'harvest.create.all',
                'harvest.update.all',
                'harvest.delete.draft.all',
                'harvest.review.all',
                'harvest.request-correction',
                'harvest.view-by-program.all',
                'harvest.view-by-buyer.all',
                'harvest.view-cycle-report.all',
                'harvest.export.all',

                // Revenue - Testing Services (full management)
                'testing-services.view.all',
                'testing-services.create.all',
                'testing-services.update.draft.all',
                'testing-services.delete.draft.all',
                'testing-services.submit.assigned',
                'testing-services.approve',
                'testing-services.reject',
                'testing-services.update-payment-status.all',
                'testing-services.upload-contract.all',
                'testing-services.view-by-client.all',
                'testing-services.export.all',

                // Buyers & Clients (full management)
                'buyers.view.all',
                'buyers.create',
                'buyers.update',
                'buyers.deactivate',
                'buyers.view-transaction-history.all',
                'clients.view.all',
                'clients.create',
                'clients.update',
                'clients.deactivate',
                'clients.view-transaction-history.all',
                'buyers-clients.export',

                // Emergency Operations (override permissions)
                'emergency.unlock-budget',
                'emergency.delete-transaction',
                'emergency.bypass-approval',
                'emergency.modify-locked-data',
                'emergency.manual-journal-entry',
                'emergency.force-archive',
                'emergency.reset-any-password',
                'emergency.override-settlement',

                // Audit & Monitoring (full access)
                'audit.view-logs.all',
                'audit.view-admin-activity.all',
                'audit.view-user-access-logs.all',
                'audit.view-critical-alerts.all',
                'audit.view-system-health.all',
                'audit.export.all',

                // System Settings (full configuration)
                'settings.view.all',
                'settings.update.budget-ceiling',
                'settings.update.batch-times',
                'settings.update.settlement-deadline',
                'settings.update.fiscal-year',
                'settings.configure.email',
                'settings.configure.notifications',
                'settings.configure.workflows',
                'settings.configure.approval-hierarchies',

                // Data Management (full access)
                'data.trigger-backup',
                'data.view-backup-history',
                'data.restore-backup',
                'data.run-year-end',
                'data.bulk-archive-programs',
                'data.export-tools',
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
                'programs.submit-for-approval.all',
                'programs.approve.all',
                'programs.reject.all',
                'programs.complete.all',
                'programs.archive.view',
                'programs.archive.manage',
                'program.override-closed-fy',

                // Payment Requests (full access)
                'payment-requests.view.all',
                'payment-requests.create.all',
                'payment-requests.update.all',
                'payment-requests.delete.all',
                'payment-requests.approve.all',
                'payment-requests.reject.all',
                'payment-requests.cancel.all',

                // Settlements (full access)
                'settlements.view.all',
                'settlements.submit.all',
                'settlements.delete.all',
                'settlements.review.all',
                'settlements.approve.all',
                'settlements.reject.all',

                // Activities (full access)
                'activities.view.all',
                'activities.create.all',
                'activities.update.all',
                'activities.delete.all',

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

                // Reports (full access - all scopes)
                'reports.program-pl.view.all',
                'reports.budget-vs-actual.view.all',
                'reports.budget-utilization.view.all',
                'reports.variance-analysis.view.all',
                'reports.revenue-harvest.view.all',
                'reports.revenue-testing.view.all',
                'reports.expense-by-coa.view.all',
                'reports.expense-by-program.view.all',
                'reports.expense-by-activity.view.all',
                'reports.site-performance.view.all',
                'reports.consolidated-financial.view.all',
                'reports.payment-request-register.view.all',
                'reports.settlement-status.view.all',
                'reports.settlement-compliance.view.all',
                'reports.approval-cycle-time.view.all',
                'reports.transaction-volume.view.all',
                'reports.user-activity.view.all',
                'reports.audit-trail.view.all',
                'reports.digital-signature-log.view.all',
                'reports.admin-activity-log.view.all',
                'reports.critical-actions.view.all',
                'reports.data-change-history.view.all',
                'reports.export.excel.all',
                'reports.export.pdf.all',
                'reports.export.csv.all',
                'reports.schedule-delivery.manage',
                'reports.historical',

                // Fiscal Year Management
                'fiscal-year.view',
                'fiscal-year.manage',
                'fiscal-year.close',
                'fiscal-year.reopen',

                // Subsidi (full management)
                'subsidi.view-eligibility.own',
                'subsidi.view-types.all',
                'subsidi.claim.create.own',
                'subsidi.claim.update.own',
                'subsidi.claim.delete.own',
                'subsidi.view-claims.own',
                'subsidi.view-claims.all',
                'subsidi.approve',
                'subsidi.reject',
                'subsidi.types.create',
                'subsidi.types.update',
                'subsidi.types.deactivate',
                'subsidi.manage-eligibility',
                'subsidi.export',

                // Revenue - Harvest (full management)
                'harvest.view.all',
                'harvest.create.all',
                'harvest.update.all',
                'harvest.delete.draft.all',
                'harvest.review.all',
                'harvest.request-correction',
                'harvest.view-by-program.all',
                'harvest.view-by-buyer.all',
                'harvest.view-cycle-report.all',
                'harvest.export.all',

                // Revenue - Testing Services (full management)
                'testing-services.view.all',
                'testing-services.create.all',
                'testing-services.update.draft.all',
                'testing-services.delete.draft.all',
                'testing-services.submit.assigned',
                'testing-services.approve',
                'testing-services.reject',
                'testing-services.update-payment-status.all',
                'testing-services.upload-contract.all',
                'testing-services.view-by-client.all',
                'testing-services.export.all',

                // Buyers & Clients (full management)
                'buyers.view.all',
                'buyers.create',
                'buyers.update',
                'buyers.deactivate',
                'buyers.view-transaction-history.all',
                'clients.view.all',
                'clients.create',
                'clients.update',
                'clients.deactivate',
                'clients.view-transaction-history.all',
                'buyers-clients.export',

                // Emergency Operations (override permissions)
                'emergency.unlock-budget',
                'emergency.delete-transaction',
                'emergency.bypass-approval',
                'emergency.modify-locked-data',
                'emergency.manual-journal-entry',
                'emergency.force-archive',
                'emergency.reset-any-password',
                'emergency.override-settlement',

                // Audit & Monitoring (full access)
                'audit.view-logs.all',
                'audit.view-admin-activity.all',
                'audit.view-user-access-logs.all',
                'audit.view-critical-alerts.all',
                'audit.view-system-health.all',
                'audit.export.all',

                // System Settings (full configuration)
                'settings.view.all',
                'settings.update.budget-ceiling',
                'settings.update.batch-times',
                'settings.update.settlement-deadline',
                'settings.update.fiscal-year',
                'settings.configure.email',
                'settings.configure.notifications',
                'settings.configure.workflows',
                'settings.configure.approval-hierarchies',

                // Data Management (full access)
                'data.trigger-backup',
                'data.view-backup-history',
                'data.restore-backup',
                'data.run-year-end',
                'data.bulk-archive-programs',
                'data.export-tools',
            ],

            'Finance Operation' => [
                // Dashboard (financial focus)
                'dashboard.view.all',

                // Payment Requests (process payments)
                'payment-requests.view.all',
                'payment-requests.process',

                // Settlements (review and approve)
                'settlements.view.all',
                'settlements.review.all',
                'settlements.request-revision',
                'settlements.approve.all',
                'settlements.reject.all',

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

                // Reports (financial + operational focus)
                'reports.program-pl.view.all',
                'reports.budget-vs-actual.view.all',
                'reports.budget-utilization.view.all',
                'reports.variance-analysis.view.all',
                'reports.revenue-harvest.view.all',
                'reports.revenue-testing.view.all',
                'reports.expense-by-coa.view.all',
                'reports.expense-by-program.view.all',
                'reports.expense-by-activity.view.all',
                'reports.site-performance.view.all',
                'reports.consolidated-financial.view.all',
                'reports.payment-request-register.view.all',
                'reports.settlement-status.view.all',
                'reports.settlement-compliance.view.all',
                'reports.approval-cycle-time.view.all',
                'reports.transaction-volume.view.all',
                'reports.audit-trail.view.all',
                'reports.digital-signature-log.view.all',
                'reports.data-change-history.view.all',
                'reports.export.excel.all',
                'reports.export.pdf.all',
                'reports.export.csv.all',
                'reports.schedule-delivery.manage',

                // Subsidi (basic + process payment)
                'subsidi.view-eligibility.own',
                'subsidi.view-types.all',
                'subsidi.claim.create.own',
                'subsidi.claim.update.own',
                'subsidi.claim.delete.own',
                'subsidi.view-claims.own',
                'subsidi.process-payment',

                // Revenue - Harvest (review + special permissions)
                'harvest.view.all',
                'harvest.update.after-48hrs',
                'harvest.review.all',
                'harvest.request-correction',
                'harvest.view-by-program.all',
                'harvest.view-by-buyer.all',
                'harvest.view-cycle-report.all',
                'harvest.export.all',

                // Revenue - Testing Services (view + payment status)
                'testing-services.view.all',
                'testing-services.update-payment-status.all',
                'testing-services.view-by-client.all',
                'testing-services.export.all',

                // Buyers & Clients (full management)
                'buyers.view.all',
                'buyers.create',
                'buyers.update',
                'buyers.deactivate',
                'buyers.view-transaction-history.all',
                'clients.view.all',
                'clients.create',
                'clients.update',
                'clients.deactivate',
                'clients.view-transaction-history.all',
                'buyers-clients.export',

                // Audit & Monitoring (limited - no admin/user logs)
                'audit.view-logs.all',
                'audit.export.all',

                // Data Management (export only)
                'data.export-tools',
            ],

            'Farm Admin' => [
                // Dashboard (site-specific)
                'dashboard.view.site',

                // Payment Requests (view only for assigned sites)
                'payment-requests.view.site',

                // Settlements (preliminary review)
                'settlements.view.site',
                'settlements.review.site',
                'settlements.request-revision',

                // Programs (view only)
                'programs.view.site',

                // Profile
                'profile.view.own',
                'profile.update.own',
                'profile.change-password.own',

                // Notifications
                'notifications.view.own',
                'notifications.manage.own',

                // Reports (operational - site scope)
                'reports.payment-request-register.view.site',
                'reports.settlement-status.view.site',
                'reports.settlement-compliance.view.site',

                // Subsidi (basic own)
                'subsidi.view-eligibility.own',
                'subsidi.view-types.all',
                'subsidi.claim.create.own',
                'subsidi.claim.update.own',
                'subsidi.claim.delete.own',
                'subsidi.view-claims.own',

                // Revenue - Harvest (site scope view)
                'harvest.view.site',

                // Revenue - Testing Services (site scope view)
                'testing-services.view.site',

                // Buyers & Clients (view only)
                'buyers.view.all',
                'clients.view.all',
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

        if (! $defaultSite) {
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
