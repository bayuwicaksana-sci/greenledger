# Chart of Accounts (COA) Implementation Guide

## Research Operations — Green Ledger

---

## Table of Contents

1. [Overview](#1-overview)
2. [Database Schema](#2-database-schema)
3. [Account Code & Hierarchy](#3-account-code--hierarchy)
4. [Standard Template](#4-standard-template)
5. [Creating Accounts](#5-creating-accounts)
6. [Importing Accounts (CSV)](#6-importing-accounts-csv)
7. [Editing & Locking Rules](#7-editing--locking-rules)
8. [Budget Control & Balance](#8-budget-control--balance)
9. [Deleting Accounts](#9-deleting-accounts)
10. [Permissions](#10-permissions)
11. [Routes Reference](#11-routes-reference)
12. [FAQs](#12-faqs)

---

## 1. Overview

The COA module provides site-isolated, hierarchical account management for Green Ledger. Each research station (site) maintains its own set of accounts. Account codes are plain numeric strings; site isolation is enforced by a unique constraint on `(site_id, account_code)` — the same code (e.g. `5211`) can exist independently in both Klaten and Magelang.

**What the system supports today:**

- Five account types: ASSET, LIABILITY, EQUITY, REVENUE, EXPENSE
- A standard agricultural template that seeds 14 accounts across all five types
- Manual bulk creation and CSV import restricted to **REVENUE and EXPENSE** only (Assets, Liabilities, and Equity can only enter the system via template application — future phases will lift this restriction per PRD COA-002)
- Per-account budget tracking with a Budget vs. Actual view on the index page
- A transaction-based lock that permanently freezes `account_code` and `account_type` once any payment split or revenue record references the account

---

## 2. Database Schema

Table: `coa_accounts`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint (PK) | Auto-increment |
| `site_id` | bigint (FK → sites) | Indexed. Determines which site owns this account |
| `account_code` | string | Indexed. Unique together with `site_id` |
| `account_name` | string | Required |
| `abbreviation` | string(10) | Nullable. Short label shown alongside the account name |
| `account_type` | string | Indexed. One of `ASSET`, `LIABILITY`, `EQUITY`, `REVENUE`, `EXPENSE` |
| `short_description` | text | Nullable |
| `parent_account_id` | bigint (FK → coa_accounts) | Nullable. Self-referential for hierarchy |
| `hierarchy_level` | integer | 1 = root, increments with each parent link |
| `is_active` | boolean | Default `false`. Inactive accounts cannot receive new transactions |
| `initial_budget` | decimal | Nullable. The budget allocation for this account |
| `budget_control` | boolean | Default `false`. When true, the account participates in Budget vs. Actual tracking |
| `first_transaction_at` | timestamp | Nullable. Set the first time a transaction references this account. Triggers the lock |
| `created_by` | bigint (FK → users) | Nullable |
| `updated_by` | bigint (FK → users) | Nullable |
| `created_at` / `updated_at` | timestamp | Laravel defaults |

**Indexes:** `site_id`, `account_code`, `account_type`, `is_active`, `budget_control`, plus a composite index on `(site_id, account_code)` (unique).

---

## 3. Account Code & Hierarchy

### Code format

Account codes are plain numeric strings. There is no site prefix embedded in the code. Site isolation is handled at the database level via the unique constraint on `(site_id, account_code)`.

Conventions by range:

| Range | Account Type |
|---|---|
| 1000–1999 | ASSET |
| 2000–2999 | LIABILITY |
| 3000–3999 | EQUITY |
| 4000–4999 | REVENUE |
| 5000–5999 | EXPENSE |

### Hierarchy levels and auto-code logic

The system infers hierarchy from the trailing-zero pattern of the parent's code. The `/coa/next-code` endpoint uses this to suggest the next available code:

| Parent code pattern | Parent level | Child step | Example children |
|---|---|---|---|
| `x000` (e.g. 5000) | Level 1 (root) | +100 | 5100, 5200, … |
| `xx00` (e.g. 5100) | Level 2 | +10 | 5110, 5120, … |
| `xxx0` or other | Level 3+ | +1 | 5111, 5112, … |
| No parent (root) | — | +1000 | 1000, 2000, … |

The `hierarchy_level` column is set automatically: root accounts get `1`, each child is `parent.hierarchy_level + 1`.

### Tree view

The Index page renders all accounts in a tree layout (`CoaTreeView` component) grouped by site, respecting parent-child relationships.

---

## 4. Standard Template

Defined in `config/coa_templates.php` under the key `standard_agricultural`. Contains 14 accounts:

| Code | Name | Type | Abbreviation | Parent |
|---|---|---|---|---|
| 1000 | Assets | ASSET | AST | — |
| 1100 | Current Assets | ASSET | CURR-AST | 1000 |
| 1110 | Cash and Equivalents | ASSET | CASH | 1100 |
| 2000 | Liabilities | LIABILITY | LIAB | — |
| 2100 | Current Liabilities | LIABILITY | CURR-LIAB | 2000 |
| 3000 | Equity | EQUITY | EQ | — |
| 3100 | Capital | EQUITY | CAP | 3000 |
| 4000 | Revenue | REVENUE | REV | — |
| 4100 | Operational Revenue | REVENUE | OP-REV | 4000 |
| 4110 | Harvest Revenue | REVENUE | HARV-REV | 4100 |
| 5000 | Expenses | EXPENSE | EXP | — |
| 5100 | Operational Expenses | EXPENSE | OP-EXP | 5000 |
| 5211 | Seeds Material | EXPENSE | MAT-SEED | 5100 |
| 5212 | Fertilizer Material | EXPENSE | MAT-FERT | 5100 |

### How template application works

1. User opens the **Templates** dialog on the COA index page and selects a site.
2. `CoaAccountTemplateService::checkConflicts()` compares every code in the template against accounts already in the database for that site, returning a conflict map.
3. If conflicts exist the user can choose to **skip existing** accounts (existing accounts are left untouched and still used as parents for new children).
4. Accounts are sorted by code (string sort guarantees parents before children) then created in order. `budget_control` is auto-set to `true` for every EXPENSE account.
5. `SiteCoaSeeder` runs this same flow with `skipExisting = true`, making it safe to re-run.

---

## 5. Creating Accounts

### Bulk create (UI)

The **Create COA Accounts** page (`config/coa/Create`) lets users add one or more accounts in a single submission. Each account is a collapsible sub-card.

**Field constraints enforced by the UI and `BulkStoreCoaAccountRequest`:**

| Field | Constraint |
|---|---|
| Site | Required. Must reference an existing site |
| Account Code | Required. Auto-suggested via `nextCode`; user may override |
| Account Name | Required |
| Abbreviation | Optional, max 255 characters |
| Account Type | **REVENUE or EXPENSE only** |
| Short Description | Optional |
| Parent Account | Optional. Dropdown includes both existing DB accounts and **in-batch siblings** (displayed as `NEW: <name>`) |
| Budget Control | Toggle, defaults off |

**In-batch parent references:** When account B selects account A (from the same submission) as its parent, the frontend stores `parent_temp_id` referencing A's `_temp_id`. The backend's `sortAccountsByDependency` creates parents first, then resolves `parent_temp_id` → actual `parent_account_id` via a temp-ID map. No backend change was needed for this — it is handled entirely within `bulkStore`.

### Single create

A single-account store route also exists (`POST /app/config/coa`) backed by `StoreCoaAccountRequest`. It allows all five account types and additionally requires a `sequence_number` field.

---

## 6. Importing Accounts (CSV)

The **Import CSV** dialog on the index page accepts a JSON payload of parsed rows (the dialog handles CSV → JSON parsing client-side).

**Expected columns per row:**

| Column | Required | Notes |
|---|---|---|
| `site_code` | Yes | Must match an existing site's `site_code` |
| `account_code` | Yes | Must not already exist for that site |
| `account_name` | Yes | |
| `account_type` | Yes | **REVENUE or EXPENSE only** |
| `short_description` | No | |
| `abbreviation` | No | Max 10 characters |
| `parent_account_code` | No | Must exist in DB or elsewhere in the same import batch |
| `is_active` | Yes | Boolean |

**Limits:** Maximum 200 rows per import.

**Two-pass import:** `CoaAccountImportService` first creates all accounts whose parent already exists in the DB (or has no parent), then makes a second pass to create accounts whose parent was just created in the first pass. This resolves one level of in-batch parent dependency without requiring recursive passes.

**Validation endpoint:** `POST /coa/import/validate` runs all checks (site existence, code uniqueness within batch and DB, parent resolution) and returns per-row errors before any data is written.

---

## 7. Editing & Locking Rules

The Edit page (`config/coa/Edit`) is split into two sections: **Account Details** and **Classification**.

### Editable fields (always)

- Account Name
- Abbreviation
- Short Description
- Initial Budget
- Parent Account
- Status (Active / Inactive)
- Budget Control

### Conditionally locked fields

Once `first_transaction_at` is set on an account (i.e. any `PaymentRequestSplit`, `RevenueHarvest`, or `RevenueTestingService` row references it), the following fields become read-only both in the UI and in `UpdateCoaAccountRequest` validation:

| Field | Lock behaviour |
|---|---|
| Account Code | `in:<current_value>` rule — submission must echo the existing code |
| Account Type | `in:<current_type>` rule — submission must echo the existing type |

The Edit page shows a **Locked** badge next to each frozen label and a helper text explaining why. The `isLocked()` method on the model is the single source of truth: it returns `true` when `first_transaction_at` is not null.

### Site

The Site selector is always disabled on the Edit page. An account's site cannot change after creation.

---

## 8. Budget Control & Balance

### Budget Control flag

`budget_control` is a per-account boolean. When the standard template is applied, it is automatically set to `true` for every EXPENSE account. For manually created accounts it defaults to `false` and can be toggled on the Create or Edit form.

### Balance calculation

The Index page shows three numeric columns per account:

| Column | Meaning |
|---|---|
| **Budget** | The value of `initial_budget` |
| **Actual** | Computed at query time via subqueries: for EXPENSE accounts it is the sum of `payment_request_splits.split_amount`; for REVENUE accounts it is the sum of `revenue_harvest.total_revenue` + `revenue_testing_services.contract_value` |
| **Balance** | `actual_amount − initial_budget` (accessor on the model) |

Both Actual and Balance are computed fresh on every page load — they are not stored columns.

---

## 9. Deleting Accounts

`DELETE /app/config/coa/{id}` is blocked by the controller under two conditions:

1. **The account has transactions** — `getTransactionCount()` returns > 0 (checks payment splits, harvest revenue, and testing-service revenue). The error message includes the count.
2. **The account has child accounts** — `childAccounts()->exists()` is true. Child accounts must be removed or re-parented first.

If neither condition applies, the account is permanently deleted. There is no soft-delete / archive step for COA accounts; deactivating (`is_active = false`) is the recommended alternative when you want to stop new usage while keeping history.

---

## 10. Permissions

All COA routes (index, create, store, bulk-store, edit, update, destroy, import, templates, next-code) are guarded by:

```
permission:coa.view.all|coa.view.site
```

`coa.view.all` grants access regardless of site. `coa.view.site` is intended for users who should only see their own site's data (the route-level middleware lets them in; further site-scoping is the responsibility of the controller or policy if added).

---

## 11. Routes Reference

All routes live under the `/app/config/` prefix and are grouped inside the application's authenticated route group.

| Method | Path | Name | Handler |
|---|---|---|---|
| GET | `coa` | `config.coa.index` | `CoaAccountController@index` |
| GET | `coa/create` | `config.coa.create` | `CoaAccountController@create` |
| POST | `coa` | `config.coa.store` | `CoaAccountController@store` |
| POST | `coa/bulk` | `config.coa.bulk-store` | `CoaAccountController@bulkStore` |
| GET | `coa/{coa}/edit` | `config.coa.edit` | `CoaAccountController@edit` |
| PUT/PATCH | `coa/{coa}` | `config.coa.update` | `CoaAccountController@update` |
| DELETE | `coa/{coa}` | `config.coa.destroy` | `CoaAccountController@destroy` |
| GET | `coa/next-code` | `config.coa.next-code` | `CoaAccountController@nextCode` |
| POST | `coa/import/validate` | `config.coa.import.validate` | `CoaAccountImportController@validate` |
| POST | `coa/import` | `config.coa.import` | `CoaAccountImportController@import` |
| GET | `coa/templates` | `config.coa.templates.index` | `CoaAccountTemplateController@index` |
| POST | `coa/templates/apply` | `config.coa.templates.apply` | `CoaAccountTemplateController@apply` |

---

## 12. FAQs

**Q: Account codes have no site prefix — how does the system prevent Klaten and Magelang from colliding?**
A: The unique constraint is on `(site_id, account_code)`, not on `account_code` alone. Both sites can independently have a `5211` account; they are distinct rows with different `site_id` values. The Index page and every dropdown filter by site.

**Q: I want to create an ASSET account manually. Why does the Create form only show Revenue and Expense?**
A: The bulk-create and CSV-import paths are intentionally restricted to REVENUE and EXPENSE for Phase 1 (PRD COA-002). ASSET, LIABILITY, and EQUITY accounts can be introduced for a site by applying the standard template, which seeds all five types. Lifting the restriction on manual creation is planned for a future phase.

**Q: An account code got locked after a transaction was posted. Can I change it?**
A: No. Once `first_transaction_at` is set, both `account_code` and `account_type` are permanently frozen by validation rules in `UpdateCoaAccountRequest`. This preserves audit integrity. If a code was entered in error before any transaction, edit it while it is still unlocked.

**Q: I deactivated an account. Can transactions still reference it?**
A: Existing transactions are unaffected. Whether new transactions can target an inactive account depends on the validation in the consuming module (e.g. payment-request split or revenue recording). The COA module itself does not enforce this at the model level.

**Q: Can I delete a parent account if its children still exist?**
A: No. The destroy action checks `childAccounts()->exists()` and returns an error. Remove or re-parent all children first.

**Q: What happens if I import a CSV row whose parent is also in the same file?**
A: `CoaAccountImportService` uses a two-pass strategy. First pass creates all rows whose parent already exists in the database or is null. Second pass creates the remaining rows, which by then have their parents available. This handles one level of in-batch dependency. Deeper nesting (grandchild in the same import whose grandparent is also new) is not supported in a single import — split it across two imports.

**Q: How is the "Actual" column on the Index page calculated?**
A: It is a runtime subquery, not a stored value. For EXPENSE accounts it sums `payment_request_splits.split_amount` where `coa_account_id` matches. For REVENUE accounts it sums `revenue_harvest.total_revenue` plus `revenue_testing_services.contract_value`. The Balance column is then `Actual − Budget`.

**Q: The template application skipped some accounts. Why?**
A: When `skipExisting = true` (the default in the UI and in `SiteCoaSeeder`), any account whose code already exists for the target site is left untouched. The existing account is still used as a parent for new child accounts that the template needs to create. Run `checkConflicts` first (the Templates dialog does this automatically) to see which codes will be skipped.

---

*Last updated: 2026-02-04 — reflects the state of the codebase at that date.*
