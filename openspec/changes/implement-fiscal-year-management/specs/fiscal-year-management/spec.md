## ADDED Requirements

### Requirement: Create Fiscal Year with Custom Dates
The system SHALL allow authorized administrators to create fiscal years with user-defined start and end dates, where the year code is automatically derived from the start date year.

#### Scenario: Creating fiscal year with calendar year period
- **WHEN** an administrator creates a fiscal year with start_date "2026-01-01" and end_date "2026-12-31"
- **THEN** the system automatically sets year code to 2026
- **AND** saves the fiscal year record with the specified dates

#### Scenario: Creating fiscal year with non-calendar period
- **WHEN** an administrator creates a fiscal year with start_date "2026-06-01" and end_date "2027-06-30"
- **THEN** the system automatically sets year code to 2026 (from start date)
- **AND** allows end date to span into the following calendar year

#### Scenario: Preventing duplicate year codes
- **WHEN** an administrator attempts to create a fiscal year with a year code that already exists
- **THEN** the system prevents creation and displays error message "Fiscal year 2026 already exists"

#### Scenario: Validating date range
- **WHEN** an administrator enters end_date before start_date
- **THEN** the system prevents creation and displays error message "End date must be after start date"

### Requirement: Edit Fiscal Year Details
The system SHALL allow authorized administrators to edit fiscal year start date, end date, and other attributes while preserving data integrity.

#### Scenario: Editing dates for fiscal year without programs
- **WHEN** an administrator edits dates for a fiscal year with no associated programs
- **THEN** the system updates the start_date and end_date
- **AND** recalculates the year code if start_date year changed

#### Scenario: Warning when editing fiscal year with programs
- **WHEN** an administrator attempts to edit dates for a fiscal year with existing programs
- **THEN** the system displays warning "This fiscal year has 12 associated programs. Changing dates may affect reporting."
- **AND** requires confirmation before saving

#### Scenario: Preventing year code conflicts during edit
- **WHEN** an administrator changes start_date such that the derived year conflicts with another fiscal year
- **THEN** the system prevents the update and displays error message

### Requirement: List Fiscal Years with Usage Statistics
The system SHALL display all fiscal years with relevant statistics including program count, open/closed status, and date ranges.

#### Scenario: Viewing fiscal years list
- **WHEN** an administrator accesses the fiscal years management page
- **THEN** the system displays all fiscal years ordered by year descending
- **AND** shows year code, date range (MMM DD, YYYY format), status badge (Open/Closed), and program count for each

#### Scenario: Filtering by status
- **WHEN** an administrator filters fiscal years by "Closed" status
- **THEN** the system displays only fiscal years where is_closed = true

#### Scenario: Searching by year code
- **WHEN** an administrator searches for "2026"
- **THEN** the system displays fiscal years matching the year code

### Requirement: Prevent Deletion of Fiscal Years with Programs
The system SHALL prevent deletion (including soft delete) of fiscal years that have associated programs to maintain data integrity.

#### Scenario: Attempting to delete fiscal year with programs
- **WHEN** an administrator attempts to delete a fiscal year with 5 associated programs
- **THEN** the system prevents deletion and displays error "Cannot delete fiscal year 2026. It has 5 associated programs."
- **AND** suggests marking as closed instead of deleting

#### Scenario: Deleting fiscal year without programs
- **WHEN** an administrator deletes a fiscal year with no associated programs
- **THEN** the system soft deletes the fiscal year record
- **AND** displays success message "Fiscal year deleted successfully"

### Requirement: Permission-Based Access Control
The system SHALL enforce permission checks for all fiscal year management operations.

#### Scenario: Authorized user creating fiscal year
- **WHEN** a user with "fiscal-year.manage" permission accesses the create fiscal year page
- **THEN** the system displays the creation form

#### Scenario: Unauthorized user attempting to create fiscal year
- **WHEN** a user without "fiscal-year.manage" permission attempts to access the create fiscal year page
- **THEN** the system returns 403 Forbidden error
- **AND** displays message "You do not have permission to manage fiscal years"

#### Scenario: View-only access
- **WHEN** a user with only "fiscal-year.view" permission accesses the fiscal years list
- **THEN** the system displays the list without create/edit/delete buttons

### Requirement: Display Fiscal Year Period Information
The system SHALL display fiscal year information prominently in contexts where it provides relevant context.

#### Scenario: Showing fiscal year period on program pages
- **WHEN** a user views a program detail page
- **THEN** the system displays the fiscal year code and date range (e.g., "FY2026: Jun 1, 2026 - Jun 30, 2027")

#### Scenario: Fiscal year badge on program list
- **WHEN** a user views the program list
- **THEN** each program row displays a fiscal year badge with year code
- **AND** shows status indicator (open/closed) if fiscal year is closed

### Requirement: Audit Trail for Fiscal Year Operations
The system SHALL log all fiscal year CRUD operations with user information and timestamps for audit purposes.

#### Scenario: Logging fiscal year creation
- **WHEN** an administrator creates a new fiscal year
- **THEN** the system logs the action with user ID, timestamp, and fiscal year details

#### Scenario: Logging fiscal year edits
- **WHEN** an administrator modifies fiscal year dates
- **THEN** the system logs the change including before and after values
