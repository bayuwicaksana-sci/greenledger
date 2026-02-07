## Why

The application requires fiscal year to function as a **primary organizational container** rather than just a timeline filter. Currently, COA accounts and programs exist at the site level with weak fiscal year binding, preventing true year-over-year isolation, evolution of COA structure per fiscal year, and proper budget commitment tracking (Allocated → Committed → Realized flow). This architectural limitation blocks implementation of the full PRD vision where fiscal year is the top-level grouping boundary and nothing can exist without fiscal year context.

## What Changes

- **Breaking**: Add `fiscal_year_id` foreign key to `coa_accounts` table - each fiscal year gets its own complete COA structure
- **Breaking**: Change `programs.fiscal_year` from integer to `fiscal_year_id` foreign key - enforce referential integrity
- **New**: `budget_commitments` table for tracking 3-state budget flow: Allocated → Committed → Realized
- **New**: COA templating/copying mechanism to create new fiscal year's COA from previous year
- **New**: Admin UI for fiscal year CRUD operations with budget metrics dashboard
- **New**: Year-end closing workflow with validation and user-selectable actions
- **New**: Historical reporting with cross-year COA comparison
- **Enhanced**: Budget tracking to show Available = Allocated - Committed (not Allocated - Realized)
- **Migration**: Clean slate approach - existing COA assigned to current fiscal year, new years use copying workflow

## Capabilities

### New Capabilities

- `fiscal-year-container-model`: Fiscal year as primary boundary - all data scoped to fiscal year via foreign keys, COA accounts bound to fiscal year, programs use proper FK relationship
- `coa-per-fiscal-year`: Each fiscal year has its own COA structure with templating/copying from previous year, allows COA evolution year-over-year, complete isolation of chart structure
- `commitment-tracking`: Three-state budget model (Allocated from budget_allocations, Committed from budget_commitments, Realized from actual transactions), Available balance calculated as Allocated - Committed
- `fiscal-year-management`: Complete CRUD operations with flexible date ranges, budget metrics dashboard (Allocated/Committed/Realized/Available), soft deletion prevention when programs/COA exist
- `year-end-closing`: Pre-close validation, user-selectable closing actions, confirmation workflow, audit logging
- `historical-reporting`: Multi-year financial analysis with year-over-year comparison, cross-year COA structure comparison

### Modified Capabilities

- `coa-management`: Now requires fiscal year context for all operations, COA creation scoped to fiscal year, unique constraint includes fiscal_year_id (allows same account code across years)
- `program-management`: Fiscal year binding via FK (not integer), validation prevents closed FY unless override, cannot delete FY with active programs
- `budget-tracking`: Available balance = Allocated - Committed (reserved funds), commitment lifecycle (PENDING → COMMITTED → RELEASED), realization tracking separate from commitment

## Impact

**Backend**:

- Breaking migration: add `fiscal_year_id` to `coa_accounts`, change `programs.fiscal_year` to `fiscal_year_id` FK
- New table: `budget_commitments` (fiscal_year_id, program_id, coa_account_id, amount, status, etc.)
- New model: `BudgetCommitment` with relationships and status constants
- Updated models: `CoaAccount` (fiscalYear relationship), `Program` (FK relationship), `FiscalYear` (new relationships)
- New service methods: `FiscalYearService::copyCoaStructure()`, `calculateBudgetMetrics()`
- Updated controllers: `CoaAccountController` (require FY context), `FiscalYearController` (budget metrics, COA copying)
- New permissions: `fiscal-year.manage`, `fiscal-year.close`, `reports.historical`, `program.override-closed-fy`

**Frontend**:

- New pages: `/admin/fiscal-years` (CRUD with metrics), `/reports/historical` (cross-year comparison)
- Enhanced pages: COA index (fiscal year selector, filter by FY), Program forms (FY validation)
- New components: Budget metrics cards (4-state display), COA copy workflow dialog
- Dashboard updates: Show Allocated/Committed/Realized/Available, utilization progress bar

**Database**:

- **Breaking**: `coa_accounts` unique constraint becomes `['site_id', 'fiscal_year_id', 'account_code']`
- **Breaking**: `programs.fiscal_year` column dropped, replaced with `fiscal_year_id` FK
- New table: `budget_commitments` with indexes on `fiscal_year_id`, `program_id`, `status`
- Migration strategy: Assign existing COA to current FY, migrate program fiscal_year integers to FK via year matching

**Testing**:

- Update: `CoaFiscalYearScopingTest` (test FK scoping)
- New: `BudgetCommitmentTest`, `CoaCopyingTest`, `FiscalYearContainerTest`
- Migration tests: Verify data integrity after migration

**Dependencies**:

- Requires clean database migration (existing deployments need migration plan)
- Future integration point for PR/PO system (commitment sources)
- COA factory updates to include `fiscal_year_id`
- Program factory updates to use `fiscal_year_id` FK
