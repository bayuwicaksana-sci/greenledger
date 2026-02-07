## 1. Database Schema - Breaking Changes

- [ ] 1.1 Create migration: add `fiscal_year_id` to `coa_accounts` table with FK constraint
- [ ] 1.2 Create migration: update `coa_accounts` unique constraint to include `fiscal_year_id`
- [ ] 1.3 Create migration: add `fiscal_year_id` to `programs` table
- [ ] 1.4 Create migration: migrate data from `programs.fiscal_year` integer to `fiscal_year_id` FK
- [ ] 1.5 Create migration: drop `programs.fiscal_year` column after data migration
- [ ] 1.6 Create migration: create `budget_commitments` table with full schema
- [ ] 1.7 Add indexes: `coa_accounts.fiscal_year_id`, `budget_commitments.fiscal_year_id`, `budget_commitments.status`

## 2. Model Updates - Relationships & Scopes

- [ ] 2.1 Update `CoaAccount` model: add `fiscal_year_id` to fillable array
- [ ] 2.2 Update `CoaAccount` model: add `fiscalYear()` BelongsTo relationship
- [ ] 2.3 Update `CoaAccount` model: add `scopeForFiscalYear()` query scope
- [ ] 2.4 Update `Program` model: update `fiscalYear()` relationship to use `fiscal_year_id` FK
- [ ] 2.5 Update `Program` model: change `fiscal_year_id` in fillable (remove `fiscal_year` integer)
- [ ] 2.6 Create `BudgetCommitment` model with relationships and status constants
- [ ] 2.7 Update `FiscalYear` model: add `coaAccounts()` HasMany relationship
- [ ] 2.8 Update `FiscalYear` model: add `budgetCommitments()` HasMany relationship
- [ ] 2.9 Update `FiscalYear` model: add `getTotalCommittedAttribute()` accessor

## 3. Factory Updates - Test Data Generation

- [ ] 3.1 Update `CoaAccountFactory`: add `fiscal_year_id` to definition
- [ ] 3.2 Update `CoaAccountFactory`: create `forFiscalYear($fyId)` state method
- [ ] 3.3 Update `ProgramFactory`: change `fiscal_year` integer to `fiscal_year_id`
- [ ] 3.4 Update `ProgramFactory`: create `forFiscalYear($fyId)` state method
- [ ] 3.5 Create `BudgetCommitmentFactory` with status states (pending, committed, released)
- [ ] 3.6 Update `FiscalYearFactory`: add relationships seeding (no auto-seed of +1 year)

## 4. Seeder Updates - Clean Slate Approach

- [ ] 4.1 Update `FiscalYearSeeder`: remove auto-creation of current-1, current+1 years
- [ ] 4.2 Update `FiscalYearSeeder`: only create current year if none exists
- [ ] 4.3 Update `SiteCoaSeeder`: require fiscal_year_id parameter, create accounts per FY
- [ ] 4.4 Update `DatabaseSeeder`: ensure fiscal year seeded before COA seeder runs
- [ ] 4.5 Create data migration seeder: assign existing COA to current fiscal year (one-time)

## 5. Service Layer - COA Copying & Budget Calculations

- [ ] 5.1 Create method: `FiscalYearService::copyCoaStructure($sourceFY, $targetFY)`
- [ ] 5.2 Implement COA copying logic: copy structure, set is_active=false for new accounts
- [ ] 5.3 Create method: `FiscalYearService::calculateBudgetMetrics($fiscalYear)`
- [ ] 5.4 Implement metrics calculation: allocated, committed, realized, available
- [ ] 5.5 Calculate utilization rate: (committed / allocated) \* 100
- [ ] 5.6 Update `FiscalYearService::validate()`: check COA count before closing
- [ ] 5.7 Update `FiscalYearService::close()`: prevent closing if active commitments exist

## 6. Request Validation - Fiscal Year Scoping

- [ ] 6.1 Update `StoreProgramRequest`: validate `fiscal_year_id` exists (not integer validation)
- [ ] 6.2 Update `StoreProgramRequest`: check fiscal year is not closed
- [ ] 6.3 Create `StoreCoaAccountRequest`: add `fiscal_year_id` validation (required, exists)
- [ ] 6.4 Create `UpdateCoaAccountRequest`: prevent changing `fiscal_year_id` after creation
- [ ] 6.5 Create `StoreBudgetCommitmentRequest`: validate fiscal_year_id, program_id, amount
- [ ] 6.6 Create `StoreBudgetCommitment Request`: check available balance before committing

## 7. Controller Updates - Fiscal Year Context

- [ ] 7.1 Update `CoaAccountController@index`: add fiscal year filter parameter
- [ ] 7.2 Update `CoaAccountController@index`: scope accounts by selected fiscal year
- [ ] 7.3 Update `CoaAccountController@store`: validate and save `fiscal_year_id`
- [ ] 7.4 Update `FiscalYearController@show`: load budget metrics via service
- [ ] 7.5 Create `FiscalYearController@copyCoaAction`: handle COA copying request
- [ ] 7.6 Create `FiscalYearController@budgetMetrics`: API endpoint for metrics
- [ ] 7.7 Update `FiscalYearController@destroy`: check COA count before deletion
- [ ] 7.8 Create `BudgetCommitmentController`: CRUD operations for commitments

## 8. Permissions & Policies

- [ ] 8.1 Add permission: `fiscal-year.manage` (create, edit, delete FY)
- [ ] 8.2 Add permission: `fiscal-year.close` (close fiscal year)
- [ ] 8.3 Add permission: `fiscal-year.reopen` (reopen closed fiscal year)
- [ ] 8.4 Add permission: `budget-commitment.manage` (create/release commitments)
- [ ] 8.5 Add permission: `program.override-closed-fy` (create programs in closed FY)
- [ ] 8.6 Update `RolePermissionSeeder`: assign permissions to Manager, AVP, Finance roles
- [ ] 8.7 Update or create `FiscalYearPolicy`: viewAny, create, update, delete, close, reopen
- [ ] 8.8 Create `BudgetCommitmentPolicy`: create, update, delete methods

## 9. Frontend - Fiscal Year Pages

- [ ] 9.1 Update `resources/js/pages/admin/fiscal-years/index.tsx`: add COA count column
- [ ] 9.2 Update `resources/js/pages/admin/fiscal-years/index.tsx`: disable delete if COA exists
- [ ] 9.3 Update `resources/js/pages/admin/fiscal-years/show.tsx`: add budget metrics cards
- [ ] 9.4 Create budget metrics component: `BudgetMetricsGrid` (4 cards: Allocated, Committed, Realized, Available)
- [ ] 9.5 Create budget progress component: `BudgetProgressBar` (visual breakdown of 3 states)
- [ ] 9.6 Create dialog: `CopyCoaDialog` (select source FY,confirm copy)
- [ ] 9.7 Add "Copy COA from Previous Year" button to fiscal year show page
- [ ] 9.8 Update fiscal year create/edit forms: show fiscal year period information

## 10. Frontend - COA Pages with Fiscal Year Context

- [ ] 10.1 Update `resources/js/pages/coa/index.tsx`: add fiscal year selector dropdown
- [ ] 10.2 Update COA index: filter accounts by selected `fiscal_year_id`
- [ ] 10.3 Update COA create form: add fiscal year selector (required field)
- [ ] 10.4 Update COA create form: disable fiscal year selector after account has transactions
- [ ] 10.5 Update COA table columns: add "Fiscal Year" column showing FY year
- [ ] 10.6 Add visual indicator: badge showing if viewing current/past/future FY
- [ ] 10.7 Create COA empty state: "No accounts for this fiscal year. Copy from previous?"

## 11. Frontend - Program Pages

- [ ] 11.1 Update program create form: use fiscal_year_id dropdown (not integer input)
- [ ] 11.2 Update program create form: show fiscal year start/end dates when selected
- [ ] 11.3 Update program edit form: display fiscal year as read-only (cannot change after creation)
- [ ] 11.4 Update program detail page: show fiscal year period and status (open/closed)
- [ ] 11.5 Add validation message: "Cannot create program for closed fiscal year" with override option
- [ ] 11.6 Update program list filters: filter by fiscal year dropdown

## 12. Frontend - Budget Commitment UI (Simplified)

- [ ] 12.1 Create commitment creation form: program, COA account, amount, description
- [ ] 12.2 Add real-time available balance check: show remaining budget before commitment
- [ ] 12.3 Create commitments list table: program, account, amount, status, date
- [ ] 12.4 Add status badges: PENDING (yellow), COMMITTED (green), RELEASED (gray)
- [ ] 12.5 Add release commitment action: change status to RELEASED
- [ ] 12.6 Display commitments on fiscal year show page: list of active commitments
- [ ] 12.7 Display commitments on program show page: commitments for this program

## 13. Historical Reporting - Cross-Year Comparison

- [ ] 13.1 Create `HistoricalReportController`: index and export methods
- [ ] 13.2 Implement historical data query: compare budget metrics across multiple FYs
- [ ] 13.3 Create `resources/js/pages/reports/historical.tsx`: multi-year comparison page
- [ ] 13.4 Create comparison table: columns for each FY,rows for metrics (Allocated, Committed, Realized)
- [ ] 13.5 Add trend chart: line chart showing budget utilization over fiscal years
- [ ] 13.6 Add COA structure comparison: table showing account additions/removals per year
- [ ] 13.7 Add Excel export functionality: download historical data as spreadsheet
- [ ] 13.8 Add permission middleware: `reports.historical` on historical routes

## 14. Year-End Closing - Updated Logic

- [ ] 14.1 Update closing validation: check for active (COMMITTED status) budget commitments
- [ ] 14.2 Update closing validation: warn if commitments exist that haven't been realized
- [ ] 14.3 Implement closing action: "Release uncommitted funds" (change COMMITTED to RELEASED)
- [ ] 14.4 Update closing action: "Archive completed programs" (mark status as archived)
- [ ] 14.5 Update closing action: "Block new programs" (check is_closed in validation)
- [ ] 14.6 Update closing dialog UI: show count of active commitments before closing
- [ ] 14.7 Add audit log entry: record closing options and commitment handling

## 15. Testing - Schema & Data Integrity

- [ ] 15.1 Test migration: verify fiscal_year_id column added to coa_accounts
- [ ] 15.2 Test migration: verify existing COA assigned to current fiscal year
- [ ] 15.3 Test migration: verify programs.fiscal_year converted to FK correctly
- [ ] 15.4 Test unique constraint: allow same account_code across different fiscal years
- [ ] 15.5 Test FK constraint: cannot delete fiscal year with associated COA accounts
- [ ] 15.6 Test FK constraint: cannot delete fiscal year with associated programs
- [ ] 15.7 Test rollback: verify down() migrations restore original schema

## 16. Testing - Business Logic

- [ ] 16.1 Test COA copying: verify all accounts copied with correct fiscal_year_id
- [ ] 16.2 Test COA copying: verify copied accounts have is_active=false
- [ ] 16.3 Test budget metrics: verify Available = Allocated - Committed calculation
- [ ] 16.4 Test commitment lifecycle: PENDING → COMMITTED → RELEASED state transitions
- [ ] 16.5 Test commitment deletion prevention: cannot delete COMMITTED commitments
- [ ] 16.6 Test over-commitment validation: prevent committing more than available
- [ ] 16.7 Test fiscal year deletion: verify error when COA or programs exist
- [ ] 16.8 Test fiscal year scoping: COA queries filtered by fiscal_year_id
- [ ] 16.9 Test program creation: validate fiscal_year_id exists and is not closed

## 17. Testing - UI & Integration

- [ ] 17.1 Test fiscal year selector: switching FY filters COA list correctly
- [ ] 17.2 Test COA creation: requires fiscal year selection
- [ ] 17.3 Test "Copy from Previous Year": creates new accounts for target FY
- [ ] 17.4 Test budget metrics display: shows 4 cards with correct values
- [ ] 17.5 Test commitment creation: reduces available balance immediately
- [ ] 17.6 Test commitment release: restores available balance
- [ ] 17.7 Test historical report: displays metrics for multiple fiscal years
- [ ] 17.8 Test closing workflow: prevents closing with active commitments
- [ ] 17.9 Test program form: cannot select closed fiscal year without permission

## 18. Documentation

- [ ] 18.1 Update ERD diagram: show fiscal_year_id on coa_accounts and programs
- [ ] 18.2 Document budget flow: Allocated → Committed → Realized with examples
- [ ] 18.3 Document COA copying workflow: when to use, how it works
- [ ] 18.4 Document migration guide: steps for existing deployments
- [ ] 18.5 Create admin user guide: how to create new fiscal year and copy COA
- [ ] 18.6 Document breaking changes: what changed, why, migration path
- [ ] 18.7 Update API documentation: new endpoints for COA copying and budget metrics

## 19. Code Cleanup & Formatting

- [ ] 19.1 Run `vendor/bin/pint --dirty` to format PHP code
- [ ] 19.2 Run `npm run lint` to check frontend code
- [ ] 19.3 Remove unused code: old fiscal_year integer handling logic
- [ ] 19.4 Update comments: clarify fiscal year container model in code comments
- [ ] 19.5 Check for TODO/FIXME comments related to old architecture

## 20. Deployment Preparation

- [ ] 20.1 Create deployment checklist: backup database, run migrations, verify
- [ ] 20.2 Test migrations on staging environment with production-like data
- [ ] 20.3 Create rollback plan: document how to revert changes if needed
- [ ] 20.4 Update .env.example: add any new configuration variables
- [ ] 20.5 Generate fresh database with new structure: test seeder flow
- [ ] 20.6 Performance test: check query performance with fiscal year scoping
- [ ] 20.7 Load test: verify performance with multiple fiscal years and large COA

## Summary

**New Architecture:**

- **COA Per Fiscal Year**: Each FY has its own complete chart of accounts
- **Strong FK Relationships**: Programs and COA use proper foreign keys
- **3-State Budget Tracking**: Allocated → Committed → Realized
- **COA Templating**: Copy structure from previous year for new FY
- **Clean Migration**: Existing data migrated to new structure

**Breaking Changes:**

1. `coa_accounts` table: added `fiscal_year_id` FK
2. `coa_accounts` unique constraint: now includes `fiscal_year_id`
3. `programs` table: `fiscal_year` integer replaced with `fiscal_year_id` FK
4. New table: `budget_commitments`

**Total Tasks**: 141 (broken down into 20 sections)
**Estimated Complexity**: High (architectural refactor with breaking changes)
**Migration Risk**: Medium (requires careful data migration testing)
