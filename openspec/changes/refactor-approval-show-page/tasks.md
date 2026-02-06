## 1. Setup and Component Structure

- [x] 1.1 Create `ContentResolver.tsx` component in `resources/js/pages/approvals/partials/`
- [x] 1.2 Create `details/` directory in `resources/js/pages/approvals/partials/`
- [x] 1.3 Define TypeScript interfaces for detail component props structure

## 2. Content Resolver Implementation

- [x] 2.1 Implement type registry map (e.g., `App\Models\Finance\PaymentRequest` → `PaymentRequestDetails`)
- [x] 2.2 Add dynamic component resolution logic based on `approvable_type`
- [x] 2.3 Implement fallback UI for unknown or unmapped types
- [x] 2.4 Add prop passing mechanism to propagate `approvable` data to detail components

## 3. Extract Existing Detail Components

- [x] 3.1 Identify current approval-specific rendering logic in `ApprovalShow.tsx`
- [x] 3.2 Create detail component for each existing approval type
- [x] 3.3 Move type-specific rendering logic from shell to detail components
- [x] 3.4 Define TypeScript interfaces for each detail component's props

## 4. Refactor ApprovalShow Shell

- [x] 4.1 Remove type-specific rendering logic from `ApprovalShow.tsx`
- [x] 4.2 Integrate `ContentResolver` into the main content area
- [x] 4.3 Ensure common header (title, status, action buttons) remains in shell
- [x] 4.4 Ensure sidebar (metadata) remains in shell
- [x] 4.5 Verify action availability (Approve, Reject, Request Changes) is type-agnostic

## 5. Testing and Verification

- [x] 5.1 Test rendering for each existing approval type
- [x] 5.2 Verify fallback UI displays for unmapped types
- [x] 5.3 Test action buttons work regardless of content type
- [x] 5.4 Verify revision context alert displays for "Changes Requested" status
- [x] 5.5 Ensure type safety with TypeScript compilation
