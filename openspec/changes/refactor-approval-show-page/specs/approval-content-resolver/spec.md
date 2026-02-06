## ADDED Requirements

### Requirement: Dynamic Content Resolution
The system SHALL dynamically resolve and render a specific React component based on the `approvable_type` provided in the approval instance data.

#### Scenario: Valid Type Resolution
- **WHEN** the approval data contains a known `approvable_type` (e.g., `App\Models\Finance\PaymentRequest`)
- **THEN** the `ContentResolver` renders the corresponding detail component (e.g., `PaymentRequestDetails`)

#### Scenario: Fallback for Unknown Type
- **WHEN** the approval data contains an unknown or unmapped `approvable_type`
- **THEN** the `ContentResolver` renders a user-friendly fallback message indicating no detail view is available

#### Scenario: Data Propagation
- **WHEN** the `ContentResolver` instantiates a detail component
- **THEN** it passes the `approvable` data object as a prop to that component
