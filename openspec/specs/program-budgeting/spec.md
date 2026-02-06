## ADDED Requirements

### Requirement: Bill of Quantities (BoQ) Structure
The system SHALL support a hierarchical budget structure (Bill of Quantities) consisting of Categories, Phases, and detailed Budget Items.

#### Scenario: Adding Budget Items
- **WHEN** a user defines the program budget
- **THEN** they can add items with Category, Phase, Item Description, Specification, Unit, Quantity, and Unit Price
- **AND** the system automatically calculates the Subtotal (Qty * Price)

#### Scenario: Budget Categorization
- **WHEN** a budget item is added
- **THEN** it must be assigned to a predefined Category (e.g., Fertilizer, Seeds, Labor)
- **AND** a Phase (e.g., Planting, Harvest)

### Requirement: Activity-Level Budgeting
The system SHALL allow allocating portions of the total program budget to specific Activities within the program.

#### Scenario: Activity Budget Allocation
- **WHEN** an Activity is created within a Program
- **THEN** the user can allocate a specific budget amount to that Activity
- **AND** the system validates that total allocated activity budgets do not exceed the Program's total budget

### Requirement: Budget Validation
The system SHALL enforce budget limits during transaction creation.

#### Scenario: Insufficient Budget
- **WHEN** a payment request is submitted for a program
- **THEN** the system checks available budget for the specific program and activity
- **AND** prevents submission if the request amount exceeds available funds
