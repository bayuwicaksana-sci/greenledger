## Why

The application currently has a `FiscalYear` model with database foundation and basic filtering, but lacks comprehensive management capabilities required by the PRD (LIFE-015 through LIFE-019). System administrators cannot create/manage fiscal years through the UI, there is no year-end closing workflow, and historical reporting across multiple fiscal years is limited. This prevents proper financial period management and compliance with organizational fiscal year policies that may differ from calendar years (e.g., June-to-June fiscal years).

## What Changes

- **New**: Admin UI for fiscal year CRUD operations with flexible date ranges
- **New**: Year-end closing workflow with user-selectable actions (archive programs, generate reports, block new transactions)
- **New**: Historical reporting page with year-over-year comparison across fiscal years
- **New**: Permission-based access control for fiscal year operations (close, reopen, manage)
- **Enhanced**: Existing dashboards and COA pages with fiscal year selector and multi-year trend charts
- **Enhanced**: Program creation/editing to validate against closed fiscal years and display FY period information
- **Enhanced**: Budget allocation tracking scoped by fiscal year (already implemented in `coa_budget_allocations` table)

## Capabilities

### New Capabilities

- `fiscal-year-management`: Complete CRUD operations for fiscal years including creation with custom start/end dates, editing, soft deletion prevention when programs exist, and listing with usage statistics
- `year-end-closing`: Year-end closing process with pre-close validation (active programs, pending PRs, unsettled transactions), user-selectable closing actions (archive completed programs, block new transactions, generate reports), confirmation workflow, and audit logging
- `historical-reporting`: Multi-year financial analysis including year-over-year comparison tables for revenue/expenses/net income, trend charts across fiscal years, and Excel export for historical data

### Modified Capabilities

- `program-core`: Add fiscal year binding validation (prevent program creation for closed FY unless override permission), display FY period on program pages, and filter programs by FY open/closed status
- `program-budgeting`: Clarify that budget tracking follows program's fiscal year regardless of transaction date (programs are "stamped" with their FY at creation and all ledgers stay in that FY)

## Impact

**Backend**:
- New controller: `FiscalYearController` (CRUD + close/reopen actions)
- New service: `FiscalYearService` (closing logic, validation, report generation)
- New request classes: `StoreFiscalYearRequest`, `CloseFiscalYearRequest`
- Updated controllers: `ProgramController` (FY validation), `CoaAccountController` (historical data queries)
- New permissions: `fiscal-year.manage`, `fiscal-year.close`, `fiscal-year.reopen`, `fiscal-year.view`, `reports.historical`

**Frontend**:
- New admin pages: `/admin/fiscal-years` (index, create, edit, show with close dialog)
- New reports page: `/reports/historical` (year-over-year comparison)
- New component: `FiscalYearSelector` (reusable dropdown with open/closed badges)
- Enhanced pages: Program create/edit forms (FY validation messages), main dashboard (FY selector), COA index (multi-year trend charts)

**Database**:
- No schema changes needed (foundation already exists in `fiscal_years` table)
- Optional: Add `blocking_rules` JSONB column to store per-FY rules (block_new_programs, block_new_transactions)

**Testing**:
- Existing: `CoaFiscalYearScopingTest` validates budget/actual scoping by FY
- New: `FiscalYearManagementTest`, `YearEndClosingTest`, `HistoricalReportingTest`

**Dependencies**:
- Leverages existing `FiscalYear` model, relationships, and scopes
- Integrates with existing approval workflows and permission system
- Uses established Inertia.js patterns for admin UI
