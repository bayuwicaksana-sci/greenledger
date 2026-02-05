# **PRODUCT REQUIREMENTS DOCUMENT (PRD)**

## **Chart of Accounts (COA) Module for Green Ledger System**

---

## **1. EXECUTIVE SUMMARY**

### **1.1 Product Overview**

The Chart of Accounts (COA) Module is a comprehensive financial classification and tracking system designed for **agroindustry research organizations** managing multiple research projects across multiple sites. The module will be integrated into the existing **Green Ledger** accounting system to provide:

- **Standardized expense and revenue tracking** across 50+ research projects annually
- **Multi-dimensional financial analysis** (by project, department, site, expense type)
- **Real-time budget control** at both project and account levels
- **Seamless integration** with Purchase Requisition (PR) and Purchase Order (PO) workflows
- **Clear separation** between Program (mission-driven) and Non-Program (support) activities

### **1.2 Business Value**

**Current Pain Points**:

- Manual tracking of expenses across 50+ projects per year
- No standardized account structure (only 6 generic accounts currently)
- Budget overruns discovered late (no real-time control)
- Difficult to answer management questions like "How much spent on seeds across all projects?"
- Poor visibility into Program vs Non-Program expense ratios
- Time-consuming report generation (3+ days for simple queries)

**Expected Benefits**:

- **80% reduction** in financial reporting time (from 3 days to < 2 hours)
- **Real-time budget visibility** preventing overruns
- **100% expense traceability** from transaction to project to account
- **Standardized financial structure** enabling multi-year analysis
- **Automated budget checks** at PR/PO creation
- **Data-driven decision making** with drill-down capabilities

### **1.3 Target Users**

- **Primary**: 50+ researchers (PIs and research staff) creating PRs
- **Secondary**: 10 finance staff managing accounts and budgets
- **Tertiary**: 5 managers requiring financial reports and oversight

### **1.4 Success Criteria**

- ✅ 63 accounts successfully configured and operational
- ✅ 95%+ PR submissions use correct account codes within 30 days of launch
- ✅ Budget control prevents 100% of over-budget PR submissions
- ✅ Management reports generated in < 30 minutes (vs 3+ days currently)
- ✅ User satisfaction score >80% after 90 days of use

---

## **2. BACKGROUND & CONTEXT**

### **2.1 Organizational Context**

**Organization Type**: Agroindustry Research Institute \
 **Primary Mission**: Conduct agricultural research and disseminate knowledge to farmers \
 **Scale of Operations**:

- 50+ research projects per fiscal year
- 3 main sites (Klaten, Yogyakarta, Bogor) with potential expansion
- 4 research departments (Agronomy, Crops Protection, Product Testing, Knowledge)
- Annual budget: ~Rp 20 billion
- Program vs Non-Program ratio target: 75:25

### **2.2 Current System State**

**Existing System**: Green Ledger (basic accounting system)

**Current COA Structure**:

6 accounts total (extremely limited):

├─ 1000 - Program

├─ 1100 - Research

├─ 1110 - Agronomy

├─ 1120 - Crops Protection

├─ 1200 - Knowledge

└─ 2000 - Non-Program

**Current Limitations**:

- ❌ No expense type granularity (cannot differentiate seeds vs fertilizer vs labor)
- ❌ No project-level tracking (50 projects share same accounts)
- ❌ No budget control mechanism (all budgets show 0.00)
- ❌ No multi-dimensional reporting
- ❌ Account codes don't follow accounting standards (1000-2000 for expenses is non-standard)
- ❌ No separation of Program vs Non-Program expenses

### **2.3 Business Requirements Drivers**

**Strategic Drivers**:

1. **Donor Compliance**: Multiple donors require detailed expense breakdown and Program vs Non-Program separation
2. **Scalability**: Planning to expand from 50 to 100+ projects by 2028
3. **Financial Transparency**: Board of Directors demands better financial visibility
4. **Audit Readiness**: External audits require proper COA structure
5. **Multi-Site Expansion**: Adding 2 new research sites in 2027

**Operational Drivers**:

1. Research managers need real-time budget status per project
2. Finance team needs to generate reports quickly (currently 3+ days manual work)
3. PIs need clear guidance on which account to use when creating PRs
4. Management needs Program vs Non-Program ratio monitoring

### **2.4 Key Stakeholders**

<table>
  <tr>
   <td><strong>Stakeholder</strong>
   </td>
   <td><strong>Role</strong>
   </td>
   <td><strong>Primary Interest</strong>
   </td>
  </tr>
  <tr>
   <td>Director of Research
   </td>
   <td>Decision Maker
   </td>
   <td>Budget control, program ratio, strategic insights
   </td>
  </tr>
  <tr>
   <td>Finance Manager
   </td>
   <td>Owner
   </td>
   <td>COA integrity, accurate reporting, audit compliance
   </td>
  </tr>
  <tr>
   <td>Research Managers
   </td>
   <td>Primary User
   </td>
   <td>Project budget monitoring, expense tracking
   </td>
  </tr>
  <tr>
   <td>Principal Investigators (PIs)
   </td>
   <td>Primary User
   </td>
   <td>Easy PR creation, clear account selection
   </td>
  </tr>
  <tr>
   <td>Finance Staff
   </td>
   <td>Power User
   </td>
   <td>Account setup, budget allocation, report generation
   </td>
  </tr>
  <tr>
   <td>External Auditors
   </td>
   <td>Compliance
   </td>
   <td>Standard COA structure, audit trail
   </td>
  </tr>
  <tr>
   <td>Donors/Funders
   </td>
   <td>Accountability
   </td>
   <td>Expense transparency, Program vs Overhead clarity
   </td>
  </tr>
</table>

---

## **3. GOALS & OBJECTIVES**

### **3.1 Business Goals**

**Primary Goals**:

1. **Establish standardized financial structure** enabling accurate expense tracking across 50+ projects
2. **Implement real-time budget control** preventing budget overruns at both project and account levels
3. **Enable multi-dimensional financial analysis** supporting data-driven decision making
4. **Ensure donor compliance** through clear Program vs Non-Program expense separation
5. **Improve operational efficiency** by reducing financial reporting time by 80%

**Secondary Goals**:

1. Provide clear audit trail from transaction to project to account
2. Support scalability to 100+ projects without COA restructuring
3. Enable predictive budget analysis through historical data
4. Facilitate cross-site financial comparison and resource optimization

### **3.2 User Goals**

**Researchers (PIs)**:

- Select correct account code in &lt;30 seconds when creating PR
- See real-time project budget status before submitting PR
- Understand why PR was rejected (if budget exceeded)

**Finance Staff**:

- Set up new projects in &lt;5 minutes
- Allocate budgets at project and account levels efficiently
- Generate standard reports in &lt;30 minutes
- Perform ad-hoc analysis with flexible filtering

**Managers**:

- View project spending status at a glance
- Compare spending patterns across projects/departments/sites
- Identify budget risks early (80% utilization alerts)

### **3.3 System Goals**

**Performance**:

- PR creation with account selection: &lt;5 seconds response time
- Budget availability check: &lt;2 seconds
- Report generation (50 projects): &lt;30 seconds
- Dashboard loading: &lt;3 seconds

**Scalability**:

- Support 500+ projects simultaneously
- Support 10,000+ transactions per year
- Handle 100+ concurrent users

**Usability**:

- 95%+ users select correct account on first try (after training)
- &lt;5 support tickets per week related to COA (after stabilization)

---

## **4. USER PERSONAS & ROLES**

### **4.1 Persona 1: Dr. Budi (Principal Investigator)**

**Demographics**:

- Age: 38
- Role: Senior Researcher, Agronomy Department
- Education: PhD in Agronomy
- Tech Savvy: Medium (comfortable with basic software, not accounting expert)

**Responsibilities**:

- Lead 3-5 research projects simultaneously
- Manage project budgets (Rp 80-150 million per project)
- Create 10-15 PRs per month for project needs
- Submit monthly project progress and financial reports

**Pain Points**:

- "I'm not an accountant - I don't know which account code to use"
- "I submitted a PR but it was rejected for 'wrong account' - now delayed 1 week"
- "I don't know if my project still has budget until Finance tells me (usually too late)"

**Goals**:

- Quickly create PRs without worrying about accounting details
- See project budget status in real-time
- Avoid PR rejections due to account errors

**User Story**:

    "As a PI, I want to select the correct account code easily when creating a PR for my cabai research project, so that my PR gets approved quickly without delays."

---

### **4.2 Persona 2: Siti (Finance Staff)**

**Demographics**:

- Age: 28
- Role: Junior Accountant
- Education: Bachelor's in Accounting
- Tech Savvy: High (expert in Excel, learning ERP systems)

**Responsibilities**:

- Process 50-100 PRs per week
- Validate account codes on PRs
- Generate monthly financial reports
- Update budget allocations when approved
- Reconcile transactions to accounts

**Pain Points**:

- "Researchers always pick wrong account codes - I spend 50% of time correcting PRs"
- "When managers ask 'how much spent on seeds?', I need 2 days to manually compile data"
- "No way to automatically check if PR exceeds project budget - I have to check Excel manually"

**Goals**:

- Reduce PR correction time by 70%
- Generate reports automatically instead of manual Excel compilation
- Enforce budget control at PR submission (not after processing)

**User Story**:

    "As a finance staff, I want the system to automatically validate account codes and budget availability when a PR is submitted, so I don't have to manually check and correct every PR."

---

### **4.3 Persona 3: Pak Andi (Research Manager)**

**Demographics**:

- Age: 45
- Role: Research Operations Manager
- Education: Master's in Agricultural Science
- Tech Savvy: Medium (proficient with reports and dashboards, not technical)

**Responsibilities**:

- Oversee 18 research projects under Agronomy department
- Approve PRs from PIs (Rp 4 billion annual budget)
- Monitor departmental budget utilization
- Report to Director on program financial performance

**Pain Points**:

- "I approve PRs but don't know real-time budget status - sometimes approved PRs exceed budget"
- "Director asks 'Agronomy spending status?' - I can't answer immediately, need to wait for Finance report"
- "Want to compare spending efficiency across my 18 projects but data is scattered"

**Goals**:

- Dashboard showing department budget status at a glance
- Drill-down capability from department → project → expense type
- Alert when any project reaches 80% budget utilization

**User Story**:

    "As a research manager, I want to see a real-time dashboard of all my department's projects with budget utilization percentages, so I can proactively manage resources and prevent overruns."

---

### **4.4 Persona 4: Ibu Ani (Finance Manager)**

**Demographics**:

- Age: 42
- Role: Finance Manager
- Education: Master's in Accounting, CPA
- Tech Savvy: Medium-High (strategic user of financial systems)

**Responsibilities**:

- Overall financial planning and control
- Annual budget allocation across 50+ projects
- Ensure audit compliance
- Report to Director and Board on financial health
- Manage 5-person finance team

**Pain Points**:

- "Current COA is too simple - cannot meet donor reporting requirements"
- "External auditors flagged COA structure as non-standard"
- "Cannot accurately calculate Program vs Non-Program ratio - donor requires 75:25 minimum"
- "Answering management questions takes days - by then the insight is outdated"

**Goals**:

- Implement audit-compliant COA structure
- Real-time Program vs Non-Program tracking
- Generate donor-required reports automatically
- Empower team to self-serve reports (reduce dependency on manual queries)

**User Story**:

    "As a finance manager, I want a comprehensive COA structure that complies with accounting standards and donor requirements, so that we pass audits and maintain donor confidence."

---

## **5. SCOPE & CONSTRAINTS**

### **5.1 In Scope**

**Phase 1 (MVP - 12 weeks)**:

- ✅ COA structure setup (63 accounts: 21 Program + 42 Non-Program)
- ✅ Account master data management (CRUD operations)
- ✅ Hierarchical account relationships (4 levels)
- ✅ Project master data management
- ✅ Multi-dimensional tagging (Year, Site, Sub-Sub-Division, Project)
- ✅ Budget allocation at project level
- ✅ Budget allocation at account level (rollup from projects)
- ✅ Real-time budget availability check
- ✅ Integration with PR module (account selection + budget check)
- ✅ Basic reporting (Budget vs Actual by Project, by Account, by Department)
- ✅ Program vs Non-Program expense separation
- ✅ User training materials and documentation

**Phase 2 (Enhancement - 6 months post-MVP)**:

- ⏭️ Advanced reporting (variance analysis, trend analysis, forecasting)
- ⏭️ Budget reallocation workflow
- ⏭️ Integration with PO module (automated budget commitment)
- ⏭️ Integration with Invoice/Payment module (automated actuals posting)
- ⏭️ Custom dashboard builder for managers
- ⏭️ Mobile view for budget checking
- ⏭️ Automated alerts (budget threshold, overrun, unused budget)
- ⏭️ Fiscal year closing and rollover automation

### **5.2 Out of Scope**

**Explicitly NOT in this module**:

- ❌ General Ledger (GL) posting engine (handled by core Green Ledger)
- ❌ Payroll integration (salaries not tracked in this COA)
- ❌ Asset management (fixed asset tracking is separate module)
- ❌ Revenue recognition workflows (complex revenue recognition is separate)
- ❌ Multi-currency support (all transactions in IDR only for Phase 1)
- ❌ Consolidation with other legal entities
- ❌ Tax calculation and filing
- ❌ Bank reconciliation

### **5.3 Technical Constraints**

**Performance**:

- Support 100 concurrent users
- 99.5% uptime during business hours (8 AM - 6 PM WIB)

**Compatibility**:

- Export formats: Excel (.xlsx), CSV, PDF

### **5.4 Business Constraints**

**Budget**:

- Development budget: TBD (to be determined by management)
- No external software purchase (build in-house or use open-source)

**Compliance**:

- Must follow Indonesian accounting standards (PSAK)
- Must meet donor reporting requirements (various international donors)
- Must be audit-ready (external audit in November 2026)

---

## **6. FUNCTIONAL REQUIREMENTS**

### **6.1 Account Management**

#### **FR-AM-001: Create Account**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance Manager, I want to create new accounts in the COA, so that I can expand the chart as organizational needs evolve.

**Detailed Requirements**:

1. System shall allow authorized users (Finance Manager only) to create new accounts
2. Required fields for new account:
    - Account Code (text, 4-6 characters, unique, alphanumeric)
    - Account Name (text, max 100 characters, required)
    - Account Type (dropdown: Expense/Revenue, required)
    - Category (dropdown: Program/Non-Program, required)
    - Sub-Category (dropdown: Research/Knowledge for Program; varies for Non-Program, required)
    - Parent Account (dropdown: accounts at level above, optional for level 1)
    - Level (auto-calculated based on parent, 1-4, read-only)
    - Description (text area, max 500 characters, required)
    - Is Active (checkbox, default: checked)
    - Budget Control Enabled (checkbox, default: checked for expense accounts)
    - Tax Applicable (checkbox, default: unchecked)
    - Typical Usage (text area, max 500 characters, optional)
    - Effective Date (date, default: today)
3. System shall validate:
    - Account Code uniqueness
    - Account Code format (no spaces, no special characters except hyphen)
    - Parent account must exist and be at level below
    - Cannot create circular parent-child relationships
4. System shall auto-generate suggested Account Code based on category and sequence
5. System shall display preview of account hierarchy position before saving
6. System shall require approval workflow for new account creation:
    - Finance Manager creates (Draft status)
    - System notifies Director for approval
    - Director approves → Active, or Rejects → Back to Draft
7. System shall log audit trail: created by, created date, approved by, approved date

**Acceptance Criteria**:

- ✅ User can successfully create account with all required fields
- ✅ System prevents duplicate account codes
- ✅ System validates parent-child relationship correctness
- ✅ Approval workflow functions correctly
- ✅ Audit trail captured completely

---

#### **FR-AM-002: Edit Account**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance Manager, I want to edit existing accounts, so I can correct errors or update descriptions.

**Detailed Requirements**:

1. System shall allow editing of accounts in Active or Draft status
2. Editable fields:
    - Account Name
    - Description
    - Is Active (can deactivate but not delete)
    - Typical Usage
    - Budget Control Enabled
    - Tax Applicable
3. Non-editable fields (after account is used):
    - Account Code (cannot change to prevent transaction orphaning)
    - Account Type
    - Category
    - Parent Account (if child accounts exist or transactions posted)
4. System shall require approval for changes to Active accounts (similar to create workflow)
5. System shall version account changes (maintain history)
6. System shall warn if deactivating account that has:
    - Active budget allocations
    - Transactions in current FY
    - Pending PRs/POs
7. System shall suggest deactivation date (not immediate) if dependencies exist

**Acceptance Criteria**:

- ✅ User can edit allowed fields successfully
- ✅ System prevents editing restricted fields with clear error message
- ✅ Approval workflow triggered for Active account changes
- ✅ Version history maintained and viewable
- ✅ Warnings displayed for deactivation dependencies

---

#### **FR-AM-003: View Account Hierarchy**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance Staff, I want to view the complete account hierarchy, so I can understand account relationships and navigate the COA.

**Detailed Requirements**:

1. System shall display accounts in expandable tree view:
    - Level 1 (root categories) always visible
    - Level 2-4 collapsible/expandable
    - Indentation shows hierarchy depth
    - +/- icons for expand/collapse
2. Display columns:
    - Account Code
    - Account Name
    - Type (icon: Expense/Revenue)
    - Status (Active/Inactive, color-coded)
    - Budget (current FY total)
    - Actual (current FY total)
    - Balance (Budget - Actual)
    - % Utilized (visual: progress bar)
    - Actions (Edit, View Details)
3. Features:
    - Search/filter by code, name, category
    - Filter by status (Active/Inactive/All)
    - Filter by fiscal year
    - Filter by site (if multi-site)
    - Expand All / Collapse All buttons
    - Sort by any column
4. Performance:
    - Load time &lt;3 seconds for 63 accounts
    - Smooth expand/collapse (&lt;100ms)
5. Breadcrumb navigation showing current selection path

**Acceptance Criteria**:

- ✅ Tree view displays correctly with proper indentation
- ✅ Expand/collapse functions smoothly
- ✅ All filters work correctly
- ✅ Budget and actual amounts accurate and up-to-date
- ✅ Performance meets &lt;3 second load time

---

#### **FR-AM-004: Account Search & Selection**

**Priority**: P0 (Critical) \
 **User Story**: As a Researcher creating a PR, I want to quickly find and select the correct account, so I can submit my PR without delays.

**Detailed Requirements**:

1. Account selection interface:
    - **Search-as-you-type** with autocomplete (debounced, 300ms delay)
    - Search across: Account Code, Account Name, Description, Typical Usage
    - Display top 10 matches as user types
    - Highlight matched text
2. Each search result shows:
    - Account Code - Account Name
    - Parent breadcrumb (e.g., "Research > Materials > Seeds")
    - Brief description (first 50 characters)
    - Budget availability indicator (color: green/yellow/red)
3. Smart suggestions:
    - Show 5 "Most Used" accounts for this user
    - Show "Recently Used" accounts (last 5)
    - Show "Recommended" accounts based on project category
4. Guided selection (optional wizard mode):
    - "What are you buying?" → Categories shown
    - User selects category → Accounts in that category shown
    - User selects account → Confirmation shown
5. Visual aids:
    - Icons per category (seeds=🌱, fertilizer=🧪, equipment=🔧)
    - Color coding (Program=blue, Non-Program=gray)
6. After selection:
    - System displays full account details
    - Shows current budget availability for selected project
    - Warns if budget low (&lt;20% remaining)

**Acceptance Criteria**:

- ✅ Autocomplete returns results within 300ms
- ✅ Search finds accounts across all searchable fields
- ✅ Most Used / Recently Used suggestions display correctly
- ✅ Guided wizard helps users unfamiliar with codes
- ✅ Budget availability shown in real-time
- ✅ User testing shows 95%+ correct selection rate

---

### **6.2 Project Management**

#### **FR-PM-001: Create Project**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance Staff, I want to create new research projects in the system, so that expenses can be tracked per project.

**Detailed Requirements**:

1. Required fields:
    - Project Code (auto-generated format: [Dept]-[Year]-[Seq], e.g., AGR-2026-001)
    - Project Name (text, max 200 characters)
    - Short Name (text, max 50 characters, for displays)
    - Principal Investigator (PI) - dropdown from user list
    - Department (dropdown: Agronomy, Crops Protection, Product Testing, Knowledge Creation, etc.)
    - Site (dropdown: Klaten, Yogyakarta, Bogor, Multi-Site)
    - Category (auto-filled based on department: Program/Non-Program)
    - Sub-Division (auto-filled based on department: Research/Knowledge)
    - Start Date (date picker, required)
    - End Date (date picker, required, must be >= Start Date)
    - Fiscal Year (auto-calculated from Start Date, read-only)
    - Status (dropdown: Planning/Active/Completed/Cancelled, default: Planning)
    - Total Budget (currency, required, must be > 0)
    - Funding Source (dropdown: Grant A, Grant B, Internal, etc.)
    - Description (text area, max 1000 characters)
2. Budget Allocation sub-form:
    - For each expense category (Seeds, Fertilizer, Labour, etc.)
    - Enter budget amount
    - System validates: Sum of allocations = Total Budget
    - Allow quick allocation: Equal split, Percentage split, Custom
3. Project Team (optional):
    - Add team members (dropdown from users)
    - Assign roles (Co-PI, Research Associate, Technician)
4. Validations:
    - Project Code uniqueness
    - Start Date not in the past (unless user confirms)
    - End Date not more than 3 years from Start Date (warning)
    - Total Budget does not exceed Department annual allocation (warning)
5. After creation:
    - System sends notification to PI
    - System creates budget records linked to this project
    - Status set to Active if Start Date is today or earlier

**Acceptance Criteria**:

- ✅ User can create project with all required fields
- ✅ Project code auto-generated correctly
- ✅ Budget allocation validated (sum = total)
- ✅ PI notified successfully
- ✅ Project appears in project list immediately

---

#### **FR-PM-002: Edit Project**

**Priority**: P1 (High) \
 **User Story**: As a Finance Manager, I want to edit project details, so I can update information as projects evolve.

**Detailed Requirements**:

1. Editable fields (if no transactions posted yet):
    - Project Name
    - Short Name
    - PI (with approval workflow)
    - Site
    - End Date (extend only, not shorten)
    - Description
2. Editable fields (always):
    - Status (with workflow: Planning → Active → Completed/Cancelled)
3. Non-editable fields (after transactions exist):
    - Project Code
    - Department
    - Category
    - Sub-Division
    - Start Date
    - Fiscal Year
    - Total Budget (use Budget Revision workflow instead)
4. Budget Revision:
    - Separate workflow for changing budget
    - Requires justification (text, required)
    - Requires Manager approval
    - Maintains revision history (original budget → revised budget)
5. Status change rules:
    - Planning → Active: Requires Start Date reached
    - Active → Completed: Requires End Date reached + no pending transactions
    - Active → Cancelled: Requires reason + approvals + handling of committed budget
    - Cannot revert from Completed/Cancelled to Active

**Acceptance Criteria**:

- ✅ User can edit allowed fields
- ✅ System enforces non-editable field restrictions
- ✅ Budget revision workflow functions correctly
- ✅ Status transitions follow defined rules
- ✅ Revision history tracked completely

---

#### **FR-PM-003: Project Budget Allocation**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance Manager, I want to allocate project budget across expense accounts, so that budget control can be enforced at account level.

**Detailed Requirements**:

1. Budget Allocation Interface:
    - Left panel: List of expense accounts (tree view)
    - Right panel: Allocation form
    - Top summary: Total Project Budget, Total Allocated, Remaining
2. Allocation methods:
    - **Manual Entry**: Enter amount for each account
    - **Import from Template**: Use saved template from similar project
    - **Import from Excel**: Upload budget breakdown
    - **Quick Split**: Equally distribute remaining budget
3. For each account allocation:
    - Account Code - Name (read-only, from selection)
    - Allocated Amount (currency input)
    - Percentage of total (auto-calculated, display)
    - Notes (optional, for planning details)
4. Validations:
    - Sum of allocations must equal Total Project Budget (strict)
    - Individual allocation must be > 0 if entered
    - Cannot allocate to inactive accounts
    - Cannot allocate to parent accounts (Level 1-2), only detail accounts (Level 3-4)
5. Save options:
    - Save as Draft (can edit later)
    - Submit for Approval (sends to Manager)
    - Save as Template (for future projects)
6. After approval:
    - Budget records created in database
    - Account-level budgets updated (sum of all project allocations)
    - Project status changed to Active (if not already)
7. Display warnings:
    - If any account allocation >50% of total (unusual concentration)
    - If any account has no budget but similar projects used it (suggestion)

**Acceptance Criteria**:

- ✅ User can allocate budget using all methods (manual, template, import)
- ✅ Sum validation enforced before saving
- ✅ Approval workflow functions correctly
- ✅ Account-level budget rollup calculated correctly
- ✅ Templates can be saved and reused

---

### **6.3 Budget Control**

#### **FR-BC-001: Real-Time Budget Check**

**Priority**: P0 (Critical) \
 **User Story**: As a system, I want to check budget availability in real-time when a PR is created, so that over-budget PRs are prevented automatically.

**Detailed Requirements**:

1. Budget check triggered when: \
    - User selects account code in PR form
    - User enters PR amount
    - User clicks "Check Budget" button
    - User clicks "Submit PR" button (final check)

Budget check logic (2-level check): \
 \
 **Level 1: Project Budget Check \
 \
** Available Budget = Project Total Budget

                   - Sum(Committed PRs for this project)

                   - Sum(Actual Spent for this project)

IF PR Amount &lt;= Available Budget THEN

    PASS Level 1

ELSE

    FAIL Level 1 (show remaining budget, suggest budget revision)

**Level 2: Account Budget Check \
 \
** Available Budget = Account Budget (for this project, this account)

                   - Committed (PRs pending/approved but not paid)

                   - Actual Spent

IF PR Amount &lt;= Available Budget THEN

    PASS Level 2

ELSE

    FAIL Level 2 (show account budget status, suggest reallocation)

2.
3. Check result display: \
    - ✅ **PASS**: Green indicator, "Budget available. You can proceed."
        - Show: Project Budget: Rp X remaining, Account Budget: Rp Y remaining
    - ⚠️ **WARNING**: Yellow indicator, "Budget will be >80% utilized"
        - Show: Remaining after this PR: Rp Z
        - Allow submission but require additional approval
    - ❌ **FAIL**: Red indicator, "Insufficient budget"
        - Show: Available: Rp A, Requested: Rp B, Shortfall: Rp C
        - Block submission
        - Provide options:
            - Reduce PR amount
            - Request budget revision
            - Select different account (if applicable)
            - Contact Finance Manager

4. Budget commitment: \
    - When PR is submitted (Draft status) → Budget reserved (soft commit)
    - When PR is approved → Budget committed (hard commit)
    - When PO is issued → Budget remains committed
    - When payment is made → Budget commitment released, Actual updated

5. Real-time updates: \
    - Budget availability updates instantly when other PRs approved/cancelled
    - Use database triggers or real-time queries (not cached data)
    - Prevent race conditions (2 users submitting PR simultaneously)

6. Performance: \
    - Budget check completes within 2 seconds
    - Handles 100 concurrent budget checks without degradation

**Acceptance Criteria**:

- ✅ Budget check correctly calculates availability at both levels
- ✅ Over-budget PRs are blocked with clear explanation
- ✅ Warning shown when budget >80% utilized
- ✅ Budget availability updates in real-time
- ✅ Performance meets &lt;2 second requirement
- ✅ Race condition testing passes (concurrent submissions)

---

#### **FR-BC-002: Budget Revision Workflow**

**Priority**: P1 (High) \
 **User Story**: As a PI, I want to request budget revisions when my project needs exceed original allocation, so I can continue my research without delays.

**Detailed Requirements**:

1. Budget Revision Request form: \
    - Project (dropdown, pre-selected if from project page)
    - Revision Type (dropdown):
        - Increase Total Budget (need additional funding)
        - Reallocate Between Accounts (move budget from account A to B)
        - Decrease Budget (return unused budget)
    - Current Budget Details (read-only, for reference)
    - Requested Changes:
        - If Total Increase: New Total Amount, Source of Additional Funds
        - If Reallocation: From Account → To Account, Amount to Move
        - If Decrease: Amount to Return, Reason
    - Justification (text area, required, min 100 characters)
    - Supporting Documents (file upload, optional: budget breakdown Excel, donor approval, etc.)
    - Urgency (dropdown: Normal/Urgent)

Approval Workflow: \
 \
 PI submits → Research Manager reviews → Finance Manager reviews → Director approves (if >10% increase)

Conditions:

- If reallocation only (no total change) → Manager approval sufficient

- If total increase &lt;10% → Finance Manager approval sufficient

- If total increase >10% → Director approval required

- If decrease → Manager approval sufficient

2.
3. At each approval stage: \
    - Approver sees:
        - Request details
        - Current budget vs requested budget (comparison table)
        - Project spending history (to assess justification)
        - Remaining project duration
        - Comments from previous approvers
    - Approver actions:
        - Approve (with optional comments)
        - Reject (with required reason)
        - Request More Info (sends back to PI with questions)

4. After approval: \
    - System updates project budget
    - System updates account allocations
    - System recalculates available budget for all pending PRs
    - System notifies PI of approval
    - System logs revision in audit trail

5. Rejection handling: \
    - PI notified with rejection reason
    - PI can revise and resubmit
    - Original request archived (not deleted)

**Acceptance Criteria**:

- ✅ Users can request budget revisions with all required info
- ✅ Approval workflow routes correctly based on revision type and amount
- ✅ Approvers can approve/reject/request info
- ✅ Budget updates applied correctly after approval
- ✅ Audit trail complete (all revisions logged)

---

### **6.4 Multi-Dimensional Tagging**

#### **FR-MD-001: Transaction Tagging**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance Staff, I want every transaction tagged with multiple dimensions, so we can analyze spending from any angle.

**Detailed Requirements**:

1. **Mandatory Tags** (required for all Program expenses): \
    - **Fiscal Year**: Auto-calculated from transaction date (e.g., 2026)
    - **Site**: Dropdown (Klaten, Yogyakarta, Bogor, Multi-Site, Head Office)
    - **Category**: Auto-filled (Program/Non-Program based on account)
    - **Sub-Division**: Auto-filled (Research/Knowledge based on account)
    - **Sub-Sub-Division**: Auto-filled (Agronomy/Crops Protection/etc. based on project)
    - **Project Code**: Dropdown with search (only for Program expenses)

2. **Optional Tags**: \
    - **Funding Source**: Dropdown (Grant A, Grant B, Internal, etc.)
    - **Activity Type**: Dropdown (Field Trial, Lab Analysis, Training, etc.)
    - **Beneficiary**: Text (for knowledge activities: e.g., "50 farmers from Klaten")
    - **Notes**: Text area (additional context)

3. **Auto-population** (reduce user input): \
    - When Project is selected:
        - Auto-fill: Site, Sub-Sub-Division, Funding Source (from project master)
        - User can override if transaction differs from project default
    - When Account is selected:
        - Auto-fill: Category, Sub-Division (from account master)
    - When Transaction Date entered:
        - Auto-calculate: Fiscal Year

4. **Tag Validation**: \
    - Non-Program expenses: Project Code NOT ALLOWED (system enforces)
    - Program expenses: Project Code REQUIRED (system enforces)
    - Site must be consistent with Project site (warning if mismatch)
    - Multi-Site project: allow any site selection

5. **Tag Display** (on transaction view): \
    - Show all tags in consistent layout
    - Use visual indicators (icons, colors) for categories
    - Show breadcrumb: Year > Site > Category > Sub-Division > Sub-Sub-Division > Project

6. **Tag Editing**: \
    - Allow tag editing if transaction not yet approved
    - Require approval for tag changes on approved transactions (audit trail)
    - Prevent changing tags if transaction already paid (system locked)

**Acceptance Criteria**:

- ✅ All mandatory tags enforced on save
- ✅ Auto-population works correctly
- ✅ Validation rules enforced (Program requires Project, Non-Program blocks Project)
- ✅ Tag editing permissions enforced
- ✅ Tags display clearly on all transaction views

---

#### **FR-MD-002: Dimension Management**

**Priority**: P2 (Medium) \
 **User Story**: As a Finance Manager, I want to manage dimension values (sites, funding sources, etc.), so I can maintain consistency across the organization.

**Detailed Requirements**:

1. Dimension Master Data pages: \
    - **Sites**: Code (KLT, YGY, BGR), Name, Address, Active status
    - **Funding Sources**: Code, Name, Donor Name, Contract Number, Active status
    - **Activity Types**: Code, Name, Description, Active status
    - **Sub-Sub-Divisions**: Code, Name, Parent Sub-Division, Active status

2. CRUD operations for each dimension: \
    - Create new dimension value (Finance Manager only)
    - Edit existing (name, description, but not code once used)
    - Deactivate (cannot delete if already used in transactions)
    - View usage statistics (how many transactions use this value)

3. Dimension hierarchies: \
    - Sub-Sub-Division must belong to a Sub-Division
    - System enforces hierarchy consistency
    - Cannot change parent if child already used in transactions

4. Bulk operations: \
    - Import dimension values from Excel
    - Export dimension list to Excel
    - Bulk activate/deactivate

**Acceptance Criteria**:

- ✅ Users can manage dimension values via UI
- ✅ Deactivation prevents new usage but preserves historical data
- ✅ Hierarchy constraints enforced
- ✅ Bulk operations function correctly

---

### **6.5 Integration with PR Module**

#### **FR-INT-001: Account Selection in PR Form**

**Priority**: P0 (Critical) \
 **User Story**: As a Researcher, I want to select an account when creating a PR, so that my expense is properly classified.

**Detailed Requirements**:

1. PR Form Integration: \
    - Add "Account Code" field to PR form (after Item Description)
    - Field type: Searchable dropdown with autocomplete (leverage FR-AM-004)
    - Position: Prominent, clearly labeled
    - Help text: "Select the expense account for this purchase. Not sure? Click 'Help me choose'"

2. Account Selection UI: \
    - **Method 1**: Direct search/autocomplete (for experienced users)
    - **Method 2**: Category browser (for new users)
        - Step 1: What are you buying? (Materials / Equipment / Services / Travel)
        - Step 2: Select specific category (Seeds / Fertilizer / Pesticide / etc.)
        - Step 3: Confirm account selection
    - **Method 3**: "Copy from similar PR" (suggest recent PRs from same project)

3. Context-aware suggestions: \
    - System analyzes Item Description using keywords
    - Suggests top 3 likely accounts
    - Example: Description contains "benih padi" → Suggest 5210 - Seeds

4. Validation: \
    - Account selection is REQUIRED (cannot submit PR without it)
    - Account must be Active
    - Account must allow transactions (Level 3-4 only, not parent accounts)
    - If Non-Program account selected, system blocks Project tagging
    - If Program account selected, system requires Project tagging

5. After selection: \
    - Display full account name and description
    - Show budget availability for this account + project combination
    - Color indicator: Green (plenty), Yellow (low), Red (insufficient)
    - If Red, prevent PR submission with message: "Insufficient budget. Available: Rp X, Requested: Rp Y"

6. Change account: \
    - User can change selection before submitting PR
    - After PR submitted, account change requires approval (via PR edit workflow)

**Acceptance Criteria**:

- ✅ Account selection field integrated seamlessly into PR form
- ✅ All 3 selection methods work correctly
- ✅ Context-aware suggestions have >70% accuracy
- ✅ Budget availability check triggered on selection
- ✅ Validation prevents submission without account or with over-budget

---

#### **FR-INT-002: Project Tagging in PR Form**

**Priority**: P0 (Critical) \
 **User Story**: As a Researcher, I want to tag my PR to a specific project, so that expenses are tracked per project.

**Detailed Requirements**:

1. Project Field in PR Form: \
    - Add "Project" field (after Account Code field)
    - Field type: Searchable dropdown with autocomplete
    - Display format: `[Project Code] - [Project Name]` (e.g., AGR-2026-001 - Penelitian Cabai)
    - Filter: Only show Active projects where user is PI or team member

2. Project Selection: \
    - Search by: Project Code, Project Name, PI name
    - Recent projects displayed first (last 5 projects user submitted PRs for)
    - If user is PI on only 1 active project, auto-select that project
    - If user is PI on multiple projects, require manual selection

3. After Project Selection: \
    - System auto-populates:
        - Site (from project)
        - Sub-Sub-Division (from project)
        - Funding Source (from project)
    - Display project details panel:
        - PI: [Name]
        - Budget: Rp [Total] (Rp [Remaining] available)
        - Duration: [Start Date] to [End Date]
        - Status: [Active]

4. Validation: \
    - Project is REQUIRED for Program accounts (5000-6999)
    - Project is NOT ALLOWED for Non-Program accounts (7000-8999)
    - Project must be Active status
    - Project must not be ended (End Date not in the past)
    - User must have permission to create PR for this project (PI or team member)

5. Budget Display: \
    - After selecting Project + Account + entering Amount:

System shows: \
 Project Budget Check:- Total Budget: Rp 80,000,000- Committed: Rp 35,000,000- Spent: Rp 21,500,000- This PR: Rp 5,000,000- Remaining: Rp 18,500,000 ✅ OKAccount Budget Check:- Account Budget (5210-Seeds): Rp 10,000,000- Committed: Rp 3,000,000- Spent: Rp 2,000,000- This PR: Rp 5,000,000- Remaining: Rp 0 ⚠️ WARNING: This will fully utilize account budget

    *

**Acceptance Criteria**:

- ✅ Project field displays only relevant, active projects
- ✅ Auto-population works correctly after project selection
- ✅ Validation enforces project requirement for Program accounts
- ✅ Budget display shows accurate, real-time information
- ✅ User cannot submit over-budget PR

---

#### **FR-INT-003: Budget Commitment on PR Approval**

**Priority**: P0 (Critical) \
 **User Story**: As a Finance System, I want to commit budget when a PR is approved, so that the same budget cannot be used twice.

**Detailed Requirements**:

1. Budget Commitment Lifecycle: \
    \
    **Stage 1: PR Created (Draft) \
   **
   _ No budget commitment
   _ Budget check performed but not reserved \* User can see "indicative" availability
2. **Stage 2: PR Submitted (Pending Approval) \
   **
   _ **Soft Commitment**: Budget reserved temporarily
   _ Amount deducted from "Available Budget" for display purposes
   _ Not yet reflected in "Committed" official reports
   _ Purpose: Prevent double-submission by multiple users
3. **Stage 3: PR Approved \
   **
   _ **Hard Commitment**: Budget formally committed
   _ Database record created in `budget_commitments` table
   _ `committed` amount updated in project and account budget tables
   _ Commitment linked to PR ID (traceability)
4. **Stage 4: PO Created from PR \
   **
   _ Commitment remains (not duplicated)
   _ Commitment now linked to both PR and PO
5. **Stage 5: Payment Made \
   **
   _ Commitment released (amount removed from `committed`)
   _ Actual updated (amount added to `actual_spent`) \* Net effect: Available budget doesn't change (committed → actual)
6. **Exception: PR Rejected or Cancelled \
   **
   _ Commitment released immediately
   _ Budget becomes available again

Database Updates: \
 \
 -- On PR Approval

INSERT INTO budget_commitments (

    project_id, account_id, pr_id, amount,

    committed_date, status

) VALUES (...);

UPDATE project_budgets

SET committed = committed + [PR_Amount]

WHERE project_id = [Project] AND account_id = [Account];

-- On Payment

DELETE FROM budget_commitments WHERE pr_id = [PR_ID];

UPDATE project_budgets

SET committed = committed - [Amount],

    actual_spent = actual_spent + [Amount]

WHERE project_id = [Project] AND account_id = [Account];

7.
8. Concurrency Control: \
    - Use database transactions (ACID properties)
    - Lock budget records during update (row-level locking)
    - Prevent race condition where 2 PRs approved simultaneously exceeding budget
    - If lock timeout, retry 3 times before failing with error message

9. Audit Trail: \
    - Every commitment logged with:
        - PR ID, Project, Account, Amount
        - Committed by (user), Committed date
        - Status (Active / Released / Cancelled)
    - Every release logged with:
        - Released date, Released reason (Payment / Cancellation)

10. Monitoring & Alerts: \
    - System monitors "orphan commitments" (committed but PR cancelled, forgot to release)
    - Weekly cleanup job to release expired commitments (PR older than 90 days)
    - Alert Finance Manager if total committed >80% of organization budget

**Acceptance Criteria**:

- ✅ Budget committed correctly on PR approval
- ✅ Commitment released correctly on payment or cancellation
- ✅ Concurrency control prevents over-commitment
- ✅ Audit trail complete and queryable
- ✅ Orphan commitment cleanup job runs successfully

---

## **7. NON-FUNCTIONAL REQUIREMENTS**

### **7.1 Performance Requirements**

#### **NFR-PERF-001: Response Time**

**Priority**: P0 (Critical)

<table>
  <tr>
   <td><strong>Operation</strong>
   </td>
   <td><strong>Target Response Time</strong>
   </td>
   <td><strong>Maximum Acceptable</strong>
   </td>
  </tr>
  <tr>
   <td>Page load (any page)
   </td>
   <td>&lt;2 seconds
   </td>
   <td>&lt;5 seconds
   </td>
  </tr>
  <tr>
   <td>Account search (autocomplete)
   </td>
   <td>&lt;300 milliseconds
   </td>
   <td>&lt;1 second
   </td>
  </tr>
  <tr>
   <td>Budget availability check
   </td>
   <td>&lt;1 second
   </td>
   <td>&lt;3 seconds
   </td>
  </tr>
  <tr>
   <td>PR submission (with budget commit)
   </td>
   <td>&lt;3 seconds
   </td>
   <td>&lt;10 seconds
   </td>
  </tr>
  <tr>
   <td>Report generation (50 projects)
   </td>
   <td>&lt;30 seconds
   </td>
   <td>&lt;2 minutes
   </td>
  </tr>
  <tr>
   <td>Dashboard load (with 5 widgets)
   </td>
   <td>&lt;3 seconds
   </td>
   <td>&lt;10 seconds
   </td>
  </tr>
  <tr>
   <td>Account tree view expand/collapse
   </td>
   <td>&lt;100 milliseconds
   </td>
   <td>&lt;500 milliseconds
   </td>
  </tr>
</table>

**Measurement**:

- Measured at 90th percentile (P90)
- Under normal load (50 concurrent users)

---

#### **NFR-PERF-002: Throughput**

**Priority**: P1 (High)

**Requirements**:

- Support 100 concurrent users without performance degradation
- Handle 500 PR submissions per day (peak: 100 per hour)
- Process 1,000 budget checks per hour
- Generate 50 concurrent reports without queue

**Stress Testing**:

- System tested with 150% of expected load (150 concurrent users)
- System maintains response time within 150% of targets under stress

---

#### **NFR-PERF-003: Scalability**

**Priority**: P1 (High)

**Requirements**:

- Database: Support 1 million transactions per year without performance degradation
- Accounts: Support up to 500 accounts (current: 63, room for 8x growth)
- Projects: Support 500 active projects simultaneously (current: 50, room for 10x growth)
- Users: Support 500 users (current: 100, room for 5x growth)

**Architecture**:

- Database indexing on key fields (account_code, project_code, transaction_date)
- Query optimization for common operations
- Database partitioning by fiscal year if needed

---

### **7.2 Usability Requirements**

#### **NFR-USA-001: Learnability**

**Priority**: P0 (Critical)

**Requirements**:

- New users (researchers) can create their first PR with correct account selection after 30-minute training
- 90% of users select correct account on first try after 1 week of use
- Users can find any feature within 3 clicks from home page
- Help documentation accessible via "?" icon on every page

**Training Materials**:

- Video tutorials (5-10 minutes each):
    - "How to Select the Right Account"
    - "How to Create a PR with Project Tagging"
    - "How to Check Your Project Budget Status"
- Quick reference card (1-page PDF) with most common accounts
- Interactive walkthrough on first login (optional tour)

---

#### **NFR-USA-002: Accessibility**

**Priority**: P2 (Medium)

**Requirements**:

- WCAG 2.1 Level AA compliance (partial - critical features only)
- Keyboard navigation support (tab through forms, enter to submit)
- Sufficient color contrast (4.5:1 minimum for text)
- No critical information conveyed by color alone (use icons + text)
- Responsive design: works on tablets (min width: 768px)

**Note**: Full accessibility (screen readers, etc.) deferred to Phase 2

---

#### **NFR-USA-003: Error Handling**

**Priority**: P0 (Critical)

**Requirements**:

- All error messages human-readable (no technical jargon or error codes)
- Errors provide actionable guidance:
    - ❌ Bad: "Error: Budget check failed"
    - ✅ Good: "Insufficient budget. Available: Rp 18 juta, Requested: Rp 25 juta. You can: 1) Reduce PR amount, 2) Request budget revision, or 3) Contact Finance."
- Form validation provides inline feedback (real-time, not just on submit)
- System provides helpful suggestions when possible
- Critical errors logged for admin review

---

### **7.3 Reliability Requirements**

#### **NFR-REL-001: Availability**

**Priority**: P0 (Critical)

**Requirements**:

- **99.5% uptime** during business hours (8 AM - 6 PM WIB, Mon-Fri)
- Planned maintenance only outside business hours (weekends)
- Maximum 2 hours unplanned downtime per month
- System status page showing current health

**Monitoring**:

- Automated health checks every 5 minutes
- Alert IT team if system unresponsive for >2 minutes
- Dashboard showing uptime percentage (daily, weekly, monthly)

---

#### **NFR-REL-002: Data Integrity**

**Priority**: P0 (Critical)

**Requirements**:

- **Zero data loss** for committed transactions
- Database ACID properties enforced (transactions are atomic)
- Data validation at application and database levels
- Foreign key constraints enforced (no orphan records)
- Budget calculations always accurate (committed + actual = total usage)

**Data Quality Checks**:

- Nightly job verifies:
    - Sum of project budgets = total organization budget
    - No negative budget balances
    - All transactions have required tags
    - All commitments have corresponding PR/PO
- Discrepancies flagged for Finance Manager review

---

#### **NFR-REL-003: Backup & Recovery**

**Priority**: P0 (Critical)

**Requirements**:

- **Daily automated backups** at 12 AM WIB
- Backup retention: 30 days rolling, plus year-end snapshots (7 years)
- Backup stored in separate location from primary database
- **Recovery Time Objective (RTO)**: 4 hours (system restored within 4 hours of failure)
- **Recovery Point Objective (RPO)**: 24 hours (maximum 1 day of data loss)

**Testing**:

- Quarterly backup restore test (verify backup integrity)
- Documented restore procedure (step-by-step)

---

### **7.4 Security Requirements**

#### **NFR-SEC-001: Authentication**

**Priority**: P0 (Critical)

**Requirements**:

- Integration with existing Green Ledger authentication
- No separate login (single sign-on)
- Session timeout after 30 minutes of inactivity
- Re-authentication required for sensitive operations (account creation, budget revision >10%)

---

#### **NFR-SEC-002: Authorization**

**Priority**: P0 (Critical)

**Requirements**:

- Role-Based Access Control (RBAC)
- Roles defined in Section 14 (Security & Access Control)
- Principle of least privilege (users only see what they need)
- Cannot bypass permissions via API or direct URL manipulation

---

#### **NFR-SEC-003: Audit Trail**

**Priority**: P0 (Critical)

**Requirements**:

- All CRUD operations on accounts, projects, budgets logged
- Audit log includes: User, Action, Timestamp, Before/After values, IP address
- Audit logs immutable (cannot be edited or deleted by anyone)
- Audit logs retained for 7 years (compliance requirement)
- Finance Manager can query audit logs via UI (search by user, date range, action)

---

### **7.5 Maintainability Requirements**

#### **NFR-MAIN-001: Code Quality**

**Priority**: P1 (High)

**Requirements**:

- Code follows organization coding standards (to be defined)
- Minimum 70% unit test coverage for business logic
- All API endpoints documented (Swagger/OpenAPI)
- Database schema documented (ER diagram, data dictionary)

---

#### **NFR-MAIN-002: Logging**

**Priority**: P1 (High)

**Requirements**:

- Application logs all errors (with stack traces)
- Application logs key operations (PR submission, budget commit, approval)
- Log levels: DEBUG, INFO, WARN, ERROR
- Logs retained for 90 days
- IT admin can access logs via centralized logging system

---

### **7.6 Compatibility Requirements**

#### **NFR-COMP-001: Browser Support**

**Priority**: P0 (Critical)

**Requirements**:

- **Desktop Browsers** (latest 2 versions):
    - Google Chrome (primary, 90% user base)
    - Mozilla Firefox
    - Microsoft Edge
    - Safari (macOS)
- **Mobile Browsers** (view-only, Phase 2 for full mobile support):
    - Chrome Mobile (Android)
    - Safari (iOS)

**Testing**: Automated cross-browser testing on every release

---

#### **NFR-COMP-002: Database Compatibility**

**Priority**: P0 (Critical)

**Requirements**:

- Compatible with PostgreSQL 12+ or MySQL 8+
- Database migrations versioned and automated (Flyway or Liquibase)
- No vendor-specific SQL in application code (use ORM)

---

## **8. USER STORIES**

### **8.1 Epic 1: Account Management**

**Epic Description**: As a Finance Manager, I want to manage the Chart of Accounts, so that our organization has a proper financial structure.

#### **Story 1.1: Create New Account**

As a Finance Manager

I want to create a new account in the COA

So that we can track a new type of expense

Acceptance Criteria:

- I can enter all required account details

- System validates account code uniqueness

- System prevents invalid parent-child relationships

- New account requires approval before becoming active

- I receive confirmation after successful creation

Priority: P0

Story Points: 5

#### **Story 1.2: Edit Account Details**

As a Finance Manager

I want to edit account descriptions and settings

So that account information stays accurate

Acceptance Criteria:

- I can edit name, description, and flags

- I cannot edit code or parent if account is in use

- Changes to active accounts require approval

- I can see version history of changes

Priority: P1

Story Points: 3

#### **Story 1.3: Deactivate Unused Account**

As a Finance Manager

I want to deactivate accounts no longer needed

So that users don't accidentally use deprecated accounts

Acceptance Criteria:

- I can deactivate any account

- System warns if account has active budget or recent transactions

- Deactivated accounts don't show in account selection dropdowns

- I can reactivate if needed

Priority: P1

Story Points: 2

---

### **8.2 Epic 2: Project Tracking**

**Epic Description**: As a Research Manager, I want to track expenses by research project, so I can manage project budgets effectively.

#### **Story 2.1: Create New Research Project**

As a Finance Staff

I want to create a new research project in the system

So that expenses can be tracked per project

Acceptance Criteria:

- I can enter project details (code, name, PI, dates, budget)

- System auto-generates project code based on department and year

- I can allocate budget across expense accounts

- System validates that allocations sum to total budget

- PI is notified when project is created

Priority: P0

Story Points: 8

#### **Story 2.2: Allocate Project Budget**

As a Finance Manager

I want to allocate a project's budget across expense accounts

So that budget control is enforced at account level

Acceptance Criteria:

- I can allocate amounts to each account

- I can use templates from similar projects

- System enforces that sum equals total project budget

- I can save as template for future projects

- Allocations require approval before activation

Priority: P0

Story Points: 5

#### **Story 2.3: View Project Budget Status**

As a PI

I want to see my project's budget status at any time

So I know how much budget I have left

Acceptance Criteria:

- I see total budget, committed, spent, and remaining

- I see breakdown by expense account

- I see list of all PRs and payments for my project

- I can see budget utilization percentage and trend chart

- Information is real-time (not cached)

Priority: P0

Story Points: 5

---

### **8.3 Epic 3: Budget Control**

**Epic Description**: As an Organization, we want automated budget control, so we prevent overspending and maintain financial discipline.

#### **Story 3.1: Check Budget When Creating PR**

As a Researcher creating a PR

I want to see if my project has enough budget

So I don't waste time submitting an over-budget PR

Acceptance Criteria:

- System checks budget automatically when I enter amount

- I see clear message: budget OK, warning, or insufficient

- If insufficient, I see exact shortfall and suggested actions

- I cannot submit PR if budget is insufficient

- I can request budget revision directly from PR form

Priority: P0

Story Points: 8

#### **Story 3.2: Commit Budget on PR Approval**

As a Finance System

I want to commit budget when a PR is approved

So that the same budget cannot be spent twice

Acceptance Criteria:

- Budget is committed automatically when PR approved

- Committed amount deducted from available budget

- Other users see updated availability immediately

- Commitment is released when payment made or PR cancelled

- Audit trail shows all commitment/release actions

Priority: P0

Story Points: 8

#### **Story 3.3: Request Budget Revision**

As a PI

I want to request additional budget or reallocate between accounts

So I can adapt to changing project needs

Acceptance Criteria:

- I can submit budget revision request with justification

- System routes to appropriate approvers based on revision type

- I can track approval status

- I'm notified when approved or rejected

- Approved revisions update project budget immediately

Priority: P1

Story Points: 8

---

### **8.4 Epic 4: Multi-Dimensional Reporting**

**Epic Description**: As a Manager, I want to analyze expenses from multiple dimensions, so I can make data-driven decisions.

#### **Story 4.1: View Budget vs Actual by Project**

As a Research Manager

I want to see budget vs actual for each of my projects

So I can identify projects at risk of overrun

Acceptance Criteria:

- I see list of all my department's projects

- For each project: budget, committed, actual, balance, %

- I can sort by any column (e.g., highest overrun risk first)

- I can drill down into project to see account-level detail

- I can filter by status, date range, site

Priority: P0

Story Points: 5

#### **Story 4.2: View Spending by Account Across Projects**

As a Finance Manager

I want to see total spending for each account across all projects

So I can identify spending patterns and budget needs

Acceptance Criteria:

- I see list of all accounts with total budget, actual, balance

- I can drill down into account to see which projects contributed

- I can filter by fiscal year, site, department

- I can export to Excel for further analysis

Priority: P0

Story Points: 5

#### **Story 4.3: View Program vs Non-Program Ratio**

As a Director

I want to see our Program vs Non-Program expense ratio

So I ensure we meet donor requirements (75:25 target)

Acceptance Criteria:

- Dashboard shows current ratio (e.g., 73:27)

- Trend chart shows ratio over time (monthly)

- I can see breakdown of what comprises each category

- I can see projection: at current rate, year-end ratio will be X

- I receive alert if ratio falls below 70:30

Priority: P1

Story Points: 5

---

### **8.5 Epic 5: PR Integration**

**Epic Description**: As a Researcher, I want seamless account selection when creating PRs, so I can submit accurate requests quickly.

#### **Story 5.1: Select Account in PR Form**

As a Researcher creating a PR

I want to easily select the correct account

So my PR is classified properly

Acceptance Criteria:

- I can search accounts by code or name

- System suggests accounts based on item description keywords

- I can browse accounts by category (guided wizard)

- I see account description and typical usage examples

- System prevents me from selecting inactive or parent accounts

Priority: P0

Story Points: 5

#### **Story 5.2: Tag PR with Project**

As a Researcher creating a PR

I want to tag my PR to my research project

So expenses are tracked per project

Acceptance Criteria:

- I can search and select my project

- System shows only projects I'm authorized to use

- System auto-fills site, department based on project

- System shows project budget status after selection

- I cannot submit Program expense PR without project tag

Priority: P0

Story Points: 5

#### **Story 5.3: See Budget Availability Before Submit**

As a Researcher creating a PR

I want to see budget availability before submitting

So I don't submit a PR that will be rejected

Acceptance Criteria:

- After entering amount, system shows budget check result

- I see both project-level and account-level availability

- Color indicator shows green/yellow/red status

- If red, I see shortfall amount and suggested actions

- I cannot submit if budget insufficient

Priority: P0

Story Points: 3

---

## **9. SYSTEM ARCHITECTURE**

### **9.1 High-Level Architecture**

┌─────────────────────────────────────────────────────────────┐

│ PRESENTATION LAYER │

├─────────────────────────────────────────────────────────────┤

│ │

│ ┌───────────────┐ ┌───────────────┐ ┌──────────────┐ │

│ │ Web UI │ │ Mobile View │ │ API Docs │ │

│ │ (React/Vue) │ │ (Responsive) │ │ (Swagger) │ │

│ └───────────────┘ └───────────────┘ └──────────────┘ │

│ │ │ │ │

└───────────┼──────────────────┼──────────────────┼───────────┘

            │                  │                  │

            └──────────────────┴──────────────────┘

                               │

┌──────────────────────────────┴──────────────────────────────┐

│ APPLICATION LAYER (API) │

├─────────────────────────────────────────────────────────────┤

│ │

│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ │

│ │ Account │ │ Project │ │ Budget │ │

│ │ Service │ │ Service │ │ Service │ │

│ └─────────────┘ └─────────────┘ └─────────────┘ │

│ │

│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ │

│ │ Tag │ │ Report │ │ Approval │ │

│ │ Service │ │ Service │ │ Service │ │

│ └─────────────┘ └─────────────┘ └─────────────┘ │

│ │

│ ┌──────────────────────────────────────────────────┐ │

│ │ Integration Layer (PR/PO Module) │ │

│ └──────────────────────────────────────────────────┘ │

│ │

└──────────────────────────┬──────────────────────────────────┘

                           │

┌──────────────────────────┴──────────────────────────────────┐

│ DATA LAYER │

├─────────────────────────────────────────────────────────────┤

│ │

│ ┌──────────────────────────────────────────────────┐ │

│ │ Relational Database (PostgreSQL / MySQL) │ │

│ │ │ │

│ │ Tables: │ │

│ │ - accounts │ │

│ │ - projects │ │

│ │ - project_budgets │ │

│ │ - budget_commitments │ │

│ │ - transactions (from PR/PO) │ │

│ │ - tags (dimensions) │ │

│ │ - audit_logs │ │

│ └──────────────────────────────────────────────────┘ │

│ │

└─────────────────────────────────────────────────────────────┘

---

### **9.2 Component Descriptions**

#### **9.2.1 Account Service**

**Responsibilities**:

- CRUD operations on accounts
- Account hierarchy management
- Account validation (code uniqueness, parent-child relationships)
- Account search and autocomplete
- Account activation/deactivation

---

#### **9.2.2 Project Service**

**Responsibilities**:

- CRUD operations on projects
- Project code auto-generation
- Project team management
- Project status lifecycle management
- Project search and filtering

---

#### **9.2.3 Budget Service**

**Responsibilities**:

- Budget allocation at project and account levels
- Real-time budget availability calculation
- Budget commitment and release
- Budget revision workflow
- Budget rollup calculations

**Critical Logic**: Budget Availability Calculation

function calculateAvailability(projectId, accountId) {

    // Get budget allocation

    const budget = getBudgetAllocation(projectId, accountId);



    // Get committed amount (approved PRs not yet paid)

    const committed = sumCommitments(projectId, accountId, status='Active');



    // Get actual spent (payments made)

    const actual = sumTransactions(projectId, accountId);



    // Calculate available

    const available = budget - committed - actual;



    return {

        budget,

        committed,

        actual,

        available,

        utilization: ((committed + actual) / budget) * 100

    };

}

---

#### **9.2.4 Tag Service**

**Responsibilities**:

- Manage dimension master data (sites, funding sources, etc.)
- Validate tag values
- Auto-populate tags based on context

---

#### **9.2.5 Report Service**

**Responsibilities**:

- Generate standard reports (Budget vs Actual, Program vs Non-Program, etc.)
- Execute ad-hoc queries with filters
- Export to Excel/CSV/PDF
- Cache frequently accessed reports

---

#### **9.2.6 Approval Service**

**Responsibilities**:

- Manage approval workflows (account creation, budget revision)
- Route approval requests to appropriate approvers
- Track approval status
- Send notifications

---

#### **9.2.7 Integration Layer (PR/PO Module)**

**Responsibilities**:

- Provide account selection widget for PR form
- Trigger budget check when PR is created
- Commit budget when PR is approved
- Release budget when payment is made or PR is cancelled
- Tag transactions with account and project

---

### **9.3 Data Flow Examples**

#### **Example 1: User Creates PR with Account and Project**

[User] → [PR Form UI]

         ↓

    1. User types "benih padi" in Item Description

         ↓

    2. System suggests: "5210 - Seeds" (Account Service: search API)

         ↓

    3. User selects Account 5210

         ↓

    4. User selects Project AGR-2026-001 (Project Service: my-projects API)

         ↓

    5. System auto-fills: Site=Klaten, Dept=Agronomy (from Project)

         ↓

    6. User enters Amount: Rp 5,000,000

         ↓

    7. System checks budget (Budget Service: availability API)

         Result: Available=Rp 18,500,000 ✅

         ↓

    8. User clicks "Submit PR"

         ↓

    9. System creates PR with tags (PR Service: create PR)

         ↓

    10. System soft-commits budget (Budget Service: commit API, status=Pending)

         ↓

    11. System sends notification to Manager for approval

#### **Example 2: Manager Approves PR → Budget Committed**

[Manager] → [Approval Dashboard]

         ↓

    1. Manager clicks "Approve" on PR-2026-00125

         ↓

    2. System changes PR status to Approved (PR Service: approve API)

         ↓

    3. System calls Budget Service: commit API (status=Active)

         ↓

    4. Budget Service updates database:

       - budget_commitments table: INSERT new record

       - project_budgets table: committed += 5,000,000

         ↓

    5. System sends notification to PI: "PR Approved"

         ↓

    6. System sends notification to Procurement: "Create PO"

#### **Example 3: Payment Made → Budget Released, Actual Updated**

[Finance] → [Payment Module]

         ↓

    1. Finance processes payment for PO linked to PR-2026-00125

         ↓

    2. System records payment transaction (Payment Service)

         ↓

    3. System calls Budget Service: release API

         ↓

    4. Budget Service updates database:

       - budget_commitments table: DELETE commitment record

       - project_budgets table: committed -= 5,000,000

       - project_budgets table: actual += 5,000,000

       - transactions table: INSERT payment record with tags

         ↓

    5. Net effect: Available budget unchanged (committed → actual)

         ↓

    6. System sends notification to PI: "Payment made for PR-2026-00125"

---

## **10. DATA MODEL**

### **10.1 Entity Relationship Diagram (ERD)**

┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐

│ accounts │ │ projects │ │ project_budgets │

├─────────────────┤ ├─────────────────┤ ├─────────────────┤

│ id (PK) │ │ id (PK) │◄────────┤ id (PK) │

│ code (UNIQUE) │◄────┐ │ code (UNIQUE) │ │ project_id (FK) │

│ name │ │ │ name │ │ account_id (FK) │───┐

│ type │ │ │ short_name │ │ budget_amount │ │

│ category │ │ │ pi_user_id (FK) │ │ fiscal_year │ │

│ sub_category │ └───┤ department │ │ created_at │ │

│ parent_id (FK) │ │ site │ └─────────────────┘ │

│ level │ │ sub_division │ │

│ description │ │ category │ │

│ is_active │ │ start_date │ │

│ budget_control │ │ end_date │ │

│ created_at │ │ fiscal_year │ │

└─────────────────┘ │ status │ │

        ▲                   │ total_budget    │                               │

        │                   │ funding_source  │                               │

        │                   │ created_at      │                               │

        │                   └─────────────────┘                               │

        │                           ▲                                         │

        │                           │                                         │

        │                           │                                         │

┌───────┴─────────┐ ┌─────────┴──────────┐ ┌────────────────────┴───┐

│ transactions │ │budget_commitments │ │ dimension_values │

├─────────────────┤ ├────────────────────┤ ├────────────────────────┤

│ id (PK) │ │ id (PK) │ │ id (PK) │

│ transaction_type│ │ project_id (FK) │ │ dimension_type │

│ account_id (FK) │ │ account_id (FK) │ │ code (UNIQUE) │

│ project_id (FK) │ │ pr_id (FK) │ │ name │

│ amount │ │ po_id (FK) │ │ parent_id (FK) │

│ description │ │ amount │ │ is_active │

│ transaction_date│ │ committed_date │ │ created_at │

│ fiscal_year │ │ status │ └────────────────────────┘

│ site │ │ committed_by │ Types:

│ pr_id (FK) │ │ created_at │ - sites

│ po_id (FK) │ └────────────────────┘ - funding_sources

│ tags (JSONB) │ - activity_types

│ created_at │

└─────────────────┘

┌─────────────────┐ ┌─────────────────┐

│ audit_logs │ │ users │

├─────────────────┤ ├─────────────────┤

│ id (PK) │ │ id (PK) │

│ table_name │ │ username │

│ record_id │ │ full_name │

│ action │ │ email │

│ user_id (FK) │───────┤ department │

│ old_values │ │ role │

│ new_values │ │ is_active │

│ ip_address │ │ created_at │

│ timestamp │ └─────────────────┘

└─────────────────┘

---

### **10.2 Table Schemas**

#### **accounts**

CREATE TABLE accounts (

    id SERIAL PRIMARY KEY,

    code VARCHAR(20) UNIQUE NOT NULL,

    name VARCHAR(100) NOT NULL,

    type VARCHAR(20) NOT NULL,  -- 'Expense', 'Revenue'

    category VARCHAR(20) NOT NULL,  -- 'Program', 'Non-Program'

    sub_category VARCHAR(50),  -- 'Research', 'Knowledge', etc.

    parent_id INTEGER REFERENCES accounts(id),

    level INTEGER NOT NULL,  -- 1-4

    description TEXT,

    typical_usage TEXT,

    is_active BOOLEAN DEFAULT TRUE,

    budget_control BOOLEAN DEFAULT TRUE,

    tax_applicable BOOLEAN DEFAULT FALSE,

    created_by INTEGER REFERENCES users(id),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



    CHECK (level BETWEEN 1 AND 4),

    CHECK (type IN ('Expense', 'Revenue')),

    CHECK (category IN ('Program', 'Non-Program'))

);

CREATE INDEX idx_accounts_code ON accounts(code);

CREATE INDEX idx_accounts_parent ON accounts(parent_id);

CREATE INDEX idx_accounts_active ON accounts(is_active);

#### **projects**

CREATE TABLE projects (

    id SERIAL PRIMARY KEY,

    code VARCHAR(30) UNIQUE NOT NULL,  -- e.g., AGR-2026-001

    name VARCHAR(200) NOT NULL,

    short_name VARCHAR(50),

    pi_user_id INTEGER REFERENCES users(id),

    department VARCHAR(50) NOT NULL,  -- Agronomy, Crops Protection, etc.

    site VARCHAR(50) NOT NULL,  -- Klaten, Yogyakarta, Bogor

    sub_division VARCHAR(50),  -- Research, Knowledge

    category VARCHAR(20) NOT NULL,  -- Program, Non-Program

    start_date DATE NOT NULL,

    end_date DATE NOT NULL,

    fiscal_year INTEGER NOT NULL,

    status VARCHAR(20) DEFAULT 'Planning',  -- Planning, Active, Completed, Cancelled

    total_budget DECIMAL(15,2) NOT NULL,

    funding_source VARCHAR(100),

    description TEXT,

    created_by INTEGER REFERENCES users(id),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



    CHECK (end_date >= start_date),

    CHECK (total_budget > 0),

    CHECK (status IN ('Planning', 'Active', 'Completed', 'Cancelled')),

    CHECK (category IN ('Program', 'Non-Program'))

);

CREATE INDEX idx_projects_code ON projects(code);

CREATE INDEX idx_projects_pi ON projects(pi_user_id);

CREATE INDEX idx_projects_status ON projects(status);

CREATE INDEX idx_projects_fiscal_year ON projects(fiscal_year);

#### **project_budgets**

CREATE TABLE project_budgets (

    id SERIAL PRIMARY KEY,

    project_id INTEGER NOT NULL REFERENCES projects(id),

    account_id INTEGER NOT NULL REFERENCES accounts(id),

    budget_amount DECIMAL(15,2) NOT NULL,

    fiscal_year INTEGER NOT NULL,

    notes TEXT,

    created_by INTEGER REFERENCES users(id),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



    UNIQUE(project_id, account_id, fiscal_year),

    CHECK (budget_amount >= 0)

);

CREATE INDEX idx_project_budgets_project ON project_budgets(project_id);

CREATE INDEX idx_project_budgets_account ON project_budgets(account_id);

CREATE INDEX idx_project_budgets_fy ON project_budgets(fiscal_year);

#### **budget_commitments**

CREATE TABLE budget_commitments (

    id SERIAL PRIMARY KEY,

    project_id INTEGER NOT NULL REFERENCES projects(id),

    account_id INTEGER NOT NULL REFERENCES accounts(id),

    pr_id INTEGER,  -- Reference to PR (from external PR module)

    po_id INTEGER,  -- Reference to PO (from external PO module)

    amount DECIMAL(15,2) NOT NULL,

    committed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    status VARCHAR(20) DEFAULT 'Active',  -- Active, Released, Cancelled

    committed_by INTEGER REFERENCES users(id),

    released_date TIMESTAMP,

    released_reason VARCHAR(50),  -- Payment, Cancellation

    notes TEXT,



    CHECK (amount > 0),

    CHECK (status IN ('Active', 'Released', 'Cancelled'))

);

CREATE INDEX idx_commitments_project ON budget_commitments(project_id);

CREATE INDEX idx_commitments_account ON budget_commitments(account_id);

CREATE INDEX idx_commitments_pr ON budget_commitments(pr_id);

CREATE INDEX idx_commitments_status ON budget_commitments(status);

#### **transactions**

CREATE TABLE transactions (

    id SERIAL PRIMARY KEY,

    transaction_type VARCHAR(20) NOT NULL,  -- PR, PO, Payment, Journal

    account_id INTEGER NOT NULL REFERENCES accounts(id),

    project_id INTEGER REFERENCES projects(id),  -- NULL for Non-Program

    amount DECIMAL(15,2) NOT NULL,

    description TEXT,

    transaction_date DATE NOT NULL,

    fiscal_year INTEGER NOT NULL,



    -- Dimensional tags

    site VARCHAR(50),

    sub_division VARCHAR(50),

    funding_source VARCHAR(100),

    activity_type VARCHAR(50),



    -- References to external modules

    pr_id INTEGER,

    po_id INTEGER,

    invoice_id INTEGER,

    payment_id INTEGER,



    -- Additional metadata (flexible JSON for future extensions)

    tags JSONB,



    created_by INTEGER REFERENCES users(id),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



    CHECK (amount != 0)

);

CREATE INDEX idx_transactions_account ON transactions(account_id);

CREATE INDEX idx_transactions_project ON transactions(project_id);

CREATE INDEX idx_transactions_date ON transactions(transaction_date);

CREATE INDEX idx_transactions_fy ON transactions(fiscal_year);

CREATE INDEX idx_transactions_type ON transactions(transaction_type);

CREATE INDEX idx_transactions_tags ON transactions USING GIN(tags); -- For JSON queries

#### **dimension_values**

CREATE TABLE dimension_values (

    id SERIAL PRIMARY KEY,

    dimension_type VARCHAR(50) NOT NULL,  -- sites, funding_sources, activity_types

    code VARCHAR(20) NOT NULL,

    name VARCHAR(100) NOT NULL,

    parent_id INTEGER REFERENCES dimension_values(id),

    description TEXT,

    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



    UNIQUE(dimension_type, code)

);

CREATE INDEX idx_dimensions_type ON dimension_values(dimension_type);

CREATE INDEX idx_dimensions_code ON dimension_values(code);

#### **audit_logs**

CREATE TABLE audit_logs (

    id SERIAL PRIMARY KEY,

    table_name VARCHAR(50) NOT NULL,

    record_id INTEGER NOT NULL,

    action VARCHAR(20) NOT NULL,  -- INSERT, UPDATE, DELETE

    user_id INTEGER REFERENCES users(id),

    old_values JSONB,  -- Before update/delete

    new_values JSONB,  -- After insert/update

    ip_address VARCHAR(45),

    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



    CHECK (action IN ('INSERT', 'UPDATE', 'DELETE'))

);

CREATE INDEX idx_audit_table ON audit_logs(table_name);

CREATE INDEX idx_audit_record ON audit_logs(record_id);

CREATE INDEX idx_audit_timestamp ON audit_logs(timestamp);

---

### **10.3 Key Queries**

#### **Query 1: Get Budget Availability (Critical for Budget Check)**

-- Real-time budget availability for a project-account combination

SELECT

    pb.budget_amount,

    COALESCE(SUM(CASE WHEN bc.status = 'Active' THEN bc.amount ELSE 0 END), 0) AS committed,

    COALESCE(SUM(t.amount), 0) AS actual_spent,

    pb.budget_amount -

        COALESCE(SUM(CASE WHEN bc.status = 'Active' THEN bc.amount ELSE 0 END), 0) -

        COALESCE(SUM(t.amount), 0) AS available

FROM project_budgets pb

LEFT JOIN budget_commitments bc

    ON bc.project_id = pb.project_id

    AND bc.account_id = pb.account_id

    AND bc.status = 'Active'

LEFT JOIN transactions t

    ON t.project_id = pb.project_id

    AND t.account_id = pb.account_id

    AND t.fiscal_year = pb.fiscal_year

WHERE pb.project_id = :project_id

    AND pb.account_id = :account_id

    AND pb.fiscal_year = :fiscal_year

GROUP BY pb.budget_amount;

#### **Query 2: Budget vs Actual Report by Project**

-- Budget vs Actual for all accounts of a project

SELECT

    a.code AS account_code,

    a.name AS account_name,

    pb.budget_amount,

    COALESCE(SUM(CASE WHEN bc.status = 'Active' THEN bc.amount ELSE 0 END), 0) AS committed,

    COALESCE(SUM(t.amount), 0) AS actual_spent,

    pb.budget_amount -

        COALESCE(SUM(CASE WHEN bc.status = 'Active' THEN bc.amount ELSE 0 END), 0) -

        COALESCE(SUM(t.amount), 0) AS balance,

    ROUND((COALESCE(SUM(CASE WHEN bc.status = 'Active' THEN bc.amount ELSE 0 END), 0) +

           COALESCE(SUM(t.amount), 0)) / pb.budget_amount * 100, 2) AS utilization_pct

FROM project_budgets pb

JOIN accounts a ON a.id = pb.account_id

LEFT JOIN budget_commitments bc

    ON bc.project_id = pb.project_id

    AND bc.account_id = pb.account_id

    AND bc.status = 'Active'

LEFT JOIN transactions t

    ON t.project_id = pb.project_id

    AND t.account_id = pb.account_id

    AND t.fiscal_year = pb.fiscal_year

WHERE pb.project_id = :project_id

    AND pb.fiscal_year = :fiscal_year

GROUP BY a.code, a.name, pb.budget_amount

ORDER BY a.code;

#### **Query 3: Program vs Non-Program Ratio**

-- Calculate Program vs Non-Program spending for a fiscal year

SELECT

    a.category,

    SUM(t.amount) AS total_spent

FROM transactions t

JOIN accounts a ON a.id = t.account_id

WHERE t.fiscal_year = :fiscal_year

GROUP BY a.category;

-- Result format:

-- category | total_spent

-- ---------------+--------------

-- Program | 15,000,000,000

-- Non-Program | 5,000,000,000

--

-- Ratio: 75:25 (Program:Non-Program)

---

## **11. USER INTERFACE REQUIREMENTS**

### **11.1 Dashboard (Home Page)**

**Purpose**: Provide at-a-glance financial overview for different user roles

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ [Logo] Green Ledger - COA Module [User: Dr. Budi ▼] │

├──────────────────────────────────────────────────────────────┤

│ │

│ 📊 FINANCIAL DASHBOARD - FY 2026 │

│ │

│ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐│

│ │ Organization │ │ My Projects │ │ Pending Actions││

│ │ Budget Status │ │ (For PIs) │ │ ││

│ │ │ │ │ │ ││

│ │ Total: 20B │ │ Active: 5 │ │ PRs Pending: 3 ││

│ │ Spent: 12B │ │ Budget: 450M │ │ Approvals: 2 ││

│ │ Balance: 8B │ │ Spent: 280M │ │ ││

│ │ [Progress Bar] │ │ [Progress Bar] │ │ [View All] ││

│ └────────────────┘ └────────────────┘ └────────────────┘│

│ │

│ Program vs Non-Program Ratio (Target: 75:25) │

│ ┌──────────────────────────────────────────────────────┐ │

│ │ Program: ████████████████ 73% │ │

│ │ Non-Program: ████ 27% │ │

│ └──────────────────────────────────────────────────────┘ │

│ ⚠️ Warning: Ratio below target (need 2% more Program) │

│ │

│ Recent Transactions │

│ ┌──────────────────────────────────────────────────────┐ │

│ │ Date │ Project │ Account │ Amount │... │ │

│ ├────────────┼──────────────┼─────────┼───────────┼───┤ │

│ │ 2026-02-03 │ AGR-2026-001 │ 5210 │ Rp 5M │... │ │

│ │ 2026-02-02 │ CRP-2026-015 │ 5230 │ Rp 3.5M │... │ │

│ │ ... │ │

│ └──────────────────────────────────────────────────────┘ │

│ │

│ Quick Links │

│ [Create PR] [View Projects] [Budget Reports] [Help] │

│ │

└──────────────────────────────────────────────────────────────┘

**Widget Requirements**:

1. **Organization Budget Status** (Finance Manager only): \
    - Shows total org budget, spent, balance
    - Progress bar color-coded (green &lt;70%, yellow 70-90%, red >90%)
    - Clickable → drills down to department-level detail

2. **My Projects** (For PIs): \
    - Shows only projects where user is PI or team member
    - Displays count of active projects, total budget, total spent
    - Clickable → goes to project list filtered to user's projects

3. **Pending Actions**: \
    - PRs pending user's approval (if Manager)
    - Budget revisions pending approval (if Finance Manager)
    - Count badges on each item

4. **Program vs Non-Program Ratio** (Finance Manager & Director): \
    - Visual bar chart showing current ratio
    - Alert if ratio deviates >3% from target
    - Clickable → goes to detailed breakdown report

5. **Recent Transactions** (All users): \
    - Last 10 transactions relevant to user (their projects or department)
    - Sortable columns
    - Clickable → view transaction details

---

### **11.2 Account Management UI**

#### **11.2.1 Account List Page**

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ Chart of Accounts │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ [Search accounts...] [FY: 2026 ▼] [All ▼] │ │

│ │ [+ Add Account] [Templates ▼] [Import CSV] [Export]│ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ [Columns ▼] [Table/Tree Toggle] [Expand All] [Collapse All]│

│ │

│ ┌──────────────────────────────────────────────────────┐ │

│ │ Code │ Name │ Type │Budget│Actual│ │

│ ├───────┼──────────────────────┼────────┼──────┼──────┤ │

│ │▼ 5000 │RESEARCH EXPENSES │Expense │ 10B │ 6.5B │ │

│ │ ▼5100│Personnel & Labour │Expense │ 3B │ 1.8B │ │

│ │ 5110│Labour │Expense │ 3B │ 1.8B │ │

│ │ ▼5200│Materials & Supplies │Expense │ 4B │ 2.5B │ │

│ │ 5210│Seeds │Expense │800M │ 450M │ │

│ │ 5220│Fertilizers │Expense │ 1.2B │ 700M │ │

│ │ 5230│Pesticides │Expense │600M │ 380M │ │

│ │ ... │ │

│ └──────────────────────────────────────────────────────┘ │

│ │

│ Showing 21 accounts of 63 total │

│ [1] [2] [3] ... [7] │

│ │

└──────────────────────────────────────────────────────────────┘

**Features**:

1. **Tree View**:
    - Collapsible hierarchy (4 levels)
    - Indentation shows parent-child relationships
    - +/- icons to expand/collapse
    - Expand All / Collapse All buttons
2. **Filters**:
    - Fiscal Year selector (affects Budget/Actual columns)
    - Status filter (Active / Inactive / All)
    - Category filter (Program / Non-Program / All)
    - Type filter (Expense / Revenue / All)
3. **Search**:
    - Real-time search as user types
    - Searches across: Code, Name, Description
4. **Actions**:
    - Add Account (Finance Manager only)
    - Edit/View (click on row)
    - Export to Excel/CSV (all accounts or filtered)

---

#### **11.2.2 Account Detail/Edit Page**

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ Account Details: 5210 - Seeds & Planting Materials │

│ [Edit] [Deactivate] [View History] [Back to List] │

├──────────────────────────────────────────────────────────────┤

│ │

│ BASIC INFORMATION │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Account Code: 5210 (read-only) │ │

│ │ Account Name: [Seeds & Planting Materials_________] │ │

│ │ │ │

│ │ Account Type: [Expense ▼] │ │

│ │ Category: [Program ▼] │ │

│ │ Sub-Category: [Research - Materials ▼] │ │

│ │ │ │

│ │ Parent Account: [5200 - Materials & Supplies ▼] │ │

│ │ Level: 4 (auto-calculated) │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ SETTINGS │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ ☑ Active │ │

│ │ ☑ Budget Control Enabled │ │

│ │ ☑ Tax Applicable (VAT) │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ DESCRIPTION & USAGE │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Description: │ │

│ │ [All seeds and planting materials for research trials]│ │

│ │ [including cereal, legume, and horticultural crops___]│ │

│ │ │ │

│ │ Typical Usage: │ │

│ │ - Rice, wheat, corn seeds │ │

│ │ - Vegetable and fruit seeds │ │

│ │ - Seedlings and propagation materials │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ BUDGET OVERVIEW (FY 2026) │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Total Budget: Rp 800,000,000 │ │

│ │ Committed: Rp 120,000,000 (15%) │ │

│ │ Actual Spent: Rp 450,000,000 (56.25%) │ │

│ │ Available: Rp 230,000,000 (28.75%) │ │

│ │ │ │

│ │ [██████████████░░░░░░] 71.25% Utilized │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ USAGE BY PROJECT (Top 5) │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Project Code │ Project Name │ Budget │ Spent│ │

│ ├─────────────────┼─────────────────────┼────────┼──────┤ │

│ │ AGR-2026-001 │ Penelitian Cabai │ 80M │ 45M │ │

│ │ AGR-2026-002 │ Padi Organik │ 120M │ 75M │ │

│ │ CRP-2026-015 │ Jagung IPM │ 60M │ 35M │ │

│ │ ... │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ [Save Changes] [Cancel] │

│ │

└──────────────────────────────────────────────────────────────┘

**Validation on Save**:

- Account Name required, max 100 characters
- Parent account must be valid (one level above)
- If deactivating: warn if has active budget or recent transactions
- If changing Budget Control setting: confirm impact on existing PRs

---

### **11.3 Project Management UI**

#### **11.3.1 Project List Page**

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ Research Projects │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ [Search projects...] [FY: 2026 ▼] [Status: All ▼]│ │

│ │ [+ New Project] [Import] [Export] │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ Filters: [My Projects] [Department ▼] [Site ▼] │

│ │

│ ┌──────────────────────────────────────────────────────┐ │

│ │Code │Name │PI │Budget│Spent│Status │ │

│ ├───────────┼────────────┼────────┼──────┼─────┼───────┤ │

│ │AGR-2026-001│Cabai Layu │Dr.Budi │ 80M │ 56M │Active │ │

│ │AGR-2026-002│Padi Organik│Dr.Siti │120M │ 88M │Active │ │

│ │CRP-2026-015│Jagung IPM │Dr.Andi │ 95M │ 62M │Active │ │

│ │PDT-2026-008│Test Pupuk X│Dr.Budi │ 60M │ 38M │Active │ │

│ │AGR-2025-042│Tomat Lama │Dr.Budi │100M │100M │Complete││

│ │ ... │ │

│ └──────────────────────────────────────────────────────┘ │

│ │

│ Summary: │

│ Total Projects: 50 │ Active: 47 │ Completed: 3 │

│ Total Budget: Rp 15B │ Total Spent: Rp 10.2B (68%) │

│ │

│ [1] [2] [3] ... [5] │

│ │

└──────────────────────────────────────────────────────────────┘

**Features**:

- **Filters**:
    - My Projects (shows only projects where user is PI/team member)
    - Department (Agronomy, Crops Protection, etc.)
    - Site (Klaten, Yogyakarta, Bogor, All)
    - Status (Active, Planning, Completed, Cancelled, All)
    - Fiscal Year
- **Sorting**: Click column headers to sort
- **Row Actions**: Click row → go to Project Detail page
- **Bulk Actions**: Select multiple → Export, Change Status (batch)
- **Summary Stats**: Shows aggregate totals at bottom

---

#### **11.3.2 Project Detail Page**

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ AGR-2026-001: Penelitian Cabai Tahan Layu │

│ [Edit] [Budget Revision] [Close Project] [View PRs] │

├──────────────────────────────────────────────────────────────┤

│ │

│ PROJECT INFORMATION │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Code: AGR-2026-001 │ │

│ │ Name: Penelitian Cabai Tahan Layu Fusarium │ │

│ │ PI: Dr. Budi Santoso │ │

│ │ Department: Agronomy │ │

│ │ Site: Klaten │ │

│ │ Duration: 2026-01-01 to 2026-12-31 │ │

│ │ Status: 🟢 Active │ │

│ │ Funding: Grant B - Sustainable Agriculture │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ BUDGET OVERVIEW │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Total Budget: Rp 80,000,000 │ │

│ │ Committed: Rp 24,500,000 (30.6%) │ │

│ │ Actual Spent: Rp 35,000,000 (43.8%) │ │

│ │ Available: Rp 20,500,000 (25.6%) │ │

│ │ │ │

│ │ [████████████████░░░░░░░] 74.4% Utilized │ │

│ │ │ │

│ │ ⚠️ Warning: Budget utilization >70%. Monitor closely. │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ BUDGET BY ACCOUNT │

│ ┌────────────────────────────────────────────────────────┐ │

│ │Account│Name │Budget │Committed│Actual │Available││ │

│ ├───────┼───────────┼───────┼─────────┼───────┼─────────┤ │

│ │5210 │Seeds │ 10M │ 3M │ 5M │ 2M ││ │

│ │5220 │Fertilizer │ 15M │ 5M │ 8.5M │ 1.5M ││ │

│ │5240 │Fungicide │ 12M │ 2M │ 6M │ 4M ││ │

│ │5110 │Labour │ 30M │ 10M │ 12M │ 8M ││ │

│ │5410 │Land Rental│ 10M │ 2.5M │ 3.5M │ 4M ││ │

│ │5510 │Travel │ 3M │ 2M │ 0 │ 1M ││ │

│ │ ... │ │

│ │TOTAL │ │ 80M │ 24.5M │ 35M │ 20.5M ││ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ RECENT TRANSACTIONS (Last 10) │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Date │ Type │ Account │ Description │ Amount │ │

│ ├────────────┼─────────┼─────────┼─────────────┼────────┤ │

│ │ 2026-02-03 │ Payment │ 5210 │ Benih cabai │ Rp 5M │ │

│ │ 2026-02-01 │ PR │ 5220 │ Pupuk NPK │ Rp 3M │ │

│ │ ... │ │

│ └────────────────────────────────────────────────────────┘ │

│ [View All Transactions] │

│ │

│ PROJECT TEAM │

│ - Dr. Budi Santoso (PI) │

│ - Siti Nurhaliza (Research Associate) │

│ - Agus Supardi (Field Technician) │

│ │

└──────────────────────────────────────────────────────────────┘

**Interactive Elements**:

1. **Budget Overview Card**:
    - Real-time data (refreshes on page load)
    - Progress bar color: Green (&lt;70%), Yellow (70-90%), Red (>90%)
    - Alert if >70% utilized
2. **Budget by Account Table**:
    - Sortable columns
    - Click account → filter transactions to that account
    - Visual indicator (icon) if account budget fully utilized
3. **Recent Transactions**:
    - Shows last 10, link to view all
    - Click transaction → view transaction details
4. **Action Buttons**:
    - Edit Project: Opens edit form (restricted to Finance)
    - Budget Revision: Opens budget revision request form
    - Close Project: Workflow to mark project as Completed
    - View PRs: Link to PR module filtered to this project

---

### **11.4 PR Integration UI (Account Selection Widget)**

**Context**: This widget is embedded in the PR form (external module)

**Widget Layout**:

┌──────────────────────────────────────────────────────────┐

│ SELECT ACCOUNT \* │

│ ┌────────────────────────────────────────────────────┐ │

│ │ 🔍 Search account by name or code... │ │

│ │ │ │

│ │ Suggestions: │ │

│ │ 🌱 5210 - Seeds & Planting Materials │ │

│ │ 🧪 5220 - Fertilizers & Soil Amendments │ │

│ │ 🐛 5230 - Pesticides │ │

│ │ │ │

│ │ Recently Used: │ │

│ │ • 5210 - Seeds (used 5 times this month) │ │

│ │ • 5240 - Fungicides (used 2 days ago) │ │

│ │ │ │

│ │ Or [Browse by Category] [Help me choose] │ │

│ └────────────────────────────────────────────────────┘ │

│ │

│ Selected: ✅ 5210 - Seeds & Planting Materials │

│ Description: All seeds and planting materials... │

│ │

│ 💰 Budget Status: │

│ Project: AGR-2026-001 - Penelitian Cabai │

│ Account Budget: Rp 10,000,000 │

│ Available: Rp 2,000,000 (20% remaining) ⚠️ │

│ │

└──────────────────────────────────────────────────────────┘

**Widget Behavior**:

1. **Search Autocomplete**:
    - Debounced (300ms delay)
    - Shows top 5 matches
    - Highlights matched text
2. **Suggestions**:
    - AI-powered based on PR item description (if available)
    - Example: Description "benih padi" → suggests 5210 - Seeds
3. **Recently Used**:
    - User's last 5 used accounts
    - Shows usage frequency
4. **Browse by Category**:
    - Opens modal with category tree
    - User clicks through: Materials → Seeds
5. **Budget Status**:
    - Shown immediately after account selected
    - Updates when project or amount changed
    - Color-coded: Green (>40%), Yellow (20-40%), Red (&lt;20%)

---

### **11.5 Reporting UI**

#### **11.5.1 Report Selection Page**

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ Reports & Analytics │

├──────────────────────────────────────────────────────────────┤

│ │

│ STANDARD REPORTS │

│ │

│ ┌─────────────────────┐ ┌─────────────────────┐ │

│ │ 📊 Budget vs Actual │ │ 💰 Spending Analysis│ │

│ │ by Project │ │ by Account │ │

│ │ │ │ │ │

│ │ View spending for │ │ See which accounts │ │

│ │ each project vs │ │ are consuming most │ │

│ │ original budget │ │ of your budget │ │

│ │ │ │ │ │

│ │ [Generate Report] │ │ [Generate Report] │ │

│ └─────────────────────┘ └─────────────────────┘ │

│ │

│ ┌─────────────────────┐ ┌─────────────────────┐ │

│ │ 🎯 Program Ratio │ │ 🏢 Department │ │

│ │ Analysis │ │ Comparison │ │

│ │ │ │ │ │

│ │ Track Program vs │ │ Compare spending │ │

│ │ Non-Program expense │ │ across Agronomy, │ │

│ │ ratio over time │ │ Crops Protection... │ │

│ │ │ │ │ │

│ │ [Generate Report] │ │ [Generate Report] │ │

│ └─────────────────────┘ └─────────────────────┘ │

│ │

│ CUSTOM REPORT BUILDER │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Build your own report with custom filters: │ │

│ │ │ │

│ │ Group By: [Project ▼] │ │

│ │ Filters: [+ Add Filter] │ │

│ │ Columns: [+ Select Columns] │ │

│ │ │ │

│ │ [Generate Custom Report] │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ SAVED REPORTS │

│ - Monthly Budget Report (last run: Feb 1, 2026) │

│ - Quarterly Program Ratio (last run: Jan 15, 2026) │

│ [View All Saved Reports] │

│ │

└──────────────────────────────────────────────────────────────┘

---

#### **11.5.2 Budget vs Actual Report Page**

**Layout**:

┌──────────────────────────────────────────────────────────────┐

│ 📊 Budget vs Actual Report │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Fiscal Year: [2026 ▼] │ │

│ │ Department: [All ▼] │ │

│ │ Site: [All ▼] │ │

│ │ Status: [Active ▼] │ │

│ │ │ │

│ │ [Apply Filters] [Reset] [Export Excel] [Export PDF]│ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ SUMMARY │

│ Total Budget: Rp 15,000,000,000 │

│ Committed: Rp 3,200,000,000 (21.3%) │

│ Actual Spent: Rp 8,500,000,000 (56.7%) │

│ Available: Rp 3,300,000,000 (22.0%) │

│ │

│ DETAILS BY PROJECT │

│ ┌────────────────────────────────────────────────────────┐ │

│ │Project │Name │Budget│Committed│Actual│Balance│% │ │

│ ├──────────┼────────┼──────┼─────────┼──────┼───────┼───┤ │

│ │AGR-2026-1│Cabai │ 80M │ 24.5M │ 35M │ 20.5M │74%│ │

│ │AGR-2026-2│Padi │120M │ 32M │ 56M │ 32M │73%│ │

│ │CRP-2026-15│Jagung │ 95M │ 28M │ 34M │ 33M │65%│ │

│ │PDT-2026-8│Pupuk │ 60M │ 12M │ 26M │ 22M │63%│ │

│ │... │ │

│ │TOTAL │ │ 15B │ 3.2B │ 8.5B │ 3.3B │78%│ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ 💡 Insights: │

│ • 3 projects over 80% budget utilization (high risk) │

│ • Average utilization: 78% (on track for 85% year-end) │

│ • Agronomy dept: 82% utilized (highest among depts) │

│ │

│ [View Chart] [Drill Down] [Schedule Email] │

│ │

└──────────────────────────────────────────────────────────────┘

**Interactive Features**:

1. **Filters**: Apply multiple filters, results update dynamically
2. **Drill Down**: Click project row → see account-level detail
3. **Sorting**: Click column headers to sort
4. **Export**: Excel (raw data), PDF (formatted report)
5. **Chart View**: Toggle to see visual bar/pie charts
6. **Schedule Email**: Set up automated report delivery (weekly/monthly)

---

## **12. INTEGRATION REQUIREMENTS**

### **12.1 Integration with PR Module**

**Integration Point 1: Account Selection in PR Form**

**How it works**:

1. PR Module embeds COA Module's account selection widget (iFrame or Web Component)
2. Widget communicates with COA API to fetch accounts and check budget
3. Selected account code passed back to PR form via callback

**API Contract**:

// PR Module calls COA Module

GET /api/accounts/search?q={query}&activeOnly=true

Response: [

    {

        "id": 123,

        "code": "5210",

        "name": "Seeds & Planting Materials",

        "category": "Program",

        "description": "All seeds and planting materials...",

        "typicalUsage": "Rice, wheat, corn seeds..."

    },

    ...

]

// Widget embedded in PR form

&lt;coa-account-selector

    id="account-selector"

    user-id="456"

    suggested-accounts="5210,5220,5230"

    on-select="handleAccountSelect">

&lt;/coa-account-selector>

// Callback when user selects account

function handleAccountSelect(account) {

    document.getElementById('pr_account_code').value = account.code;

    document.getElementById('pr_account_name').value = account.name;

    // Trigger budget check

    checkBudgetAvailability();

}

---

**Integration Point 2: Budget Check on PR Submission**

**Flow**:

1. User fills PR form (including account, project, amount)
2. User clicks "Check Budget" or "Submit PR"
3. PR Module calls COA API to check budget availability
4. If insufficient, PR Module prevents submission and shows error
5. If sufficient, PR Module allows submission

**API Contract**:

// PR Module calls COA Module

POST /api/budgets/check-availability

Request: {

    "projectId": 45,

    "accountId": 123,

    "amount": 5000000,

    "fiscalYear": 2026

}

Response: {

    "available": true,

    "details": {

        "projectBudget": {

            "total": 80000000,

            "committed": 24500000,

            "actual": 35000000,

            "available": 20500000,

            "requestedAmount": 5000000,

            "remainingAfter": 15500000

        },

        "accountBudget": {

            "total": 10000000,

            "committed": 3000000,

            "actual": 5000000,

            "available": 2000000,

            "requestedAmount": 5000000,

            "remainingAfter": -3000000  // NEGATIVE = INSUFFICIENT

        }

    },

    "status": "INSUFFICIENT",  // OK, WARNING, INSUFFICIENT

    "message": "Insufficient account budget. Available: Rp 2,000,000, Requested: Rp 5,000,000. Shortfall: Rp 3,000,000",

    "suggestions": [

        "Reduce PR amount to Rp 2,000,000 or less",

        "Request budget reallocation from another account",

        "Contact Finance Manager"

    ]

}

// If status = "INSUFFICIENT", PR Module shows error and blocks submission

if (response.status === "INSUFFICIENT") {

    showError(response.message);

    displaySuggestions(response.suggestions);

    disableSubmitButton();

}

---

**Integration Point 3: Budget Commitment on PR Approval**

**Flow**:

1. Manager approves PR in PR Module
2. PR Module calls COA API to commit budget
3. COA Module creates commitment record and updates budget tables
4. COA Module returns success/failure

**API Contract**:

// PR Module calls COA Module on PR approval

POST /api/budgets/commit

Request: {

    "prId": 125,  // PR ID from PR module

    "projectId": 45,

    "accountId": 123,

    "amount": 5000000,

    "fiscalYear": 2026,

    "committedBy": 789  // User ID of approver

}

Response: {

    "success": true,

    "commitmentId": 999,

    "message": "Budget committed successfully",

    "updatedBudget": {

        "projectAvailable": 15500000,

        "accountAvailable": 2000000

    }

}

// If success=false (e.g., budget was spent by another PR in the meantime)

Response: {

    "success": false,

    "error": "INSUFFICIENT_BUDGET",

    "message": "Budget no longer available. Another PR consumed remaining budget.",

    "currentAvailable": 500000

}

// PR Module handles failure: notify user, revert PR approval, suggest action

---

**Integration Point 4: Budget Release on Payment or Cancellation**

**Flow**:

1. Payment made for PO (linked to PR) OR PR is cancelled
2. Payment/PR Module calls COA API to release budget
3. COA Module updates:
    - Delete commitment record
    - Decrease committed amount
    - Increase actual amount (if payment) or just release (if cancelled)

**API Contract**:

// On Payment

POST /api/budgets/release

Request: {

    "prId": 125,

    "paymentId": 555,  // Payment ID from Payment module

    "reason": "PAYMENT",

    "actualAmount": 4800000  // Actual payment (may differ from PR if negotiated)

}

Response: {

    "success": true,

    "message": "Budget commitment released and actual updated",

    "updatedBudget": {

        "committed": 19500000,  // Decreased

        "actual": 39800000,      // Increased

        "available": 15700000    // Net: no change (committed→actual)

    }

}

// On PR Cancellation

POST /api/budgets/release

Request: {

    "prId": 125,

    "reason": "CANCELLED",

    "notes": "Supplier unavailable, PR cancelled by PI"

}

Response: {

    "success": true,

    "message": "Budget commitment released",

    "updatedBudget": {

        "committed": 19500000,  // Decreased

        "actual": 35000000,      // Unchanged

        "available": 20500000    // Increased (budget freed up)

    }

}

---

### **12.2 Integration with PO Module**

**Integration Point: PO Inherits Account and Project from PR**

**Flow**:

1. Procurement creates PO from approved PR
2. PO Module fetches PR details including account and project
3. PO inherits these tags (read-only, cannot change)
4. Budget commitment transferred from PR to PO (or remains if 1:1 relationship)

**API Contract**:

// PO Module fetches PR details (including COA tags)

GET /api/pr/{pr_id}/coa-tags

Response: {

    "prId": 125,

    "accountId": 123,

    "accountCode": "5210",

    "accountName": "Seeds & Planting Materials",

    "projectId": 45,

    "projectCode": "AGR-2026-001",

    "projectName": "Penelitian Cabai",

    "site": "Klaten",

    "fiscalYear": 2026,

    "commitmentId": 999

}

// PO Module updates commitment (link to PO)

PUT /api/budgets/commitments/{commitment_id}

Request: {

    "poId": 777  // Link commitment to PO

}

Response: {

    "success": true,

    "message": "Commitment now linked to PO"

}

---

### **12.3 Integration with Payment/Invoice Module**

**Integration Point: Payment Creates Transaction with COA Tags**

**Flow**:

1. Finance processes payment for invoice (linked to PO → PR)
2. Payment Module creates payment record
3. Payment Module calls COA API to:
    - Release budget commitment
    - Create transaction record with COA tags (account, project, amount)

**API Contract**:

// Payment Module calls COA to create transaction

POST /api/transactions

Request: {

    "transactionType": "PAYMENT",

    "accountId": 123,

    "projectId": 45,

    "amount": 4800000,

    "description": "Payment for benih padi - Supplier ABC",

    "transactionDate": "2026-02-03",

    "prId": 125,

    "poId": 777,

    "invoiceId": 888,

    "paymentId": 555,

    "tags": {

        "site": "Klaten",

        "fundingSource": "Grant B"

    }

}

Response: {

    "success": true,

    "transactionId": 1234,

    "message": "Transaction recorded successfully",

    "budgetUpdated": true

}

// COA Module automatically:

// 1. Releases commitment (calls internal release function)

// 2. Updates actual spent

// 3. Creates transaction record

// 4. Logs in audit trail

---

### **12.4 Integration with User Management (Green Ledger Core)**

**Integration Point: User Roles and Permissions**

**COA Module relies on Green Ledger's user management for**:

- Authentication (login session)
- User details (name, email, department)
- Role-based access control (RBAC)

**API Contract**:

// COA Module calls Green Ledger User API

GET /api/users/current

Response: {

    "userId": 456,

    "username": "budi.santoso",

    "fullName": "Dr. Budi Santoso",

    "email": "budi@org.com",

    "department": "Agronomy",

    "role": "RESEARCHER",  // or FINANCE_STAFF, FINANCE_MANAGER, DIRECTOR

    "permissions": [

        "coa.accounts.view",

        "coa.projects.view_own",

        "coa.pr.create",

        "coa.reports.view_dept"

    ]

}

// COA Module checks permissions before allowing actions

if (user.permissions.includes('coa.accounts.create')) {

    // Show "Add Account" button

} else {

    // Hide button

}

**Permissions Defined**:

<table>
  <tr>
   <td><strong>Permission</strong>
   </td>
   <td><strong>Description</strong>
   </td>
   <td><strong>Roles with Permission</strong>
   </td>
  </tr>
  <tr>
   <td><code>coa.accounts.view</code>
   </td>
   <td>View all accounts
   </td>
   <td>All users
   </td>
  </tr>
  <tr>
   <td><code>coa.accounts.create</code>
   </td>
   <td>Create new accounts
   </td>
   <td>Finance Manager
   </td>
  </tr>
  <tr>
   <td><code>coa.accounts.edit</code>
   </td>
   <td>Edit accounts
   </td>
   <td>Finance Manager
   </td>
  </tr>
  <tr>
   <td><code>coa.projects.view_all</code>
   </td>
   <td>View all projects
   </td>
   <td>Finance Manager, Director
   </td>
  </tr>
  <tr>
   <td><code>coa.projects.view_own</code>
   </td>
   <td>View own projects
   </td>
   <td>Researchers (PI)
   </td>
  </tr>
  <tr>
   <td><code>coa.projects.create</code>
   </td>
   <td>Create projects
   </td>
   <td>Finance Staff
   </td>
  </tr>
  <tr>
   <td><code>coa.budgets.allocate</code>
   </td>
   <td>Allocate budgets
   </td>
   <td>Finance Manager
   </td>
  </tr>
  <tr>
   <td><code>coa.budgets.revise</code>
   </td>
   <td>Approve budget revisions
   </td>
   <td>Finance Manager, Director
   </td>
  </tr>
  <tr>
   <td><code>coa.reports.view_org</code>
   </td>
   <td>View org-level reports
   </td>
   <td>Finance Manager, Director
   </td>
  </tr>
  <tr>
   <td><code>coa.reports.view_dept</code>
   </td>
   <td>View department reports
   </td>
   <td>Managers
   </td>
  </tr>
  <tr>
   <td><code>coa.reports.export</code>
   </td>
   <td>Export reports
   </td>
   <td>Finance Staff, Managers
   </td>
  </tr>
</table>

---

## **13. REPORTING REQUIREMENTS**

### **13.1 Standard Reports**

#### **Report 1: Budget vs Actual by Project**

**Purpose**: Show budget utilization for each project

**Filters**:

- Fiscal Year (required)
- Department (optional)
- Site (optional)
- Status (Active/Completed/All)
- PI (optional)

**Columns**:

- Project Code
- Project Name
- PI Name
- Department
- Site
- Total Budget
- Committed Amount
- Actual Spent
- Available Balance
- Utilization % (Committed+Actual)/Budget

**Sorting**: Default by Utilization % descending (highest risk first)

**Export**: Excel, PDF, CSV

**Frequency**: On-demand, can schedule weekly/monthly email

---

#### **Report 2: Spending Analysis by Account**

**Purpose**: Show which expense accounts consume most budget

**Filters**:

- Fiscal Year (required)
- Account Category (Program/Non-Program/All)
- Department (optional)
- Site (optional)

**Columns**:

- Account Code
- Account Name
- Category
- Total Budget (sum across all projects)
- Actual Spent
- Balance
- Utilization %
- Number of Transactions
- Number of Projects Using This Account

**Sorting**: Default by Actual Spent descending

**Drill-down**: Click account → see list of projects using this account

**Visualization**: Pie chart showing top 10 accounts by spending

**Export**: Excel, PDF, CSV

---

#### **Report 3: Program vs Non-Program Ratio**

**Purpose**: Track Program vs Non-Program expense ratio over time

**Filters**:

- Fiscal Year (required)
- Time Period (Monthly/Quarterly/Yearly)

**Metrics**:

- Program Total Spent
- Non-Program Total Spent
- Ratio (Program:Non-Program)
- Target Ratio (configurable, default 75:25)
- Variance from Target

**Visualization**:

- Line chart: Ratio trend over time
- Stacked bar chart: Program vs Non-Program by month/quarter
- Gauge chart: Current ratio vs target

**Alert**: If ratio deviates >3% from target, send alert to Finance Manager

**Export**: PDF (with charts), Excel (data only)

---

#### **Report 4: Department Comparison**

**Purpose**: Compare spending efficiency across departments

**Filters**:

- Fiscal Year (required)
- Site (optional)

**Columns**:

- Department
- Number of Projects
- Total Budget
- Actual Spent
- Utilization %
- Budget per Project (avg)
- Spending Velocity (Spent per Month)

**Visualization**:

- Bar chart comparing budget utilization by department
- Scatter plot: Budget vs Actual for each department

**Insights**:

- Which department has highest/lowest utilization
- Which department manages budget most efficiently

**Export**: Excel, PDF

---

#### **Report 5: Transaction Detail Report**

**Purpose**: Detailed list of all transactions with filters

**Filters**:

- Date Range (required)
- Account (optional)
- Project (optional)
- Department (optional)
- Site (optional)
- Transaction Type (PR/PO/Payment/All)
- Amount Range (min-max)

**Columns**:

- Transaction Date
- Transaction Type
- Account Code
- Account Name
- Project Code
- Project Name
- Description
- Amount
- PR/PO/Invoice Number
- Created By

**Sorting**: Default by Date descending (newest first)

**Pagination**: 50 rows per page

**Export**: Excel (up to 10,000 rows), CSV (unlimited)

---

### **13.2 Dashboard Widgets**

#### **Widget 1: Budget Utilization Gauge**

**Type**: Gauge chart \
 **Data**: Current FY total budget utilization % \
 **Colors**: Green (&lt;70%), Yellow (70-90%), Red (>90%) \
 **Update**: Real-time on page load

---

#### **Widget 2: Top 5 Projects by Spending**

**Type**: Horizontal bar chart \
 **Data**: Top 5 projects with highest actual spending \
 **Click**: Drill down to project detail page

---

#### **Widget 3: Program vs Non-Program Ratio**

**Type**: Donut chart \
 **Data**: Current FY ratio \
 **Alert**: Show warning icon if off-target

---

#### **Widget 4: Budget Alerts**

**Type**: List/table \
 **Data**: Projects with >80% budget utilization or deficit \
 **Action**: Click to view project detail

---

#### **Widget 5: Recent Transactions**

**Type**: Table \
 **Data**: Last 10 transactions for user's projects or department \
 **Update**: Refresh every 5 minutes

---

### **13.3 Ad-hoc Query Builder**

**Purpose**: Allow power users (Finance Staff, Managers) to build custom queries

**Features**:

1. **Select Dimensions**:
    - Group by: Project / Account / Department / Site / Fiscal Year
    - Time period: Date range, Month, Quarter, Year
2. **Select Metrics**:
    - Budget, Committed, Actual, Balance, Utilization %
    - Transaction Count, Average Transaction Size
3. **Filters**:
    - Any dimension (Project, Account, etc.)
    - Amount range
    - Date range
    - Status
4. **Visualization**:
    - Table (default)
    - Bar chart, Line chart, Pie chart
    - Pivot table
5. **Save Query**:
    - Save for reuse
    - Share with other users
    - Schedule automated email delivery

**Example Query**:

Group By: Account

Filters:

- Fiscal Year = 2026

- Department = Agronomy

- Account Category = Program

Metrics: Budget, Actual Spent, Utilization %

Visualization: Bar chart

Sort: Utilization % descending

---

## **14. SECURITY & ACCESS CONTROL**

### **14.1 User Roles**

<table>
  <tr>
   <td><strong>Role</strong>
   </td>
   <td><strong>Description</strong>
   </td>
   <td><strong>Count (est.)</strong>
   </td>
  </tr>
  <tr>
   <td><strong>Researcher</strong>
   </td>
   <td>PIs and research staff who create PRs
   </td>
   <td>50 users
   </td>
  </tr>
  <tr>
   <td><strong>Research Coordinator</strong>
   </td>
   <td>Approve PRs within department
   </td>
   <td>5 users
   </td>
  </tr>
  <tr>
   <td><strong>Research Manager</strong>
   </td>
   <td>Oversee department budgets, approve PRs
   </td>
   <td>4 users
   </td>
  </tr>
  <tr>
   <td><strong>Finance Staff</strong>
   </td>
   <td>Process PRs, manage accounts and budgets
   </td>
   <td>5 users
   </td>
  </tr>
  <tr>
   <td><strong>Finance Manager</strong>
   </td>
   <td>Overall financial control, approve budget revisions
   </td>
   <td>1 user
   </td>
  </tr>
  <tr>
   <td><strong>Director</strong>
   </td>
   <td>Executive oversight, approve major decisions
   </td>
   <td>1 user
   </td>
  </tr>
  <tr>
   <td><strong>Admin</strong>
   </td>
   <td>System administration (IT)
   </td>
   <td>2 users
   </td>
  </tr>
</table>

---

### **14.2 Permission Matrix**

<table>
  <tr>
   <td><strong>Feature</strong>
   </td>
   <td><strong>Researcher</strong>
   </td>
   <td><strong>Coordinator</strong>
   </td>
   <td><strong>Manager</strong>
   </td>
   <td><strong>Finance Staff</strong>
   </td>
   <td><strong>Finance Mgr</strong>
   </td>
   <td><strong>Director</strong>
   </td>
   <td><strong>Admin</strong>
   </td>
  </tr>
  <tr>
   <td><strong>Accounts</strong>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>View all accounts
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Create account
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Edit account
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Deactivate account
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td><strong>Projects</strong>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>View own projects
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>View all projects
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅ Dept only
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Create project
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Edit project
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Close project
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅ Own dept
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td><strong>Budgets</strong>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>View project budget
   </td>
   <td>✅ Own
   </td>
   <td>✅ Dept
   </td>
   <td>✅ Dept
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Allocate budget
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
  </tr>
  <tr>
   <td>Request revision
   </td>
   <td>✅ Own
   </td>
   <td>✅ Dept
   </td>
   <td>✅ Dept
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
  </tr>
  <tr>
   <td>Approve revision &lt;10%
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
  </tr>
  <tr>
   <td>Approve revision >10%
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
  </tr>
  <tr>
   <td><strong>PR Integration</strong>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>Select account in PR
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Check budget
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Override budget check
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
  </tr>
  <tr>
   <td><strong>Reports</strong>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>View own project reports
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>View dept reports
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>View org reports
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Export reports
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Schedule reports
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>❌
   </td>
  </tr>
  <tr>
   <td><strong>Admin</strong>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
   <td>
   </td>
  </tr>
  <tr>
   <td>View audit logs
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>Manage users/roles
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
  <tr>
   <td>System configuration
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>❌
   </td>
   <td>✅
   </td>
  </tr>
</table>

---

### **14.3 Data Access Rules**

#### **Rule 1: Project-Level Access**

**Researchers can only view/interact with projects where they are**:

- Principal Investigator (PI), OR
- Team member (explicitly added to project team)

**Implementation**:

-- When user queries projects, filter by:

SELECT \* FROM projects

WHERE pi_user_id = :current_user_id

OR id IN (

       SELECT project_id FROM project_team_members

       WHERE user_id = :current_user_id

);

---

#### **Rule 2: Department-Level Access**

**Managers can only view/interact with projects and budgets in their department**

**Implementation**:

-- When manager queries projects, filter by:

SELECT \* FROM projects

WHERE department = (

    SELECT department FROM users WHERE id = :current_user_id

);

---

#### **Rule 3: Site-Level Access (optional for future)**

**If organization implements site-based access control**:

- Site Managers can only view projects at their site

---

#### **Rule 4: Fiscal Year Access**

**Users can view historical fiscal years (read-only)**:

- Cannot edit accounts/projects from closed fiscal years
- Cannot create PRs against closed fiscal year budgets

**Implementation**:

- System tracks FY status (Open / Closed)
- UI shows warning and disables edit buttons for closed FY data

---

### **14.4 Sensitive Operations**

**Operations requiring re-authentication (password or 2FA)**:

1. Create or edit account (Finance Manager)
2. Approve budget revision >10% (Director)
3. Override budget check (Finance Manager, Director)
4. Deactivate account with active transactions
5. View audit logs (Finance Manager, Director, Admin)

**Implementation**:

- Before allowing operation, show modal: "Please enter your password to confirm"
- Log this re-authentication event in audit trail

---

### **14.5 Audit Trail**

**All critical operations logged**:

<table>
  <tr>
   <td><strong>Operation</strong>
   </td>
   <td><strong>Logged Data</strong>
   </td>
  </tr>
  <tr>
   <td>Account created/edited/deleted
   </td>
   <td>User, timestamp, old values, new values
   </td>
  </tr>
  <tr>
   <td>Project created/edited/closed
   </td>
   <td>User, timestamp, old values, new values
   </td>
  </tr>
  <tr>
   <td>Budget allocated/revised
   </td>
   <td>User, timestamp, old budget, new budget, justification
   </td>
  </tr>
  <tr>
   <td>Budget committed
   </td>
   <td>User, timestamp, PR/PO ID, amount
   </td>
  </tr>
  <tr>
   <td>Budget released
   </td>
   <td>User, timestamp, reason (payment/cancellation)
   </td>
  </tr>
  <tr>
   <td>Transaction created
   </td>
   <td>User, timestamp, all transaction details
   </td>
  </tr>
  <tr>
   <td>Report generated
   </td>
   <td>User, timestamp, report type, filters
   </td>
  </tr>
  <tr>
   <td>User login/logout
   </td>
   <td>User, timestamp, IP address
   </td>
  </tr>
  <tr>
   <td>Permission changed
   </td>
   <td>Admin user, timestamp, affected user, old role, new role
   </td>
  </tr>
</table>

**Audit Log Retention**: 7 years (compliance requirement)

**Audit Log Access**: Finance Manager, Director, Admin only

**Audit Log Immutability**: Logs cannot be edited or deleted by anyone (database-level constraint)

---

## **15. SUCCESS METRICS & KPIs**

### **15.1 Adoption Metrics**

<table>
  <tr>
   <td><strong>Metric</strong>
   </td>
   <td><strong>Target</strong>
   </td>
   <td><strong>Measurement Method</strong>
   </td>
  </tr>
  <tr>
   <td><strong>User Adoption Rate</strong>
   </td>
   <td>>95% of PRs use COA accounts within 30 days
   </td>
   <td>Count PRs with account code / Total PRs
   </td>
  </tr>
  <tr>
   <td><strong>Correct Account Selection</strong>
   </td>
   <td>>90% of PRs have correct account (no correction needed)
   </td>
   <td>Count PRs without account correction / Total PRs
   </td>
  </tr>
  <tr>
   <td><strong>Training Completion</strong>
   </td>
   <td>100% of target users complete training within 2 weeks
   </td>
   <td>Training attendance records
   </td>
  </tr>
  <tr>
   <td><strong>Support Ticket Volume</strong>
   </td>
   <td>&lt;10 COA-related tickets per week after 30 days
   </td>
   <td>Count tickets tagged "COA"
   </td>
  </tr>
</table>

---

### **15.2 Performance Metrics**

<table>
  <tr>
   <td><strong>Metric</strong>
   </td>
   <td><strong>Target</strong>
   </td>
   <td><strong>Measurement Method</strong>
   </td>
  </tr>
  <tr>
   <td><strong>PR Creation Time</strong>
   </td>
   <td>&lt;5 minutes (vs 10 minutes previously)
   </td>
   <td>User survey, time tracking
   </td>
  </tr>
  <tr>
   <td><strong>Budget Check Response Time</strong>
   </td>
   <td>&lt;2 seconds
   </td>
   <td>Server logs, 90th percentile
   </td>
  </tr>
  <tr>
   <td><strong>Report Generation Time</strong>
   </td>
   <td>&lt;30 seconds for 50 projects
   </td>
   <td>Automated performance tests
   </td>
  </tr>
  <tr>
   <td><strong>System Uptime</strong>
   </td>
   <td>>99.5% during business hours
   </td>
   <td>Monitoring tools (Pingdom, New Relic)
   </td>
  </tr>
</table>

---

### **15.3 Business Impact Metrics**

<table>
  <tr>
   <td><strong>Metric</strong>
   </td>
   <td><strong>Baseline (Before)</strong>
   </td>
   <td><strong>Target (After)</strong>
   </td>
   <td><strong>Measurement Method</strong>
   </td>
  </tr>
  <tr>
   <td><strong>Financial Reporting Time</strong>
   </td>
   <td>3 days
   </td>
   <td>&lt;2 hours (93% reduction)
   </td>
   <td>Time log, manager survey
   </td>
  </tr>
  <tr>
   <td><strong>Budget Overrun Incidents</strong>
   </td>
   <td>8 per year
   </td>
   <td>&lt;2 per year (75% reduction)
   </td>
   <td>Count of projects exceeding budget without revision
   </td>
  </tr>
  <tr>
   <td><strong>PR Rejection Rate (wrong account)</strong>
   </td>
   <td>20%
   </td>
   <td>&lt;5% (75% reduction)
   </td>
   <td>Count rejections / Total PRs
   </td>
  </tr>
  <tr>
   <td><strong>Audit Findings (COA-related)</strong>
   </td>
   <td>5 findings
   </td>
   <td>0 findings
   </td>
   <td>External audit report
   </td>
  </tr>
  <tr>
   <td><strong>Program Ratio Compliance</strong>
   </td>
   <td>68:32 (off-target)
   </td>
   <td>75:25 (on-target)
   </td>
   <td>Quarterly Program vs Non-Program ratio
   </td>
  </tr>
</table>

---

### **15.4 User Satisfaction Metrics**

<table>
  <tr>
   <td><strong>Metric</strong>
   </td>
   <td><strong>Target</strong>
   </td>
   <td><strong>Measurement Method</strong>
   </td>
  </tr>
  <tr>
   <td><strong>Overall Satisfaction Score</strong>
   </td>
   <td>>80%
   </td>
   <td>User survey (5-point scale, post-implementation)
   </td>
  </tr>
  <tr>
   <td><strong>Ease of Use Rating</strong>
   </td>
   <td>>4.0/5.0
   </td>
   <td>User survey (specific question: "How easy is it to select the correct account?")
   </td>
  </tr>
  <tr>
   <td><strong>Would Recommend</strong>
   </td>
   <td>>85%
   </td>
   <td>User survey (Net Promoter Score)
   </td>
  </tr>
</table>

**Survey Questions** (example):

1. "How satisfied are you with the new COA module?" (1-5 scale)
2. "How easy is it to find and select the correct account when creating a PR?" (1-5 scale)
3. "How helpful is the budget availability check?" (1-5 scale)
4. "How would you rate the quality of financial reports?" (1-5 scale)
5. "What improvement would you suggest?" (open-ended)

---

### **15.5 Dashboard for Tracking Metrics**

**Metric Dashboard** (for Project Manager / Finance Manager):

┌──────────────────────────────────────────────────────────────┐

│ COA Module - Success Metrics Dashboard │

├──────────────────────────────────────────────────────────────┤

│ │

│ ADOPTION (30-Day Post-Launch) │

│ ┌────────────────────┐ ┌────────────────────┐ │

│ │ User Adoption Rate │ │ Correct Account │ │

│ │ │ │ Selection Rate │ │

│ │ 97% ✅ │ │ 92% ✅ │ │

│ │ Target: 95% │ │ Target: 90% │ │

│ └────────────────────┘ └────────────────────┘ │

│ │

│ PERFORMANCE │

│ ┌────────────────────┐ ┌────────────────────┐ │

│ │ Avg Budget Check │ │ Avg Report Gen │ │

│ │ Response Time │ │ Time │ │

│ │ │ │ │ │

│ │ 1.2s ✅ │ │ 22s ✅ │ │

│ │ Target: &lt;2s │ │ Target: &lt;30s │ │

│ └────────────────────┘ └────────────────────┘ │

│ │

│ BUSINESS IMPACT │

│ ┌────────────────────────────────────────────────────────┐ │

│ │ Financial Reporting Time Reduction │ │

│ │ Before: 3 days → After: 1.5 hours (98% reduction) ✅ │ │

│ │ │ │

│ │ Budget Overrun Incidents (YTD) │ │

│ │ Current: 1 incident → Target: &lt;2/year ✅ On track │ │

│ │ │ │

│ │ Program vs Non-Program Ratio │ │

│ │ Current: 74:26 → Target: 75:25 ⚠️ Within 1% │ │

│ └────────────────────────────────────────────────────────┘ │

│ │

│ USER SATISFACTION (Last Survey: Jan 15, 2026) │

│ Overall Satisfaction: 84% ✅ (Target: >80%) │

│ Ease of Use: 4.2/5.0 ✅ (Target: >4.0) │

│ Would Recommend (NPS): 87% ✅ (Target: >85%) │

│ │

│ [View Detailed Metrics] [Export Report] [Schedule Email] │

│ │

└──────────────────────────────────────────────────────────────┘

---

## **16. IMPLEMENTATION PHASES**

### **16.1 Phase 1: MVP (Weeks 1-12)**

**Objective**: Launch core COA functionality enabling basic financial tracking

#### **Week 1-2: Foundation**

**Deliverables**:

- [ ] PRD approved by stakeholders
- [ ] Technical design document completed
- [ ] Development environment setup
- [ ] Database schema designed and reviewed
- [ ] Project kickoff meeting with dev team

**Team**:

- Project Manager + Finance Manager (PRD finalization)
- Tech Lead (architecture design)
- Full team (kickoff)

---

#### **Week 3-4: Backend Development**

**Deliverables**:

- [ ] Database tables created (accounts, projects, budgets, etc.)
- [ ] Account Service API (CRUD operations)
- [ ] Project Service API (CRUD operations)
- [ ] Budget Service API (allocation, availability check)
- [ ] Unit tests (70% coverage)

**Team**:

- 2 Backend Developers

---

#### **Week 5-6: Frontend Development**

**Deliverables**:

- [ ] Account management UI (list, create, edit)
- [ ] Project management UI (list, create, edit)
- [ ] Dashboard (basic widgets)
- [ ] Account selection widget (for PR integration)

**Team**:

- 2 Frontend Developers
- UI/UX Designer (design review)

---

#### **Week 7-8: Integration & Budget Control**

**Deliverables**:

- [ ] PR module integration (account selection)
- [ ] Budget check API integrated with PR submission
- [ ] Budget commitment logic (on PR approval)
- [ ] Budget release logic (on payment/cancellation)
- [ ] Integration testing

**Team**:

- 1 Backend Developer (COA side)
- 1 Developer from PR Module team
- QA Tester

---

#### **Week 9: Testing & Bug Fixing**

**Deliverables**:

- [ ] Comprehensive test plan executed
- [ ] Bug triage and fixing (P0 and P1 bugs)
- [ ] Performance testing (load test with 100 concurrent users)
- [ ] Security testing (basic penetration test)

**Team**:

- QA Team (2 testers)
- Developers (bug fixing)
