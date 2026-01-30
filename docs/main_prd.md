**PRODUCT REQUIREMENTS DOCUMENT**

**Finance & Accounting Application**

_Multi-Site Budget Management & Financial System_

# **Table of Contents**

# **1. Executive Summary**

This Product Requirements Document outlines specifications for a comprehensive, multi-site Finance and Accounting Application designed for managing research program operations across multiple research stations. The system integrates budget management, payment processing, revenue tracking, settlement workflows, and financial reporting in a unified platform with complete audit trail and digital signature capabilities.

### **1.1 Project Overview**

The organization manages agricultural research programs with an annual budget of approximately 3 billion rupiah across multiple research stations. Current operations rely on Excel Online and WhatsApp, creating inefficiencies, data accuracy issues, and lack of proper financial controls. This application will provide end-to-end financial management including:

- Multi-site budget management with site-specific Chart of Accounts

- Revenue tracking from harvest sales and product testing services

- Integrated expense management with multi-program payment split capability

- Activity-based cost tracking within research programs

- Program lifecycle management with archival for historical data

- Profit & Loss reporting per program and per site

- Digital signatures and comprehensive audit trail for compliance

### **1.2 Key Business Drivers**

**Financial Visibility:** Real-time visibility into program profitability combining revenue from harvest sales and product testing with research expenses.

**Multi-Site Management:** Consolidated view across multiple research stations while maintaining site-specific financial tracking and accountability.

**Operational Efficiency:** Eliminate dual-entry processes, reduce approval cycle time from 3-5 days to same-day, and automate settlement tracking to achieve 100% compliance with 48-hour deadline.

**Data-Driven Decisions:** Accurate cost and revenue tracking per activity enables managers to optimize resource allocation and identify most profitable research programs.

### **1.3 Critical Success Factors**

- 95% reduction in manual data entry errors through automated workflows

- 100% compliance with settlement deadlines via automated tracking and notifications

- Real-time program profitability visibility (revenue minus expenses)

- 70% reduction in approval cycle time enabling faster fund disbursement

- Complete digital audit trail for all financial transactions

- Successful user adoption across 20 daily active users within 2 months of launch

### **1.4 Scope Summary**

**In Scope (Phase 1):**

- Multi-site architecture with multiple research stations

- Revenue management for harvest sales and product testing services

- Chart of Accounts management with site-specific COA structures

- Activity-based budgeting and expense tracking

- Multi-program payment split with proportional cost allocation

- Program lifecycle management with archival capabilities

- Profit & Loss statements per program and per site

- System administrator panel for configuration and maintenance

- All features from original scope: budget, payment requests, settlement, subsidi

**Out of Scope (Future Phases):**

- Balance Sheet reporting

- Full Cash Flow Statement (optional reporting only)

- Inventory management for harvested goods

- Customer relationship management (CRM) features

- Integration with external accounting software

- Payroll management

# **2. Business Context & Problem Statement**

## **2.1 Organizational Context**

The Research and Knowledge department operates two agricultural research stations in Central Java:

- Klaten Research Station: Focus on rice, vegetable crops, and soil analysis

- Magelang Research Station: Focus on corn, specialty crops, and product testing services

Each station manages multiple concurrent research programs with both revenue-generating activities (harvest sales, testing services) and operational expenses. The organization operates on an annual budget cycle with a total allocation of approximately 3 billion rupiah across both sites.

## **2.2 Current State Analysis**

### **Current Tools & Processes**

- Excel Online: Primary tool for budget tracking, payment requests, and financial records

- WhatsApp: Communication channel for approval requests and notifications

- Physical Documents: Paper-based receipts and settlement documentation

- Manual Reconciliation: Monthly manual reconciliation between sites

### **Operational Volumes**

- 20+ daily active users across both research stations

- 20+ payment requests per day processed in two batches (10 AM, 2 PM)

- Multiple research programs running concurrently (avg 4 programs per Research Officer)

- Recurring revenue from harvest cycles (especially crops like chili with bi-weekly harvests)

- Ad-hoc revenue from product testing services for sister companies and external clients

## **2.3 Critical Pain Points**

### **Lack of Revenue Visibility**

No integrated system to track both revenue and expenses at program level makes it impossible to calculate true program profitability. Managers cannot identify which research programs generate positive returns versus which require subsidy.

### **Multi-Site Complexity**

Separate Excel files for Klaten and Magelang with manual consolidation creates version control issues, delayed reporting, and inability to compare performance across sites in real-time.

### **Split Cost Allocation**

When shared resources are purchased for multiple programs (e.g., fertilizer for 3 programs), manual split calculation in Excel is error-prone and difficult to audit. No systematic way to ensure split amounts add up correctly.

### **Activity-Level Tracking**

Research programs consist of multiple activities (land preparation, planting, harvest, analysis) but Excel provides no structure to track costs per activity, making it difficult to optimize resource allocation.

### **Historical Data Management**

Completed programs remain in active Excel sheets cluttering views and slowing performance. No systematic archival process means historical data for audit and trend analysis is difficult to access.

### **Inconsistent COA**

Each site uses slightly different account codes and naming conventions in Excel. This makes consolidated financial reporting extremely time-consuming and prone to mapping errors.

# **3. Project Goals & Objectives**

## **3.1 Strategic Goals**

### **Goal 1: Unified Financial Management**

Integrate revenue and expense tracking in a single system enabling true program profitability analysis. Eliminate Excel and WhatsApp for all financial workflows.

### **Goal 2: Multi-Site Excellence**

Provide site-specific financial tracking with consolidated reporting capabilities. Enable cross-site collaboration while maintaining site accountability.

### **Goal 3: Activity-Based Costing**

Enable granular cost tracking at activity level within programs to support data-driven resource allocation and process optimization.

### **Goal 4: Long-term Sustainability**

Build scalable architecture supporting program lifecycle management, historical data archival, and easy year-end closing to serve organizational needs for 5+ years.

## **3.2 Measurable Objectives**

1. Achieve 100% transaction accuracy by eliminating manual data entry errors

2. Reduce payment approval cycle from 3-5 days to same-day (< 8 hours)

3. Enable real-time program P&L visibility within 24 hours of transactions

4. Achieve 100% settlement compliance with 48-hour deadline

5. Generate consolidated multi-site financial reports in < 5 minutes

6. Support 50+ concurrent users without performance degradation

7. Maintain complete audit trail with digital signatures for all transactions

8. Archive completed programs within 30 days of fiscal year-end

9. Reduce administrative overhead by 60% within 6 months of go-live

10. Achieve 90% user adoption rate within 2 months of launch

# **4. User Personas & Roles**

The system supports six distinct user roles plus system administrators with specific responsibilities, access levels, and functional requirements.

## **4.1 AVP of Agri Services (Budget Owner & System Administrator)**

### **Primary Responsibilities**

- Approve annual budgets and major budget revisions across both sites

- Review consolidated financial performance (P&L, budget utilization)

- Approve program completion and archival

- Strategic resource allocation between Klaten and Magelang stations

- System administration: COA configuration, user management, system settings

### **Key User Needs**

- Executive dashboard with multi-site consolidated view and site-specific drill-down

- Program profitability comparison across sites

- Mobile access for approvals while traveling

- Admin panel for emergency overrides and system maintenance

- Dual admin activity monitoring (with Manager)

### **System Access Level**

Full administrative access to all sites, programs, transactions, and system configuration. Can override locks, modify COA, manage users, and archive programs. All admin actions logged with detailed audit trail.

## **4.2 Research and Knowledge Manager (Budget Controller & System Administrator)**

### **Primary Responsibilities**

- Create and manage program budgets with activity breakdown

- Approve payment requests and settlements (first-level approval)

- Monitor budget utilization and program profitability across both sites

- Mark programs as completed and initiate archival process

- System administration: COA maintenance, user role assignment, configuration

### **Key User Needs**

- Dashboard with site filtering (All/Klaten/Magelang toggle)

- Pending approvals queue with aging indicators

- Activity-level cost tracking and variance analysis

- Batch approval for efficiency

- Admin panel access for day-to-day configuration

### **System Access Level**

Full administrative access equivalent to AVP. Can view and manage all programs across sites, configure COA, manage users, and perform system maintenance. Works in dual-admin setup with AVP for mutual oversight.

## **4.3 Research Associate (Supervisor Level)**

### **Primary Responsibilities**

- Collaborate with Manager on budget planning for assigned programs

- Create activities and activity budgets within programs

- Submit payment requests and revenue records for assigned programs

- Lead research programs from planning through completion

- Assign and manage Research Officers to programs

### **Key User Needs**

- Program-centric view showing assigned programs with activity breakdown

- Quick revenue recording for harvest sales and testing services

- Multi-program payment split capability for shared purchases

- Activity performance tracking (budget vs. actual per activity)

### **System Access Level**

Full access to assigned programs including budget view, transaction creation, and activity management. Read-only access to other programs within same site. Can create and modify programs pending Manager approval.

## **4.4 Research Officer (Officer Level)**

### **Primary Responsibilities**

- Submit payment requests for field activities with multi-program split

- Record harvest revenue (quantity, price, buyer, payment date)

- Upload settlement documentation within 48 hours

- Manage up to 4 concurrent programs across activities

- Tag expenses to specific activities within programs

### **Key User Needs**

- Mobile-friendly interface for field use

- Simplified multi-program payment request with split calculator

- Settlement reminder system with deadline countdown

- Budget availability check before submission

- Activity selector for expense tagging

### **System Access Level**

Full access to assigned programs for transaction creation. Read-only access to all other programs within organization. Can view budget, submit requests/revenue, and upload settlements. Cannot modify budgets or activities.

## **4.5 Finance Operation (Head Office)**

### **Primary Responsibilities**

- Process approved payment requests in batches (10 AM and 2 PM)

- Review and approve/reject settlement documentation

- Maintain Chart of Accounts structure for both sites

- Verify revenue records and payment receipts

- Generate financial reports and reconciliations

- Coordinate with Farm Admin on documentation

### **Key User Needs**

- Batch processing queue with multi-site visibility

- Settlement review interface with revision request capability

- COA management tools for account creation and maintenance

- Reconciliation dashboard comparing batches to bank transfers

- Export capabilities for accounting software integration

### **System Access Level**

Full read access to all financial transactions across sites. Authority to process payments, approve settlements, manage COA, and request settlement revisions. Cannot approve budgets or payment requests.

## **4.6 Farm Admin (Multi-Site Field Operations)**

### **Primary Responsibilities**

- Review payment request documentation for both Klaten and Magelang

- Verify settlement completeness before Finance Operation final review

- Manage physical receipt archiving at both sites

- Coordinate field-level documentation with Finance Operation

### **Key User Needs**

- Mobile interface with offline capability for field access

- Document scanning and upload from both sites

- Communication channel with Finance Operation

- Site-filtered view of pending documentation

### **System Access Level**

Access to all site transactions for documentation review. Can view payment requests and settlements across Klaten and Magelang. Cannot approve payments or settlements but can flag issues for Finance Operation.

## **4.7 User Distribution & Volume**

Initial system will support approximately 20 active daily users:

- 1 AVP of Agri Services (both sites)

- 2-3 Research and Knowledge Managers (cross-site)

- 4-5 Research Associates (2-3 per site)

- 8-10 Research Officers (4-5 per site)

- 2-3 Finance Operation staff (HO, covering both sites)

- 1 Farm Admin (multi-site coverage)

System architecture designed to scale to 50+ concurrent users within 12-24 months as research programs expand.

# **5. Functional Requirements**

This section details functional requirements organized by module. Each requirement has a unique identifier for traceability and is categorized by priority (Must Have, Should Have, Could Have).

## **5.1 Multi-Site Architecture**

Foundation module enabling site-specific financial tracking with consolidated reporting across Klaten and Magelang research stations.

|    ID    | Feature                | Description                                                                                                                         |
| :------: | :--------------------- | :---------------------------------------------------------------------------------------------------------------------------------- |
| SITE-001 | Site Master Data       | System maintains master data for each site: Site Code (KLT, MGL), Full Name, Address, Contact Info, and Active Status.              |
| SITE-002 | Site Selection         | All programs, budgets, and transactions must be associated with a specific site at creation. Site cannot be changed after creation. |
| SITE-003 | Site-Specific COA      | Each site has dedicated Chart of Accounts with site prefix (KLT-xxxx, MGL-xxxx). COA structure can differ between sites.            |
| SITE-004 | Cross-Site Visibility  | Managers and AVP can view data across all sites. RO/RA limited to assigned site unless explicitly granted cross-site access.        |
| SITE-005 | Site Filtering         | All dashboards and reports support site filtering: All Sites, Klaten Only, or Magelang Only with persistent filter preferences.     |
| SITE-006 | Consolidated Reporting | System generates consolidated reports across sites while maintaining site-level detail for drill-down analysis.                     |
| SITE-007 | Cross-Site Assignment  | RO or RA from Klaten can be assigned to Magelang program and vice versa with explicit permission grant.                             |
| SITE-008 | Site Performance       | Dashboard displays comparative performance metrics between sites: budget utilization, revenue, expenses, net income.                |
| SITE-009 | Site Administration    | System administrators can add new sites, modify site details, or deactivate sites with proper approval workflow.                    |

## **5.2 Chart of Accounts (COA) Management**

Comprehensive COA management enabling site-specific financial categorization with standardized account structure for revenue and expense tracking.

### **COA Structure & Format**

|   ID    | Feature            | Description                                                                                                                    |
| :-----: | :----------------- | :----------------------------------------------------------------------------------------------------------------------------- |
| COA-001 | Account Format     | Standard format: {SITE}-{CATEGORY}-{CODE} e.g., KLT-4-1100 for Klaten Harvest Revenue. Supports up to 3 levels of hierarchy.   |
| COA-002 | Account Categories | System supports Revenue (4xxx) and Expense (5xxx) categories. Optional support for Assets/Liabilities/Equity in future phases. |
| COA-003 | Site Prefix        | Account codes must include site prefix (KLT- or MGL-). System prevents duplicate account codes across sites.                   |
| COA-004 | Account Naming     | Each account has Code, Full Name, Short Description, Account Type (Revenue/Expense), and Active Status.                        |
| COA-005 | Account Hierarchy  | Accounts organized hierarchically: Main Category > Sub-Category > Detail Account. Support parent-child relationships.          |

### **COA Administration**

|   ID    | Feature            | Description                                                                                                                                       |
| :-----: | :----------------- | :------------------------------------------------------------------------------------------------------------------------------------------------ |
| COA-006 | Create Account     | Finance Operation and System Administrators can create new COA accounts with validation for unique code, proper formatting, and required fields.  |
| COA-007 | Modify Account     | Account names and descriptions can be modified. Account codes and types locked after first transaction to maintain audit integrity.               |
| COA-008 | Deactivate Account | Accounts can be deactivated (not deleted) to prevent new transactions while preserving historical data. Show warning if account has transactions. |
| COA-009 | Account Templates  | System provides standard COA templates for common agricultural research accounts (Fertilizer, Seeds, Labor, Harvest Revenue, Testing Revenue).    |
| COA-010 | Bulk Import        | Support CSV import for creating multiple accounts simultaneously with validation and error reporting.                                             |

### **COA Usage & Validation**

|   ID    | Feature           | Description                                                                                                                         |
| :-----: | :---------------- | :---------------------------------------------------------------------------------------------------------------------------------- |
| COA-011 | Mandatory Mapping | All payment requests and revenue records must be mapped to valid active COA account. System validates account exists and is active. |
| COA-012 | Site Validation   | System validates transaction site matches COA account site. Cannot use KLT account for MGL transaction and vice versa.              |
| COA-013 | Account Dropdown  | Transaction entry forms show filteredCOA accounts: site-specific, active only, organized by category, with search/autocomplete.     |
| COA-014 | Usage Reporting   | Generate reports showing transaction counts and amounts per COA account for utilization analysis and cleanup.                       |
| COA-015 | Account History   | View complete transaction history for any COA account with date range filtering and export capability.                              |

## **5.3 Revenue Management**

Comprehensive revenue tracking for harvest sales and product testing services with proper COA mapping and program linkage for P&L calculation.

### **5.3.1 Harvest Revenue Management**

Track recurring harvest revenue from agricultural research programs with quantity, pricing, and buyer information.

|   ID    | Feature            | Description                                                                                                                                                     |
| :-----: | :----------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| REV-001 | Harvest Recording  | RO records harvest with: Program, Harvest Date, Crop Type, Quantity (kg), Price per Unit, Total Revenue, Buyer Name, Payment Method, Payment Date, COA Account. |
| REV-002 | Harvest Cycle      | System tracks harvest cycle number for recurring harvests (Harvest #1, #2, #3 from same program) enabling cycle performance analysis.                           |
| REV-003 | Variable Pricing   | Price per unit can vary by harvest to reflect market fluctuations. System calculates total revenue automatically from quantity x price.                         |
| REV-004 | Buyer Master Data  | Maintain buyer directory with Name, Contact, Payment Terms. Dropdown selection for harvest recording with option to add new buyer.                              |
| REV-005 | Payment Validation | Payment method restricted to Cash or Bank Transfer (no credit terms). Payment date must be harvest date or later.                                               |
| REV-006 | Manager Review     | Harvest revenue auto-posted upon RO submission but flagged for Manager review. Manager can request corrections within 48 hours.                                 |
| REV-007 | Revenue Correction | Finance Operation can modify harvest records if RO made data entry error. All modifications logged in audit trail with reason.                                  |
| REV-008 | Harvest Schedule   | For recurring crops, system can track planned harvest schedule vs. actual with variance alerts for program management.                                          |

### **5.3.2 Product Testing Service Revenue**

Track revenue from research testing services provided to sister companies and external clients.

|   ID    | Feature           | Description                                                                                                                                                               |
| :-----: | :---------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| REV-009 | Service Recording | RA Product Testing records service with: Client Name, Service Type, Service Description, Contract Value, Start Date, Completion Date, Payment Received Date, COA Account. |
| REV-010 | Client Types      | System distinguishes between Sister Company (no formal contract required) and External Client (contract expected). Different workflows based on client type.              |
| REV-011 | Site Assignment   | Service revenue assigned to site where RA who performed service is based. RA Klaten service revenue goes to KLT COA account.                                              |
| REV-012 | Payment Terms     | Standard payment term is upon completion. System allows recording service first, payment later with payment pending status.                                               |
| REV-013 | Service Linking   | Optional linkage between product testing service and related research program if applicable for integrated cost-benefit analysis.                                         |
| REV-014 | Manager Approval  | Manager must approve product testing service revenue records before posting to ensure pricing and terms are appropriate.                                                  |

### **5.3.3 Revenue Reporting & Analysis**

|   ID    | Feature             | Description                                                                                                                      |
| :-----: | :------------------ | :------------------------------------------------------------------------------------------------------------------------------- |
| REV-015 | Revenue Dashboard   | Dashboard shows total revenue by: Site, Program, Revenue Type (Harvest/Testing), Time Period with YTD comparisons.               |
| REV-016 | Revenue by Program  | Program view displays all revenue streams associated with program for P&L calculation: harvest cycles, related testing services. |
| REV-017 | COA Revenue Report  | Generate reports showing revenue by COA account, comparing Klaten vs. Magelang, with drill-down to transaction detail.           |
| REV-018 | Buyer Analysis      | Report showing revenue by buyer, repeat purchase frequency, average transaction size for relationship management.                |
| REV-019 | Export Revenue Data | Export revenue transactions to Excel, PDF, CSV with selectable fields and date ranges for external reporting.                    |

## **5.4 Activity Management & Sub-Budgeting**

Enable activity-based cost tracking within programs with hierarchical budgeting at activity level for granular financial control.

|   ID    | Feature             | Description                                                                                                                              |
| :-----: | :------------------ | :--------------------------------------------------------------------------------------------------------------------------------------- |
| ACT-001 | Activity Creation   | RA and RO can create activities within assigned programs: Activity Name, Description, Planned Start/End Date, Budget Allocation, Status. |
| ACT-002 | Activity Hierarchy  | Activities organized under programs: Program > Activity. Simple two-level hierarchy for clarity.                                         |
| ACT-003 | Activity Budget     | Each activity has dedicated budget allocation. Sum of activity budgets must not exceed program total budget with validation.             |
| ACT-004 | Flexible Allocation | Activity budgets are guidelines. System allows overruns at activity level as long as program total is not exceeded.                      |
| ACT-005 | Expense Tagging     | All payment requests must be tagged to specific activity within program. Multi-program requests tag to multiple activities.              |
| ACT-006 | Activity Status     | Activities have status: Planned, Active, Completed, Cancelled. Status affects whether new expenses can be tagged.                        |
| ACT-007 | Activity Reporting  | Generate activity-level cost reports showing Budget vs. Actual, Variance, Percent Complete for performance analysis.                     |
| ACT-008 | Activity Timeline   | Visual timeline showing activities along program duration with budget consumption over time.                                             |

## **5.5 Multi-Program Payment Split**

Advanced capability to allocate single payment request across multiple programs and activities with validation and proportional settlement.

|    ID     | Feature               | Description                                                                                                                          |
| :-------: | :-------------------- | :----------------------------------------------------------------------------------------------------------------------------------- |
| SPLIT-001 | Split Entry Interface | Payment request form allows adding multiple program-activity splits. Each split includes: Program, Activity, Amount, COA Account.    |
| SPLIT-002 | Split Validation      | System validates sum of split amounts equals total request amount. Error message if mismatch prevents submission.                    |
| SPLIT-003 | Auto-calculate        | Option to auto-calculate final split entry to balance total. User can override if needed.                                            |
| SPLIT-004 | Visual Split Summary  | Display split breakdown in table format showing Program, Activity, Percentage, Amount before submission for verification.            |
| SPLIT-005 | Cross-Site Split      | Single payment request can split across programs from different sites (e.g., KLT program + MGL program).                             |
| SPLIT-006 | Budget Validation     | For each split entry, validate that program-activity has sufficient available budget before allowing submission.                     |
| SPLIT-007 | Settlement Split      | Settlement for multi-program request shows split breakdown. If actual < request, surplus distributed proportionally across programs. |
| SPLIT-008 | Approval Routing      | Multi-program request routed to Manager for approval (single approval covers all splits). No separate approvals per program.         |
| SPLIT-009 | Split Reporting       | Transaction reports show split details. Program-level reports aggregate all split amounts correctly.                                 |
| SPLIT-010 | Split Modification    | Before Manager approval, requester can modify split allocations. After approval, splits are locked.                                  |

## **5.6 Program Lifecycle & Archive Management**

Complete program lifecycle from creation through completion to archival with year-end management and historical data retention.

### **Program Status Lifecycle**

|    ID    | Feature              | Description                                                                                                                            |
| :------: | :------------------- | :------------------------------------------------------------------------------------------------------------------------------------- |
| LIFE-001 | Program Status       | Programs progress through statuses: Draft > Active > Completed > Archived. Status determines allowed operations.                       |
| LIFE-002 | Draft Programs       | Draft programs created during budget planning. No transactions allowed until Manager submits for AVP approval.                         |
| LIFE-003 | Activation           | Program activates upon AVP budget approval. Status changes to Active and budget becomes available for spending.                        |
| LIFE-004 | Mark Completed       | Manager marks program Completed when all activities finished. System validates: all settlements done, final research report submitted. |
| LIFE-005 | Completion Checklist | Before marking complete, system checks: (1) All payment requests settled, (2) No pending transactions, (3) Research report uploaded.   |
| LIFE-006 | AVP Approval         | Program completion requires AVP approval. AVP reviews program deliverables and financial close before approving.                       |
| LIFE-007 | Budget Surplus       | Upon completion, any remaining budget returned to global pool. Surplus amount displayed during completion approval.                    |

### **Archive Management**

|    ID    | Feature              | Description                                                                                                                         |
| :------: | :------------------- | :---------------------------------------------------------------------------------------------------------------------------------- |
| LIFE-008 | Archive Process      | Completed programs can be archived by Manager or AVP. Archive action hides program from default views but preserves all data.       |
| LIFE-009 | Archive Confirmation | Archive action requires confirmation dialog explaining implications: hidden from active views, no new transactions, data preserved. |
| LIFE-010 | Archive Reason       | User must provide reason for archiving (free text). Common reasons: fiscal year-end, program completed, long-term storage.          |
| LIFE-011 | Default View         | Archived programs hidden from default lists and dashboards. Checkbox Show Archived Programs enables visibility.                     |
| LIFE-012 | Unarchive            | Manager or AVP can unarchive programs if needed for review or corrections. Unarchive action logged with reason.                     |
| LIFE-013 | Data Retention       | Archived programs retained for 5 years. After 5 years, data moved to cold storage or permanently archived per policy.               |
| LIFE-014 | Archive Filtering    | All report and dashboard interfaces support Archive Status filter: Active Only, Archived Only, or All Programs.                     |

### **Fiscal Year Management**

|    ID    | Feature              | Description                                                                                                                       |
| :------: | :------------------- | :-------------------------------------------------------------------------------------------------------------------------------- |
| LIFE-015 | Fiscal Year          | Programs associated with fiscal year (e.g., 2026). Annual budget of 3 billion applies to each fiscal year.                        |
| LIFE-016 | Year-End Process     | At fiscal year-end, System Administrators can run year-end closing process prompting to complete or archive active programs.      |
| LIFE-017 | Multi-Year Programs  | Programs spanning multiple years supported. Budget allocated annually (e.g., 2026: 100M, 2027: 120M) with year-specific tracking. |
| LIFE-018 | Carryover Programs   | Programs active across year boundary automatically continue into new year. No action needed unless budget requires renewal.       |
| LIFE-019 | Historical Reporting | Generate historical reports for any past fiscal year showing programs, revenue, expenses, and net results.                        |

## **5.7 System Administrator Panel**

Comprehensive administrative interface for Manager and AVP to configure system, manage users, maintain master data, and perform emergency overrides with full audit trail.

### **User & Access Management**

|   ID    | Feature            | Description                                                                                                                            |
| :-----: | :----------------- | :------------------------------------------------------------------------------------------------------------------------------------- |
| ADM-001 | User Creation      | Administrators create users with: Full Name, Email, Role, Site Assignment, Password. Auto-generate temporary password for first login. |
| ADM-002 | Role Assignment    | Assign users to roles: RO, RA, Manager, Finance Operation, Farm Admin, System Administrator. Users can have multiple roles.            |
| ADM-003 | Site Access        | Configure site access per user: Primary Site, Additional Sites (for cross-site access). Determines visible programs.                   |
| ADM-004 | Program Assignment | Administrators can assign/unassign users to specific programs. Changes reflected immediately in user's accessible programs.            |
| ADM-005 | Deactivate Users   | Deactivate user accounts when staff leave. Deactivated users cannot login but historical data preserved with their name.               |
| ADM-006 | Password Reset     | Administrators can reset user passwords. User must change password upon next login for security.                                       |
| ADM-007 | Access Logs        | View complete user access logs: login times, IP addresses, actions performed, logout. Export for security audit.                       |

### **System Configuration**

|   ID    | Feature                | Description                                                                                                                 |
| :-----: | :--------------------- | :-------------------------------------------------------------------------------------------------------------------------- |
| ADM-008 | Global Settings        | Configure: Annual budget ceiling, batch processing times (morning/afternoon), settlement deadline hours, fiscal year dates. |
| ADM-009 | Email Configuration    | Set SMTP server details, notification email templates, from address, email frequency settings.                              |
| ADM-010 | Workflow Config        | Configure approval hierarchies, budget thresholds for multi-level approval, auto-routing rules.                             |
| ADM-011 | Notification Templates | Customize email and in-app notification templates for different event types with merge fields for dynamic content.          |
| ADM-012 | Subsidi Configuration  | Create subsidi types, set monthly pagu amounts, configure eligibility rules by role or individual users.                    |
| ADM-013 | Site Management        | Add new research sites, modify site details, set default COA prefixes, activate/deactivate sites.                           |

### **Emergency Operations & Overrides**

|   ID    | Feature             | Description                                                                                                                                     |
| :-----: | :------------------ | :---------------------------------------------------------------------------------------------------------------------------------------------- |
| ADM-014 | Unlock Budget       | Administrators can unlock locked budgets in emergency situations. Requires confirmation and mandatory reason. Action logged with alert.         |
| ADM-015 | Delete Transactions | Only administrators can delete approved transactions in case of critical errors. Requires detailed justification and creates audit alert.       |
| ADM-016 | Bypass Approval     | In urgent situations, administrators can mark payment request as approved without Manager approval. Triggers immediate notification to Manager. |
| ADM-017 | Modify Locked Data  | Administrators can modify locked settlements or completed programs if errors detected post-closure. All modifications tracked in detail.        |
| ADM-018 | Manual Adjustments  | Create manual journal entries to correct financial discrepancies. Requires detailed explanation and links to related transactions.              |

### **Admin Monitoring & Audit**

|   ID    | Feature                 | Description                                                                                                                             |
| :-----: | :---------------------- | :-------------------------------------------------------------------------------------------------------------------------------------- |
| ADM-019 | Admin Activity Log      | Comprehensive log of all administrator actions: user management, config changes, overrides, deletions with timestamp and justification. |
| ADM-020 | Dual Admin Monitor      | Manager and AVP (two administrators) can view each other's admin activities. Alert if suspicious patterns detected.                     |
| ADM-021 | Critical Action Alerts  | Email alerts sent to both administrators when critical actions performed: delete transaction, unlock budget, bypass approval.           |
| ADM-022 | System Health Dashboard | Admin dashboard shows: active users, system performance, error logs, pending issues, database size, backup status.                      |
| ADM-023 | Backup & Restore        | Administrators can trigger manual backups, view backup history, restore from backup point with proper authorization.                    |

## **5.8 Additional Core Modules (Summary)**

The following modules retain functionality from original scope with enhancements for multi-site and activity-based tracking:

- Budget Management: Enhanced with multi-site budget allocation, activity-level sub-budgets, and automatic surplus return to pool upon program completion

- Payment Request: Extended with multi-program split capability, activity tagging, site-specific COA mapping, and cross-site purchase support

- Settlement: Proportional settlement for multi-program requests, activity-level expense reconciliation, and automated surplus distribution

- Subsidi Management: Unchanged from original scope with monthly pagu tracking and eligibility management

- Digital Signature & Audit: Enhanced audit trail capturing site, program, activity for complete transaction lineage

- Notification System: Site-aware notifications, multi-program request alerts, and admin action notifications

- Dashboard & Reporting: Multi-site filtering, program P&L, activity cost breakdown, consolidated revenue-expense views

Detailed requirements for these modules available in comprehensive requirements matrix (separate document).

# **6. User Flows**

This section illustrates critical user journeys through the multi-site system including revenue recording, multi-program expenses, and program lifecycle.

## **6.1 Record Harvest Revenue Flow**

11. RO completes harvest at Klaten research station (e.g., 500kg chili at 20,000/kg)

12. RO logs into system and navigates to Revenue > Record Harvest

13. RO selects: Program (Chili Trial 2026), Harvest Cycle (Harvest #3 for recurring crop)

14. RO enters: Harvest Date, Quantity (500kg), Price per kg (20,000), Buyer (PT Sayur Segar from dropdown)

15. System auto-calculates total revenue: 10,000,000

16. RO selects COA account: KLT-4-1100 (Klaten Harvest Revenue)

17. RO enters payment details: Transfer, Payment Date, Bank Reference

18. RO submits harvest record - revenue posted immediately to program P&L

19. Manager receives notification of new harvest revenue for review

20. Manager can request correction within 48 hours if data entry error detected

21. After 48 hours, harvest record becomes immutable (only Finance can modify)

22. Revenue immediately reflects in program P&L and site revenue dashboard

## **6.2 Multi-Program Payment Request Flow**

23. RO needs to purchase 50kg fertilizer for multiple programs across sites

24. RO creates payment request with total amount: 10,000,000

25. RO clicks Add Program Split and configures:
    - Split 1: Program Rice Klaten > Activity Land Prep > 4,000,000 > COA KLT-5-1100

    - Split 2: Program Rice Klaten > Activity Planting > 2,000,000 > COA KLT-5-1100

    - Split 3: Program Corn Magelang > Activity Land Prep > 2,500,000 > COA MGL-5-1100

    - Split 4: Program Corn Magelang > Activity Maintenance > 1,500,000 > COA MGL-5-1150

26. System validates: (a) Total splits = 10,000,000 ✓, (b) All programs have sufficient budget ✓

27. RO uploads quotation and submits request

28. Manager receives single approval request covering all 4 splits

29. Manager reviews split allocation and approves entire request

30. Request assigned to next batch (morning 10 AM or afternoon 2 PM)

31. Finance Operation processes payment and marks as Paid

32. Budget deducted from each program-activity according to split amounts

33. RO receives payment and must settle within 48 hours

34. Settlement shows 4-program split breakdown with actual amounts

35. If actual spend is 9M (saved 1M), surplus distributed proportionally: Rice Klaten +600k, Corn Magelang +400k

## **6.3 Program Completion & Archive Flow**

36. Research program completes all planned activities (e.g., Rice Trial Klaten 2026)

37. Manager reviews program status: (a) All payment requests settled ✓, (b) No pending transactions ✓, (c) Research report ready ✓

38. Manager navigates to program details and clicks Mark as Completed

39. System displays completion checklist with validation results

40. System shows budget summary: Allocated 50M, Spent 45M, Revenue 20M, Net -25M, Surplus 5M

41. Manager uploads final research report PDF and confirms completion

42. Completion request routes to AVP for approval

43. AVP reviews program deliverables, financial performance, and research outcomes

44. AVP approves completion - program status changes to Completed

45. Surplus 5M automatically returned to global budget pool for Klaten site

46. Program visible in Completed Programs tab but still accessible for reporting

47. At fiscal year-end (Dec 31, 2026), Manager archives all completed 2026 programs

48. Archive action: (a) Select programs, (b) Enter reason: Fiscal year 2026 closure, (c) Confirm archive

49. Archived programs hidden from default views but accessible via Show Archived filter

50. Historical data preserved for 5 years for audit and trend analysis

51. In 2027, dashboard shows only 2027 active programs providing clean workspace

# **7. Technical Requirements**

## **7.1 Multi-Site Architecture Considerations**

The system employs a single database with site as data attribute rather than separate databases per site. This approach enables:

- Consolidated reporting with simple queries across sites

- Cross-site program assignments without data synchronization

- Unified user management and authentication

- Simpler backup and disaster recovery procedures

- Easier addition of new sites without infrastructure changes

### **Database Design for Multi-Site**

Key tables include site_id column with foreign key to sites table:

- programs: site_id, program_code, name, fiscal_year, status

- coa_accounts: site_id, account_code, account_name, account_type

- transactions: site_id, program_id, activity_id, coa_account_id, amount

- revenue: site_id, program_id, revenue_type, amount, date

- users: primary_site_id, additional_sites[] for cross-site access

All queries filtered by site_id based on user permissions for row-level security.

## **7.2 Implementation Timeline (Updated)**

With expanded scope, timeline extended to 7-8 months with phased delivery:

### **Phase 1A: Foundation (Months 1-2)**

- Month 1: Requirements finalization, database design, multi-site architecture, COA structure

- Month 2: Core modules - authentication, multi-site framework, COA management, user management

### **Phase 1B: Core Financial Features (Months 3-4)**

- Month 3: Budget management, simple payment requests (single program), basic revenue recording

- Month 4: Settlement workflow, activity management, approval workflows

### **Phase 1C: Advanced Features (Months 5-6)**

- Month 5: Multi-program payment split, advanced revenue (harvest cycles, buyers), program lifecycle

- Month 6: Admin panel, archive management, subsidi module, comprehensive dashboards

### **Phase 1D: Testing & Deployment (Months 7-8)**

- Month 7: User acceptance testing, training, data migration, performance optimization

- Month 8: Production deployment, parallel run, stabilization, post-launch support

# **8. Success Metrics & KPIs**

| Metric                   | Baseline (Excel) | Target (6 Months) |
| :----------------------- | :--------------: | :---------------: |
| Data entry errors        |       ~15%       |       < 1%        |
| Approval cycle time      |     3-5 days     |     < 8 hours     |
| Program P&L visibility   |     Monthly      |     Real-time     |
| Settlement compliance    |       ~65%       |       100%        |
| Cross-site consolidation |     2-3 days     |    < 5 minutes    |
| User adoption rate       |    N/A (new)     |        90%        |

# **9. Appendix**

## **9.1 Glossary**

| Term                | Definition                                                                                                 |
| :------------------ | :--------------------------------------------------------------------------------------------------------- |
| COA                 | Chart of Accounts - structured list of financial accounts for categorizing transactions                    |
| Activity            | Distinct phase or work component within research program (e.g., Land Preparation, Planting)                |
| Multi-Program Split | Allocating single payment request across multiple programs and activities with defined proportions         |
| P&L                 | Profit and Loss Statement - financial report showing revenue minus expenses equals net income              |
| Archive             | Process of hiding completed programs from default views while preserving all data for historical reference |
| Harvest Cycle       | Sequential number for recurring harvests from same program enabling cycle-by-cycle performance tracking    |

**_--- End of Product Requirements Document ---_**

_Multi-Site Finance & Accounting System_
