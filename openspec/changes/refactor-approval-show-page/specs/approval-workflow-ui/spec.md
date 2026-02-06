## ADDED Requirements

### Requirement: Polymorphic Approval Page Shell
The Approval Show page SHALL act as a generic shell that provides the workflow context (status, history, actions) while delegating the content rendering to a resolver.

#### Scenario: Shell Structure
- **WHEN** the Approval Show page loads
- **THEN** it displays the common header (title, status, action buttons) and sidebar (metadata)
- **AND** it renders the `ContentResolver` within the main content area

#### Scenario: Action Availability
- **WHEN** the page is loaded for a pending approval where the user is an approver
- **THEN** the shell displays the "Approve", "Reject", and "Request Changes" actions regardless of the content type

#### Scenario: Revision Context
- **WHEN** the approval status is "Changes Requested" and the user is the submitter
- **THEN** the shell displays the revision context alert and resubmit options regardless of the content type
