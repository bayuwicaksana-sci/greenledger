## 1. Backend Foundation - Controllers & Services

- [x] 1.1 Create `FiscalYearController` with resource routes (index, create, store, show, edit, update, destroy)
- [x] 1.2 Add admin middleware to fiscal year routes in `routes/web.php`
- [x] 1.3 Create `FiscalYearService` class with methods: close(), reopen(), validate(), generateYearEndReport()
- [x] 1.4 Create `StoreFiscalYearRequest` with validation rules (start_date, end_date, auto-calculate year from start_date)
- [x] 1.5 Create `UpdateFiscalYearRequest` with validation for editing dates
- [x] 1.6 Create `CloseFiscalYearRequest` with validation for options array and notes field
- [x] 1.7 Implement FiscalYearController@close action with pre-close validation
- [x] 1.8 Implement FiscalYearController@reopen action with permission check
- [x] 1.9 Add soft delete prevention logic in destroy method (check program count)

## 2. Permissions & Authorization

- [x] 2.1 Add fiscal year permissions to `lang/en/permissions.php`: fiscal-year.manage, fiscal-year.view, fiscal-year.close, fiscal-year.reopen
- [x] 2.2 Add `reports.historical` permission to permissions file
- [x] 2.3 Add `program.override-closed-fy` permission for creating programs in closed FYs
- [x] 2.4 Update `RolePermissionSeeder` to assign fiscal-year.* permissions to Manager and AVP roles
- [x] 2.5 Assign reports.historical permission to Finance Operation, Manager, and AVP roles
- [x] 2.6 Create or update `FiscalYearPolicy` with methods: viewAny, create, update, delete, close, reopen
- [x] 2.7 Register FiscalYearPolicy in `AuthServiceProvider`

## 3. Year-End Closing Logic

- [x] 3.1 Implement FiscalYearService@close() method with transaction wrapper
- [x] 3.2 Add pre-close validation: count active programs, pending payment requests, unsettled transactions
- [x] 3.3 Implement "Archive completed programs" action (query Program::completed(), update status to archived)
- [ ] 3.4 Implement "Block new programs" action (add validation in StoreProgramRequest)
- [ ] 3.5 Implement "Block new transactions" action (add validation in PaymentRequestController and RevenueControllers)
- [ ] 3.6 Implement "Generate year-end report" action (call generateYearEndReport() method)
- [x] 3.7 Implement "Send notifications" action (notify PIs, Managers, AVPs via Laravel notifications)
- [x] 3.8 Add audit logging for close action (user_id, timestamp, options, notes, execution results)
- [x] 3.9 Implement FiscalYearService@reopen() method with audit logging
- [x] 3.10 Create notification class: FiscalYearClosedNotification with email and database channels

## 4. Year-End Report Generation

- [ ] 4.1 Install Laravel Dompdf package (`composer require barryvdh/laravel-dompdf`)
- [ ] 4.2 Create `FiscalYearReportGenerator` service class
- [ ] 4.3 Implement PDF report template view: resources/views/reports/fiscal-year-end.blade.php
- [ ] 4.4 Query and format report data: FY period, total programs, revenue/expense breakdown, budget utilization
- [ ] 4.5 Add program list to PDF with status indicators
- [ ] 4.6 Generate PDF and store in storage/app/fiscal-year-reports/ directory
- [ ] 4.7 Return PDF file path from generateYearEndReport() method
- [ ] 4.8 Add download link in fiscal year show page for generated reports

## 5. Admin UI - Fiscal Year CRUD

- [ ] 5.1 Create Inertia page: resources/js/pages/admin/fiscal-years/index.tsx (list view)
- [ ] 5.2 Create Inertia page: resources/js/pages/admin/fiscal-years/create.tsx (create form)
- [ ] 5.3 Create Inertia page: resources/js/pages/admin/fiscal-years/edit.tsx (edit form)
- [ ] 5.4 Create Inertia page: resources/js/pages/admin/fiscal-years/show.tsx (detail view with close button)
- [ ] 5.5 Implement fiscal year list table with columns: Year, Date Range, Status Badge, Program Count, Actions
- [ ] 5.6 Add search and filter controls: search by year, filter by status (open/closed)
- [ ] 5.7 Implement create form with start_date, end_date inputs; display auto-calculated year read-only
- [ ] 5.8 Add date range validation: end_date > start_date, display error messages
- [ ] 5.9 Implement edit form with warning when fiscal year has programs
- [ ] 5.10 Add delete confirmation with program count check (disable delete if programs exist)

## 6. Admin UI - Year-End Closing Dialog

- [ ] 6.1 Create CloseDialogComponent with checkbox options (archive, block_programs, block_transactions, generate_report, send_notifications)
- [ ] 6.2 Add pre-close validation display: active programs count, pending PRs count, unsettled transactions count
- [ ] 6.3 Display warning messages if validation checks fail
- [ ] 6.4 Add notes textarea (optional) for administrator comments
- [ ] 6.5 Implement confirmation dialog: "Are you sure you want to close FY2026?"
- [ ] 6.6 Submit close action via Inertia form to FiscalYearController@close
- [ ] 6.7 Display success message after closure: "Fiscal year 2026 has been closed successfully"
- [ ] 6.8 Show loading indicator during close action execution
- [ ] 6.9 Add reopen button (visible only to users with fiscal-year.reopen permission)
- [ ] 6.10 Implement reopen dialog requiring reason input (mandatory)

## 7. Fiscal Year Selector Component

- [ ] 7.1 Create reusable component: resources/js/components/FiscalYearSelector.tsx
- [ ] 7.2 Implement dropdown showing all fiscal years in descending order
- [ ] 7.3 Display status badges: "Open" (blue) and "Closed" (gray) next to year codes
- [ ] 7.4 Add tooltip on hover showing full date range (e.g., "FY2026: Jun 1, 2026 - Jun 30, 2027")
- [ ] 7.5 Emit onChange event when user selects different fiscal year
- [ ] 7.6 Persist selected fiscal year in session storage
- [ ] 7.7 Style component with Tailwind CSS consistent with app design

## 8. Program Validation Enhancements

- [ ] 8.1 Update `StoreProgramRequest` validation rules to check if selected fiscal year is closed
- [ ] 8.2 Add custom validation rule: prevent creation for closed FY unless user has program.override-closed-fy permission
- [ ] 8.3 Display validation error message: "Fiscal year 2025 is closed. Cannot create programs for closed fiscal years."
- [ ] 8.4 Add warning message in program create form when closed FY is selected (but allow if user has override)
- [ ] 8.5 Mark closed fiscal years in fiscal year dropdown with "(Closed)" indicator
- [ ] 8.6 Update ProgramController@store to log override action when user creates program for closed FY
- [ ] 8.7 Add validation to prevent program activation if fiscal year is closed

## 9. Program Pages - Fiscal Year Display

- [ ] 9.1 Add fiscal year period display to program detail page (show/edit pages)
- [ ] 9.2 Display format: "FY2026: Jun 1, 2026 - Jun 30, 2027" with status badge
- [ ] 9.3 Add fiscal year badge to program list table (ProgramsTable component)
- [ ] 9.4 Implement tooltip on fiscal year badge hover showing date range
- [ ] 9.5 Add fiscal year column to program list with sortable header
- [ ] 9.6 Add fiscal year filter dropdown to program index page
- [ ] 9.7 Implement filter by fiscal year status: "Open FYs Only", "Closed FYs Only", "All"
- [ ] 9.8 Update ProgramController@index to handle fiscal_year and fy_status query parameters

## 10. Historical Reporting - Backend

- [ ] 10.1 Create `HistoricalReportController` with index method
- [ ] 10.2 Add route: GET /reports/historical (protected by reports.historical permission)
- [ ] 10.3 Implement query to fetch year-over-year summary data (revenue, expenses, net income, budget utilization, program counts)
- [ ] 10.4 Optimize query performance with eager loading and indexed fields
- [ ] 10.5 Implement caching for year-over-year summary (cache key: "historical-summary-{site_id}", TTL: 1 day)
- [ ] 10.6 Create method to fetch revenue breakdown by COA account for selected fiscal year
- [ ] 10.7 Create method to fetch expense breakdown by COA account for selected fiscal year
- [ ] 10.8 Implement Excel export functionality using Laravel Excel package
- [ ] 10.9 Generate Excel file with worksheets: Summary, Revenue Breakdown, Expense Breakdown, Program List

## 11. Historical Reporting - Frontend

- [ ] 11.1 Create Inertia page: resources/js/pages/reports/historical/index.tsx
- [ ] 11.2 Implement year-over-year comparison table with metrics: Total Revenue, Total Expenses, Net Income, Budget Utilization %, etc.
- [ ] 11.3 Calculate and display YoY % change column with color coding (green for positive, red for negative)
- [ ] 11.4 Add site filter dropdown (All Sites, Klaten, Magelang)
- [ ] 11.5 Add fiscal year range selector (default: last 3 years)
- [ ] 11.6 Implement drill-down modal for revenue/expense account breakdown
- [ ] 11.7 Create trend line charts using Chart.js or Recharts library
- [ ] 11.8 Implement multi-metric comparison chart (Revenue, Expenses, Net Income on same chart)
- [ ] 11.9 Add "Export to Excel" button triggering download of historical report
- [ ] 11.10 Display loading indicator during data fetch and export operations

## 12. Dashboard Enhancements

- [ ] 12.1 Add FiscalYearSelector component to main dashboard header
- [ ] 12.2 Update dashboard data queries to filter by selected fiscal year
- [ ] 12.3 Display current selected fiscal year prominently in dashboard
- [ ] 12.4 Add trend sparklines to dashboard cards showing last 3 fiscal years data
- [ ] 12.5 Update dashboard card tooltips to show fiscal year context
- [ ] 12.6 Persist dashboard fiscal year selection in user session

## 13. COA Index Enhancements

- [ ] 13.1 Add FiscalYearSelector to COA index page header
- [ ] 13.2 Update CoaAccountController@index to filter budget allocations by selected fiscal year
- [ ] 13.3 Display fiscal year period and status in page header
- [ ] 13.4 Add sparkline charts to account rows showing budget/actual trend for last 3 fiscal years
- [ ] 13.5 Update actual amount calculations to scope by selected fiscal year
- [ ] 13.6 Add tooltip to sparklines showing year-specific values on hover

## 14. Testing - Backend

- [ ] 14.1 Create `FiscalYearManagementTest` (Feature test)
- [ ] 14.2 Test: Creating fiscal year with calendar year period
- [ ] 14.3 Test: Creating fiscal year with non-calendar period (Jun-Jun)
- [ ] 14.4 Test: Preventing duplicate year codes
- [ ] 14.5 Test: Validating date range (end_date > start_date)
- [ ] 14.6 Test: Editing fiscal year dates
- [ ] 14.7 Test: Preventing deletion of fiscal year with programs
- [ ] 14.8 Test: Successful deletion of fiscal year without programs
- [ ] 14.9 Test: Permission checks for fiscal year operations
- [ ] 14.10 Create `YearEndClosingTest` (Feature test)
- [ ] 14.11 Test: Pre-close validation displays warnings
- [ ] 14.12 Test: Closing with "archive completed programs" action
- [ ] 14.13 Test: Closing with "block new programs" action
- [ ] 14.14 Test: Closing with "generate report" action
- [ ] 14.15 Test: Closing with "send notifications" action
- [ ] 14.16 Test: Multiple actions executed in sequence
- [ ] 14.17 Test: Reopen fiscal year with permission
- [ ] 14.18 Test: Unauthorized reopen attempt returns 403
- [ ] 14.19 Test: Audit logging for close and reopen actions
- [ ] 14.20 Create `HistoricalReportingTest` (Feature test)
- [ ] 14.21 Test: Year-over-year comparison query returns correct data
- [ ] 14.22 Test: Revenue breakdown by COA account
- [ ] 14.23 Test: Expense breakdown by COA account
- [ ] 14.24 Test: Filtering by site
- [ ] 14.25 Test: Filtering by fiscal year range
- [ ] 14.26 Test: Excel export generates file with correct worksheets
- [ ] 14.27 Test: Permission check for historical reports access

## 15. Testing - Frontend & Integration

- [ ] 15.1 Test: FiscalYearSelector component renders fiscal years with badges
- [ ] 15.2 Test: Selecting fiscal year triggers data reload
- [ ] 15.3 Test: Program creation blocked for closed fiscal year (integration test)
- [ ] 15.4 Test: Program creation allowed with override permission (integration test)
- [ ] 15.5 Test: Fiscal year badge displays correctly on program list
- [ ] 15.6 Test: Historical reporting page renders year-over-year table
- [ ] 15.7 Test: Trend charts display correctly with multi-year data
- [ ] 15.8 Test: Close dialog checkboxes work correctly
- [ ] 15.9 Test: Pre-close warnings display when validation fails
- [ ] 15.10 Test: Confirmation dialog prevents accidental closure

## 16. Database & Seeder Updates

- [ ] 16.1 Update `FiscalYearSeeder` to create only current year if none exists (remove auto-creation of current+1)
- [ ] 16.2 Add comment in seeder documenting that future years should be created via UI
- [ ] 16.3 Create factory: `FiscalYearFactory` for testing purposes
- [ ] 16.4 Add fiscal year status (open/closed) to existing test seeders (DatabaseSeeder)

## 17. Code Formatting & Standards

- [ ] 17.1 Run `vendor/bin/pint --dirty` to format all new PHP files
- [ ] 17.2 Run `npm run lint` to check TypeScript/React files
- [ ] 17.3 Fix any linting errors in new frontend files
- [ ] 17.4 Verify all new files follow Laravel and React best practices

## 18. Documentation

- [ ] 18.1 Add fiscal year management section to `docs/database_schema.md`
- [ ] 18.2 Document fiscal year CRUD operations in admin guide
- [ ] 18.3 Document year-end closing process with screenshots
- [ ] 18.4 Document permission requirements for each role
- [ ] 18.5 Create troubleshooting guide for common fiscal year issues
- [ ] 18.6 Document Excel export format and column descriptions

## 19. Deployment Preparation

- [ ] 19.1 Verify no database migrations needed (schema already exists)
- [ ] 19.2 Update RolePermissionSeeder with new permissions
- [ ] 19.3 Test deployment on staging environment
- [ ] 19.4 Run all tests: `php artisan test`
- [ ] 19.5 Create deployment checklist for production
- [ ] 19.6 Prepare rollback plan documentation
- [ ] 19.7 Schedule deployment during low-traffic window

## 20. Post-Deployment

- [ ] 20.1 Run `php artisan db:seed --class=RolePermissionSeeder` on production
- [ ] 20.2 Verify fiscal years are seeded correctly
- [ ] 20.3 Test fiscal year CRUD operations on production
- [ ] 20.4 Test year-end closing workflow with test fiscal year
- [ ] 20.5 Verify historical reporting page loads correctly
- [ ] 20.6 Monitor application logs for fiscal year related errors
- [ ] 20.7 Notify administrators of new fiscal year management features
- [ ] 20.8 Conduct brief training session for admins on year-end closing process
