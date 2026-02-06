## ADDED Requirements

### Requirement: Budget Tracking Follows Program Fiscal Year
The system SHALL track all budget allocations, commitments, and actual spending according to the program's assigned fiscal year, NOT the transaction date.

#### Scenario: Transaction in different calendar year belongs to program's fiscal year
- **WHEN** Program A has fiscal_year = 2026 (period: Jun 1, 2026 - Jun 30, 2027)
- **AND** a payment request is created on 2027-04-15 for Program A
- **THEN** the expense is recorded under fiscal year 2026
- **AND** appears in FY2026 budget reports and P&L statements

#### Scenario: Clarifying fiscal year stamping in program details
- **WHEN** a user views program budget details
- **THEN** the system displays explanation text "All budget tracking for this program follows FY2026, regardless of when transactions occur."
- **AND** shows fiscal year period dates for reference

#### Scenario: Budget allocation linked to fiscal year
- **WHEN** budget is allocated to a program
- **THEN** the system creates or updates coa_budget_allocations records with fiscal_year_id matching the program's fiscal year
- **AND** ensures budget amounts are tracked per fiscal year

### Requirement: Fiscal Year Scoping for Budget Queries
The system SHALL scope all budget-related queries (available budget, utilization, variance) by the program's fiscal year.

#### Scenario: Calculating available budget for a program
- **WHEN** the system calculates available budget for Program A (fiscal_year = 2026)
- **THEN** it queries only transactions where program.fiscal_year = 2026
- **AND** ignores transactions from other fiscal years even if program existed in those years

#### Scenario: Budget utilization percentage by fiscal year
- **WHEN** a user views budget utilization for FY2026
- **THEN** the system calculates (actual_spent / allocated_budget) * 100 using only FY2026 programs
- **AND** displays site-level and organization-level utilization percentages

#### Scenario: Multi-year program budget rollup
- **WHEN** a program spans multiple fiscal years (edge case: program created in FY2026 but continues into FY2027)
- **THEN** the system tracks budget separately for each fiscal year the program is active in
- **AND** requires explicit budget reallocation for carryover years (manual process, not automatic)

### Requirement: Budget Validation Against Fiscal Year Allocations
The system SHALL validate payment requests against budget allocations for the program's fiscal year, not current calendar year.

#### Scenario: Validating payment request against fiscal year budget
- **WHEN** a payment request is submitted for Program A with fiscal_year = 2026 on date 2027-05-01
- **THEN** the system checks budget availability against FY2026 allocations
- **AND** prevents submission if FY2026 budget is exhausted, regardless of current date being in 2027

#### Scenario: Budget exhaustion message references fiscal year
- **WHEN** a payment request exceeds available budget
- **THEN** the system displays error "Insufficient budget for FY2026. Available: Rp 18,000,000, Requested: Rp 25,000,000."
- **AND** clarifies that budget limit is fiscal-year-specific

## MODIFIED Requirements

### Requirement: Budget Validation
The system SHALL enforce budget limits during transaction creation, scoped by the program's fiscal year.

#### Scenario: Insufficient Budget
- **WHEN** a payment request is submitted for a program
- **THEN** the system checks available budget for the specific program and activity within the program's fiscal year
- **AND** prevents submission if the request amount exceeds available funds for that fiscal year

#### Scenario: Cross-fiscal-year budget check
- **WHEN** checking budget availability for a program created in FY2026 but current date is in FY2027
- **THEN** the system validates against FY2026 budget allocations (program's fiscal year)
- **AND** does NOT use FY2027 budget limits even though transaction date is in 2027
