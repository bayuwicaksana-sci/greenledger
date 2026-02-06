## 1. Backend

- [x] 1.1 Update `ApprovalInstanceController::show` to append an `edit_url` to the `approvable` object in the response.
- [x] 1.2 Update `ApprovalInstanceController::show` to load actions in a way that ensures the latest comment is easily retrievable (already done, just verifying sort order).

## 2. Frontend

- [x] 2.1 Update `resources/js/Pages/approvals/show.tsx` to accept the new `edit_url` prop (nested in `approval.approvable`).
- [x] 2.2 Create a `RevisionContextAlert` component (inline or separate) that:
    - Checks if status is `changes_requested`.
    - Finds the latest `request_changes` action from the `actions` prop.
    - Displays the comment.
    - Displays an "Edit" button linking to `edit_url`.
- [x] 2.3 Integrate `RevisionContextAlert` into `ApprovalShow` at the top of the page.
- [x] 2.4 Enhance the "Resubmit" button placement or text to make it clear it follows the edit.

## 3. Verification

- [x] 3.1 Verify that for a `Program` approval in `changes_requested` state, the alert appears with the correct comment and a working Edit link.
- [x] 3.2 Verify that for a `CoaAccount` approval in `changes_requested` state, the alert appears with the correct comment and a working Edit link.
- [x] 3.3 Verify that for other states (`pending`, `approved`), the alert does NOT appear.
