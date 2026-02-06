## Why

The current approval workflow has a significant UX gap when changes are requested by an approver. The submitter sees a generic "Resubmit" button but no clear indication of *why* changes are needed or *where* to make them, forcing them to hunt through the approval history logs to find the comment. This fragmentation disconnects the feedback from the action required to resolve it.

## What Changes

- **Revision Context Block**: Introduce a prominent alert block on the Approval Show page when status is `changes_requested`.
- **Comment Visibility**: Display the latest rejection/revision comment directly in the context block.
- **Direct Action Links**: Add a primary "Edit [Item]" button alongside the "Resubmit" button to bridge the gap between feedback and action.
- **Resubmit Clarity**: Ensure the resubmission action is visually linked to resolving the specific request.

## Capabilities

### New Capabilities
- `approval-revision-context`: Enhances the approval UI to surface revision details, comments, and direct edit actions for the submitter.

### Modified Capabilities
<!-- None. We are enhancing the UI layer of existing approval logic. -->

## Impact

- **Frontend**: 
  - `resources/js/Pages/approvals/show.tsx`: Major update to include the new context block.
  - New component (optional): `RevisionContextAlert` for reusability.
- **Backend**:
  - `ApprovalInstanceController`: No changes expected to logic, but ensuring the latest action comment is easily accessible (already loaded via `actions`).
- **Data**: No database changes.
