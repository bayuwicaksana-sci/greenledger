## 1. Database & Models

- [x] 1.1 Update `programs` table migration to add `type`, `status`, `scientific_background`, `experimental_design`, `harvest_planning`.
- [x] 1.2 Create migration for `activities` table (program_id, name, budget_allocation, etc.).
- [x] 1.3 Create migration for `budget_items` table (program_id, activity_id, category, phase, description, spec, unit, qty, price, notes).
- [x] 1.4 Create migration for `revenues` table (program_id, type, amount, date, related_client/harvest details).
- [x] 1.5 Update `Program` model with fillables, casts (JSON for complex fields), and relationships (`activities`, `budgetItems`, `revenues`).
- [x] 1.6 Create `Activity`, `BudgetItem`, and `Revenue` models with relationships.

## 2. Backend Logic

- [x] 2.1 Update `ProgramController` to handle creating/updating `Research` vs `Non-Program` types (validation logic).
- [x] 2.2 Implement `ProgramRequest` form request with conditional validation rules based on `type`.
- [x] 2.3 Add methods to `ProgramController` to handle lifecycle transitions (activate, complete, archive).
- [x] 2.4 Create `ActivityController` or add methods to manage program activities.
- [x] 2.5 Create `BudgetItemController` or add methods to manage BoQ items.
- [x] 2.6 Create `RevenueController` to handle harvest and service revenue recording.

## 3. Frontend - Program Management

- [x] 3.1 Update `Programs/Create` page to allow selecting Program Type.
- [x] 3.2 Implement `ResearchProgramForm` component (Scientific Background, Experimental Design inputs).
- [x] 3.3 Implement `NonProgramForm` component (Activity Description inputs).
- [x] 3.4 Implement `BoQManager` component for adding/editing budget items in a categorized list.
- [x] 3.5 Implement `HarvestPlanningForm` component.

## 4. Frontend - Revenue & Views

- [x] 4.1 Create `Revenue/Create` page or modal for recording Harvest/Service revenue.
- [x] 4.2 Update `Programs/Show` page to display new complex attributes (Scientific, BoQ, Revenue summary).
- [x] 4.3 Update `Programs/Index` to support filtering by Type and Status.

## 5. Verification

- [x] 5.1 Write feature tests for creating Research and Non-Program types.
- [x] 5.2 Write feature tests for BoQ item creation and validation.
- [x] 5.3 Write feature tests for Revenue recording and validation.
- [x] 5.4 Manual verification of the entire workflow (Create -> Budget -> Revenue -> Archive).