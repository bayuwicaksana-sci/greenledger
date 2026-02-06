## Why

The current `ApprovalShow` page attempts to render details for all approval types (Journal Entries, Payment Requests, etc.) within a single monolithic component. This violates the Open-Closed principle and DRY, making the code fragile and difficult to maintain as new approval types are added.

## What Changes

- **Refactor `ApprovalShow`**: Convert it into a "shell" component that handles generic workflow logic (history, actions, status) and delegates content rendering.
- **Introduce `ContentResolver`**: specific component that dynamically maps approval types (e.g., `App\Models\Finance\PaymentRequest`) to specialized React components.
- **Create Specialized Detail Components**: Implement individual components for existing approval types to handle their specific rendering logic.

## Capabilities

### New Capabilities
- `approval-content-resolver`: A mechanism to dynamically resolve and render specialized content components based on the approval target type.

### Modified Capabilities
- `approval-workflow-ui`: Updating the UI structure to support the content resolver pattern.

## Impact

- **Frontend**: 
    - `resources/js/pages/approvals/show.tsx` (Major refactor)
    - New directory: `resources/js/pages/approvals/partials/details/`
    - New component: `ContentResolver.tsx`
    - New detail components (e.g., `PaymentRequestDetails.tsx`)
- **Backend**: No changes expected, strictly a frontend architectural refactor.
