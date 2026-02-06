## Context

The current `ApprovalShow` page renders details for diverse approval types (Journal Entries, Payment Requests, etc.) within a single component. This "god component" approach is difficult to maintain and extend. We need a polymorphic UI architecture where the page shell handles common workflow actions, while specialized components render the content specific to each approval type.

## Goals / Non-Goals

**Goals:**
- Implement a `ContentResolver` pattern to dynamically load detail components based on the approval target type.
- Refactor `ApprovalShow.tsx` to be a generic "shell" component.
- Extract existing rendering logic into specialized detail components.
- Ensure type safety for the props passed to detail components.

**Non-Goals:**
- Changing the backend API or data structure for approvals.
- Redesigning the visual style of the approval workflow actions (history, buttons).

## Decisions

### 1. Component Registry Pattern
We will use a static registry map in `ContentResolver.tsx` to map backend model classes (e.g., `App\Models\Finance\PaymentRequest`) to React components.
- **Rationale:** Simple, explicit, and easy to maintain. Dynamic imports were considered but deemed unnecessary complexity for the current scale.
- **Alternative:** Passing the component name from the backend. **Rejected** to keep frontend logic decoupled from backend implementation details and avoid "magic strings" in the API response controlling UI implementation.

### 2. "Shell & Content" Architecture
The `ApprovalShow` page will act as a layout shell. It will provide the `ApprovalContext` (status, actions, history) and a slot for the content.
- **Rationale:** consistent UX across all approval types while allowing complete flexibility for the detail view.

### 3. Dedicated Directory Structure
All detail components will reside in `resources/js/pages/approvals/partials/details/`.
- **Rationale:** Keeps the approval domain self-contained and organized.

## Risks / Trade-offs

- **Risk:** Type casting safety. The `approvable` data from the backend is `any` or a generic object.
- **Mitigation:** Each detail component will define its own interface and we will assume the backend sends the correct shape for that `approvable_type`. Runtime validation is out of scope but can be added later if needed.

- **Risk:** Missing components for new types.
- **Mitigation:** The `ContentResolver` will have a fallback UI ("No detail view available for [Type]") to prevent crashes and aid development.
