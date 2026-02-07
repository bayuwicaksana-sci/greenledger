**PRODUCT REQUIREMENTS DOCUMENT**

**Fiscal Year Management System**

Research Operations & Budget Control Platform

# Table of Contents

**1\. Executive Summary**

**2\. Product Overview**

2.1 Problem Statement

2.2 Solution Overview

2.3 Success Criteria

**3\. User Personas & Stakeholders**

**4\. Core Functional Requirements**

4.1 Fiscal Year Management

4.2 Budget Allocation & Tracking

4.3 Research Program Management

4.4 Purchase Requisition (PR) Workflow

4.5 Purchase Order (PO) Management

4.6 Approval Workflow Engine

4.7 Document Management System

4.8 Reporting & Analytics

**5\. Technical Architecture**

5.1 System Architecture

5.2 Database Schema

5.3 API Specifications

**6\. UI/UX Specifications**

**7\. Security & Compliance**

**8\. Implementation Roadmap**

**9\. Appendices**

# 1\. Executive Summary

This Product Requirements Document (PRD) defines the comprehensive specifications for developing a Fiscal Year Management System tailored for Research Operations in the Agroindustry sector. The system addresses critical challenges in managing 45-60 concurrent research topics with complex financial tracking, procurement workflows, and hierarchical approval processes.

### Key Objectives

- Establish centralized fiscal year container for all research financial activities
- Implement real-time budget tracking with commitment vs. realization visibility
- Automate Purchase Requisition (PR) to Purchase Order (PO) workflow
- Enable hierarchical approval chain with full audit trail
- Provide comprehensive dashboard for budget health monitoring
- Ensure compliance-ready documentation and reporting

### Business Impact

**Time Savings:** Reduce manual budget tracking from 10 hours/week to < 1 hour

**Cost Control:** Prevent budget overruns through real-time availability checking

**Audit Compliance:** Achieve 100% documentation completeness for external audits

**Decision Speed:** Enable instant approval/rejection decisions with live data

# 2\. Product Overview

## 2.1 Problem Statement

Research Managers in agroindustry face operational chaos when managing multiple concurrent research projects due to:

- Manual budget tracking across 45-60 research topics leads to inaccurate spending visibility
- PR/PO processes rely on email-based approvals, causing delays and lost documentation
- No centralized system to check budget availability before approving expenditures
- Critical financial documents (PRs, POs, invoices) scattered across email and local drives
- Inability to generate real-time reports for management or audit purposes
- Year-end closing requires 2-3 weeks of manual reconciliation work

## 2.2 Solution Overview

The Fiscal Year Management System provides a unified platform that:

- Creates fiscal year containers as master budget envelopes for all research activities
- Tracks budget allocation, commitment (approved PRs), realization (paid POs), and available balance
- Implements automated PR-to-PO workflow with configurable approval chains
- Centralizes all financial documents with intelligent linking and version control
- Delivers real-time dashboards showing budget health, utilization rates, and alerts
- Generates compliance-ready reports for audits, management reviews, and forecasting

## 2.3 Success Criteria

| **Metric**                | **Current State**   | **Target State** |
| ------------------------- | ------------------- | ---------------- |
| Budget Tracking Accuracy  | 70% (manual errors) | 99% (automated)  |
| Approval Cycle Time       | 5-7 days average    | 2-3 days average |
| Document Retrieval Time   | 30-60 minutes       | < 2 minutes      |
| Year-End Closing Duration | 2-3 weeks           | 3-5 days         |
| Audit Compliance Score    | 75%                 | 95%+             |

# 3\. User Personas & Stakeholders

## Research Manager

### Responsibilities

- Oversees 45-60 research topics annually
- Approves budget allocations per research program
- Reviews and approves Purchase Requisitions from research teams
- Monitors overall fiscal year budget health
- Generates reports for senior management

### Pain Points

- Cannot quickly determine if budget is available for new PR requests
- Spends hours manually consolidating spending data from multiple sources
- Frequent budget overruns discovered only at month-end
- Difficulty tracking which PRs are pending vs. approved

### Success Needs

- One-click budget availability check before approving PRs
- Real-time dashboard showing budget utilization across all programs
- Automated alerts when programs exceed 80% budget utilization
- Instant access to PR/PO history for any research topic

## Lead Researcher / Principal Investigator

### Responsibilities

- Conducts 3-5 research topics simultaneously
- Creates Purchase Requisitions for materials, equipment, services
- Manages allocated budget for assigned research programs
- Submits spending justifications and progress reports

### Pain Points

- Unclear how much budget remains for their research topics
- PR approval status unknown - have to follow up via email/phone
- Past PR/PO documents hard to find for reference
- No visibility into why PR was rejected or delayed

### Success Needs

- Self-service portal to view remaining budget for their topics
- Real-time PR status tracking (draft, submitted, approved, rejected)
- Ability to attach supporting documents to PR requests
- Notification when PR moves through approval stages

## Finance Officer

### Responsibilities

- Validates budget availability before final PR approval
- Converts approved PRs into Purchase Orders
- Processes vendor invoices and payments
- Reconciles spending vs. budget at month/year end
- Prepares financial reports for audits

### Pain Points

- PRs arrive without clear budget code allocation
- Manual checking if requested amount exceeds available budget
- Difficulty tracking committed vs. actual spending
- Missing invoices or PO documents during audit preparation

### Success Needs

- Automated budget validation before PR reaches finance desk
- Clear linking between PR → PO → Invoice → Payment
- Centralized document repository with full audit trail
- One-click export of spending reports by fiscal year/program/category

## Senior Management / Director

### Responsibilities

- Sets annual research budget allocations
- Approves budget reallocations between programs
- Reviews quarterly spending performance
- Makes strategic decisions on research portfolio

### Pain Points

- Receives budget reports weeks after period ends
- No visibility into pipeline of committed spending
- Difficult to compare performance across research programs
- Cannot quickly assess impact of budget cuts/additions

### Success Needs

- Executive dashboard with high-level KPIs (utilization, variance, burn rate)
- Drill-down capability from summary to program/topic details
- Scenario analysis tools (e.g., "what if we cut Program X by 20%?")
- Mobile-friendly view for on-the-go monitoring

## Procurement Administrator

### Responsibilities

- Receives approved PRs and initiates PO creation
- Coordinates with vendors for quotes and deliveries
- Updates PO status (ordered, received, invoiced)
- Maintains vendor master data

### Pain Points

- Handwritten or poorly formatted PRs delay processing
- No standard template for PR submissions
- Frequent back-and-forth to clarify PR details
- Difficult to track PO delivery status

### Success Needs

- Structured PR form with mandatory fields (item, quantity, spec, urgency)
- Automatic routing of approved PRs to procurement queue
- PO status tracking (draft, sent to vendor, delivered, invoiced)
- Integration with vendor communication (future enhancement)

# 4\. Core Functional Requirements

## 4.1 Fiscal Year Management

FR-FY-001: Create Fiscal Year

**Description:** System shall allow authorized users (Finance/Admin) to create new fiscal year records.

**Input Fields:**

- Year (e.g., 2026) - numeric, 4 digits
- Start Date (default: Jan 1, YYYY)
- End Date (default: Dec 31, YYYY)
- Total Budget Allocated - currency, mandatory
- Budget Freeze Date - date when no new PRs accepted
- Status - dropdown: Planning / Active / Closed / Audited

**Business Rules:**

- No overlapping fiscal years (start/end dates cannot conflict)
- Only one fiscal year can have "Active" status at a time
- Total Budget must be > 0
- Budget Freeze Date must be between Start and End Date
- System auto-sets Status to "Active" on Start Date at 00:00

**Validation:**

- Year must be unique (no duplicate fiscal years)
- End Date must be after Start Date
- Cannot delete fiscal year if it has associated programs/transactions
- Budget amount must not exceed organizational limit (if configured)

FR-FY-002: View Fiscal Year Dashboard

**Description:** System shall display comprehensive fiscal year overview with real-time financial metrics.

**Header Section:**

- Fiscal Year name (e.g., "Fiscal Year 2026")
- Date range (Jan 1 - Dec 31, 2026)
- Status badge with color coding (Active=Green, Planning=Blue, Closed=Gray)
- Days remaining until Budget Freeze Date

**Budget Overview Panel:**

- Total Allocated Budget - large, prominent display
- Committed Amount - sum of all approved PRs
- Realized Amount - sum of all paid POs/invoices
- Available Balance - calculated as: Allocated - Committed
- Utilization Rate - progress bar showing Committed/Allocated %
- Visual alerts if utilization > 85% (yellow) or > 95% (red)

**Research Portfolio Summary:**

- Total Programs count
- Total Research Topics count (Active / Completed / On Hold)
- Quick links to view program lists

**Procurement Statistics:**

- Total PRs (count and value) with breakdown: Pending / Approved / Rejected
- Total POs (count and value) with breakdown: Draft / Issued / Completed
- Average PR Approval Time (in days)
- Top 5 Programs by spending

**Alerts & Notifications:**

- Programs exceeding budget threshold (e.g., > 90% utilized)
- Pending PRs awaiting approval > 5 days
- Unreconciled invoices or discrepancies
- Budget freeze approaching (< 30 days warning)

## 4.2 Budget Allocation & Tracking

FR-BA-001: Allocate Budget to Research Programs

**Description:** System shall enable Research Managers to distribute fiscal year budget across research programs.

**Functionality:**

- Select fiscal year from dropdown
- Create/select Research Program (program name, code, description)
- Enter allocated budget amount for the program
- Set budget allocation date and justification
- System auto-validates: sum of program allocations ≤ fiscal year total budget
- Support budget reallocation (move budget from Program A to Program B)

**Constraints:**

- Cannot allocate more budget than fiscal year available balance
- Budget reallocation requires justification and approval (configurable)
- System maintains full audit trail of all allocation/reallocation actions
- Once fiscal year is "Closed", budget allocations are locked

FR-BA-002: Track Budget Commitment vs. Realization

**Description:** System shall differentiate between committed budget (approved PRs) and realized spending (paid POs).

**Commitment Tracking:**

- When PR is approved → add PR amount to "Committed" bucket
- When PR is rejected/cancelled → remove from "Committed"
- Committed budget is "reserved" but not yet spent
- Available Balance = Allocated - Committed (not Allocated - Realized)

**Realization Tracking:**

- When PO is issued → amount moves from "Committed" to "Realized"
- When invoice is paid → realization is confirmed
- Realization represents actual cash outflow
- Gap between Committed and Realized = outstanding obligations

**Display Requirements:**

- All three metrics (Allocated, Committed, Realized) visible simultaneously
- Visual representation: stacked bar chart or waterfall diagram
- Ability to drill down: click "Committed" to see list of approved PRs
- Export capability for external reporting (Excel, PDF)

## 4.3 Research Program Management

FR-RP-001: Create and Manage Research Programs

**Description:** System shall provide structured management of research programs and their associated topics.

**Program Creation:**

- Program Code - unique identifier (e.g., "AGR-SOIL-2026")
- Program Name - descriptive title
- Program Description - objectives and scope
- Program Manager - assigned user (Research Manager or Lead Researcher)
- Linked Fiscal Year - select from active fiscal years
- Allocated Budget - auto-synced from budget allocation module
- Start Date and End Date - program duration
- Status - dropdown: Planning / Active / Completed / On Hold / Cancelled

**Research Topics:**

- Each Program can contain multiple Research Topics (1:many relationship)
- Topic fields: Topic Code, Title, Lead Researcher, Budget, Timeline
- Topics inherit fiscal year from parent program
- Budget allocation: sum of topic budgets ≤ program budget
- Ability to add/remove topics during program lifecycle

**Budget Roll-up:**

- System auto-calculates total committed/realized at program level
- Program dashboard shows: Allocated, Committed, Realized for all topics combined
- Red flag if any topic exceeds its allocated budget
- Notification to Program Manager if overall program exceeds 80% utilization

FR-RP-002: Program Budget Health Indicators

**Description:** System shall provide visual indicators of program financial health status.

**Health Score Calculation:**

- Green: Budget utilization 0-70% (healthy)
- Yellow: Budget utilization 71-90% (caution)
- Red: Budget utilization 91-100% (critical)
- Display as color-coded badge on program list and detail pages

**Alert Triggers:**

- Email notification to Program Manager when status changes from Green to Yellow
- Daily digest to Research Manager listing all programs in Red status
- Dashboard widget showing count of programs in each health category
- Ability to set custom thresholds per program (optional override)

## 4.4 Purchase Requisition (PR) Workflow

FR-PR-001: Create Purchase Requisition

**Description:** System shall provide standardized form for creating purchase requisitions.

**Mandatory Fields:**

- PR Number - auto-generated (format: PR-YYYY-NNNN)
- Fiscal Year - auto-filled based on current active fiscal year
- Research Program - dropdown of programs user has access to
- Research Topic - filtered dropdown based on selected program
- Requestor - auto-filled with logged-in user details
- Request Date - auto-filled with current date
- Item Description - text area (what is being purchased)
- Quantity - numeric
- Unit Price - currency
- Total Amount - auto-calculated (Quantity × Unit Price)
- Budget Category - dropdown: Materials, Equipment, Services, Personnel, Others
- Urgency Level - dropdown: Normal, High, Critical
- Justification - text area explaining why purchase is needed

**Optional Fields:**

- Preferred Vendor - suggest vendor name
- Technical Specifications - attach spec sheet or document
- Delivery Location - address or site code
- Expected Delivery Date

**Attachments:**

- Support multiple file uploads (PDF, JPG, PNG, DOCX, XLSX)
- Max file size: 10MB per file, max 5 files per PR
- Display attached files as clickable links with file name and size
- Auto-link to central document repository

**Validation Rules:**

- Total Amount must be > 0
- Cannot submit PR if Program has 0 available budget
- Warning (not blocking) if PR amount > 50% of program remaining budget
- Justification must be at least 20 characters
- Cannot select closed/completed programs

FR-PR-002: PR Status Tracking

**Description:** System shall maintain clear status lifecycle for each PR.

**Status Values:**

- DRAFT - PR created but not submitted (can be edited/deleted)
- SUBMITTED - PR sent for approval (locked from editing)
- PENDING_COORDINATOR - Awaiting coordinator review
- PENDING_MANAGER - Awaiting manager approval
- PENDING_FINANCE - Awaiting finance validation
- APPROVED - PR approved, ready for PO creation
- REJECTED - PR denied (with rejection reason)
- CANCELLED - PR withdrawn by requestor
- CONVERTED_TO_PO - PR has been converted to PO

**Status Transitions:**

- DRAFT → SUBMITTED (by Requestor)
- SUBMITTED → PENDING_COORDINATOR (automatic routing)
- PENDING_COORDINATOR → PENDING_MANAGER or REJECTED
- PENDING_MANAGER → PENDING_FINANCE or REJECTED
- PENDING_FINANCE → APPROVED or REJECTED
- APPROVED → CONVERTED_TO_PO (by Procurement)
- Any status → CANCELLED (by Requestor, before APPROVED)
- REJECTED → DRAFT (reopen for revision)

**Status Display:**

- Color-coded badges: Draft=Gray, Pending=Yellow, Approved=Green, Rejected=Red
- Timeline view showing date/time each status was reached
- Display approver name and comments at each stage
- Email notifications to requestor on status changes

## 4.5 Purchase Order (PO) Management

FR-PO-001: Convert PR to PO

**Description:** System shall enable Procurement team to convert approved PRs into Purchase Orders.

**Conversion Process:**

- PO Number - auto-generated (format: PO-YYYY-NNNN)
- Auto-populate all fields from source PR (item, quantity, amount, program, etc.)
- Add Vendor Information: Vendor Name, Address, Contact, Tax ID
- Add Payment Terms: Net 30, Net 60, Advance Payment, etc.
- Set Expected Delivery Date
- Attach vendor quote or agreement document
- PO creation date and creator user auto-recorded

**Budget Impact:**

- Upon PO creation, amount moves from "Committed" to "Realized" in fiscal year tracking
- Source PR status changes to "CONVERTED_TO_PO"
- System maintains link: PO references original PR number
- If PO amount differs from PR amount (due to negotiation), system flags variance
- Variance requires justification before PO can be issued

**PO Approval (if required):**

- Optional: PO may require final approval before being sent to vendor
- Configurable approval threshold (e.g., POs > \$10K require manager sign-off)
- Approval workflow similar to PR but typically faster (1-2 levels)

FR-PO-002: PO Lifecycle Management

**Description:** System shall track PO from issuance through payment completion.

**PO Status Values:**

- DRAFT - PO being prepared
- ISSUED - PO sent to vendor
- ACKNOWLEDGED - Vendor confirmed receipt
- GOODS_RECEIVED - Items delivered and inspected
- INVOICED - Vendor invoice received
- PAID - Invoice processed and paid
- COMPLETED - PO fully closed
- CANCELLED - PO cancelled before completion

**Status Updates:**

- Procurement team manually updates status or system auto-updates via integrations
- Each status change requires date/timestamp and user who made the change
- Optional: upload delivery receipt when marking GOODS_RECEIVED
- Upload invoice PDF when marking INVOICED
- Reference payment voucher number when marking PAID

**Reconciliation:**

- System compares PO amount vs. Invoice amount
- Flag discrepancies (invoice higher/lower than PO)
- Discrepancy requires Finance approval before payment
- Final paid amount recorded in fiscal year realization

## 4.6 Approval Workflow Engine

FR-AW-001: Configurable Approval Chains

**Description:** System shall support flexible, rule-based approval routing for PRs.

**Approval Rules Setup:**

- Define approval chains by: Amount threshold, Budget category, Urgency, Program
- Example Rule: "PRs &lt; \$5K → Manager only; PRs \$5K-\$50K → Manager + Finance; PRs &gt; \$50K → Manager + Finance + Director"
- Assign approvers by role (e.g., "Research Manager") or specific user
- Support parallel approvals (multiple approvers at same level) or sequential
- Allow escalation if approval pending > X days (configurable)

**Approval Actions:**

- Approve - move to next approval level or mark as APPROVED if final stage
- Reject - send back to requestor with rejection reason (mandatory comment)
- Request Revision - return to requestor for changes before re-submission
- Delegate - assign approval to another user (with justification)

**Approval Interface:**

- Approvers receive email notification with PR summary and action buttons
- Clicking notification opens PR detail page with Approve/Reject buttons
- Display full PR details, attachments, budget availability check
- Show approval history (who approved at each stage and when)
- Mandatory comment box for approvers to explain decision

**SLA Tracking:**

- Set target approval time per level (e.g., 2 business days)
- System tracks elapsed time since PR reached current level
- Send reminder notifications if approval overdue
- Escalate to higher authority if SLA breached (configurable)
- Dashboard showing all overdue approvals for managers

FR-AW-002: Budget Availability Check at Approval

**Description:** System shall automatically verify budget availability during approval process.

**Pre-Approval Check:**

- Before approver sees the PR, system runs budget validation:
- \- Check: Available Balance in Program ≥ PR Amount
- \- If TRUE: show approver "Budget Available: \$X" in green
- \- If FALSE: show approver "INSUFFICIENT BUDGET - Remaining: \$X" in red

**Approval Behavior:**

- If budget available: approver can proceed with Approve/Reject normally
- If insufficient budget: approver sees warning but can still approve with override permission
- Override requires: Admin role or special "Budget Override" permission
- Override approval logged with justification (why exceeding budget is allowed)
- System flags overridden approvals for Finance review

**Budget Reservation:**

- Upon approval, system immediately reserves (commits) the PR amount
- Subsequent PRs for same program see reduced available balance
- If PR is later rejected or cancelled, reservation is released
- Prevents double-spending by ensuring budget is allocated before PO issuance

## 4.7 Document Management System

FR-DM-001: Centralized Document Repository

**Description:** System shall provide unified storage and retrieval for all financial documents.

**Document Types:**

- Purchase Requisitions (PR) - PDF export of PR form
- Purchase Orders (PO) - PDF export with vendor details
- Vendor Quotes/Proposals
- Delivery Receipts / Packing Slips
- Vendor Invoices
- Payment Vouchers
- Budget Allocation Memos
- Email Approvals (optional: auto-capture from email system)
- Supporting Documents (specs, contracts, agreements)

**Folder Structure:**

- Fiscal Year → Program → Topic → PR/PO → Documents
- Example path: /FY-2026/AGR-SOIL-2026/Topic-pH-Analysis/PR-2026-0042/
- Auto-create folders when PR/PO created
- Each PR/PO gets unique subfolder with auto-naming

**Metadata & Indexing:**

- Every document tagged with: Fiscal Year, Program, Topic, PR#, PO#, Document Type
- Upload date, uploaded by user, file size
- Full-text search capability (search document content, not just filenames)
- Filter by date range, program, document type, status

**Version Control:**

- If same document uploaded multiple times, system creates versions
- Display version number and upload timestamp
- Option to view/download any version (current + all historical)
- Mark specific version as "Final" or "Official"

**Access Control:**

- Documents inherit access permissions from parent PR/PO
- Requestor can always view own PR/PO documents
- Approvers can view documents of PRs in their approval queue
- Finance team has read access to all documents
- Admin can grant special permissions for auditors

FR-DM-002: Document Linking & Audit Trail

**Description:** System shall maintain complete chain of custody for all financial transactions.

**Transaction Linking:**

- Every PO links back to source PR
- Every Invoice links to PO it is paying for
- Every Payment links to Invoice being settled
- Visual flowchart showing: PR → PO → Invoice → Payment
- Click any item in chain to view full details and documents

**Change Audit Log:**

- System records every create/update/delete action on PRs and POs
- Log fields: Timestamp, User, Action (created, edited, approved, etc.), Field Changed, Old Value, New Value
- Display audit log as timeline on PR/PO detail page
- Export audit log for compliance reporting

**Email Thread Capture (Optional Enhancement):**

- Auto-capture email approvals if email integration available
- Store email as .eml or PDF in document repository
- Link email to relevant PR approval action

## 4.8 Reporting & Analytics

FR-RA-001: Standard Financial Reports

**Description:** System shall generate predefined reports for management and auditors.

**Fiscal Year Budget Summary:**

- Shows for selected fiscal year: Total Budget, Committed, Realized, Available
- Breakdown by Program (table format)
- Breakdown by Budget Category (pie chart: Materials, Equipment, Services, etc.)
- Trend chart: Monthly spending over fiscal year
- Export to PDF or Excel

**Program Performance Report:**

- Select specific program or all programs
- Table showing: Program Name, Allocated Budget, Committed, Realized, Utilization %
- Highlight programs exceeding 90% utilization
- List top 5 programs by spending
- Include drill-down to topic-level details

**PR/PO Status Report:**

- Filter by fiscal year, program, status, date range
- Show list of PRs with columns: PR#, Date, Requestor, Amount, Status, Days Pending
- Show list of POs with columns: PO#, PR#, Vendor, Amount, Status, Expected Delivery
- Summary counts: Total PRs, Pending, Approved, Rejected, Average approval time
- Export to Excel with all details

**Audit Compliance Report:**

- For selected fiscal year, verify: All PRs have approved status or rejection reason
- All POs have source PR reference
- All paid POs have invoice document attached
- List any exceptions or missing documents
- Generate compliance score (%)

**Spending Forecast Report:**

- Calculate burn rate: Total Realized ÷ Days Elapsed
- Project spending to end of fiscal year: Burn Rate × Days Remaining
- Compare projected total vs. allocated budget
- Flag if projection exceeds budget (early warning)
- Suggest budget reallocation from under-spent programs

FR-RA-002: Custom Dashboards & Visualizations

**Description:** System shall provide interactive dashboards for real-time monitoring.

**Executive Dashboard (for Directors):**

- KPI cards: Total Budget, % Utilized, # Active Programs, # Pending Approvals
- Gauge charts: Budget utilization (Green &lt; 70%, Yellow 70-90%, Red &gt; 90%)
- Bar chart: Top 10 programs by spending
- Line chart: Monthly spending trend vs. budget plan
- Alert panel: Programs in red status, overdue approvals, budget warnings

**Research Manager Dashboard:**

- Program portfolio view: Table or card view of all programs
- Quick filters: Show only Red status, Show only programs I manage
- Pending approvals widget: PRs awaiting my approval (clickable)
- Recent activity feed: Latest PR submissions, approvals, PO creations
- Budget utilization heatmap: Visual grid of programs colored by utilization %

**Researcher Dashboard:**

- My Research Topics: List of topics assigned to me
- Budget remaining per topic (progress bar)
- My PRs: Table of all PRs I created with status
- Quick action: "Create New PR" button
- Notifications: Approvals, rejections, PO updates

**Finance Dashboard:**

- Pending Finance Approvals queue
- PO payment pipeline: POs awaiting invoice, invoices awaiting payment
- Cash flow forecast: Projected payments for next 30/60/90 days
- Budget variance table: Programs with significant over/under spending
- Document compliance checker: List PRs/POs missing required documents

# 5\. Technical Architecture

## 5.1 System Architecture Overview

The Fiscal Year Management System shall be built as a modern web application with three-tier architecture: Presentation Layer (Frontend), Application Layer (Backend API), and Data Layer (Database).

**Presentation Layer (Frontend):**

- Technology Stack: React.js or Vue.js with TypeScript
- UI Component Library: Material-UI, Ant Design, or Tailwind CSS
- State Management: Redux or Vuex
- Responsive design for desktop, tablet, mobile
- Progressive Web App (PWA) capabilities for offline access
- Chart library: Chart.js or Recharts for visualizations

**Application Layer (Backend API):**

- Technology Stack: Node.js with Express.js OR Python with FastAPI/Django
- RESTful API architecture with JSON payloads
- Authentication: JWT (JSON Web Tokens) for stateless auth
- Authorization: Role-Based Access Control (RBAC)
- Business logic layer handling all workflow rules
- Background job processor for email notifications, reports (e.g., Bull Queue, Celery)

**Data Layer:**

- Primary Database: PostgreSQL (relational database)
- Document Storage: AWS S3, Azure Blob Storage, or local file system with DB metadata
- Caching Layer: Redis for session management and performance optimization
- Full-text Search: PostgreSQL full-text search or Elasticsearch (optional)
- Database migrations: Managed via Alembic (Python) or Sequelize/TypeORM (Node.js)

**Integration Layer (Future):**

- Email integration (SMTP for notifications, IMAP for email capture)
- ERP integration (if organization has existing system)
- Accounting system integration (e.g., QuickBooks, SAP)
- Vendor management system integration
- Single Sign-On (SSO) via SAML or OAuth2 (Google, Microsoft)

## 5.2 Database Schema

The following tables form the core database schema. All tables include standard audit fields: created_at, updated_at, created_by, updated_by.

### Table: fiscal_years

| **Column**         | **Data Type** | **Constraints**  | **Description**                |
| ------------------ | ------------- | ---------------- | ------------------------------ |
| id                 | INTEGER       | PRIMARY KEY      | Unique identifier              |
| year               | INTEGER       | UNIQUE, NOT NULL | Fiscal year (e.g., 2026)       |
| start_date         | DATE          | NOT NULL         | FY start date                  |
| end_date           | DATE          | NOT NULL         | FY end date                    |
| total_budget       | DECIMAL(15,2) | NOT NULL         | Total allocated budget         |
| budget_freeze_date | DATE          | NULL             | Date after which no new PRs    |
| status             | VARCHAR(20)   | NOT NULL         | Planning/Active/Closed/Audited |
| description        | TEXT          | NULL             | Notes about this fiscal year   |

### Table: research_programs

| **Column**         | **Data Type** | **Constraints**   | **Description**               |
| ------------------ | ------------- | ----------------- | ----------------------------- |
| id                 | INTEGER       | PRIMARY KEY       | Unique identifier             |
| fiscal_year_id     | INTEGER       | FK → fiscal_years | Linked fiscal year            |
| program_code       | VARCHAR(50)   | UNIQUE, NOT NULL  | e.g., AGR-SOIL-2026           |
| program_name       | VARCHAR(200)  | NOT NULL          | Program title                 |
| description        | TEXT          | NULL              | Program objectives            |
| program_manager_id | INTEGER       | FK → users        | Assigned manager              |
| allocated_budget   | DECIMAL(15,2) | NOT NULL          | Budget for this program       |
| start_date         | DATE          | NOT NULL          | Program start                 |
| end_date           | DATE          | NULL              | Program end (can be ongoing)  |
| status             | VARCHAR(20)   | NOT NULL          | Planning/Active/Completed/... |

### Table: research_topics

| **Column**         | **Data Type** | **Constraints**        | **Description**       |
| ------------------ | ------------- | ---------------------- | --------------------- |
| id                 | INTEGER       | PRIMARY KEY            | Unique identifier     |
| program_id         | INTEGER       | FK → research_programs | Parent program        |
| topic_code         | VARCHAR(50)   | UNIQUE, NOT NULL       | e.g., TOPIC-pH-001    |
| topic_title        | VARCHAR(200)  | NOT NULL               | Topic name            |
| lead_researcher_id | INTEGER       | FK → users             | Lead researcher       |
| allocated_budget   | DECIMAL(15,2) | NOT NULL               | Budget for this topic |
| start_date         | DATE          | NOT NULL               | Topic start           |
| end_date           | DATE          | NULL                   | Topic end             |

### Table: purchase_requisitions

| **Column**        | **Data Type** | **Constraints**        | **Description**         |
| ----------------- | ------------- | ---------------------- | ----------------------- |
| id                | INTEGER       | PRIMARY KEY            | Unique identifier       |
| pr_number         | VARCHAR(50)   | UNIQUE, NOT NULL       | PR-YYYY-NNNN            |
| fiscal_year_id    | INTEGER       | FK → fiscal_years      | Fiscal year             |
| program_id        | INTEGER       | FK → research_programs | Research program        |
| topic_id          | INTEGER       | FK → research_topics   | Research topic          |
| requestor_id      | INTEGER       | FK → users             | Who requested           |
| request_date      | DATE          | NOT NULL               | Date requested          |
| item_description  | TEXT          | NOT NULL               | What is being purchased |
| quantity          | DECIMAL(10,2) | NOT NULL               | Quantity                |
| unit_price        | DECIMAL(15,2) | NOT NULL               | Price per unit          |
| total_amount      | DECIMAL(15,2) | NOT NULL               | Qty × Price             |
| budget_category   | VARCHAR(50)   | NOT NULL               | Materials/Equipment/... |
| urgency           | VARCHAR(20)   | NOT NULL               | Normal/High/Critical    |
| justification     | TEXT          | NOT NULL               | Why needed              |
| preferred_vendor  | VARCHAR(200)  | NULL                   | Vendor suggestion       |
| expected_delivery | DATE          | NULL                   | When needed             |
| status            | VARCHAR(30)   | NOT NULL               | DRAFT/SUBMITTED/...     |

### Table: pr_approvals

| **Column**     | **Data Type** | **Constraints**            | **Description**               |
| -------------- | ------------- | -------------------------- | ----------------------------- |
| id             | INTEGER       | PRIMARY KEY                | Unique identifier             |
| pr_id          | INTEGER       | FK → purchase_requisitions | Related PR                    |
| approver_id    | INTEGER       | FK → users                 | Who approved/rejected         |
| approval_level | INTEGER       | NOT NULL                   | 1=Coordinator, 2=Manager, ... |
| action         | VARCHAR(20)   | NOT NULL                   | APPROVED/REJECTED/...         |
| comments       | TEXT          | NULL                       | Approver notes                |
| action_date    | TIMESTAMP     | NOT NULL                   | When action taken             |

### Table: purchase_orders

| **Column**        | **Data Type** | **Constraints**            | **Description**   |
| ----------------- | ------------- | -------------------------- | ----------------- |
| id                | INTEGER       | PRIMARY KEY                | Unique identifier |
| po_number         | VARCHAR(50)   | UNIQUE, NOT NULL           | PO-YYYY-NNNN      |
| pr_id             | INTEGER       | FK → purchase_requisitions | Source PR         |
| vendor_name       | VARCHAR(200)  | NOT NULL                   | Vendor name       |
| vendor_address    | TEXT          | NULL                       | Vendor address    |
| vendor_contact    | VARCHAR(100)  | NULL                       | Contact info      |
| vendor_tax_id     | VARCHAR(50)   | NULL                       | Tax ID            |
| po_date           | DATE          | NOT NULL                   | PO issue date     |
| total_amount      | DECIMAL(15,2) | NOT NULL                   | PO amount         |
| payment_terms     | VARCHAR(50)   | NULL                       | Net 30, etc.      |
| expected_delivery | DATE          | NULL                       | Delivery date     |
| actual_delivery   | DATE          | NULL                       | When delivered    |
| invoice_number    | VARCHAR(50)   | NULL                       | Vendor invoice #  |
| status            | VARCHAR(30)   | NOT NULL                   | DRAFT/ISSUED/...  |

### Table: documents

| **Column**     | **Data Type** | **Constraints**            | **Description**         |
| -------------- | ------------- | -------------------------- | ----------------------- |
| id             | INTEGER       | PRIMARY KEY                | Unique identifier       |
| fiscal_year_id | INTEGER       | FK → fiscal_years          | Fiscal year             |
| program_id     | INTEGER       | FK → research_programs     | Program (if applicable) |
| pr_id          | INTEGER       | FK → purchase_requisitions | PR (if applicable)      |
| po_id          | INTEGER       | FK → purchase_orders       | PO (if applicable)      |
| document_type  | VARCHAR(50)   | NOT NULL                   | PR/PO/Invoice/...       |
| file_name      | VARCHAR(255)  | NOT NULL                   | Original filename       |
| file_path      | VARCHAR(500)  | NOT NULL                   | Storage path or URL     |
| file_size      | INTEGER       | NULL                       | File size in bytes      |
| mime_type      | VARCHAR(100)  | NULL                       | application/pdf, etc.   |
| version        | INTEGER       | DEFAULT 1                  | Version number          |

### Table: users

| **Column**    | **Data Type** | **Constraints**  | **Description**                |
| ------------- | ------------- | ---------------- | ------------------------------ |
| id            | INTEGER       | PRIMARY KEY      | Unique identifier              |
| username      | VARCHAR(100)  | UNIQUE, NOT NULL | Login username                 |
| email         | VARCHAR(255)  | UNIQUE, NOT NULL | Email address                  |
| full_name     | VARCHAR(200)  | NOT NULL         | Full name                      |
| password_hash | VARCHAR(255)  | NOT NULL         | Hashed password                |
| role          | VARCHAR(50)   | NOT NULL         | Researcher/Manager/Finance/... |
| department    | VARCHAR(100)  | NULL             | Department name                |
| is_active     | BOOLEAN       | DEFAULT TRUE     | Account status                 |
| last_login    | TIMESTAMP     | NULL             | Last login time                |

Additional Tables: approval_rules (for workflow config), budget_transactions (for budget allocation history), notifications (for email queue), audit_log (for change tracking).

## 5.3 API Specifications

The backend API follows RESTful conventions with standard HTTP methods (GET, POST, PUT, DELETE). All endpoints return JSON responses.

### Base URL: /api/v1

### Authentication Endpoints

**POST /auth/login**

Description: User login with username/password

Request: {username, password}

Response: {token, user{id, name, role}}

**POST /auth/logout**

Description: User logout (invalidate token)

Request: {token}

Response: {success: true}

**GET /auth/me**

Description: Get current user profile

Request: Authorization header

Response: {user{id, name, email, role, ...}}

**POST /auth/refresh**

Description: Refresh expired token

Request: {refreshToken}

Response: {newToken}

### Fiscal Year Endpoints

**GET /fiscal-years**

Description: List all fiscal years

Request: Query params: status, year

Response: \[{id, year, start_date, ...}\]

**GET /fiscal-years/:id**

Description: Get single fiscal year details

Request: Path: fiscal_year_id

Response: {id, year, total_budget, committed, realized, available, programs\[...\]}

**POST /fiscal-years**

Description: Create new fiscal year

Request: {year, start_date, end_date, total_budget, ...}

Response: {id, year, ...}

**PUT /fiscal-years/:id**

Description: Update fiscal year

Request: {total_budget, status, ...}

Response: {id, year, ...}

**DELETE /fiscal-years/:id**

Description: Delete fiscal year (if no transactions)

Request: Path: fiscal_year_id

Response: {success: true}

**GET /fiscal-years/:id/dashboard**

Description: Get dashboard data for FY

Request: Path: fiscal_year_id

Response: {budget_summary, programs_summary, procurement_stats, alerts\[...\]}

### Research Program Endpoints

**GET /programs**

Description: List all research programs

Request: Query: fiscal_year_id, status

Response: \[{id, program_code, name, allocated_budget, ...}\]

**GET /programs/:id**

Description: Get program details

Request: Path: program_id

Response: {id, program_code, allocated_budget, committed, realized, topics\[...\]}

**POST /programs**

Description: Create new program

Request: {fiscal_year_id, program_code, name, allocated_budget, ...}

Response: {id, program_code, ...}

**PUT /programs/:id**

Description: Update program

Request: {allocated_budget, status, ...}

Response: {id, ...}

**DELETE /programs/:id**

Description: Delete program (if no PRs)

Request: Path: program_id

Response: {success: true}

### Purchase Requisition Endpoints

**GET /purchase-requisitions**

Description: List PRs with filters

Request: Query: fiscal_year_id, program_id, status, requestor_id

Response: \[{id, pr_number, item_description, total_amount, status, ...}\]

**GET /purchase-requisitions/:id**

Description: Get PR details

Request: Path: pr_id

Response: {id, pr_number, item, qty, price, justification, approvals\[...\], documents\[...\]}

**POST /purchase-requisitions**

Description: Create new PR (draft)

Request: {program_id, topic_id, item_description, quantity, unit_price, ...}

Response: {id, pr_number, status: DRAFT}

**PUT /purchase-requisitions/:id**

Description: Update PR (only if DRAFT)

Request: {item_description, quantity, ...}

Response: {id, pr_number, ...}

**POST /purchase-requisitions/:id/submit**

Description: Submit PR for approval

Request: {pr_id}

Response: {id, status: SUBMITTED, next_approver}

**POST /purchase-requisitions/:id/approve**

Description: Approve PR

Request: {pr_id, comments}

Response: {id, status: PENDING_NEXT or APPROVED}

**POST /purchase-requisitions/:id/reject**

Description: Reject PR

Request: {pr_id, reason}

Response: {id, status: REJECTED}

**POST /purchase-requisitions/:id/cancel**

Description: Cancel PR

Request: {pr_id}

Response: {id, status: CANCELLED}

**DELETE /purchase-requisitions/:id**

Description: Delete PR (only DRAFT)

Request: Path: pr_id

Response: {success: true}

### Purchase Order Endpoints

**GET /purchase-orders**

Description: List POs with filters

Request: Query: fiscal_year_id, pr_id, status

Response: \[{id, po_number, vendor_name, total_amount, status, ...}\]

**GET /purchase-orders/:id**

Description: Get PO details

Request: Path: po_id

Response: {id, po_number, pr{...}, vendor, amount, status, documents\[...\]}

**POST /purchase-orders**

Description: Create PO from approved PR

Request: {pr_id, vendor_name, vendor_address, payment_terms, ...}

Response: {id, po_number, status: DRAFT}

**PUT /purchase-orders/:id**

Description: Update PO

Request: {vendor_name, expected_delivery, ...}

Response: {id, po_number, ...}

**POST /purchase-orders/:id/issue**

Description: Issue PO to vendor

Request: {po_id}

Response: {id, status: ISSUED}

**POST /purchase-orders/:id/update-status**

Description: Update PO status

Request: {po_id, new_status, notes}

Response: {id, status}

### Document Management Endpoints

**GET /documents**

Description: List documents with filters

Request: Query: fiscal_year_id, pr_id, po_id, document_type

Response: \[{id, file_name, document_type, upload_date, ...}\]

**GET /documents/:id**

Description: Get document metadata

Request: Path: document_id

Response: {id, file_name, file_path, mime_type, ...}

**GET /documents/:id/download**

Description: Download document file

Request: Path: document_id

Response: Binary file stream

**POST /documents**

Description: Upload new document

Request: Multipart form: {file, pr_id, po_id, document_type}

Response: {id, file_name, file_path}

**DELETE /documents/:id**

Description: Delete document

Request: Path: document_id

Response: {success: true}

### Reporting Endpoints

**GET /reports/fiscal-year-summary**

Description: Generate FY summary report

Request: Query: fiscal_year_id

Response: {total_budget, committed, realized, breakdown_by_category\[...\]}

**GET /reports/program-performance**

Description: Program performance report

Request: Query: fiscal_year_id, program_id

Response: \[{program, allocated, committed, realized, utilization %}\]

**GET /reports/pr-po-status**

Description: PR/PO status report

Request: Query: fiscal_year_id, date_range, status

Response: \[{pr_number, date, amount, status, approver, ...}\]

**GET /reports/audit-compliance**

Description: Audit compliance report

Request: Query: fiscal_year_id

Response: {total_prs, missing_documents\[\], compliance_score %}

**GET /reports/spending-forecast**

Description: Spending forecast

Request: Query: fiscal_year_id

Response: {burn_rate, projected_total, days_remaining, overspend_risk}

Note: All API endpoints require authentication via JWT token in Authorization header. Error responses follow standard HTTP status codes: 200 (OK), 201 (Created), 400 (Bad Request), 401 (Unauthorized), 403 (Forbidden), 404 (Not Found), 500 (Server Error). Error response format: {error: {code, message, details}}

# 6\. UI/UX Specifications

## 6.1 Design Principles

**Clarity & Simplicity:** Interface must be intuitive for non-technical users. Minimize jargon, use clear labels, provide inline help.

**Efficiency:** Reduce clicks and data entry. Auto-fill fields where possible, provide templates, enable batch operations.

**Consistency:** Use standard UI patterns across all pages. Consistent button placement, color coding, terminology.

**Responsiveness:** Support desktop (primary), tablet, and mobile devices. Adaptive layout, touch-friendly controls.

**Accessibility:** WCAG 2.1 AA compliance. Keyboard navigation, screen reader support, sufficient color contrast.

**Feedback & Validation:** Immediate validation on form inputs. Clear error messages, success confirmations, loading indicators.

## 6.2 Color Coding Standards

| **Color** | **Meaning**                   | **Usage**                                              |
| --------- | ----------------------------- | ------------------------------------------------------ |
| Green     | Healthy / Active / Approved   | Budget utilization <70%, Active status, Approved PRs   |
| Yellow    | Caution / Pending             | Budget utilization 70-90%, Pending approvals, Warnings |
| Red       | Critical / Rejected / Overdue | Budget >90%, Rejected PRs, Overdue approvals           |
| Blue      | Info / Planning               | Planning status, Informational messages, Links         |
| Gray      | Inactive / Closed / Draft     | Closed fiscal years, Draft PRs, Disabled buttons       |
| Orange    | Urgent                        | High/Critical urgency PRs, Immediate action required   |

## 6.3 Key Page Layouts

### Fiscal Year Dashboard (Main Landing Page)

- Page Header: "Fiscal Year 2026" | Status Badge | Quick Actions (Create PR, View Reports)
- Top KPI Row (4 cards): Total Budget | Committed | Realized | Available Balance
- Budget Utilization Section: Progress bar with percentage | Visual alerts if >85%
- Quick Stats Grid: Active Programs | Pending Approvals | Recent Activity
- Alerts Panel: Red-flagged programs, overdue approvals, budget warnings
- Action Buttons: "View All Programs" | "View All PRs" | "Generate Report"

### Purchase Requisition Form

- Breadcrumb: Home > Purchase Requisitions > Create New
- Form Title: "New Purchase Requisition" | Save as Draft button (top right)
- Section 1 - Basic Info: Fiscal Year (auto-filled) | Program (dropdown) | Topic (filtered dropdown)
- Section 2 - Item Details: Item Description (textarea) | Quantity | Unit Price | Total (auto-calculated, read-only)
- Section 3 - Classification: Budget Category (dropdown) | Urgency (radio buttons) | Expected Delivery Date (datepicker)
- Section 4 - Justification: Justification (rich text editor, minimum 20 chars)
- Section 5 - Attachments: Upload files (drag-drop zone) | List uploaded files with delete option
- Section 6 - Vendor (optional): Preferred Vendor | Technical Specs (file upload)
- Budget Availability Check: Display "Available Budget: \$X" with color coding (Green if sufficient, Red if insufficient)
- Action Buttons: "Save as Draft" (gray) | "Submit for Approval" (blue, primary)
- Validation: Inline validation on blur, form-level validation on submit

### Purchase Requisitions List

- Page Header: "Purchase Requisitions" | "Create New PR" button (top right)
- Filters Panel (collapsible): Fiscal Year | Program | Status | Date Range | Requestor | Search by PR# or item
- Results Table Columns: PR Number (link) | Date | Requestor | Program | Item Description (truncated) | Amount | Status Badge | Actions
- Status Filter Pills: All | Draft | Pending | Approved | Rejected (with counts)
- Table Features: Sortable columns | Pagination (25/50/100 per page) | Export to Excel button
- Row Actions: View Details (eye icon) | Edit (if DRAFT) | Cancel (if not APPROVED)
- Bulk Actions: Select multiple PRs | Bulk export | Bulk status update (if applicable)

### Purchase Requisition Detail Page

- Breadcrumb: Home > Purchase Requisitions > PR-2026-0042
- Page Header: "PR-2026-0042" | Status Badge (large) | Action Buttons (contextual: Approve/Reject if pending, Edit if draft)
- Tab Navigation: Details | Approval History | Documents | Audit Log
- Details Tab: All PR fields in read-only format | Budget info (Program budget, Remaining budget) | Requestor info
- Approval History Tab: Timeline view showing each approval stage | Approver name, date, action, comments | Visual progress indicator
- Documents Tab: List of uploaded documents with download links | Upload new document button (if allowed) | Document preview (for PDFs/images)
- Audit Log Tab: Chronological log of all changes | Timestamp | User | Action | Old Value | New Value
- Related Items Section: Show linked PO if PR converted to PO | Show related PRs from same topic
- Comments Section: Internal notes/comments by approvers or requestor | Add new comment box

## 6.4 Navigation Structure

Main Navigation Menu (Sidebar or Top Bar):

- **Dashboard (Home icon)**
- **Fiscal Years (Calendar icon)**
- List Fiscal Years
- Create New Fiscal Year
- **Research Programs (Folder icon)**
- List Programs
- Create New Program
- Manage Research Topics
- **Purchase Requisitions (Cart icon)**
- My PRs
- All PRs (for managers/finance)
- Create New PR
- Pending Approvals (badge with count)
- **Purchase Orders (Document icon)**
- List POs
- Create PO from PR
- PO Pipeline (status board view)
- **Documents (File icon)**
- Document Repository (searchable)
- Upload Document
- **Reports (Chart icon)**
- Fiscal Year Summary
- Program Performance
- PR/PO Status Report
- Audit Compliance
- Spending Forecast
- **Settings (Gear icon) - Admin only**
- User Management
- Approval Workflow Configuration
- Budget Categories
- System Configuration

## 6.5 Responsive Design Breakpoints

| **Device Type** | **Screen Width** | **Layout Adjustments**                                           |
| --------------- | ---------------- | ---------------------------------------------------------------- |
| Desktop         | \> 1024px        | Full sidebar nav, multi-column layouts, all features visible     |
| Tablet          | 768px - 1024px   | Collapsible sidebar, 2-column layouts, condensed tables          |
| Mobile          | < 768px          | Bottom tab nav, single column, simplified forms, swipeable cards |

# 7\. Security & Compliance Requirements

## 7.1 Authentication & Authorization

**User Authentication**

- Username/password authentication with bcrypt hashing (cost factor 12+)
- JWT tokens for session management (access token: 15min, refresh token: 7 days)
- Optional: Multi-factor authentication (MFA) via email OTP or authenticator app
- Password policy: Minimum 8 characters, require uppercase, lowercase, number, special char
- Account lockout after 5 failed login attempts (lockout duration: 15 minutes)
- Password reset via email with time-limited reset token

**Role-Based Access Control (RBAC)**

- Roles: Admin, Research Manager, Lead Researcher, Finance Officer, Procurement, Auditor
- Admin: Full system access, user management, configuration
- Research Manager: View all programs, approve PRs, allocate budgets
- Lead Researcher: Create PRs for assigned topics, view own program budget
- Finance Officer: Approve PRs, create POs, view all financial data
- Procurement: Convert PRs to POs, manage vendor info, update PO status
- Auditor: Read-only access to all data, export reports
- Permissions granular at feature level (e.g., "can_approve_pr", "can_create_fiscal_year")
- Implement permission checks at both API and UI layers

**Data Access Controls**

- Researchers can only view/edit PRs for programs they are assigned to
- Program Managers can view all PRs within their programs
- Finance/Admin can view all PRs system-wide
- Document access restricted based on related PR/PO permissions
- Sensitive fields (e.g., salary data) masked for non-authorized users

## 7.2 Data Security

**Data Encryption**

- HTTPS/TLS 1.3 for all client-server communication
- Database encryption at rest (encrypt sensitive columns: passwords, tax IDs)
- Secure file storage with access controls (S3 bucket policies or equivalent)
- Environment variables for API keys, database credentials (never hardcoded)

**Audit Logging**

- Log all create/update/delete operations on critical entities (PRs, POs, budget)
- Log successful and failed login attempts
- Log privilege escalation or permission override events
- Audit logs immutable (cannot be edited or deleted by non-admin)
- Retain audit logs for minimum 7 years (compliance requirement)
- Daily backup of audit logs to separate storage

**Input Validation & Sanitization**

- Server-side validation for all user inputs
- SQL injection prevention via parameterized queries (ORM usage)
- XSS prevention via input sanitization and output encoding
- File upload validation: Check file type, size, scan for malware (ClamAV or equivalent)
- CSRF protection via tokens on all state-changing operations

## 7.3 Compliance Requirements

- Financial record retention for 7 years (as per tax regulations)
- Audit trail completeness: Every financial transaction traceable from source to payment
- Separation of duties: PR creator cannot be sole approver
- Budget freeze enforcement: No new PRs after freeze date (system-enforced)
- Document integrity: Store hash/checksum of uploaded documents to detect tampering
- User activity monitoring: Track who accessed sensitive financial data
- GDPR compliance (if applicable): Data export, right to deletion, consent management
- Regular security updates: Patch dependencies monthly, vulnerability scanning

# 8\. Implementation Roadmap

The system shall be implemented in phased approach to manage complexity and enable early value delivery. Each phase builds on the previous, with clear milestones and testing.

## Phase 1: Foundation (Weeks 1-4)

- Set up development environment (database, backend framework, frontend scaffold)
- Implement database schema (all core tables)
- Build authentication system (login, JWT, RBAC)
- Create user management (CRUD users, assign roles)
- Develop Fiscal Year module (create, view, edit fiscal years)
- Build basic dashboard (display fiscal year list, status)
- Deliverable: Working login + admin can create fiscal years and users

## Phase 2: Budget & Programs (Weeks 5-8)

- Implement Research Program module (CRUD programs, link to fiscal year)
- Implement Research Topic module (CRUD topics, link to programs)
- Build budget allocation workflow (allocate budget to programs, validation)
- Create program dashboard (show allocated, committed, realized)
- Implement budget tracking logic (calculate available balance)
- Build program list and detail views
- Deliverable: Managers can create programs, allocate budget, view budget health

## Phase 3: Purchase Requisitions (Weeks 9-13)

- Develop PR creation form (all mandatory/optional fields, validation)
- Implement PR status workflow (draft, submitted, pending, approved, rejected)
- Build approval workflow engine (configurable rules, routing logic)
- Create approval interface (approver can view PR, approve/reject with comments)
- Implement email notifications (PR submitted, approved, rejected)
- Build PR list view with filters and search
- Build PR detail view with approval history timeline
- Implement budget availability check (real-time validation before approval)
- Deliverable: Full PR creation and approval workflow functional

## Phase 4: Purchase Orders (Weeks 14-17)

- Develop PO creation workflow (convert approved PR to PO)
- Implement PO status lifecycle (draft, issued, goods received, invoiced, paid)
- Build PO management interface (view, edit, update status)
- Implement budget impact logic (move amount from committed to realized)
- Create PO list and detail views
- Build vendor management (basic vendor master data)
- Implement invoice reconciliation (compare PO vs invoice amount)
- Deliverable: Complete PR-to-PO-to-payment workflow

## Phase 5: Document Management (Weeks 18-20)

- Implement document upload functionality (attach files to PRs/POs)
- Build document storage system (file system or cloud storage)
- Create document repository view (browse, search, filter documents)
- Implement document linking (PR → PO → Invoice chain)
- Build document version control
- Implement access controls (who can view/upload documents)
- Create audit trail for document actions (uploaded, viewed, deleted)
- Deliverable: Centralized document repository with full traceability

## Phase 6: Reporting & Analytics (Weeks 21-24)

- Develop fiscal year summary report (budget breakdown, trends)
- Build program performance report (utilization, variance)
- Create PR/PO status report (approval times, pending queue)
- Implement audit compliance report (missing docs, compliance score)
- Build spending forecast report (burn rate, projection)
- Create executive dashboard (KPIs, alerts, visualizations)
- Implement role-specific dashboards (Manager, Researcher, Finance)
- Add export functionality (Excel, PDF) for all reports
- Deliverable: Complete reporting suite with dashboards

## Phase 7: Testing, Security & Deployment (Weeks 25-28)

- Comprehensive testing (unit tests, integration tests, UI tests)
- Security audit (penetration testing, vulnerability scan)
- Performance optimization (database indexing, caching, query optimization)
- User acceptance testing (UAT) with pilot user group
- Documentation (user manual, admin guide, API documentation)
- Training materials (video tutorials, user guides)
- Production deployment (server setup, SSL certificate, monitoring)
- Post-launch support plan (bug fixes, user support)
- Deliverable: Production-ready system, live for users

## Success Criteria per Phase

| **Phase** | **Success Metric**        | **Acceptance Criteria**                        |
| --------- | ------------------------- | ---------------------------------------------- |
| Phase 1   | User login functional     | Admin can create users, users can login        |
| Phase 2   | Budget allocation working | Budget sum validation, program budgets tracked |
| Phase 3   | PR workflow complete      | 10 test PRs approved via workflow              |
| Phase 4   | PO creation working       | 5 PRs converted to POs successfully            |
| Phase 5   | Documents linked          | All test PRs/POs have attached documents       |
| Phase 6   | Reports generated         | All 5 standard reports export correctly        |
| Phase 7   | System live               | No critical bugs, 20+ real users onboarded     |

# 9\. Appendices

## 9.1 Glossary of Terms

**Fiscal Year (FY):** The 12-month period for which an organization plans and tracks its budget. May or may not align with calendar year.

**Budget Allocation:** The process of assigning a portion of the total fiscal year budget to specific research programs or projects.

**Committed Budget:** The amount of budget that has been approved (via approved PRs) but not yet spent. Represents financial obligations.

**Realized Budget:** The actual amount of money spent (via paid POs/invoices). Represents cash outflow.

**Available Balance:** The remaining budget that can still be committed. Calculated as: Allocated - Committed.

**Purchase Requisition (PR):** A formal request to purchase goods or services, submitted by a researcher and requiring approval before procurement.

**Purchase Order (PO):** A binding commitment to purchase from a vendor, created after PR approval. Sent to vendor as authorization to supply goods/services.

**Approval Workflow:** The sequence of approval stages a PR must pass through (e.g., Coordinator → Manager → Finance) before being approved.

**Budget Utilization Rate:** The percentage of allocated budget that has been committed. Formula: (Committed / Allocated) × 100%

**Burn Rate:** The rate at which budget is being spent over time. Used for forecasting when budget will be exhausted.

**Budget Freeze Date:** The cutoff date after which no new purchase requisitions will be accepted for a fiscal year.

**Document Chain:** The complete linkage of documents from PR → PO → Invoice → Payment, forming an audit trail.

**Audit Trail:** A chronological record of all system activities, including who did what and when, to ensure accountability and compliance.

## 9.2 Sample Approval Workflow Configuration

Example: PR Approval Rules for Different Amount Thresholds

| **PR Amount**      | **Urgency** | **Approval Chain**                         | **SLA (Business Days)** |
| ------------------ | ----------- | ------------------------------------------ | ----------------------- |
| < \$5,000          | Normal      | Coordinator → Manager                      | 2 days                  |
| \$5,000 - \$50,000 | Normal      | Coordinator → Manager → Finance            | 3 days                  |
| \> \$50,000        | Normal      | Coordinator → Manager → Finance → Director | 5 days                  |
| Any amount         | Critical    | Manager → Finance (skip Coordinator)       | 1 day                   |

## 9.3 Sample Budget Allocation Scenario

Fiscal Year 2026: Total Budget \$15,000,000

| Soil Fertility Enhancement | \$4,500,000 | 30% |
| -------------------------- | ----------- | --- |
| Pest Control Innovation    | \$3,000,000 | 20% |
| Irrigation Optimization    | \$3,000,000 | 20% |
| Crop Yield Improvement     | \$2,250,000 | 15% |
| Post-Harvest Technology    | \$1,500,000 | 10% |
| Reserve / Contingency      | \$750,000   | 5%  |

## 9.4 Stakeholder Communication Plan

- Weekly Email Digest (to Research Managers)Summary of pending approvals, budget utilization alerts, new PR submissions
- Monthly Report (to Senior Management)Fiscal year performance, program spending vs budget, forecast for year-end
- Quarterly Review MeetingAll stakeholders review budget health, discuss reallocations, address bottlenecks
- Real-time Notifications (to Approvers)Email/SMS when PR awaits approval, reminders for overdue approvals
- End-of-Year Report (to Finance/Audit)Complete spending summary, compliance status, document completeness check

## 9.5 Assumptions & Constraints

### Assumptions

- Users have basic computer literacy and internet access
- Organization has dedicated server/cloud infrastructure for hosting
- Email server available for sending notifications (SMTP access)
- All financial data in single currency (no multi-currency requirement)
- Fiscal year always aligns with calendar year (Jan 1 - Dec 31)
- No integration with external ERP in Phase 1 (future enhancement)
- English is the primary language (multi-language support not required initially)

### Constraints

- Development budget limited to \$X (to be defined by organization)
- Must launch within 7 months (28 weeks)
- Team size: 3-5 developers + 1 QA + 1 project manager
- Must support minimum 100 concurrent users
- Database must handle up to 10,000 PRs per fiscal year
- Document storage: up to 1TB total capacity

## 9.6 Future Enhancements (Post-Launch)

- Mobile app (iOS/Android) for on-the-go PR approvals
- Integration with existing ERP systems (SAP, Oracle, etc.)
- Vendor portal (vendors can view POs, upload invoices directly)
- Advanced analytics with machine learning (predict budget overruns, optimize allocation)
- Multi-currency support for international research collaborations
- E-signature integration for digital approval signing
- Automated email parsing (capture email approvals into system)
- Budget scenario modeling ("what-if" analysis for different allocation strategies)
- Integration with procurement marketplaces (auto-fetch vendor quotes)
- Blockchain-based audit trail for immutable financial records

_- END OF DOCUMENT -_
