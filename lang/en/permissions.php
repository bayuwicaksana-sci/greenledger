<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Translation Lines
    |--------------------------------------------------------------------------
    |
    | Organized by ACTION TYPE for easy maintenance and consistent naming.
    | Each permission includes:
    | - 'label': Short, user-friendly name
    | - 'description': Explanation of what the permission allows with scope context
    |
    */

    // VIEW PERMISSIONS - All read/viewing operations
    'view' => [
        // Dashboard
        'dashboard.view.own' => [
            'label' => 'View Own Dashboard',
            'description' =>
                'View dashboard with personal metrics and assigned items',
        ],
        'dashboard.view.assigned' => [
            'label' => 'View Assigned Dashboards',
            'description' =>
                'View dashboard for all assigned programs and activities',
        ],
        'dashboard.view.site' => [
            'label' => 'View Site Dashboard',
            'description' =>
                'View dashboard metrics for all activities within assigned sites',
        ],
        'dashboard.view.all' => [
            'label' => 'View All Dashboards',
            'description' =>
                'View comprehensive dashboard across all sites and programs',
        ],

        // Programs
        'programs.view.own' => [
            'label' => 'View Own Programs',
            'description' =>
                'View programs you created or are directly involved in',
        ],
        'programs.view.assigned' => [
            'label' => 'View Assigned Programs',
            'description' => 'View programs that have been assigned to you',
        ],
        'programs.view.site' => [
            'label' => 'View Site Programs',
            'description' => 'View all programs within your assigned sites',
        ],
        'programs.view.all' => [
            'label' => 'View All Programs',
            'description' =>
                'View programs across all sites without restrictions',
        ],
        'programs.archive.view' => [
            'label' => 'View Archived Programs',
            'description' => 'Access and view archived program records',
        ],

        // Payment Requests
        'payment-requests.view.own' => [
            'label' => 'View Own Payment Requests',
            'description' => 'View payment requests you have submitted',
        ],
        'payment-requests.view.assigned' => [
            'label' => 'View Assigned Payment Requests',
            'description' => 'View payment requests for assigned programs',
        ],
        'payment-requests.view.site' => [
            'label' => 'View Site Payment Requests',
            'description' => 'View all payment requests within assigned sites',
        ],
        'payment-requests.view.all' => [
            'label' => 'View All Payment Requests',
            'description' =>
                'View payment requests across all sites and programs',
        ],

        // Settlements
        'settlements.view.own' => [
            'label' => 'View Own Settlements',
            'description' => 'View settlements you have submitted',
        ],
        'settlements.view.assigned' => [
            'label' => 'View Assigned Settlements',
            'description' => 'View settlements for assigned programs',
        ],
        'settlements.view.site' => [
            'label' => 'View Site Settlements',
            'description' => 'View all settlements within assigned sites',
        ],
        'settlements.view.all' => [
            'label' => 'View All Settlements',
            'description' => 'View settlements across all sites and programs',
        ],

        // Activities
        'activities.view.own' => [
            'label' => 'View Own Activities',
            'description' => 'View activities you created or manage',
        ],
        'activities.view.assigned' => [
            'label' => 'View Assigned Activities',
            'description' => 'View activities assigned to you or your programs',
        ],
        'activities.view.site' => [
            'label' => 'View Site Activities',
            'description' => 'View all activities within assigned sites',
        ],
        'activities.view.all' => [
            'label' => 'View All Activities',
            'description' => 'View activities across all sites and programs',
        ],

        // Sites
        'sites.view.assigned' => [
            'label' => 'View Assigned Sites',
            'description' => 'View information about sites assigned to you',
        ],
        'sites.view.all' => [
            'label' => 'View All Sites',
            'description' => 'View all site locations and their information',
        ],

        // Users
        'users.view.site' => [
            'label' => 'View Site Users',
            'description' => 'View users within your assigned sites',
        ],
        'users.view.all' => [
            'label' => 'View All Users',
            'description' => 'View all users across the system',
        ],
        'users.view-logs' => [
            'label' => 'View User Access Logs',
            'description' => 'View user login history and access logs',
        ],

        // Chart of Accounts
        'coa.view.site' => [
            'label' => 'View Site Chart of Accounts',
            'description' => 'View chart of accounts for assigned sites',
        ],
        'coa.view.all' => [
            'label' => 'View All Accounts',
            'description' => 'View complete chart of accounts across all sites',
        ],
        'coa.view-usage' => [
            'label' => 'View Account Usage',
            'description' => 'View usage statistics and reports for accounts',
        ],
        'coa.view-transactions' => [
            'label' => 'View Account Transactions',
            'description' => 'View transaction history for specific accounts',
        ],

        // Program Assignments
        'program-assignments.view.assigned' => [
            'label' => 'View Assigned Program Assignments',
            'description' =>
                'View assignment details for programs assigned to you',
        ],
        'program-assignments.view.all' => [
            'label' => 'View All Program Assignments',
            'description' =>
                'View all program assignment details across the system',
        ],

        // Notifications
        'notifications.view.own' => [
            'label' => 'View Own Notifications',
            'description' => 'View your personal notifications',
        ],

        // Profile
        'profile.view.own' => [
            'label' => 'View Own Profile',
            'description' => 'View your personal profile information',
        ],
        'profile.view-activity' => [
            'label' => 'View Own Activity History',
            'description' => 'View your activity and action history',
        ],

        // Subsidi
        'subsidi.view-eligibility.own' => [
            'label' => 'View Own Subsidi Eligibility',
            'description' => 'Check your personal subsidy eligibility status',
        ],
        'subsidi.view-types.all' => [
            'label' => 'View All Subsidi Types',
            'description' => 'View available subsidy program types',
        ],
        'subsidi.view-claims.own' => [
            'label' => 'View Own Subsidi Claims',
            'description' => 'View subsidy claims you have submitted',
        ],
        'subsidi.view-claims.all' => [
            'label' => 'View All Subsidi Claims',
            'description' => 'View all subsidy claims across the organization',
        ],

        // Buyers & Clients
        'buyers.view.all' => [
            'label' => 'View All Buyers',
            'description' => 'View all registered buyer information',
        ],
        'buyers.view-transaction-history.assigned' => [
            'label' => 'View Assigned Buyer Transactions',
            'description' =>
                'View transaction history for buyers in assigned programs',
        ],
        'buyers.view-transaction-history.all' => [
            'label' => 'View All Buyer Transactions',
            'description' => 'View complete transaction history for all buyers',
        ],
        'clients.view.all' => [
            'label' => 'View All Clients',
            'description' => 'View all registered client information',
        ],
        'clients.view-transaction-history.assigned' => [
            'label' => 'View Assigned Client Transactions',
            'description' =>
                'View transaction history for clients in assigned programs',
        ],
        'clients.view-transaction-history.all' => [
            'label' => 'View All Client Transactions',
            'description' =>
                'View complete transaction history for all clients',
        ],
    ],

    // CREATE PERMISSIONS - All creation operations
    'create' => [
        // Programs
        'programs.create.site' => [
            'label' => 'Create Site Programs',
            'description' => 'Create new programs within your assigned sites',
        ],
        'programs.create.all' => [
            'label' => 'Create Any Program',
            'description' => 'Create programs in any site without restrictions',
        ],

        // Payment Requests
        'payment-requests.create.own' => [
            'label' => 'Create Own Payment Requests',
            'description' => 'Submit payment requests for your own expenses',
        ],
        'payment-requests.create.assigned' => [
            'label' => 'Create Assigned Payment Requests',
            'description' => 'Submit payment requests for assigned programs',
        ],
        'payment-requests.create.all' => [
            'label' => 'Create Any Payment Request',
            'description' => 'Submit payment requests for any program or site',
        ],

        // Settlements
        'settlements.submit.own' => [
            'label' => 'Submit Own Settlements',
            'description' =>
                'Submit settlement documents for your own advances',
        ],
        'settlements.submit.assigned' => [
            'label' => 'Submit Assigned Settlements',
            'description' =>
                'Submit settlement documents for assigned programs',
        ],
        'settlements.submit.all' => [
            'label' => 'Submit Any Settlement',
            'description' => 'Submit settlement documents for any program',
        ],

        // Activities
        'activities.create.assigned' => [
            'label' => 'Create Assigned Activities',
            'description' => 'Create new activities for assigned programs',
        ],
        'activities.create.all' => [
            'label' => 'Create Any Activity',
            'description' =>
                'Create activities for any program across all sites',
        ],

        // Users
        'users.create' => [
            'label' => 'Create Users',
            'description' => 'Add new user accounts to the system',
        ],

        // Sites
        'sites.create' => [
            'label' => 'Create Sites',
            'description' => 'Add new site locations to the system',
        ],

        // Chart of Accounts
        'coa.create' => [
            'label' => 'Create Accounts',
            'description' => 'Add new accounts to the chart of accounts',
        ],
        'coa.import' => [
            'label' => 'Import Chart of Accounts',
            'description' => 'Bulk import accounts from external files',
        ],

        // Subsidi
        'subsidi.claim.create.own' => [
            'label' => 'Create Subsidi Claims',
            'description' => 'Submit new subsidy claims for personal benefits',
        ],
        'subsidi.types.create' => [
            'label' => 'Create Subsidi Types',
            'description' => 'Define new subsidy program types',
        ],

        // Buyers & Clients
        'buyers.create' => [
            'label' => 'Create Buyers',
            'description' => 'Add new buyer records to the system',
        ],
        'clients.create' => [
            'label' => 'Create Clients',
            'description' => 'Add new client records to the system',
        ],
    ],

    // UPDATE PERMISSIONS - All modification operations
    'update' => [
        // Programs
        'programs.update.own' => [
            'label' => 'Update Own Programs',
            'description' => 'Modify programs you created',
        ],
        'programs.update.assigned' => [
            'label' => 'Update Assigned Programs',
            'description' => 'Modify programs assigned to you',
        ],
        'programs.update.all' => [
            'label' => 'Update Any Program',
            'description' => 'Modify any program across all sites',
        ],

        // Payment Requests
        'payment-requests.update.own' => [
            'label' => 'Update Own Payment Requests',
            'description' => 'Modify payment requests you submitted',
        ],
        'payment-requests.update.assigned' => [
            'label' => 'Update Assigned Payment Requests',
            'description' => 'Modify payment requests for assigned programs',
        ],
        'payment-requests.update.all' => [
            'label' => 'Update Any Payment Request',
            'description' => 'Modify any payment request in the system',
        ],

        // Settlements
        'settlements.update.own' => [
            'label' => 'Update Own Settlements',
            'description' =>
                'Modify settlements you submitted (before approval)',
        ],
        'settlements.update.assigned' => [
            'label' => 'Update Assigned Settlements',
            'description' =>
                'Modify settlements for assigned programs (before approval)',
        ],

        // Activities
        'activities.update.assigned' => [
            'label' => 'Update Assigned Activities',
            'description' => 'Modify activities for assigned programs',
        ],
        'activities.update.all' => [
            'label' => 'Update Any Activity',
            'description' => 'Modify any activity across all sites',
        ],

        // Users
        'users.update' => [
            'label' => 'Update Users',
            'description' => 'Modify user profile information and settings',
        ],
        'users.deactivate' => [
            'label' => 'Deactivate Users',
            'description' => 'Disable user accounts without deleting them',
        ],
        'users.reset-password' => [
            'label' => 'Reset User Passwords',
            'description' => 'Perform password resets for other users',
        ],

        // Sites
        'sites.update' => [
            'label' => 'Update Sites',
            'description' => 'Modify site information and settings',
        ],
        'sites.activate' => [
            'label' => 'Activate/Deactivate Sites',
            'description' => 'Enable or disable site locations',
        ],

        // Chart of Accounts
        'coa.update' => [
            'label' => 'Update Accounts',
            'description' => 'Modify chart of accounts details',
        ],
        'coa.deactivate' => [
            'label' => 'Deactivate Accounts',
            'description' => 'Disable accounts in the chart of accounts',
        ],

        // Notifications
        'notifications.manage.own' => [
            'label' => 'Manage Own Notifications',
            'description' =>
                'Mark notifications as read/unread and delete them',
        ],
        'notifications.manage.all' => [
            'label' => 'Manage All Notifications',
            'description' => 'Manage notifications for all users',
        ],

        // Profile
        'profile.update.own' => [
            'label' => 'Update Own Profile',
            'description' => 'Modify your personal profile information',
        ],
        'profile.change-password.own' => [
            'label' => 'Change Own Password',
            'description' => 'Update your account password',
        ],

        // Subsidi
        'subsidi.claim.update.own' => [
            'label' => 'Update Own Subsidi Claims',
            'description' =>
                'Modify subsidy claims you submitted (draft status)',
        ],
        'subsidi.types.update' => [
            'label' => 'Update Subsidi Types',
            'description' => 'Modify subsidy program type definitions',
        ],
        'subsidi.types.deactivate' => [
            'label' => 'Deactivate Subsidi Types',
            'description' => 'Disable subsidy program types',
        ],

        // Buyers & Clients
        'buyers.update' => [
            'label' => 'Update Buyers',
            'description' => 'Modify buyer information and details',
        ],
        'buyers.deactivate' => [
            'label' => 'Deactivate Buyers',
            'description' => 'Disable buyer accounts',
        ],
        'clients.update' => [
            'label' => 'Update Clients',
            'description' => 'Modify client information and details',
        ],
        'clients.deactivate' => [
            'label' => 'Deactivate Clients',
            'description' => 'Disable client accounts',
        ],
    ],

    // DELETE PERMISSIONS - All deletion operations
    'delete' => [
        // Programs
        'programs.delete.assigned' => [
            'label' => 'Delete Assigned Programs',
            'description' => 'Remove programs assigned to you (draft only)',
        ],
        'programs.delete.all' => [
            'label' => 'Delete Any Program',
            'description' => 'Remove any program (draft only)',
        ],

        // Payment Requests
        'payment-requests.delete.own' => [
            'label' => 'Delete Own Payment Requests',
            'description' =>
                'Remove payment requests you submitted (draft only)',
        ],
        'payment-requests.delete.assigned' => [
            'label' => 'Delete Assigned Payment Requests',
            'description' =>
                'Remove payment requests for assigned programs (draft only)',
        ],
        'payment-requests.delete.all' => [
            'label' => 'Delete Any Payment Request',
            'description' => 'Remove any payment request (draft only)',
        ],

        // Settlements
        'settlements.delete.own' => [
            'label' => 'Delete Own Settlements',
            'description' => 'Remove settlements you submitted (pending only)',
        ],
        'settlements.delete.assigned' => [
            'label' => 'Delete Assigned Settlements',
            'description' =>
                'Remove settlements for assigned programs (pending only)',
        ],
        'settlements.delete.all' => [
            'label' => 'Delete Any Settlement',
            'description' => 'Remove any settlement (pending only)',
        ],

        // Activities
        'activities.delete.assigned' => [
            'label' => 'Delete Assigned Activities',
            'description' =>
                'Remove activities for assigned programs (no transactions)',
        ],
        'activities.delete.all' => [
            'label' => 'Delete Any Activity',
            'description' => 'Remove any activity (no transactions)',
        ],

        // Subsidi
        'subsidi.claim.delete.own' => [
            'label' => 'Delete Own Subsidi Claims',
            'description' => 'Remove subsidy claims you submitted (draft only)',
        ],
    ],

    // APPROVAL PERMISSIONS - Approval workflow operations
    'approval' => [
        // Programs
        'programs.submit-for-approval.assigned' => [
            'label' => 'Submit Assigned Programs for Approval',
            'description' =>
                'Submit assigned program budgets to approval workflow',
        ],
        'programs.submit-for-approval.all' => [
            'label' => 'Submit Any Program for Approval',
            'description' => 'Submit any program budget to approval workflow',
        ],
        'programs.approve.all' => [
            'label' => 'Approve Programs',
            'description' => 'Approve program budgets and activate them',
        ],
        'programs.reject.all' => [
            'label' => 'Reject Programs',
            'description' => 'Reject program budget proposals with feedback',
        ],
        'programs.complete.all' => [
            'label' => 'Mark Programs Complete',
            'description' =>
                'Mark programs as completed pending final approval',
        ],

        // Payment Requests
        'payment-requests.approve.all' => [
            'label' => 'Approve Payment Requests',
            'description' =>
                'Review and approve payment requests for processing',
        ],
        'payment-requests.reject.all' => [
            'label' => 'Reject Payment Requests',
            'description' => 'Reject payment requests with reasons',
        ],
        'payment-requests.cancel.all' => [
            'label' => 'Cancel Payment Requests',
            'description' => 'Cancel approved payment requests when necessary',
        ],
        'payment-requests.process' => [
            'label' => 'Process Payments',
            'description' => 'Mark approved payments as processed/paid',
        ],

        // Settlements
        'settlements.approve.all' => [
            'label' => 'Approve Settlements',
            'description' => 'Review and approve settlement documentation',
        ],
        'settlements.reject.all' => [
            'label' => 'Reject Settlements',
            'description' => 'Reject settlement submissions with feedback',
        ],

        // Subsidi
        'subsidi.approve' => [
            'label' => 'Approve Subsidi Claims',
            'description' => 'Review and approve employee subsidy requests',
        ],
        'subsidi.reject' => [
            'label' => 'Reject Subsidi Claims',
            'description' => 'Reject subsidy claims with reasons',
        ],
        'subsidi.process-payment' => [
            'label' => 'Process Subsidi Payments',
            'description' => 'Mark approved subsidy claims as paid',
        ],
    ],

    // REVIEW PERMISSIONS - Review and request operations
    'review' => [
        // Settlements
        'settlements.review.site' => [
            'label' => 'Review Site Settlements',
            'description' =>
                'Perform preliminary review of settlements within assigned sites',
        ],
        'settlements.review.all' => [
            'label' => 'Review All Settlements',
            'description' => 'Perform comprehensive review of all settlements',
        ],
        'settlements.request-revision' => [
            'label' => 'Request Settlement Revision',
            'description' => 'Request changes to submitted settlements',
        ],

        // Revenue - Harvest
        'harvest.review.all' => [
            'label' => 'Review Harvest Records',
            'description' => 'Review and validate harvest revenue entries',
        ],
        'harvest.request-correction' => [
            'label' => 'Request Harvest Correction',
            'description' => 'Request corrections to harvest records',
        ],
    ],

    // ARCHIVE PERMISSIONS - Archiving operations
    'archive' => [
        'programs.archive.manage' => [
            'label' => 'Manage Program Archives',
            'description' => 'Archive and restore program records',
        ],
    ],

    // MANAGE PERMISSIONS - General management operations
    'manage' => [
        // Users & Roles
        'users.assign-roles' => [
            'label' => 'Assign User Roles',
            'description' => 'Grant or revoke role assignments for users',
        ],
        'users.assign-sites' => [
            'label' => 'Assign User Sites',
            'description' => 'Assign users to specific site locations',
        ],

        // Sites
        'sites.compare-performance' => [
            'label' => 'Compare Site Performance',
            'description' =>
                'View and compare performance metrics across sites',
        ],

        // Program Assignments
        'program-assignments.assign' => [
            'label' => 'Assign Programs to Users',
            'description' => 'Assign users to program teams',
        ],
        'program-assignments.remove' => [
            'label' => 'Remove Program Assignments',
            'description' => 'Remove users from program teams',
        ],

        // Notifications
        'notifications.configure' => [
            'label' => 'Configure Notification Settings',
            'description' => 'Manage system-wide notification preferences',
        ],

        // Subsidi
        'subsidi.manage-eligibility' => [
            'label' => 'Manage Subsidi Eligibility',
            'description' => 'Configure user eligibility for subsidy programs',
        ],
    ],

    // EXPORT PERMISSIONS - Export and download operations
    'export' => [
        // Chart of Accounts
        'coa.export' => [
            'label' => 'Export Chart of Accounts',
            'description' => 'Download chart of accounts in various formats',
        ],

        // Subsidi
        'subsidi.export' => [
            'label' => 'Export Subsidi Data',
            'description' => 'Download subsidy claims and payment reports',
        ],

        // Buyers & Clients
        'buyers-clients.export' => [
            'label' => 'Export Buyers & Clients',
            'description' => 'Download buyer and client information',
        ],

        // Reports - Excel
        'reports.export.excel.assigned' => [
            'label' => 'Export Assigned Reports to Excel',
            'description' =>
                'Download reports for assigned programs in Excel format',
        ],
        'reports.export.excel.all' => [
            'label' => 'Export All Reports to Excel',
            'description' => 'Download any report in Excel format',
        ],

        // Reports - PDF
        'reports.export.pdf.assigned' => [
            'label' => 'Export Assigned Reports to PDF',
            'description' =>
                'Download reports for assigned programs in PDF format',
        ],
        'reports.export.pdf.all' => [
            'label' => 'Export All Reports to PDF',
            'description' => 'Download any report in PDF format',
        ],

        // Reports - CSV
        'reports.export.csv.assigned' => [
            'label' => 'Export Assigned Reports to CSV',
            'description' =>
                'Download reports for assigned programs in CSV format',
        ],
        'reports.export.csv.all' => [
            'label' => 'Export All Reports to CSV',
            'description' => 'Download any report in CSV format',
        ],
    ],

    // REPORTS PERMISSIONS - Reporting and analytics
    'reports' => [
        // Financial Reports - Program P&L
        'reports.program-pl.view.assigned' => [
            'label' => 'View Assigned Program P&L',
            'description' =>
                'Access profit & loss reports for assigned programs',
        ],
        'reports.program-pl.view.all' => [
            'label' => 'View All Program P&L',
            'description' => 'Access profit & loss reports across all programs',
        ],

        // Financial Reports - Budget vs Actual
        'reports.budget-vs-actual.view.assigned' => [
            'label' => 'View Assigned Budget vs Actual',
            'description' =>
                'Compare budget to actual expenses for assigned programs',
        ],
        'reports.budget-vs-actual.view.all' => [
            'label' => 'View All Budget vs Actual',
            'description' =>
                'Compare budget to actual expenses across all programs',
        ],

        // Financial Reports - Budget Utilization
        'reports.budget-utilization.view.assigned' => [
            'label' => 'View Assigned Budget Utilization',
            'description' => 'Track budget usage rates for assigned programs',
        ],
        'reports.budget-utilization.view.all' => [
            'label' => 'View All Budget Utilization',
            'description' => 'Track budget usage rates across all programs',
        ],

        // Financial Reports - Variance Analysis
        'reports.variance-analysis.view.assigned' => [
            'label' => 'View Assigned Variance Analysis',
            'description' => 'Analyze budget variances for assigned programs',
        ],
        'reports.variance-analysis.view.all' => [
            'label' => 'View All Variance Analysis',
            'description' => 'Analyze budget variances across all programs',
        ],

        // Financial Reports - Revenue (Harvest)
        'reports.revenue-harvest.view.assigned' => [
            'label' => 'View Assigned Harvest Revenue',
            'description' =>
                'Access harvest revenue reports for assigned programs',
        ],
        'reports.revenue-harvest.view.all' => [
            'label' => 'View All Harvest Revenue',
            'description' =>
                'Access harvest revenue reports across all programs',
        ],

        // Financial Reports - Revenue (Testing)
        'reports.revenue-testing.view.assigned' => [
            'label' => 'View Assigned Testing Revenue',
            'description' =>
                'Access testing service revenue reports for assigned programs',
        ],
        'reports.revenue-testing.view.all' => [
            'label' => 'View All Testing Revenue',
            'description' =>
                'Access testing service revenue reports across all programs',
        ],

        // Financial Reports - Expenses by COA
        'reports.expense-by-coa.view.assigned' => [
            'label' => 'View Assigned Expense by Account',
            'description' =>
                'View expense breakdown by chart of accounts for assigned programs',
        ],
        'reports.expense-by-coa.view.all' => [
            'label' => 'View All Expense by Account',
            'description' =>
                'View expense breakdown by chart of accounts across all programs',
        ],

        // Financial Reports - Expenses by Program
        'reports.expense-by-program.view.assigned' => [
            'label' => 'View Assigned Expense by Program',
            'description' =>
                'View expense breakdown by program for assigned programs',
        ],
        'reports.expense-by-program.view.all' => [
            'label' => 'View All Expense by Program',
            'description' => 'View expense breakdown across all programs',
        ],

        // Financial Reports - Expenses by Activity
        'reports.expense-by-activity.view.assigned' => [
            'label' => 'View Assigned Expense by Activity',
            'description' =>
                'View expense breakdown by activity for assigned programs',
        ],
        'reports.expense-by-activity.view.all' => [
            'label' => 'View All Expense by Activity',
            'description' =>
                'View expense breakdown by activity across all programs',
        ],

        // Financial Reports - Site Performance & Consolidated
        'reports.site-performance.view.all' => [
            'label' => 'View Site Performance Comparison',
            'description' =>
                'Compare financial performance across different sites',
        ],
        'reports.consolidated-financial.view.all' => [
            'label' => 'View Consolidated Financial Report',
            'description' =>
                'Access organization-wide consolidated financial statements',
        ],

        // Operational Reports - Payment Request Register
        'reports.payment-request-register.view.own' => [
            'label' => 'View Own Payment Request Register',
            'description' => 'View register of your payment requests',
        ],
        'reports.payment-request-register.view.assigned' => [
            'label' => 'View Assigned Payment Request Register',
            'description' =>
                'View payment request register for assigned programs',
        ],
        'reports.payment-request-register.view.site' => [
            'label' => 'View Site Payment Request Register',
            'description' => 'View payment request register for assigned sites',
        ],
        'reports.payment-request-register.view.all' => [
            'label' => 'View All Payment Request Register',
            'description' =>
                'View complete payment request register across all programs',
        ],

        // Operational Reports - Settlement Status
        'reports.settlement-status.view.own' => [
            'label' => 'View Own Settlement Status',
            'description' => 'Track status of your settlement submissions',
        ],
        'reports.settlement-status.view.assigned' => [
            'label' => 'View Assigned Settlement Status',
            'description' => 'Track settlement status for assigned programs',
        ],
        'reports.settlement-status.view.site' => [
            'label' => 'View Site Settlement Status',
            'description' => 'Track settlement status for assigned sites',
        ],
        'reports.settlement-status.view.all' => [
            'label' => 'View All Settlement Status',
            'description' => 'Track settlement status across all programs',
        ],

        // Operational Reports - Settlement Compliance
        'reports.settlement-compliance.view.site' => [
            'label' => 'View Site Settlement Compliance',
            'description' =>
                'Monitor settlement deadline compliance for assigned sites',
        ],
        'reports.settlement-compliance.view.all' => [
            'label' => 'View All Settlement Compliance',
            'description' =>
                'Monitor settlement deadline compliance across all sites',
        ],

        // Operational Reports - System Metrics
        'reports.approval-cycle-time.view.all' => [
            'label' => 'View Approval Cycle Time',
            'description' => 'Analyze time taken for approval workflows',
        ],
        'reports.transaction-volume.view.all' => [
            'label' => 'View Transaction Volume',
            'description' => 'Monitor transaction volumes and trends',
        ],
        'reports.user-activity.view.all' => [
            'label' => 'View User Activity Report',
            'description' => 'Track user activity and system usage patterns',
        ],

        // Compliance & Audit Reports
        'reports.audit-trail.view.all' => [
            'label' => 'View Audit Trail Report',
            'description' => 'Access complete system audit trail',
        ],
        'reports.digital-signature-log.view.all' => [
            'label' => 'View Digital Signature Log',
            'description' => 'View log of all digital signatures and approvals',
        ],
        'reports.admin-activity-log.view.all' => [
            'label' => 'View Admin Activity Log',
            'description' => 'Monitor administrative actions and changes',
        ],
        'reports.critical-actions.view.all' => [
            'label' => 'View Critical Actions Report',
            'description' =>
                'Review high-risk operations and emergency actions',
        ],
        'reports.data-change-history.view.all' => [
            'label' => 'View Data Change History',
            'description' => 'Track changes to critical data records',
        ],

        // Report Scheduling
        'reports.schedule-delivery.manage' => [
            'label' => 'Manage Scheduled Reports',
            'description' =>
                'Create and manage automated report delivery schedules',
        ],
    ],

    // SUBSIDI PERMISSIONS - Subsidy-specific operations
    'subsidi' => [
        // (Most subsidi permissions are already in other categories)
    ],

    // REVENUE PERMISSIONS - Revenue tracking operations
    'revenue' => [
        // Harvest - View
        'harvest.view.assigned' => [
            'label' => 'View Assigned Harvest Records',
            'description' => 'View harvest data for assigned programs',
        ],
        'harvest.view.site' => [
            'label' => 'View Site Harvest Records',
            'description' => 'View harvest data for assigned sites',
        ],
        'harvest.view.all' => [
            'label' => 'View All Harvest Records',
            'description' => 'View harvest data across all programs',
        ],

        // Harvest - Create/Update
        'harvest.create.assigned' => [
            'label' => 'Record Assigned Harvest',
            'description' => 'Enter harvest data for assigned programs',
        ],
        'harvest.create.all' => [
            'label' => 'Record Any Harvest',
            'description' => 'Enter harvest data for any program',
        ],
        'harvest.update.own' => [
            'label' => 'Update Own Harvest (Within 48hrs)',
            'description' =>
                'Modify harvest records you entered within 48 hours',
        ],
        'harvest.update.assigned' => [
            'label' => 'Update Assigned Harvest',
            'description' => 'Modify harvest records for assigned programs',
        ],
        'harvest.update.all' => [
            'label' => 'Update Any Harvest',
            'description' => 'Modify any harvest record',
        ],
        'harvest.update.after-48hrs' => [
            'label' => 'Update Harvest After 48 Hours',
            'description' =>
                'Modify locked harvest records older than 48 hours',
        ],

        // Harvest - Delete
        'harvest.delete.draft.own' => [
            'label' => 'Delete Own Draft Harvest',
            'description' => 'Remove draft harvest records you created',
        ],
        'harvest.delete.draft.assigned' => [
            'label' => 'Delete Assigned Draft Harvest',
            'description' =>
                'Remove draft harvest records for assigned programs',
        ],
        'harvest.delete.draft.all' => [
            'label' => 'Delete Any Draft Harvest',
            'description' => 'Remove any draft harvest record',
        ],

        // Harvest - Reporting
        'harvest.view-by-program.assigned' => [
            'label' => 'View Assigned Harvest by Program',
            'description' =>
                'View harvest data grouped by program for assigned programs',
        ],
        'harvest.view-by-program.all' => [
            'label' => 'View All Harvest by Program',
            'description' =>
                'View harvest data grouped by program across all sites',
        ],
        'harvest.view-by-buyer.assigned' => [
            'label' => 'View Assigned Harvest by Buyer',
            'description' =>
                'View harvest data grouped by buyer for assigned programs',
        ],
        'harvest.view-by-buyer.all' => [
            'label' => 'View All Harvest by Buyer',
            'description' =>
                'View harvest data grouped by buyer across all sites',
        ],
        'harvest.view-cycle-report.assigned' => [
            'label' => 'View Assigned Harvest Cycle Report',
            'description' =>
                'View harvest cycle analysis for assigned programs',
        ],
        'harvest.view-cycle-report.all' => [
            'label' => 'View All Harvest Cycle Report',
            'description' => 'View harvest cycle analysis across all programs',
        ],
        'harvest.export.assigned' => [
            'label' => 'Export Assigned Harvest Data',
            'description' => 'Download harvest data for assigned programs',
        ],
        'harvest.export.all' => [
            'label' => 'Export All Harvest Data',
            'description' => 'Download harvest data for all programs',
        ],

        // Testing Services - View
        'testing-services.view.assigned' => [
            'label' => 'View Assigned Testing Services',
            'description' =>
                'View testing service records for assigned programs',
        ],
        'testing-services.view.site' => [
            'label' => 'View Site Testing Services',
            'description' => 'View testing service records for assigned sites',
        ],
        'testing-services.view.all' => [
            'label' => 'View All Testing Services',
            'description' => 'View testing service records across all programs',
        ],

        // Testing Services - Create/Update
        'testing-services.create.assigned' => [
            'label' => 'Record Assigned Testing Services',
            'description' => 'Enter testing service data for assigned programs',
        ],
        'testing-services.create.all' => [
            'label' => 'Record Any Testing Service',
            'description' => 'Enter testing service data for any program',
        ],
        'testing-services.update.draft.assigned' => [
            'label' => 'Update Assigned Draft Testing Services',
            'description' =>
                'Modify draft testing service records for assigned programs',
        ],
        'testing-services.update.draft.all' => [
            'label' => 'Update Any Draft Testing Service',
            'description' => 'Modify any draft testing service record',
        ],

        // Testing Services - Delete
        'testing-services.delete.draft.assigned' => [
            'label' => 'Delete Assigned Draft Testing Services',
            'description' =>
                'Remove draft testing service records for assigned programs',
        ],
        'testing-services.delete.draft.all' => [
            'label' => 'Delete Any Draft Testing Service',
            'description' => 'Remove any draft testing service record',
        ],

        // Testing Services - Workflow
        'testing-services.submit.assigned' => [
            'label' => 'Submit Assigned Testing Services',
            'description' => 'Submit testing service records for approval',
        ],
        'testing-services.approve' => [
            'label' => 'Approve Testing Services',
            'description' => 'Review and approve testing service submissions',
        ],
        'testing-services.reject' => [
            'label' => 'Reject Testing Services',
            'description' => 'Reject testing service submissions with feedback',
        ],
        'testing-services.update-payment-status.assigned' => [
            'label' => 'Update Assigned Testing Payment Status',
            'description' =>
                'Update payment status for testing services in assigned programs',
        ],
        'testing-services.update-payment-status.all' => [
            'label' => 'Update Any Testing Payment Status',
            'description' => 'Update payment status for any testing service',
        ],

        // Testing Services - Documents & Reporting
        'testing-services.upload-contract.assigned' => [
            'label' => 'Upload Assigned Testing Contracts',
            'description' =>
                'Upload contract documents for testing services in assigned programs',
        ],
        'testing-services.upload-contract.all' => [
            'label' => 'Upload Any Testing Contract',
            'description' =>
                'Upload contract documents for any testing service',
        ],
        'testing-services.view-by-client.assigned' => [
            'label' => 'View Assigned Testing by Client',
            'description' =>
                'View testing services grouped by client for assigned programs',
        ],
        'testing-services.view-by-client.all' => [
            'label' => 'View All Testing by Client',
            'description' =>
                'View testing services grouped by client across all programs',
        ],
        'testing-services.export.assigned' => [
            'label' => 'Export Assigned Testing Data',
            'description' =>
                'Download testing service data for assigned programs',
        ],
        'testing-services.export.all' => [
            'label' => 'Export All Testing Data',
            'description' => 'Download testing service data for all programs',
        ],
    ],

    // AUDIT PERMISSIONS - Audit and monitoring operations
    'audit' => [
        'audit.view-logs.all' => [
            'label' => 'View All Audit Logs',
            'description' =>
                'Access complete system audit trail and activity logs',
        ],
        'audit.view-admin-activity.all' => [
            'label' => 'View Admin Activity',
            'description' =>
                'Monitor administrative actions and system changes',
        ],
        'audit.view-user-access-logs.all' => [
            'label' => 'View User Access Logs',
            'description' => 'Monitor user login history and access patterns',
        ],
        'audit.view-critical-alerts.all' => [
            'label' => 'View Critical Alerts',
            'description' => 'Monitor and respond to critical system alerts',
        ],
        'audit.view-system-health.all' => [
            'label' => 'View System Health',
            'description' => 'Monitor system health and performance metrics',
        ],
        'audit.export.all' => [
            'label' => 'Export Audit Data',
            'description' => 'Download audit logs and compliance reports',
        ],
    ],

    // SETTINGS PERMISSIONS - System configuration
    'settings' => [
        'settings.view.all' => [
            'label' => 'View System Settings',
            'description' => 'Access system configuration and settings',
        ],
        'settings.update.budget-ceiling' => [
            'label' => 'Update Budget Ceiling',
            'description' => 'Modify organization-wide budget limits',
        ],
        'settings.update.batch-times' => [
            'label' => 'Update Batch Processing Times',
            'description' => 'Configure automated batch processing schedules',
        ],
        'settings.update.settlement-deadline' => [
            'label' => 'Update Settlement Deadlines',
            'description' => 'Configure settlement submission deadline rules',
        ],
        'settings.update.fiscal-year' => [
            'label' => 'Update Fiscal Year Dates',
            'description' => 'Configure fiscal year start and end dates',
        ],
        'settings.configure.email' => [
            'label' => 'Configure Email Settings',
            'description' => 'Manage email server and notification settings',
        ],
        'settings.configure.notifications' => [
            'label' => 'Configure Notification Templates',
            'description' => 'Design and manage notification message templates',
        ],
        'settings.configure.workflows' => [
            'label' => 'Configure Workflows',
            'description' => 'Design and modify approval workflow rules',
        ],
        'settings.configure.approval-hierarchies' => [
            'label' => 'Configure Approval Hierarchies',
            'description' =>
                'Define multi-level approval chains and thresholds',
        ],
    ],

    // EMERGENCY PERMISSIONS - Emergency override operations
    'emergency' => [
        'emergency.unlock-budget' => [
            'label' => 'Emergency: Unlock Budget',
            'description' =>
                'Override budget locks in critical situations (requires justification)',
        ],
        'emergency.delete-transaction' => [
            'label' => 'Emergency: Delete Transaction',
            'description' =>
                'Remove financial transactions (fully logged and audited)',
        ],
        'emergency.bypass-approval' => [
            'label' => 'Emergency: Bypass Approval',
            'description' =>
                'Skip normal approval workflow when urgent (requires justification)',
        ],
        'emergency.modify-locked-data' => [
            'label' => 'Emergency: Modify Locked Data',
            'description' =>
                'Edit locked or finalized records (requires justification)',
        ],
        'emergency.manual-journal-entry' => [
            'label' => 'Emergency: Manual Journal Entry',
            'description' =>
                'Create manual accounting adjustments (requires justification)',
        ],
        'emergency.force-archive' => [
            'label' => 'Emergency: Force Archive',
            'description' =>
                'Force archive or restore records bypassing normal rules',
        ],
        'emergency.reset-any-password' => [
            'label' => 'Emergency: Reset Any Password',
            'description' =>
                'Reset passwords for any user including administrators',
        ],
        'emergency.override-settlement' => [
            'label' => 'Emergency: Override Settlement',
            'description' =>
                'Override settlement approval requirements (requires justification)',
        ],
    ],

    // DATA PERMISSIONS - Data management operations
    'data' => [
        'data.trigger-backup' => [
            'label' => 'Trigger System Backup',
            'description' => 'Initiate manual database backup operations',
        ],
        'data.view-backup-history' => [
            'label' => 'View Backup History',
            'description' => 'Access backup logs and restoration history',
        ],
        'data.restore-backup' => [
            'label' => 'Restore from Backup',
            'description' => 'Restore system data from backup files',
        ],
        'data.run-year-end' => [
            'label' => 'Run Year-End Process',
            'description' => 'Execute annual fiscal year closure procedures',
        ],
        'data.bulk-archive-programs' => [
            'label' => 'Bulk Archive Programs',
            'description' => 'Archive multiple programs in batch operations',
        ],
        'data.export-tools' => [
            'label' => 'Access Data Export Tools',
            'description' => 'Use advanced data export and migration tools',
        ],
    ],

    // APPROVAL SYSTEM PERMISSIONS - Workflow and approval management
    'approval-system' => [
        // Workflow Management (Admin)
        'approval-workflows.view.all' => [
            'label' => 'View All Approval Workflows',
            'description' =>
                'View all configured approval workflows and their versions',
        ],
        'approval-workflows.create' => [
            'label' => 'Create Approval Workflows',
            'description' => 'Create new approval workflow templates',
        ],
        'approval-workflows.update' => [
            'label' => 'Update Approval Workflows',
            'description' => 'Modify existing approval workflow configurations',
        ],
        'approval-workflows.delete' => [
            'label' => 'Delete Approval Workflows',
            'description' =>
                'Remove approval workflow templates (inactive only)',
        ],
        'approval-workflows.activate' => [
            'label' => 'Activate/Deactivate Workflows',
            'description' => 'Enable or disable approval workflows',
        ],
        'approval-workflows.duplicate' => [
            'label' => 'Duplicate Workflows',
            'description' => 'Create copies of existing workflows',
        ],

        // Instance Management (Runtime)
        'approval-instances.view.own' => [
            'label' => 'View Own Approval Instances',
            'description' =>
                'View approval instances you submitted or are assigned to',
        ],
        'approval-instances.view.assigned' => [
            'label' => 'View Assigned Approval Instances',
            'description' => 'View approval instances pending your action',
        ],
        'approval-instances.view.all' => [
            'label' => 'View All Approval Instances',
            'description' => 'View all approval instances across the system',
        ],
        'approval-instances.submit' => [
            'label' => 'Submit for Approval',
            'description' => 'Submit items to approval workflows',
        ],
        'approval-instances.cancel.own' => [
            'label' => 'Cancel Own Approvals',
            'description' => 'Cancel approval instances you submitted',
        ],
        'approval-instances.cancel.all' => [
            'label' => 'Cancel Any Approval',
            'description' => 'Cancel any approval instance in the system',
        ],

        // Approval Actions (Runtime)
        'approval-actions.approve' => [
            'label' => 'Approve Requests',
            'description' =>
                'Approve items in approval workflows when assigned as approver',
        ],
        'approval-actions.reject' => [
            'label' => 'Reject Requests',
            'description' => 'Reject items in approval workflows with feedback',
        ],
        'approval-actions.request-changes' => [
            'label' => 'Request Changes',
            'description' =>
                'Request modifications to submitted approval items',
        ],
        'approval-actions.view-history' => [
            'label' => 'View Approval History',
            'description' =>
                'View complete approval action history and audit trail',
        ],
    ],

    // PERSONAL PERMISSIONS - Personal/profile operations
    'personal' => [
        // (Most personal permissions are already in other categories)
    ],
];
