## ADDED Requirements

### Requirement: Harvest Revenue Recording
The system SHALL allow recording of revenue from harvest sales, linked to specific programs and harvest cycles.

#### Scenario: Recording Harvest Sale
- **WHEN** a user records a harvest sale
- **THEN** they must specify Program, Harvest Date, Crop Type, Quantity, Price per Unit, Buyer, and Payment details
- **AND** the system calculates Total Revenue

#### Scenario: Recurring Harvest Cycles
- **WHEN** recording a harvest for a program with multiple harvests
- **THEN** the system tracks the specific Harvest Cycle number (e.g., Harvest #1, #2)

### Requirement: Product Testing Service Revenue
The system SHALL allow recording of revenue from product testing services provided to external or internal clients.

#### Scenario: Recording Service Revenue
- **WHEN** a user records service revenue
- **THEN** they must specify Client Name, Service Type, Contract Value, and Service Dates
- **AND** the revenue is linked to the site where the service was performed

### Requirement: Revenue Validation and Approval
The system SHALL enforce validation rules and approval workflows for revenue records.

#### Scenario: Harvest Revenue Approval
- **WHEN** a Harvest Revenue record is submitted
- **THEN** it enters a "Pending Review" state
- **AND** a Manager must approve it before it is finalized in the P&L

#### Scenario: Buyer Management
- **WHEN** recording a harvest sale
- **THEN** the user selects a Buyer from a master list
- **AND** can add a new Buyer if they do not exist
