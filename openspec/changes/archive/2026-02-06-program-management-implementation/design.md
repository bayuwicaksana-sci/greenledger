## Context

The current `Program` model is simplistic, only storing basic details like name and code. The new requirements demand a significant expansion to support two distinct program types ("Research" and "Non-Program") with deep hierarchical data (Scientific Background, Experimental Design, Harvest Planning, BoQ). The system must also support multi-site operations and rigorous lifecycle states.

## Goals / Non-Goals

**Goals:**
- Implement a flexible data model to store complex program attributes without over-normalizing the database.
- Create a detailed Bill of Quantities (BoQ) structure for accurate budgeting.
- Enable site-specific data isolation and consolidated reporting.
- Implement robust validation for type-specific requirements (Research vs Non-Program).

**Non-Goals:**
- Full integration with external accounting software (Phase 2).
- Inventory management for harvested goods (beyond revenue recording).

## Decisions

### 1. Data Modeling for Complex Attributes
**Decision**: Use JSON columns for `scientific_background`, `experimental_design`, and `harvest_planning` on the `programs` table.
**Rationale**: These attributes are highly specific, deeply nested, and primarily read/written as a document. Normalizing them into separate tables (`program_scientific_backgrounds`, `program_treatments`, etc.) would create excessive joins and complexity for little gain in query capability (we primarily query by program ID).
**Alternatives**:
- *One-to-One Tables*: Cleaner schema but higher complexity.
- *EAV (Entity-Attribute-Value)*: Too slow and complex to maintain.

### 2. Bill of Quantities (BoQ) Structure
**Decision**: Use relational tables for the budget structure (`budget_items` table linked to `programs` and `activities`).
**Rationale**: Budget items need to be queried, aggregated, and linked to transactions (expenses) individually. JSON would make financial reporting and constraint enforcement (budget vs actual) inefficient and risky.

### 3. Program Type Handling
**Decision**: Single `programs` table with a `type` column (`research` | `non_program`) and conditional validation in the Controller/Request classes.
**Rationale**: Shared core attributes (budget, site, code, name, lifecycle) outweigh the differences. Polymorphic relations (e.g. `programable_id`, `programable_type`) are overkill given the shared financial core.

### 4. Revenue Recording
**Decision**: Separate `revenues` table with polymorphic relationship or nullable foreign keys to support both "Harvest" (linked to `harvest_cycles`) and "Service" (linked to client).
**Rationale**: Allows a unified revenue stream for P&L reporting while accommodating distinct data requirements for each source.

## Risks / Trade-offs

- **JSON Data Integrity**:
  - *Risk*: JSON columns lack schema enforcement at the database level.
  - *Mitigation*: Strict validation using Laravel Data Transfer Objects (DTOs) and Form Requests before saving.

- **Complex Forms**:
  - *Risk*: The frontend forms for creating programs will be very large and complex.
  - *Mitigation*: Use React Context or multi-step wizard approach to manage form state. Split components logically (e.g., `ScientificBackgroundForm`, `BoQForm`).

- **Migration of Existing Data**:
  - *Risk*: Existing programs lack the new required fields.
  - *Mitigation*: Make new fields nullable for legacy records, or provide a default "Migration" state. Run a migration script to backfill essential defaults.

## Migration Plan

1.  **Database**:
    -   Alter `programs` table: Add `type`, `scientific_background` (json), `experimental_design` (json), `harvest_planning` (json), `status`.
    -   Create `activities`, `budget_items`, `revenues` tables.
2.  **Code**:
    -   Update `Program` model with casts and relationships.
    -   Implement `ProgramRequest` with conditional logic.
    -   Build frontend forms.
3.  **Deployment**:
    -   Run migrations.
    -   Deploy code.
