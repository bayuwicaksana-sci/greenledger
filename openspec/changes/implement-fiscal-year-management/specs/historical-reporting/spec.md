## ADDED Requirements

### Requirement: Year-Over-Year Comparison Table
The system SHALL provide a dedicated historical reporting page displaying year-over-year comparison of key financial metrics across multiple fiscal years.

#### Scenario: Viewing multi-year comparison
- **WHEN** a user with "reports.historical" permission accesses the historical reports page
- **THEN** the system displays a comparison table with columns for each fiscal year (FY2024, FY2025, FY2026, etc.)
- **AND** rows showing metrics: Total Revenue, Total Expenses, Net Income, Budget Utilization %, Active Programs, Average Program Budget

#### Scenario: Calculating year-over-year change percentages
- **WHEN** the system displays year-over-year comparison
- **THEN** it includes a YoY % column showing percentage change from previous year
- **AND** displays positive changes in green and negative changes in red
- **AND** calculates percentage as ((Current - Previous) / Previous) * 100

#### Scenario: Filtering comparison by site
- **WHEN** a user selects "Klaten" site filter on historical reports page
- **THEN** the system displays year-over-year metrics filtered to Klaten site only
- **AND** updates all calculations to reflect site-specific data

#### Scenario: Filtering comparison by date range
- **WHEN** a user selects fiscal years 2024-2026 using a date range filter
- **THEN** the system displays only those three fiscal years in the comparison table
- **AND** hides data for years outside the selected range

### Requirement: Revenue and Expense Breakdown by Account
The system SHALL provide drill-down capability to view detailed revenue and expense breakdown by COA account for selected fiscal years.

#### Scenario: Drilling down into revenue accounts
- **WHEN** a user clicks on "Total Revenue" metric for FY2026
- **THEN** the system displays a breakdown table showing revenue by COA account (e.g., KLT-4-1100 Harvest Revenue, MGL-4-2100 Testing Services)
- **AND** shows amount and percentage of total revenue for each account

#### Scenario: Drilling down into expense accounts
- **WHEN** a user clicks on "Total Expenses" metric for FY2026
- **THEN** the system displays a breakdown table showing expenses by COA account category (Seeds, Fertilizer, Labor, etc.)
- **AND** shows amount and percentage of total expenses for each category

#### Scenario: Comparing account performance across years
- **WHEN** a user selects an account "KLT-4-1100 Harvest Revenue" from the breakdown
- **THEN** the system displays a trend line chart showing this account's revenue across all selected fiscal years

### Requirement: Trend Charts for Key Metrics
The system SHALL display visual trend charts showing financial metric trends across fiscal years.

#### Scenario: Revenue trend chart
- **WHEN** a user views the historical reports page
- **THEN** the system displays a line chart showing Total Revenue trend across fiscal years
- **AND** includes data points for each fiscal year with values labeled

#### Scenario: Multi-metric comparison chart
- **WHEN** a user enables "Compare Metrics" view
- **THEN** the system displays a multi-line chart showing Revenue, Expenses, and Net Income trends on the same chart
- **AND** uses different colors for each metric line
- **AND** includes a legend identifying each metric

#### Scenario: Budget utilization chart
- **WHEN** a user views budget utilization trend
- **THEN** the system displays a bar chart showing budget utilization percentage for each fiscal year
- **AND** highlights years with >90% utilization in red
- **AND** highlights years with <70% utilization in yellow

### Requirement: Excel Export for Historical Data
The system SHALL allow users to export historical comparison data to Excel format for external analysis.

#### Scenario: Exporting year-over-year summary
- **WHEN** a user clicks "Export to Excel" button on historical reports page
- **THEN** the system generates an Excel file with worksheets: Summary (year-over-year table), Revenue Breakdown, Expense Breakdown, Program List
- **AND** downloads the file named "Historical_Report_FY2024-2026_YYYY-MM-DD.xlsx"

#### Scenario: Including filters in export
- **WHEN** a user has applied site filter "Klaten" before exporting
- **THEN** the exported Excel file contains only Klaten site data
- **AND** includes a note in the Summary sheet indicating "Filtered by Site: Klaten"

#### Scenario: Exporting trend data
- **WHEN** a user exports historical data
- **THEN** the Excel file includes a "Trends" worksheet with month-by-month data points for revenue, expenses, and net income across all selected fiscal years

### Requirement: Fiscal Year Selector Component Enhancement
The system SHALL enhance existing dashboards with a reusable fiscal year selector component that displays year code and status.

#### Scenario: Using fiscal year selector on dashboard
- **WHEN** a user accesses the main dashboard
- **THEN** the system displays a fiscal year dropdown selector in the header
- **AND** shows the currently selected fiscal year (default: current year)

#### Scenario: Fiscal year selector showing closed years
- **WHEN** a user opens the fiscal year dropdown
- **THEN** the system lists all fiscal years in descending order by year
- **AND** displays "Closed" badge next to closed fiscal years
- **AND** displays "Open" badge (or no badge) next to open fiscal years

#### Scenario: Changing fiscal year selection
- **WHEN** a user selects a different fiscal year from the dropdown (e.g., FY2025)
- **THEN** the system reloads dashboard data filtered to FY2025
- **AND** updates all charts, metrics, and tables to reflect FY2025 data
- **AND** persists the selection for the user's session

#### Scenario: Fiscal year selector on COA index
- **WHEN** a user accesses the COA (Chart of Accounts) index page
- **THEN** the system displays the fiscal year selector
- **AND** filters budget allocations and actual amounts by selected fiscal year
- **AND** displays trend sparklines showing budget/actual for last 3 fiscal years

### Requirement: Multi-Year Trend Display on Existing Pages
The system SHALL add trend indicators and sparklines to existing pages to show multi-year context without leaving the page.

#### Scenario: Program list with fiscal year trends
- **WHEN** a user views the program list
- **THEN** each program row displays a small trend indicator showing budget utilization for this program type across fiscal years (if historical data exists)

#### Scenario: COA account balance trends
- **WHEN** a user views the COA account list
- **THEN** each account displays a sparkline chart showing actual spending/revenue for the last 3 fiscal years
- **AND** hovering over the sparkline shows tooltip with year-specific values

### Requirement: Permission-Based Access to Historical Reports
The system SHALL enforce permission checks for historical reporting features.

#### Scenario: Authorized access to historical reports
- **WHEN** a user with "reports.historical" permission navigates to /reports/historical
- **THEN** the system displays the historical reports page

#### Scenario: Unauthorized access attempt
- **WHEN** a user without "reports.historical" permission attempts to access /reports/historical
- **THEN** the system returns 403 Forbidden error
- **AND** displays message "You do not have permission to view historical reports"

#### Scenario: Partial access with fiscal-year.view permission
- **WHEN** a user has "fiscal-year.view" but not "reports.historical" permission
- **THEN** the user can see fiscal year selector on dashboards and filter current data
- **AND** cannot access the dedicated historical reports page
- **AND** does not see multi-year comparison tables

### Requirement: Performance Optimization for Multi-Year Queries
The system SHALL optimize database queries for multi-year reporting to maintain acceptable performance.

#### Scenario: Cached year-over-year summary
- **WHEN** the historical reports page is loaded
- **THEN** the system uses cached year-over-year summary data (refreshed daily)
- **AND** displays data within 3 seconds

#### Scenario: Paginated drill-down results
- **WHEN** a user drills down into account breakdown for a fiscal year
- **THEN** the system displays paginated results (50 accounts per page)
- **AND** allows searching/filtering within the breakdown

#### Scenario: Date range limitation warning
- **WHEN** a user attempts to select more than 5 fiscal years for comparison
- **THEN** the system displays warning "Selecting too many years may impact performance. Consider limiting to 3-5 years."
- **AND** allows proceeding but shows loading indicator during data fetch
