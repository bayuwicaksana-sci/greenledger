## ADDED Requirements

### Requirement: Revision Context Visibility
The system SHALL prominently display revision details when an approval request is in the `changes_requested` state.

#### Scenario: Viewing a request requiring changes
- **WHEN** a user views an approval request with status `changes_requested`
- **THEN** the system displays a prominent alert/banner at the top of the page
- **AND** the alert contains the comment from the `request_changes` action
- **AND** the alert identifies which approver requested the changes

### Requirement: Direct Edit Action
The system SHALL provide a direct link to edit the item being approved when changes are requested.

#### Scenario: Accessing edit from approval view
- **WHEN** a submitter views their request in `changes_requested` state
- **THEN** a primary "Edit [Item Type]" button is displayed in the revision context block
- **AND** clicking the button navigates the user to the edit page for that specific item

### Requirement: Resubmission Clarity
The system SHALL make the resubmission action distinct and contextually linked to the revision.

#### Scenario: Resubmitting after changes
- **WHEN** a submitter views their request in `changes_requested` state
- **THEN** the "Resubmit" button is displayed alongside the "Edit" action
- **AND** it is clear that this action returns the item to the approval workflow
