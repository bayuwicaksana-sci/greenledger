## Context

The application has a foundational `FiscalYear` model with database schema (`fiscal_years` table with `id`, `year`, `start_date`, `end_date`, `is_closed`, timestamps, soft deletes) and relationships to `programs` and `coa_budget_allocations`. Basic fiscal year filtering exists in the COA controller and comprehensive tests validate year-scoped queries (`CoaFiscalYearScopingTest`). However, there is no UI for managing fiscal years, no year-end closing workflow, and limited multi-year reporting capabilities.

**Current State:**

- Database foundation complete with foreign key constraints
- `FiscalYearSeeder` creates current-1, current, and current+1 years
- Programs are bound to fiscal years at creation via `programs.fiscal_year` column
- Budget allocations tracked per FY via `coa_budget_allocations.fiscal_year_id`
- All ledger entries follow program's fiscal year (not transaction date)

**Constraints:**

- Must support flexible fiscal year periods (not just calendar years)
- Year code derived from `start_date` year (user clarification)
- Closing actions must be user-selectable, not hardcoded
- Cannot delete fiscal years with associated programs
- All admin operations require permission checks

**Stakeholders:**

- System Administrators (Manager, AVP) who close fiscal years
- Finance Operation who generate historical reports
- Research Associates/Officers who need FY context when creating programs

## Goals / Non-Goals

**Goals:**

- Enable admin UI for fiscal year lifecycle management (create, edit, close, reopen)
- Implement flexible year-end closing with user-selected actions and validation
- Provide multi-year financial analysis and historical reporting
- Enforce fiscal year rules on program creation (block closed FYs unless override)
- Maintain complete audit trail for all fiscal year operations

**Non-Goals:**

- Automatic fiscal year creation (manual via admin UI only)
- Transaction reassignment to different fiscal years (deferred to Phase 5)
- Multi-year budget allocation UI for programs (simplified: programs bound to one FY)
- Integration with external accounting systems
- Scheduled year-end closing (admin triggers manually)

## Decisions

### Decision 1: Service Layer for Year-End Closing Logic

**Choice:** Create `FiscalYearService` to encapsulate closing logic rather than putting it in the controller.

**Rationale:**

- Year-end closing involves complex orchestration (validation, conditional actions, report generation, audit logging)
- Service layer keeps controller thin and makes logic testable in isolation
- Allows reuse if closing is triggered from multiple entry points (UI, CLI command)
- Follows existing pattern in the codebase (e.g., approval workflows use service layer)

**Alternatives Considered:**

- **Controller-based logic**: Rejected because it mixes HTTP concerns with business logic
- **Event-driven**: Rejected as overkill for this use case; no need for async processing

### Decision 2: Checkbox-Based Closing Actions (No Presets)

**Choice:** Store closing options as checkbox selections in request, execute selected actions dynamically.

**Rationale:**

- User requirements specify "user discretion" for what happens during close
- Different organizations may have different year-end processes
- Flexible approach allows admin to close "cleanly" (archive everything) or "soft close" (just mark as closed)
- Avoids hardcoding assumptions about year-end procedures

**Implementation:**

```php
// CloseFiscalYearRequest
public function rules(): array {
    return [
        'notes' => 'nullable|string|max:1000',
        'options' => 'required|array',
        'options.archive_completed_programs' => 'boolean',
        'options.block_new_programs' => 'boolean',
        'options.block_new_transactions' => 'boolean',
        'options.generate_report' => 'boolean',
        'options.send_notifications' => 'boolean',
    ];
}
```

**Alternatives Considered:**

- **Predefined close modes** (e.g., "soft", "hard"): Rejected as too rigid
- **Post-close actions**: Rejected because admin should decide upfront

### Decision 3: Validation Rules Enforced at Multiple Layers

**Choice:** Enforce fiscal year rules at request validation, policy, and database constraint levels.

**Rationale:**

- Defense in depth: multiple layers prevent invalid state
- Request validation provides user-friendly error messages
- Policies enforce authorization logic (e.g., `fiscal-year.close` permission)
- Database constraints (foreign keys, unique year) prevent data corruption

**Example:**

```php
// In StoreProgramRequest
public function rules(): array {
    return [
        'fiscal_year' => [
            'required',
            'integer',
            'exists:fiscal_years,year',
            function ($attribute, $value, $fail) {
                $fy = FiscalYear::where('year', $value)->first();
                if ($fy && $fy->is_closed && !auth()->user()->can('program.override-closed-fy')) {
                    $fail("Fiscal year {$value} is closed. Cannot create programs.");
                }
            },
        ],
    ];
}
```

**Alternatives Considered:**

- **Middleware-only validation**: Rejected because request classes provide better error messages
- **Database-only constraints**: Rejected because they don't provide user-friendly feedback

### Decision 4: Historical Reporting via Dedicated Page (Not Just Dashboard Enhancement)

**Choice:** Create `/reports/historical` page in addition to enhancing existing dashboards.

**Rationale:**

- User requirements specify "create separate historical reporting page"
- Dedicated page allows deeper analysis without cluttering main dashboard
- Dashboard enhancements (FY selector, trend charts) provide quick overview
- Historical page provides detailed year-over-year tables and Excel export

**Both Approaches:**

1. **Dashboard enhancement**: Add `FiscalYearSelector` component, show current FY data, display trend lines
2. **Dedicated page**: Multi-year comparison tables, drill-down by account/program, export functionality

**Alternatives Considered:**

- **Dashboard-only**: Rejected per user requirements
- **Historical page only**: Rejected because users need quick FY switching in daily workflow

### Decision 5: Soft Delete Prevention for Fiscal Years with Programs

**Choice:** Prevent deletion (even soft delete) of fiscal years that have associated programs. Show error with program count.

**Rationale:**

- Fiscal year is part of program's identity (referenced in ledger scoping)
- Soft deleting FY would break program display and reporting
- Better to mark as closed and keep historical record intact
- If truly needed, admin must manually reassign programs first

**Implementation:**

```php
// In FiscalYearController@destroy
public function destroy(FiscalYear $fiscalYear) {
    $programCount = $fiscalYear->programs()->count();
    if ($programCount > 0) {
        return redirect()->back()->with('error',
            "Cannot delete fiscal year {$fiscalYear->year}. It has {$programCount} associated programs."
        );
    }

    $fiscalYear->delete();
    return redirect()->route('admin.fiscal-years.index')
        ->with('success', 'Fiscal year deleted successfully.');
}
```

**Alternatives Considered:**

- **Cascade soft delete**: Rejected because programs shouldn't be deleted with FY
- **Orphan programs**: Rejected because programs must always have a fiscal year

### Decision 6: Year Code Auto-Calculated from Start Date

**Choice:** When creating fiscal year, `year` field is auto-calculated from `start_date->format('Y')` and displayed read-only.

**Rationale:**

- User clarification: "year to be used as code is the start date year"
- Prevents confusion and data entry errors
- Ensures consistency between year code and date range
- Simplifies validation (only need to validate date range, year follows)

**UI Flow:**

1. Admin enters start_date (e.g., 2026-06-01)
2. System auto-fills year = 2026 (read-only field)
3. Admin enters end_date (e.g., 2027-06-30)
4. Validation: end_date > start_date ✓

**Alternatives Considered:**

- **Manual year entry**: Rejected because prone to human error
- **Year derived from end_date**: Rejected per user clarification

## Risks / Trade-offs

### Risk 1: Admin Closes Fiscal Year with Active Programs

**Risk:** Admin closes FY2026 while 12 programs are still active. This could cause confusion or reporting issues.

**Mitigation:**

- Pre-close validation displays warning with active program count
- Provide "block new transactions" option to soft-lock active programs
- Allow closing anyway (user discretion) but require notes field for audit
- Docs/training emphasize completing programs before year-end

### Risk 2: Performance Impact of Multi-Year Reporting Queries

**Risk:** Historical reporting page queries multiple years of data. With 3+ years and thousands of transactions, queries could be slow.

**Mitigation:**

- Use eager loading and indexed queries (fiscal_year column already indexed via FK)
- Implement pagination for detailed drill-downs
- Cache year-over-year summary data (invalidate on transaction changes)
- Provide date range filter to limit query scope
- Monitor query performance and add composite indexes if needed

### Risk 3: Permission Confusion (Who Can Close?)

**Risk:** Multiple roles might expect access to year-end closing, leading to permission conflicts or audit issues.

**Mitigation:**

- Clear permission structure: `fiscal-year.close` (Manager, AVP), `fiscal-year.reopen` (emergency only, AVP)
- Document role assignments in seed data and RBAC guide
- UI shows permission requirements on buttons ("Requires: fiscal-year.close")
- Audit log captures all close/reopen actions with user ID

### Risk 4: Closed FY Blocking Legitimate Corrections

**Risk:** FY2026 is closed, but Finance discovers a data entry error in a 2026 program. They cannot correct it.

**Rationale (Trade-off Accepted):**

- Closed fiscal years should be immutable for audit compliance
- If corrections needed, admin can reopen FY, make changes, and re-close
- `fiscal-year.reopen` permission restricted to senior admins
- Alternative: Transaction reassignment feature (Phase 5, deferred)
- Audit log captures all reopen actions for compliance

### Risk 5: Seeder Creates Future Years Automatically

**Risk:** `FiscalYearSeeder` creates current+1 year with assumed dates (Jan-Dec). If org uses June-June, this is wrong.

**Mitigation:**

- Update seeder to create only current year if none exists
- Document that admins should manually create future years via UI
- Alternative: Make seeder configurable with fiscal year period setting
- For now: Accept that initial seed might need manual correction

## Migration Plan

**Phase 1: Backend Foundation (No UI)**

1. Create `FiscalYearController` with CRUD routes (admin middleware)
2. Create `FiscalYearService` with closing logic
3. Create request classes with validation
4. Add permissions to `RolePermissionSeeder`
5. Write tests: `FiscalYearManagementTest`, `YearEndClosingTest`
6. **Rollback:** Routes protected by admin middleware, no public-facing changes

**Phase 2: Admin UI**

1. Create `/admin/fiscal-years` pages (index, create, edit, show)
2. Implement close dialog with checkbox options
3. Add `FiscalYearSelector` component
4. **Rollback:** Delete new routes and pages, existing functionality unaffected

**Phase 3: Program Validation Enhancement**

1. Update `StoreProgramRequest` with closed FY validation
2. Add FY period display to program pages
3. Add FY filter to program index
4. **Rollback:** Remove validation rule, revert program pages

**Phase 4: Historical Reporting**

1. Create `/reports/historical` page with year-over-year table
2. Enhance dashboard with FY selector
3. Add trend charts to COA index
4. **Rollback:** Delete reports route, revert dashboard changes

**Phase 5: Documentation & Training**

1. Update admin guide with FY management procedures
2. Create video walkthrough of year-end closing process
3. Document permission requirements

**Deployment Strategy:**

- Deploy during low-traffic window (weekend)
- Run migrations (none needed, schema already exists)
- Seed permissions: `php artisan db:seed --class=RolePermissionSeeder`
- Notify admins of new functionality
- Monitor for errors in first week

**Rollback Strategy:**

- If critical issues found, disable admin routes via middleware
- Existing program/COA functionality unaffected (FY model already in use)
- Database unchanged, no data loss risk
- Re-enable after fixes deployed

## Open Questions

### Q1: Should closed fiscal years be visible in FiscalYearSelector?

**Context:** When users filter programs or COA by fiscal year, should closed years appear in dropdown?

**Options:**

- A) Show all years (open + closed), with badge indicating status
- B) Show open years by default, "Show Closed" checkbox to reveal
- C) Show last 3 years regardless of status (most common use case)

**Recommendation:** Option A (show all with badges) for transparency. Users may need to reference closed years frequently.

### Q2: What happens to budget allocations when FY closes?

**Context:** `coa_budget_allocations` table links to `fiscal_year_id`. When FY closes, should allocations be locked?

**Current Behavior:** No locks, allocations remain editable.

**Question:** Should `is_closed` prevent editing of budget allocations for that FY?

**Recommendation:** Yes, add validation in `CoaBudgetAllocationController` to check if linked FY is closed. Allow override with `fiscal-year.reopen` permission.

### Q3: PDF Year-End Report Format?

**Context:** Close dialog has "Generate year-end summary report" option. What should PDF contain?

**Recommendation:**

- Header: FY period, close date, closed by
- Summary: Total programs, total revenue, total expenses, net income
- Breakdown: Revenue by COA account, Expenses by COA account
- Program list: All programs with status (completed/active/cancelled)
- Budget utilization: Allocated vs. actual by site
- Use Laravel Dompdf library

### Q4: Notification Recipients for "Send Notifications" Option?

**Context:** Close dialog allows sending notifications. Who should receive them?

**Options:**

- A) All users with `fiscal-year.view` permission
- B) Managers and AVPs only
- C) PIs of active programs in the closed FY
- D) Configurable email list in system settings

**Recommendation:** Option C (PIs of active programs) + option B (Managers/AVPs). Use Laravel notifications with database + email channels.
