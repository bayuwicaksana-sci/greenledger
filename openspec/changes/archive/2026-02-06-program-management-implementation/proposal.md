## Why

To fully implement the Program Management feature, enabling detailed tracking of Research and Non-Program activities, multi-site budget management (BoQ), and revenue tracking. This replaces the current partial implementation with a robust system matching the PRD requirements.

## What Changes

- **Program Types**: Support for two distinct program types ("Research" and "Non-Program") with different data requirements.
- **Detailed Attributes**: Storage for Scientific Background, Experimental Design, and Harvest Planning data.
- **Budgeting (BoQ)**: Implementation of a detailed Bill of Quantities structure with Categories, Phases, and Unit analysis.
- **Revenue Management**: Capabilities to record and track Harvest Revenue and Product Testing Service revenue.
- **Lifecycle Management**: Formalized states (Draft, Active, Completed, Archived) with approval workflows.
- **Multi-Site**: Support for site-specific data and consolidated views.

## Capabilities

### New Capabilities
- `program-core`: Core program entity management, including "Research" vs "Non-Program" types, scientific details, lifecycle states, and multi-site support.
- `program-budgeting`: Detailed Bill of Quantities (BoQ) budgeting, activity-level allocation, and expense tracking.
- `program-revenue`: Management of revenue streams including Harvest cycles and Product Testing services.

### Modified Capabilities
<!-- No existing specs to modify in this repository context. -->

## Impact

- **Database**:
  - Modification of `programs` table.
  - New tables for `program_activities`, `budget_items`, `revenues`, `harvests`.
- **Backend**:
  - `ProgramController`: Enhanced with type-specific validation and logic.
  - New Controllers: `RevenueController`, `BudgetItemController`.
  - Models: `Program`, `Activity`, `BudgetItem`, `Revenue`.
- **Frontend**:
  - New/Updated Pages: `Programs/Create`, `Programs/Edit`, `Programs/Show`.
  - Components for BoQ entry and Revenue recording.
- **Routes**: Updates to `routes/web.php` for new resources.
