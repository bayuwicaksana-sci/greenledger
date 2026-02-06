## ADDED Requirements

### Requirement: Pre-Close Validation and Warnings
The system SHALL validate fiscal year status before closing and display warnings about active programs, pending payment requests, and unsettled transactions.

#### Scenario: Pre-close check with active programs
- **WHEN** an administrator initiates fiscal year closing for FY2026 with 12 active programs
- **THEN** the system displays warning "⚠️ This fiscal year has 12 active programs"
- **AND** allows the administrator to proceed with closing

#### Scenario: Pre-close check with pending payment requests
- **WHEN** an administrator initiates fiscal year closing with 3 pending payment requests
- **THEN** the system displays warning "⚠️ This fiscal year has 3 pending payment requests"
- **AND** suggests resolving pending requests before closing

#### Scenario: Pre-close check with unsettled transactions
- **WHEN** an administrator initiates fiscal year closing with unsettled transactions
- **THEN** the system displays warning "⚠️ This fiscal year has unsettled transactions"
- **AND** provides count of unsettled transactions

#### Scenario: Clean fiscal year closure
- **WHEN** an administrator initiates fiscal year closing with no active programs, pending requests, or unsettled transactions
- **THEN** the system displays success indicator "✓ Ready to close. All validations passed."

### Requirement: User-Selectable Closing Actions
The system SHALL provide checkbox options for administrators to select which actions to perform during fiscal year closure.

#### Scenario: Selecting archive completed programs action
- **WHEN** an administrator checks "Archive all completed programs" during fiscal year close
- **THEN** the system automatically moves all completed programs to archived status upon closing
- **AND** logs the archival action for each program

#### Scenario: Selecting block new programs action
- **WHEN** an administrator checks "Block new programs for this fiscal year" during close
- **THEN** the system prevents creation of new programs with this fiscal year after closing
- **AND** displays validation error when users attempt to create programs for closed FY

#### Scenario: Selecting block new transactions action
- **WHEN** an administrator checks "Block new transactions for programs in this fiscal year" during close
- **THEN** the system prevents new payment requests and revenue records for programs in the closed FY
- **AND** displays error message "Cannot create transactions for programs in closed fiscal year"

#### Scenario: Selecting generate report action
- **WHEN** an administrator checks "Generate year-end summary report (PDF)" during close
- **THEN** the system generates a PDF report with fiscal year summary, program list, revenue/expense breakdown, and budget utilization
- **AND** stores the PDF in the system for later access

#### Scenario: Selecting send notifications action
- **WHEN** an administrator checks "Send notification to all program managers" during close
- **THEN** the system sends email notifications to all PIs of programs in the closed fiscal year
- **AND** sends notifications to users with Manager and AVP roles

#### Scenario: Multiple actions selected
- **WHEN** an administrator selects multiple closing actions
- **THEN** the system executes all selected actions in sequence
- **AND** reports success or failure for each action

### Requirement: Closing Confirmation and Notes
The system SHALL require confirmation before closing a fiscal year and allow administrators to add notes explaining the closure.

#### Scenario: Adding notes during closure
- **WHEN** an administrator enters notes "End of FY2026 budget cycle. All programs reviewed."
- **THEN** the system stores the notes with the closure record
- **AND** displays the notes in audit log

#### Scenario: Confirmation dialog
- **WHEN** an administrator clicks "Close Fiscal Year" button
- **THEN** the system displays confirmation dialog "Are you sure you want to close FY2026? This action will mark the fiscal year as closed and execute selected actions."
- **AND** requires clicking "Confirm" to proceed

#### Scenario: Canceling closure
- **WHEN** an administrator clicks "Cancel" in the confirmation dialog
- **THEN** the system aborts the closing process
- **AND** returns to the fiscal year detail page without changes

### Requirement: Mark Fiscal Year as Closed
The system SHALL update the fiscal year's is_closed status to true when closing is confirmed and executed.

#### Scenario: Successful closure
- **WHEN** an administrator confirms fiscal year closure
- **THEN** the system sets is_closed = true for the fiscal year record
- **AND** updates updated_at timestamp
- **AND** displays success message "Fiscal year 2026 has been closed successfully"

#### Scenario: Closed badge display
- **WHEN** a closed fiscal year is displayed in lists or dropdowns
- **THEN** the system shows a "Closed" badge next to the year code
- **AND** applies visual styling to indicate closed status (e.g., gray badge)

### Requirement: Reopen Closed Fiscal Year
The system SHALL allow authorized administrators to reopen a closed fiscal year in emergency situations.

#### Scenario: Reopening fiscal year with permission
- **WHEN** a user with "fiscal-year.reopen" permission reopens a closed fiscal year
- **THEN** the system sets is_closed = false
- **AND** logs the reopen action with user ID, timestamp, and reason
- **AND** displays success message "Fiscal year 2026 has been reopened"

#### Scenario: Unauthorized reopen attempt
- **WHEN** a user without "fiscal-year.reopen" permission attempts to reopen a fiscal year
- **THEN** the system returns 403 Forbidden error
- **AND** displays message "You do not have permission to reopen fiscal years. Contact system administrator."

#### Scenario: Reopen reason required
- **WHEN** a user with reopen permission initiates reopen action
- **THEN** the system displays dialog requiring reason input (mandatory text field)
- **AND** prevents reopening without a reason

#### Scenario: Reversing closing actions on reopen
- **WHEN** a fiscal year is reopened
- **THEN** the system does NOT automatically reverse actions performed during close (e.g., archived programs remain archived)
- **AND** displays note "Reopened. Previous closing actions were not reversed. Review archived programs if needed."

### Requirement: Audit Logging for Close/Reopen Actions
The system SHALL maintain complete audit trail of all fiscal year closing and reopening actions.

#### Scenario: Logging closure with actions
- **WHEN** an administrator closes a fiscal year with selected actions
- **THEN** the system logs the closure event with user ID, timestamp, selected actions, notes, and execution results

#### Scenario: Logging reopen with reason
- **WHEN** an administrator reopens a closed fiscal year
- **THEN** the system logs the reopen event with user ID, timestamp, reason, and changed state (from closed to open)

#### Scenario: Viewing closure history
- **WHEN** an administrator views fiscal year details
- **THEN** the system displays closure history showing who closed/reopened the fiscal year, when, and with what actions/reasons

### Requirement: Permission-Based Access to Closing Operations
The system SHALL enforce permission checks for fiscal year closing and reopening operations.

#### Scenario: Authorized user accessing close action
- **WHEN** a user with "fiscal-year.close" permission views an open fiscal year
- **THEN** the system displays "Close Fiscal Year" button

#### Scenario: Unauthorized user attempting to close
- **WHEN** a user without "fiscal-year.close" permission attempts to access the close dialog
- **THEN** the system returns 403 Forbidden error

#### Scenario: Reopen permission restriction
- **WHEN** a user has "fiscal-year.close" but not "fiscal-year.reopen" permission
- **THEN** the system displays close button but not reopen button
- **AND** prevents access to reopen functionality
