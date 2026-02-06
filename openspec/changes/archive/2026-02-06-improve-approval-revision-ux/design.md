## Context

Currently, when an approval is in the `changes_requested` state, the UX is confusing for the submitter. They see a "Resubmit" button but have to hunt through the history timeline to find the specific comment explaining what needs to change. There is also no direct link to edit the underlying item from the approval view.

## Goals / Non-Goals

**Goals:**
- Provide a clear, prominent "Revision Context" block at the top of the Approval Show page when status is `changes_requested`.
- Display the latest comment associated with the `request_changes` action.
- Provide a direct link to edit the approvable item (Program, PaymentRequest, etc.).
- Ensure the "Resubmit" action is visually associated with resolving this request.

**Non-Goals:**
- Changing the backend state machine or logic.
- Implementing "inline editing" within the approval page (editing happens on the resource's edit page).

## Decisions

### 1. Revision Context Component
**Decision**: Implement the revision context directly in `resources/js/Pages/approvals/show.tsx` rather than a separate reusable component for now, as it relies heavily on specific props of the `ApprovalInstance` (actions, status, approvable).
**Rationale**: It's a specific view logic for this page. Extracting it prematurely might overcomplicate passing props.

### 2. Finding the Comment
**Decision**: In the frontend component, find the *latest* action with `action_type === 'request_changes'`.
**Rationale**: The `actions` array is already loaded and sorted by date. The latest one is the reason for the current state.

### 3. Dynamic Edit Link
**Decision**: Construct the edit URL dynamically based on `approvable_type` and `approvable_id`.
**Rationale**: We can map `App\Models\Program` -> `programs.edit` and `App\Models\CoaAccount` -> `config.coa.edit`.
**Mapping**:
- `App\Models\Program` -> `route('site.programs.edit', { site: site_code, program: id })`
- `App\Models\CoaAccount` -> `route('config.coa.edit', { coa: id })` (Note: COA is global config, but accessed via site context sometimes? No, config routes are under `/app/config`). *Correction*: COA routes are global. We need a reliable way to resolve the edit route.
- **Alternative**: Add a `edit_url` attribute to the `approvable` polymorphic relation response in the controller. This is cleaner and more robust than frontend string matching.

### 4. Controller Enhancement
**Decision**: Update `ApprovalInstanceController::show` to append an `edit_url` to the `approvable` object if the user has permission to edit it.
**Rationale**: Keeps routing logic in the backend where it belongs.

## Risks / Trade-offs

- **Risk**: `edit_url` generation might be complex for some models.
- **Mitigation**: Use a simple match expression in the controller or a trait method on the models (`getEditUrl()`). For now, a match in the controller is least intrusive.

## Migration Plan

1.  **Backend**: Update `ApprovalInstanceController.php` to append `edit_url`.
2.  **Frontend**: Update `show.tsx` to render the Alert block using the new data.
