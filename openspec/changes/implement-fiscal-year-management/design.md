## Context

The application needs fiscal year to function as a **container/partition model** (PRD-aligned) rather than just a timeline marker. Current architecture has COA accounts at site-level with weak fiscal year binding via integer column, preventing true year-over-year isolation, COA evolution per fiscal year, and proper budget commitment tracking (Allocated → Committed → Realized).

**Current State:**

-Database foundation exists (`fiscal_years` table with basic schema)

- COA accounts are site-level (`site_id` only, **NO fiscal_year_id**)
- Programs weakly bound via `fiscal_year` integer column (not FK)
- Budget allocations correctly use `fiscal_year_id` FK
- Budget tracking is 2-state: Allocated → Realized (missing "Committed" state)
- `FiscalYearSeeder` creates multiple years automatically

**Required Changes:**

- Add `fiscal_year_id` FK to `coa_accounts` - each FY gets own COA copy
- Change `programs.fiscal_year` integer to `fiscal_year_id` FK
- Add `budget_commitments` table for 3-state budget tracking
- Implement COA templating to copy structure from previous FY
- Clean slate migration: existing COA assigned to current FY

**Constraints:**

- Breaking schema changes require careful migration
- Must preserve existing program/budget data during migration
- COA unique constraint must include fiscal_year_id (allows same code across years)
- Cannot delete fiscal years with associated COA accounts or programs
- All operations require fiscal year context

**Stakeholders:**

- System Administrators who manage fiscal year lifecycle
- Finance Operations who need commitment tracking
- Research Managers who need COA per fiscal year
- Developers who implement PRD requirements

## Goals / Non-Goals

**Goals:**

- Fiscal year as primary container - nothing exists without FY context
- COA per fiscal year with templating/copying mechanism
- 3-state budget tracking: Allocated → Committed → Realized
- Strong FK relationships for data integrity
- Year-end closing workflow with validation
- Historical reporting with cross-year COA comparison
- Clean migration path for existing data

**Non-Goals:**

- Full PR/PO procurement system (commitment tracking only, PRs deferred)
- Automatic fiscal year creation (manual via admin UI)
- Multi-year budget allocation for single program
- Transaction reassignment between fiscal years
- Integration with external accounting systems

## Decisions

### Decision 1: COA Per Fiscal Year (PRD Full Partition Model)

**Choice:** Add `fiscal_year_id` FK to `coa_accounts` table. Each fiscal year gets its own complete COA structure.

**Rationale:**

- Aligns with PRD requirement: "fiscal year as container, nothing exists without FY"
- Allows COA evolution year-over-year (add/remove accounts per year)
- True data isolation between fiscal years
- Historical integrity preserved (can't modify closed year's COA)
- Supports organizational changes in chart of accounts structure

**Implementation:**

```php
// Migration
Schema::table('coa_accounts', function (Blueprint $table) {
    $table
        ->foreignId('fiscal_year_id')
        ->after('site_id')
        ->constrained('fiscal_years')
        ->restrictOnDelete();

    // Update unique constraint
    $table->dropUnique(['site_id', 'account_code']);
    $table->unique(['site_id', 'fiscal_year_id', 'account_code']);
});
```

**Data Migration Strategy:**

```php
// Assign all existing COA accounts to current active FY
$currentFY = FiscalYear::where('is_closed', false)
    ->orderBy('year', 'desc')
    ->first();
CoaAccount::whereNull('fiscal_year_id')->update([
    'fiscal_year_id' => $currentFY->id,
]);
```

**Alternatives Considered:**

- **Template Model** (COA site-level, only budgets per FY): Rejected - doesn't meet PRD requirement for full isolation
- **Shared COA with versioning**: Rejected - too complex, doesn't provide true isolation

### Decision 2: COA Templating/Copying Mechanism

**Choice:** Implement "Copy COA from Previous Year" workflow in `FiscalYearService`.

**Rationale:**

- Creating complete COA manually for each new FY is tedious
- Most organizations use similar COA structure year-over-year
- Copying provides baseline that can be edited before year activation
- Maintains data isolation while reducing admin burden

**Implementation:**

```php
// In FiscalYearService
public function copyCoaStructure(FiscalYear $sourceFY, FiscalYear $targetFY): int
{
    $sourceAccounts = $sourceFY->coaAccounts()->get();
    $copiedCount = 0;

    foreach ($sourceAccounts as $account) {
        CoaAccount::create([
            'site_id' => $account->site_id,
            'fiscal_year_id' => $targetFY->id,
            'account_code' => $account->account_code,
            'account_name' => $account->account_name,
            'account_type' => $account->account_type,
            // ... copy structure, budgets set separately
            'is_active' => false, // New year accounts start inactive
        ]);
        $copiedCount++;
    }

    return $copiedCount;
}
```

**UI Flow:**

1. Admin creates new fiscal year (e.g., 2027)
2. System detects no COA accounts for FY 2027
3. Shows "Copy from Previous Year" button
4. Admin selects source FY (e.g., 2026) and confirms
5. System copies all COA structure to new FY
6. Admin can edit/add/remove accounts before activating FY

**Alternatives Considered:**

- **Auto-copy on FY creation**: Rejected - admin should explicitly choose
- **COA templates library**: Deferred to future, simple copy sufficient for now

### Decision 3: Programs Use FK Relationship (Not Integer)

**Choice:** Change `programs.fiscal_year` from integer to `fiscal_year_id` foreign key.

**Rationale:**

- Enforces referential integrity at database level
- Prevents orphaned programs if FY deleted
- Enables proper cascade rules (RESTRICT on delete)
- Follows Laravel/database best practices
- Simplifies relationship queries (no manual year matching)

**Migration Strategy:**

```php
// Add new FK column
Schema::table('programs', function (Blueprint $table) {
    $table->foreignId('fiscal_year_id')->nullable()->constrained();
});

// Migrate data: match year integers to fiscal_years table
DB::statement('
    UPDATE programs p
    SET fiscal_year_id = (SELECT id FROM fiscal_years WHERE year = p.fiscal_year)
');

// Drop old column, make new one required
Schema::table('programs', function (Blueprint $table) {
    $table->dropColumn('fiscal_year');
    $table->foreignId('fiscal_year_id')->nullable(false)->change();
});
```

**Model Update:**

```php
// BEFORE
public function fiscalYear(): BelongsTo {
    return $this->belongsTo(FiscalYear::class, 'fiscal_year', 'year');
}

// AFTER
public function fiscalYear(): BelongsTo {
    return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
}
```

**Alternatives Considered:**

- **Keep integer, add index**: Rejected - no integrity enforcement
- **Keep both columns**: Rejected - data duplication, maintenance burden

### Decision 4: 3-State Budget Tracking (Commitment Model)

**Choice:** Add `budget_commitments` table to track Reserved/Committed funds separately from Realized amounts.

**Rationale:**

- **PRD Requirement**: Track Allocated → Committed → Realized flow
- **Business Need**: Know "what's promised but not yet spent" (approved PRs, reserved funds)
- **Available Balance**: Should be `Allocated - Committed` (not `Allocated - Realized`) to prevent over-committing
- **Future-Proof**: Foundation for PR/PO system without building full procurement now

**Schema:**

```php
Schema::create('budget_commitments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fiscal_year_id')->constrained()->restrictOnDelete();
    $table->foreignId('program_id')->constrained()->restrictOnDelete();
    $table->foreignId('coa_account_id')->constrained()->restrictOnDelete();
    $table->decimal('amount', 15, 2);
    $table->string('description');
    $table->string('status'); // PENDING, COMMITTED, RELEASED
    $table->date('commitment_date');
    $table->foreignId('committed_by')->nullable()->constrained('users');
    $table->timestamps();
    $table->softDeletes();
});
```

**Status Lifecycle:**

- `PENDING`: Commitment created, awaiting approval
- `COMMITTED`: Approved, funds reserved (counts toward Available calculation)
- `RELEASED`: Cancelled or moved to realized (freed up for other use)

**Budget Calculation:**

```php
// In FiscalYearService
public function calculateBudgetMetrics(FiscalYear $fy): array {
    $allocated = $fy->budgetAllocations()->sum('budget_amount');
    $committed = $fy->budgetCommitments()->where('status', 'COMMITTED')->sum('amount');
    $realized = // sum of actual transactions

    return [
        'allocated' => $allocated,
        'committed' => $committed,
        'realized' => $realized,
        'available' => $allocated - $committed, // KEY: Not Allocated - Realized
        'utilization_rate' => ($committed / $allocated) * 100,
    ];
}
```

**Alternatives Considered:**

- **Wait for full PR system**: Rejected - need commitment tracking now
- **Use "pending" flag on allocations**: Rejected - doesn't track multiple commitments per account
- **Spreadsheet tracking**: Rejected - not integrated, no validation

### Decision 5: Clean Slate Migration (Not Backward Compatible)

**Choice:** Implement breaking changes with clean migration. Existing data migrated to new structure, but old schema not preserved.

**Rationale:**

- **User Decision**: "Clean slate" migration strategy chosen
- Trying to maintain backward compatibility would double complexity
- Current deployment likely doesn't have production data yet
- Clear migration path easier to reason about and test
- Future is container model, not transitional hybrid

**Migration Path:**

1. Identify current active fiscal year
2. Assign all existing `coa_accounts` to that FY
3. Migrate `programs.fiscal_year` integers to FK via year matching
4. Create `budget_commitments` table (empty, ready for use)
5. Update unique constraints and indexes
6. Drop old columns after data migration

**Rollback Plan:**

- Migrations have `down()` methods to reverse changes
- If issues found, can rollback database and fix before re-migrating
- Keep backup of pre-migration state

**Alternatives Considered:**

- **Dual-write period** (support both old and new): Rejected - too complex
- **Gradual migration** (feature flags): Rejected - clean break simpler

### Decision 6: Fiscal Year Seeder - Single Year Only

**Choice:** Update `FiscalYearSeeder` to create only current year if none exists. Remove auto-creation of past/future years.

**Rationale:**

- Per implementation plan: "Clean slate - future years created via UI"
- Admin should explicitly create fiscal years as needed
- Prevents assumption about organizational fiscal year structure
- Each new FY requires COA copying decision (manual trigger)

**Implementation:**

```php
// In FiscalYearSeeder
public function run(): void {
    if (FiscalYear::count() > 0) {
        return; // Only seed if empty
    }

    $currentYear = (int) date('Y');
    FiscalYear::create([
        'year' => $currentYear,
        'start_date' => "{$currentYear}-01-01",
        'end_date' => "{$currentYear}-12-31",
        'is_closed' => false,
    ]);
}
```

**Future Years Workflow:**

1. Admin goes to `/admin/fiscal-years`
2. Clicks "Create New Fiscal Year"
3. Enters year, start/end dates
4. Clicks "Copy COA from Previous Year" (optional)
5. Reviews/edits COA structure
6. Activates fiscal year

**Alternatives Considered:**

- **Auto-create next year at year-end**: Rejected - admin might want Feb-to-Feb FY
- **Pre-seed 3 years**: Rejected per implementation plan

### Decision 7: Deletion Prevention with Usage Checks

**Choice:** Prevent deletion of fiscal years that have associated COA accounts or programs. Soft delete also blocked.

**Rationale:**

- Deleting FY with COA would orphan budget allocations
- Deleting FY with programs would break ledger scoping
- Better to mark as closed and preserve historical record
- If deletion truly needed, requires manual cleanup first (admin knows impact)

**Implementation:**

```php
// In FiscalYearController
public function destroy(FiscalYear $fiscalYear) {
    $coaCount = $fiscalYear->coaAccounts()->count();
    $programCount = $fiscalYear->programs()->count();

    if ($coaCount > 0 || $programCount > 0) {
        return back()->with('error',
            "Cannot delete FY {$fiscalYear->year}. " .
            "Has {$coaCount} COA accounts and {$programCount} programs."
        );
    }

    $fiscalYear->delete();
    return redirect()->route('fiscal-years.index')
        ->with('success', 'Fiscal year deleted.');
}
```

**UI Indication:**

- Delete button disabled if FY has data
- Tooltip explains: "Cannot delete - has X accounts and Y programs"
- For empty FY (e.g., accidentally created), deletion allowed

**Alternatives Considered:**

- **Cascade delete**: Rejected - too dangerous, data loss
- **Orphan data**: Rejected - violates container model
- **Archive instead of delete**: Considered, but closed status serves this purpose

## Data Model Diagrams

### Entity Relationship (Refined)

```
┌─────────────────┐
│  FiscalYear     │
│  id (PK)        │──┐
│  year           │  │
│  start_date     │  │
│  end_date       │  │
│  is_closed      │  │
└─────────────────┘  │
                     │
         ┌───────────┴───────────┬───────────────┐
         │                       │               │
         ▼                       ▼               ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  CoaAccount      │  │  Program         │  │  BudgetCommitment│
│  id (PK)         │  │  id (PK)         │  │  id (PK)         │
│  site_id (FK)    │  │  site_id (FK)    │  │  fiscal_year_id  │
│  fiscal_year_id ━╋  │  fiscal_year_id ━╋  │  program_id (FK) │
│  account_code    │  │  program_code    │  │  coa_account_id  │
│  account_name    │  │  status          │  │  amount          │
│  ...             │  │  ...             │  │  status          │
└──────────────────┘  └──────────────────┘  └──────────────────┘
         │
         │
         ▼
┌──────────────────────┐
│  CoaBudgetAllocation │
│  id (PK)             │
│  coa_account_id (FK)━│
│  fiscal_year_id (FK)━│
│  budget_amount       │
└──────────────────────┘
```

**Key Changes from Current:**

- **CoaAccount** now has `fiscal_year_id` FK (new!)
- **Program** uses `fiscal_year_id` FK (changed from integer)
- **BudgetCommitment** table added (new!)
- All entities bound to FiscalYear via proper FKs

### Budget Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Fiscal Year Budget                        │
│                 (From budget_allocations)                    │
│                                                               │
│  Total Allocated: $15,000,000                                │
└───────────┬─────────────────────────────────────────────────┘
            │
            ├──► COMMITTED (budget_commitments where status=COMMITTED)
            │    $8,000,000
            │     └─► Available = Allocated - Committed
            │         $7,000,000  ← Can still commit new PRs
            │
            └──► REALIZED (actual transactions/payments)
                 $5,000,000
                  └─► Outstanding Obligations = Committed - Realized
                      $3,000,000  ← Approved but not yet paid
```

**3-State Model:**

1. **Allocated**: Total budget assigned (from allocations table)
2. **Committed**: Funds reserved (approved PRs, commitments table)
3. **Realized**: Funds actually spent (payment/transaction tables)

**Key Metric:**

- `Available Balance = Allocated - Committed`
- NOT `Allocated - Realized` (old 2-state model)

## Technical Specifications

### Database Schema Changes

#### Migration 1: Add fiscal_year_id to coa_accounts

```sql
ALTER TABLE coa_accounts
ADD COLUMN fiscal_year_id BIGINT UNSIGNED NOT NULL;

ALTER TABLE coa_accounts
ADD CONSTRAINT fk_coa_fiscal_year
FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id) ON DELETE RESTRICT;

-- Update unique constraint
ALTER TABLE coa_accounts DROP CONSTRAINT coa_accounts_site_id_account_code_unique;
ALTER TABLE coa_accounts ADD UNIQUE (site_id, fiscal_year_id, account_code);

CREATE INDEX idx_coa_fiscal_year ON coa_accounts(fiscal_year_id);
```

#### Migration 2: Change programs fiscal_year to FK

```sql
ALTER TABLE programs
ADD COLUMN fiscal_year_id BIGINT UNSIGNED;

UPDATE programs p
SET fiscal_year_id = (SELECT id FROM fiscal_years WHERE year = p.fiscal_year);

ALTER TABLE programs
ADD CONSTRAINT fk_program_fiscal_year
FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id) ON DELETE RESTRICT;

ALTER TABLE programs DROP COLUMN fiscal_year;
ALTER TABLE programs MODIFY fiscal_year_id BIGINT UNSIGNED NOT NULL;
```

#### Migration 3: Create budget_commitments table

**File**: See implementation plan for full schema

### API Endpoints (New/Modified)

```
POST   /api/fiscal-years/{id}/copy-coa
  - Request: {source_fiscal_year_id}
  - Response: {copied_count, target_fiscal_year}

GET    /api/fiscal-years/{id}/budget-metrics
  - Response: {allocated, committed, realized, available, utilization_rate}

POST   /api/budget-commitments
  - Request: {fiscal_year_id, program_id, coa_account_id, amount, description}
  - Response: {commitment}

DELETE /api/budget-commitments/{id}
  - Changes status to RELEASED, not hard delete
```

### Frontend Components

#### Budget Metrics Dashboard

```tsx
<div className="grid grid-cols-4 gap-4">
  <MetricCard title="Allocated" value={allocated} color="blue" />
  <MetricCard title="Committed" value={committed} color="yellow" />
  <MetricCard title="Realized" value={realized} color="green" />
  <MetricCard title="Available" value={available} color="gray" />
</div>

<ProgressBar
  allocated={allocated}
  sections={[
    {label: 'Realized', value: realized, color: 'green'},
    {label: 'Committed (Pending)', value: committed - realized, color: 'yellow'},
    {label: 'Available', value: allocated - committed, color: 'gray'},
  ]}
/>
```

#### Fiscal Year Selector (COA Pages)

```tsx
<Select
    value={selectedFiscalYearId}
    onChange={handleFiscalYearChange}
    options={fiscalYears.map((fy) => ({
        value: fy.id,
        label: `FY ${fy.year} (${fy.is_closed ? 'Closed' : 'Active'})`,
    }))}
/>
```

## Testing Strategy

- **Unit Tests**: Budget calculation logic, commitment status transitions
- **Feature Tests**: COA copying, FY creation with validation, deletion prevention
- **Migration Tests**: Verify data integrity after schema changes
- **Integration Tests**: Full COA-to-Program-to-Budget flow within FY scope

## Deployment Checklist

1. Backup production database
2. Run migrations in order (fiscal_year_id → programs FK → commitments table)
3. Verify existing data migrated correctly
4. Run test suite
5. Check for orphaned records (should be none)
6. Update factories for new schema
