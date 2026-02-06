## ADDED Requirements

### Requirement: Program Types
The system SHALL support two distinct program types: "Research" and "Non-Program". Each type SHALL have specific data requirements and validation rules.

#### Scenario: Creating a Research Program
- **WHEN** a user creates a program and selects "Research" type
- **THEN** the system requires scientific background, experimental design, and harvest planning fields
- **AND** the system validates that these fields are present before saving

#### Scenario: Creating a Non-Program
- **WHEN** a user creates a program and selects "Non-Program" type
- **THEN** the system requires "Non-Program Category" and "Activity Description"
- **AND** the system hides scientific background and harvest planning sections

### Requirement: Detailed Program Attributes
The system SHALL store detailed attributes for programs including scientific background, experimental design, and timeline planning.

#### Scenario: Storing Scientific Background
- **WHEN** a Research Program is saved
- **THEN** the system stores the background, problem statement, hypothesis, objectives, and references

#### Scenario: Storing Experimental Design
- **WHEN** a Research Program is saved
- **THEN** the system stores trial design, treatment structure (treatments, replications, samples), and plot layout details

### Requirement: Program Lifecycle
The system SHALL manage program lifecycle through defined states: Draft, Active, Completed, Archived.

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

### Requirement: Multi-Site Program Support
The system SHALL associate every program with a specific Site (e.g., Klaten, Magelang) and enforce site-based access control.

#### Scenario: Site Assignment
- **WHEN** a program is created
- **THEN** the user must select a Site
- **AND** the program is associated with that Site's code (e.g., KLT, MGL)

#### Scenario: Site Filtering
- **WHEN** a user views the program list
- **THEN** they only see programs for sites they have access to
