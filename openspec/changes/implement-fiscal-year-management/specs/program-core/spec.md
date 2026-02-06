## ADDED Requirements

### Requirement: Fiscal Year Binding at Program Creation
The system SHALL bind every program to a specific fiscal year at creation time, and SHALL enforce that all transactions for that program belong to the program's fiscal year regardless of transaction date.

#### Scenario: Program bound to fiscal year at creation
- **WHEN** a user creates a program and selects fiscal year 2026
- **THEN** the system stores fiscal_year = 2026 in the program record
- **AND** all subsequent transactions for this program are tagged with fiscal year 2026

#### Scenario: Transaction date does not affect fiscal year
- **WHEN** a payment request is created on 2027-03-15 for a program with fiscal_year = 2026
- **THEN** the transaction is recorded under fiscal year 2026 (not 2027)
- **AND** appears in FY2026 financial reports

#### Scenario: Fiscal year displayed on program detail page
- **WHEN** a user views a program detail page
- **THEN** the system displays fiscal year code and date range prominently (e.g., "FY2026: Jun 1, 2026 - Jun 30, 2027")
- **AND** shows fiscal year status badge (Open/Closed)

### Requirement: Prevent Program Creation for Closed Fiscal Years
The system SHALL prevent creation of new programs for closed fiscal years unless the user has override permission.

#### Scenario: Creating program for open fiscal year
- **WHEN** a user creates a program and selects an open fiscal year (is_closed = false)
- **THEN** the system allows program creation
- **AND** proceeds with normal validation

#### Scenario: Blocked creation for closed fiscal year
- **WHEN** a user without "program.override-closed-fy" permission attempts to create a program for a closed fiscal year
- **THEN** the system prevents submission and displays validation error "Fiscal year 2025 is closed. Cannot create programs for closed fiscal years."
- **AND** suggests selecting an open fiscal year

#### Scenario: Override permission allows creation for closed fiscal year
- **WHEN** a user with "program.override-closed-fy" permission creates a program for a closed fiscal year
- **THEN** the system allows creation with a warning message "Creating program for closed fiscal year 2025"
- **AND** logs the override action in audit trail

#### Scenario: Validation message with fiscal year status
- **WHEN** the program creation form displays fiscal year options
- **THEN** closed fiscal years are marked with (Closed) indicator in the dropdown
- **AND** selecting a closed fiscal year displays inline warning before submission

### Requirement: Filter Programs by Fiscal Year Status
The system SHALL allow users to filter the program list by fiscal year and by fiscal year open/closed status.

#### Scenario: Filtering by specific fiscal year
- **WHEN** a user selects fiscal year "2026" from the filter dropdown on program list
- **THEN** the system displays only programs where fiscal_year = 2026
- **AND** updates the program count indicator

#### Scenario: Filtering by open/closed fiscal year status
- **WHEN** a user applies filter "Closed Fiscal Years Only"
- **THEN** the system displays programs whose associated fiscal year has is_closed = true
- **AND** displays fiscal year status badge on each program row

#### Scenario: Combining fiscal year and program status filters
- **WHEN** a user filters by fiscal year "2026" AND program status "Active"
- **THEN** the system displays only active programs in fiscal year 2026
- **AND** shows count of matching programs

### Requirement: Fiscal Year Badge on Program List
The system SHALL display fiscal year information as a visual badge on the program list for quick identification.

#### Scenario: Fiscal year badge display
- **WHEN** a user views the program list
- **THEN** each program row displays a badge showing the fiscal year code (e.g., "FY2026")
- **AND** the badge color indicates status: blue for open, gray for closed

#### Scenario: Hover tooltip on fiscal year badge
- **WHEN** a user hovers over a fiscal year badge
- **THEN** the system displays tooltip showing full fiscal year date range (e.g., "FY2026: Jun 1, 2026 - Jun 30, 2027")
- **AND** status text ("Open" or "Closed")

#### Scenario: Sorting programs by fiscal year
- **WHEN** a user clicks on the "Fiscal Year" column header in program list
- **THEN** the system sorts programs by fiscal_year in descending order (most recent first)
- **AND** allows toggling between ascending and descending sort

## MODIFIED Requirements

### Requirement: Program Lifecycle
The system SHALL manage program lifecycle through defined states: Draft, Active, Completed, Archived, and SHALL prevent lifecycle transitions that would violate fiscal year closure rules.

#### Scenario: Program Activation
- **WHEN** a Draft program is approved
- **THEN** its status changes to Active
- **AND** budget becomes available for spending

#### Scenario: Program Completion
- **WHEN** all activities are finished and settled
- **THEN** a Manager can mark the program as Completed
- **AND** the system validates no pending transactions exist

#### Scenario: Program Archival
- **WHEN** a program is Completed
- **THEN** a Manager can Archive the program
- **AND** the program is hidden from default active views

#### Scenario: Preventing activation of programs in closed fiscal years
- **WHEN** a Manager attempts to activate a Draft program whose fiscal year is closed
- **THEN** the system prevents activation and displays error "Cannot activate program in closed fiscal year 2025. Reopen the fiscal year or reassign the program to an open fiscal year."
