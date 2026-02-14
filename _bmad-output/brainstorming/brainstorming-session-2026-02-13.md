---
stepsCompleted: [1]
inputDocuments: []
session_topic: 'Building a correct chronological development roadmap for GreenLedger'
session_goals: 'Identify critical foundation work before tackling pain points, Map technical dependencies, Balance user adoption with feature rollout, Create a defensible achievable delivery sequence'
selected_approach: ''
techniques_used: []
ideas_generated: []
context_file: ''
---

# Brainstorming Session Results

**Facilitator:** Bayu
**Date:** 2026-02-13

## Session Overview

**Topic:** GreenLedger Implementation Roadmap - Chronological Sequencing

**Goals:**
- Identify critical foundation work before tackling pain points
- Map technical dependencies
- Balance user adoption with feature rollout
- Create a defensible, achievable delivery sequence

**Context Loaded:**
✅ Full PRD context (main_prd.md & main_prd_2.md)
✅ Multi-site architecture complexity
✅ 13-person organization structure
✅ Business process workflows

**Session Strategy:** Strategic roadmap brainstorm focused on CHRONOLOGICAL SEQUENCING rather than jumping straight into PRD pain points

## Technique Selection

**Approach:** AI-Recommended Techniques

**Selected Techniques:**

1. **Decision Tree Mapping** (Structured) - Expose all prerequisite relationships and dependency chains
2. **Morphological Analysis** (Deep) - Systematically explore implementation parameters and combinations
3. **Constraint Mapping** (Deep) - Ground roadmap in technical, organizational, and resource reality
4. **Failure Analysis** (Deep) - De-risk the roadmap by learning from common financial system implementation failures

**AI Rationale:**
For strategic roadmap sequencing of a complex multi-site financial system, this four-technique approach provides:
- **Decision Trees** expose hidden dependencies that force sequencing
- **Morphological Analysis** ensures no critical parameters are overlooked
- **Constraint Mapping** ensures the roadmap is actually achievable
- **Failure Analysis** avoids known pitfalls that derail similar projects

**Estimated Duration:** 80-100 minutes
**Expected Outcome:** A defensible, chronologically correct implementation roadmap

---

## Technique Execution #1: Decision Tree Mapping

**Focus:** Exposing prerequisite relationships and dependency chains

**Key Discoveries:**

### Phase 1 Foundation Sequencing:

1. **RBAC Management** - Controls who can configure systems
2. **Approval Workflow Engine** - Gates FY closing, COA, Program, and Payment approvals
3. **User Management** - Assigns people to roles
4. **Sites + Fiscal Years** - Created in parallel (independent top-level entities)
5. **Global COA** - Universal chart of accounts (codes like 1000, 2000)
6. **Per-Site-Per-FiscalYear COA** - Children of Global COA, tagged as "Program" or "Non-Program"
7. **Program/Research Topic** - Site-bound with Activities + Budget/BoQs (wizard form)
8. **Payment Request Engine** - Unified engine handling both Program and Non-Program payments

### Critical Architectural Insights:

- **Approval Workflow Engine is a gating mechanism** - Must be configured early because multiple entities (Fiscal Year closing, COA creation, Program approval, Payment approval) require it
- **COA tagging determines payment rules** - Program vs Non-Program distinction is at COA level, not feature level. One Payment Request engine serves both with rules driven by COA type
- **Hierarchy discovered:** Program → Activities → Budget → BoQs → COA Accounts
- **User-to-Program assignment** happens in Program creation form (many-to-many pivot via form, not separate feature)
- **Revenue recording is independent** - Can be built anytime after COA exists
- **Domain-specific features don't gate core workflows** - Asset Tracking, Construction management, Travel management are separate systems that don't require Payment Request engine to exist first

### Dependency Chains Identified:

- Site must exist before Program can be created (Program is site-bound)
- Fiscal Year must exist before Budget can be created (Budget is FY-bound)
- Global COA must exist before Per-Site-Per-FiscalYear COA can be created
- Per-Site-Per-FiscalYear COA must exist before BoQ can reference accounts
- Approval Workflow must be configured before COA and Program approvals work
- Payment Request must exist before Settlement can occur

---

## Technique Execution #2 & #3: Morphological Analysis + Constraint Mapping + Failure Analysis

**Focus:** Systematic parameter exploration, reality grounding, and de-risking

### Morphological Analysis Results:

**Key Implementation Dimensions Identified:**

1. **Foundation Parameters:** RBAC → Auth → User Mgmt gates all admin functions
2. **Data Model Parameters:** Global COA (parent) → Per-Site-Per-FY COA (child) strict dependency
3. **Business Process Parameters:** Unified Payment Engine serves both Program & Non-Program
4. **Stakeholder Rollout:** Admin functions early → Associates mid → Officers late
5. **Parallelization Opportunities:** Sites+FY parallel, Revenue+Settlement parallel, Domain-specific features parallel

**Critical Insights from Parameter Combinations:**
- Approval Workflow Engine must exist BEFORE it's needed (not built on-demand)
- COA structure must be locked after Programs created
- Payment/Settlement are critical path (must be rock-solid)
- Domain-specific features (Asset, Construction, Travel) don't gate core workflows

### Constraint Mapping Results:

**Technical Constraints:**
- Database schema prerequisite for all models
- Approval Engine gates approval-dependent features
- Authentication prerequisite for access control

**Organizational Constraints:**
- RBAC determines who can configure workflows
- Only Manager can assign Users to Programs
- Finance needs settlement process training

**Data Constraints:**
- Sites must exist before Programs
- Fiscal Years must exist before Budgets
- COA must exist before BoQs
- Users must exist before Program assignment

**Critical Risk Constraints:**
- Approval workflow misconfiguration = system deadlock
- COA changes after use = broken references
- Settlement variance untested = live failures
- Premature user rollout = chaos and data errors

### Failure Analysis Results:

**Top 10 Failure Patterns Identified & Prevention Strategies:**

1. ❌ Approval Workflow misconfigured
   - **Prevention:** Validate workflow with actual managers BEFORE coding

2. ❌ COA structure changed mid-execution
   - **Prevention:** Lock COA before Programs, require AVP approval for changes

3. ❌ Settlement edge cases untested
   - **Prevention:** Build incrementally, test multi-program scenarios

4. ❌ Premature user rollout
   - **Prevention:** Phased rollout starting with power users

5. ❌ Insufficient Payment/Settlement testing
   - **Prevention:** Full integration testing before go-live

6. ❌ Configuration work underestimated
   - **Prevention:** Plan dedicated configuration phase

7. ❌ Settlement complexity underestimated
   - **Prevention:** Design carefully, test real scenarios

8. ❌ Data migration neglected
   - **Prevention:** Plan strategy early, import historical data if needed

9. ❌ COA changes after Programs created
   - **Prevention:** Lock structure, audit trail for changes

10. ❌ User training timing issues
    - **Prevention:** Just-in-time training with documentation

---

# COMPLETE DEVELOPMENT ROADMAP: GreenLedger Implementation

**Total Timeline:** 40 weeks (~9-10 months)
**Approach:** Sequential phases with strategic parallelization
**Success Strategy:** Build foundation first, lock down critical structures, test thoroughly before go-live

---

## PHASE 1: FOUNDATION (Weeks 1-3)

**Goals:** Establish core infrastructure and access control

### 1. Database Design & Schema (Week 1)
- Design complete schema for all entities
- Plan migration strategy for historical data
- Design for scalability and comprehensive audit trail
- **Owner:** Tech Lead
- **Deliverable:** Schema approved, migration plan documented

### 2. RBAC Management (Weeks 1-2)
- Define roles: AVP, Manager, RA, RO, FS, Finance, Farm Admin, System Admin
- Build role-permission matrix
- Implement access control layer in framework
- Build User role management UI
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Unit tests for RBAC, access control verification
- **Deliverable:** Users can log in with correct role-based access

### 3. User Management (Weeks 2-3)
- User CRUD (Create, Read, Update, Delete)
- User-Role assignment
- User-Site assignment (many-to-many pivot)
- Password management and security
- User deactivation
- User search and filtering
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** User creation, role assignment, site assignment workflows
- **Deliverable:** User management system operational

**Phase 1 Validation:** All users can log in with appropriate permissions

---

## PHASE 2: APPROVAL & CONFIGURATION ENGINE (Weeks 4-6)

**Goals:** Build approval workflow system before other features depend on it

### 1. Approval Workflow Engine (Weeks 4-5)
- Design approval rule configuration data model
- Build approval rule configuration UI (AVP/Manager only)
- Multi-level approval support (Manager → AVP, with bypasses)
- Approval delegation (Manager on leave → escalate to AVP)
- Approval SLA tracking (Normal 12h, Urgent 5h)
- Approval override capability with mandatory audit trail
- Status tracking: Pending, Approved, Rejected, Revision Requested
- **Owner:** Backend Lead + Frontend Lead
- **Stakeholder Input Required:** Managers + AVP validate approval rules
- **Testing:** Approval flows, escalation paths, SLA enforcement
- **Deliverable:** Approval workflow configurable and testable

### 2. Notification System (Weeks 5-6)
- Email notifications for approvals, rejects, revisions
- In-app notifications with persistence
- SMS alerts for urgent/overdue items (optional)
- Notification templates with merge fields
- Notification preferences per user
- **Owner:** Backend Lead
- **Testing:** Notification delivery, template rendering
- **Deliverable:** Users receive notifications for actions

**Phase 2 Validation:** Managers can configure approval workflows and receive notifications

---

## PHASE 3: MASTER DATA SETUP (Weeks 7-9)

**Goals:** Create top-level entities and master data structures

### 1. Site Management (Weeks 7-8)
- Create Sites (Klaten, Magelang, future-proof for additions)
- Site master data (code, name, address, contact)
- Site status (Active, Inactive)
- Site-specific COA prefix configuration
- Site-User role assignment management
- Site performance tracking
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Site creation, data validation
- **Deliverable:** Sites created and configured

### 2. Fiscal Year Management (Weeks 7-8) [PARALLEL with Sites]
- Create Fiscal Years (2026, 2027, etc.)
- FY date ranges (start, end)
- FY status (Open, Closed, Archived)
- FY-specific settings (annual budget ceiling, batch times)
- Year-end closing workflow design
- **Owner:** Backend Lead + Frontend Lead
- **Stakeholder Validation:** Finance confirms FY structure
- **Testing:** FY creation, date validation, status transitions
- **Deliverable:** Fiscal Years created and configured

### 3. Global COA Management (Weeks 8-9)
- Design Global COA hierarchy (top-level accounts: 1000, 2000, 3000, 4xxx Revenue, 5xxx Expense)
- Create Global COA accounts with descriptions
- COA account properties (code, name, type, status)
- Parent-child relationships for hierarchy
- COA templates for standard agricultural accounts (Fertilizer, Seeds, Labor, etc.)
- Account status management (Active, Inactive)
- **Owner:** Backend Lead + Frontend Lead
- **Stakeholder Input:** Finance team validates COA structure
- **Testing:** COA hierarchy, parent-child relationships, template functionality
- **Deliverable:** Global COA structure established

**Phase 3 Validation:** Sites, Fiscal Years, and Global COA foundation ready

---

## PHASE 4: SITE-SPECIFIC COA & ACCOUNTING SETUP (Weeks 10-12)

**Goals:** Build per-site, per-fiscal-year accounting structure and LOCK IT DOWN

### 1. Per-Site-Per-FiscalYear COA Creation (Weeks 10-11)
- Create COA accounts for each Site × Fiscal Year combination
- Inherit from Global COA parent accounts
- Type tagging: "Program" or "Non-Program" (explicit select field)
- COA account status (Active, Inactive)
- COA account usage statistics
- Bulk import from CSV with validation
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** COA creation, parent relationships, type tagging
- **Deliverable:** Per-site COA accounts created for all site-FY combinations

### 2. COA Structure Validation & Lock-Down (Weeks 11-12)
- Comprehensive COA structure review with Finance
- Test all COA account references (payment requests will use these)
- Approve final COA structure in writing
- **LOCK COA:** Prevent changes except by AVP approval
- Implement COA change audit trail
- Document COA structure for user reference
- **Owner:** Finance Lead + Tech Lead
- **Stakeholder Approval:** Finance + Manager sign-off
- **Testing:** COA structure completeness, reference validation
- **Deliverable:** COA locked, ready for use

**CRITICAL PHASE GATE:** After this phase, COA is locked. All future changes require:
- Detailed justification
- AVP approval
- Complete audit trail logging
- Impact analysis on existing Programs/Budgets

**Phase 4 Validation:** COA structure locked and ready for Program creation

---

## PHASE 5: PROGRAM & BUDGET MANAGEMENT (Weeks 13-16)

**Goals:** Enable research program creation and budget planning

### 1. Program Module (Weeks 13-14)
- Program creation form (name, site, fiscal year, objectives)
- Program status lifecycle: Draft → Active → Completed → Archived
- Program master data (description, start/end dates, budget, lead)
- Program-User assignment (many-to-many: RA as lead, RO as team members)
- Program search and filtering by site, status, lead
- Program detail view with drill-down to activities and budgets
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Program creation, status transitions, user assignments
- **Deliverable:** Programs can be created

### 2. Activity Management (Weeks 14-15)
- Activity creation within programs (site-inherited)
- Activity types/categories (Land Prep, Planting, Harvest, etc.)
- Activity phases (Planning, Execution, Completion)
- Activity dates (start, planned end, actual end)
- Activity status (Planned, Active, Completed, Cancelled)
- Activity timeline visualization
- Activity budget tracking (aggregate BoQs)
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Activity creation, status transitions, date validation
- **Deliverable:** Activities can be created and managed

### 3. Budget & Bills of Quantities (BoQ) Wizard (Weeks 15-16)
- Multi-step wizard form:
  - Step 1: Program info confirmation
  - Step 2: Activity selection/creation
  - Step 3: Budget header (total amount, description)
  - Step 4: BoQ items (iterative adding)
- BoQ item fields:
  - Activity Category + Phase (dropdown)
  - Item description
  - Specification
  - Quantity + Unit (e.g., "50 kg")
  - Unit Price
  - Auto-calculated Subtotal
  - COA Account selection (Per-Site-Per-FY, filtered by Program tag)
  - Estimated Realization Date (auto-calculated from activity)
  - Notes
- Budget validation:
  - Sum of BoQs ≤ total program budget
  - All BoQs mapped to valid COA accounts
  - All required fields completed
- Budget total auto-calculated from BoQs
- Draft save capability during wizard
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Wizard workflow, calculations, validations, error messages
- **Deliverable:** Complete budget plans can be created with BoQs

### 4. Budget Approval Workflow (Weeks 15-16)
- Budget status: Draft → Submitted → Approved → Active
- Manager review:
  - Validate RA budget feasibility
  - Verify COA mappings are appropriate
  - Approve or request revision
- AVP final approval:
  - Validate budget against annual allocation
  - Approve or request revision
- Budget rejection capability with reasons
- Budget revision tracking (versions)
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Approval routing, revision workflows, version tracking
- **Deliverable:** Budgets can be submitted and approved

**Phase 5 Validation:** Programs with budgets can be created and approved

---

## PHASE 6: PAYMENT REQUEST ENGINE (Weeks 17-20)

**Goals:** Build unified payment request system for Program and Non-Program expenses

### 1. Payment Request Core (Weeks 17-18)
- Payment request creation form:
  - Request type: Single program or Multi-program split
  - Total amount
  - Description/purpose
  - Urgency: Normal (12h) or Urgent (5h)
  - Payment method: Bank Transfer, Cash, Virtual Account
  - Bank details (if transfer)
  - Attachment (quotation, proforma)
- Request status: Draft → Submitted → Approved → Disbursed → Settled
- Budget availability check at submission
- Cost tagging to activities
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Request creation, budget validation, status transitions
- **Deliverable:** Payment requests can be created

### 2. Multi-Program Split Capability (Weeks 18-19)
- Add split line items (Program → Activity → Amount → COA)
- Each split line:
  - Program selection (filtered by site)
  - Activity selection (from program)
  - Amount
  - COA Account selection
  - Percentage (auto-calculated)
- Validations:
  - Sum of splits = Total amount (error if mismatch)
  - Each split has available budget
  - All COAs are correct type (Program)
- Auto-complete final split to balance total
- Visual split breakdown table with percentages
- Cross-site split support (Program from Klaten + Program from Magelang)
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Split creation, validation, cross-site scenarios
- **Deliverable:** Multi-program splits work correctly

### 3. Approval Routing (Weeks 19-20)
- Approval matrix based on requester level + amount:
  - RO/FS < 5M: RA reviews → Manager approves
  - RO/FS > 5M: RA reviews → Manager → AVP approves
  - RA < 5M: Manager approves (no RA review)
  - RA > 5M: Manager → AVP approves
  - Manager any amount: AVP approves
- Self-approval prevention
- Approval routing UI for each approver
- Approve/Reject/Request Revision options
- Revision requests with comments
- SLA tracking and alerts
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Approval routing rules, SLA enforcement
- **Deliverable:** Approval routing works correctly

### 4. Finance Operation Review (Weeks 19-20)
- Farm Admin verification:
  - Physical document check
  - Document completeness verification
  - Flag for procurement involvement (items > 1M)
- FO final review:
  - Verify nominal amount
  - Compliance check
  - Procurement flag confirmation
  - Payment method appropriateness
- FO can override payment method with notification to requester
- FO can adjust settlement deadline (3×24h or custom)
- Disbursement scheduling
- Batch processing (10 AM, 2 PM windows)
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** FA/FO workflows, override capabilities
- **Deliverable:** Payment requests processed by Finance

**Phase 6 Validation:** Complete payment request workflow end-to-end

---

## PHASE 7: SETTLEMENT & ACCOUNTING (Weeks 21-23)

**Goals:** Build settlement process with comprehensive variance handling

### 1. Settlement Submission (Weeks 21-22)
- Settlement deadline management:
  - Default: 2×24 hours after disbursement
  - Extension request: Up to 7 days total (with FO approval)
  - FO can adjust deadline directly (3×24h or custom)
  - Auto-block if timeout reached
- Evidence upload:
  - Proof of payment (receipt, invoice, bank transfer proof)
  - Proof of work (photos, delivery proof, service completion certificate)
  - Official report (if vendor lacks formal invoice)
  - File limit: 5-10 MB per file, 50 MB total
- Settlement status: Pending → Submitted → Verified by FA → Verified by FO → Closed
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Deadline enforcement, evidence upload, auto-blocking
- **Deliverable:** Settlements can be submitted with evidence

### 2. Settlement Verification Workflow (Weeks 22-23)
- FA verification (Physical document review):
  - Check original documents match digital uploads
  - Verify completeness of evidence
  - Approve or request revision
  - Add verification notes
- FO verification (Financial verification):
  - Verify nominal amount accuracy
  - Check compliance with approved budget
  - Compare Actual vs Disbursed (variance detection)
  - Approve & Close or Request Revision
- Revision workflow:
  - Request sent back to requester with specific issues
  - Requester uploads revised evidence
  - Re-verification by FA/FO
- Settlement closure:
  - Mark settlement as Closed
  - Update program/activity budget status
  - Lock from further edits (but keep audit trail)
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Verification workflows, revision cycles, closure
- **Deliverable:** Settlements can be fully verified and closed

### 3. Variance Handling (Weeks 22-23)
- Variance detection (Actual vs Disbursed):
  - Exact Match: Status = Closed, no action needed
  - Underpayment (Actual < Disbursed):
    - Auto-calculate shortfall
    - Create new Payment Request for difference
    - Link to original payment request
    - Same approval flow applies
    - Funds from same program budget
  - Overpayment (Actual > Disbursed):
    - Auto-calculate excess
    - Create return request to program budget
    - Requester must return excess to designated account
    - Track return confirmation
- Proportional distribution for multi-program settlements:
  - Calculate each program's proportion
  - Distribute variance proportionally
  - Update each program's budget accordingly
- Variance reporting and audit trail
- **Owner:** Backend Lead + Frontend Lead
- **Stress Testing:** Multi-program settlements, variance scenarios, edge cases
- **Deliverable:** Variance handling automated and accurate

**Phase 7 Validation:** Settlements can handle all variance scenarios

---

## PHASE 8: REVENUE MANAGEMENT (Weeks 24-25)

**Goals:** Enable revenue recording (independent of expense workflows)

### 1. Harvest Revenue Recording (Week 24)
- Harvest entry form:
  - Program selection (site-inherited)
  - Crop type
  - Harvest date
  - Quantity (kg, tons, units)
  - Price per unit
  - Auto-calculated total revenue
  - Buyer name (dropdown with new buyer option)
  - Payment method (Cash, Bank Transfer)
  - Payment date
  - COA Account (Program COAs only)
- Harvest cycle tracking (Harvest #1, #2, #3 for recurring crops)
- Buyer master data (maintained separately)
- Manager review and correction window (48 hours)
- Auto-posting to program P&L after manager sign-off
- Manager can request corrections with reasons
- Finance can modify with full audit trail
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Revenue calculations, COA mapping, manager review workflow
- **Deliverable:** Harvest revenue can be recorded and posted

### 2. Product Testing Service Revenue (Week 25)
- Service entry form:
  - Client name (Sister Company, External)
  - Service type
  - Service description
  - Contract value
  - Start date
  - Completion date
  - Payment received date
  - COA Account (Program COAs only)
- Client types handling:
  - Sister Company: No formal contract required
  - External Client: Contract expected
- Payment terms tracking (Upon completion typical)
- Optional program linkage for cost-benefit analysis
- Manager approval required before posting
- Posting to program P&L after approval
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Service revenue entry, client handling, approval workflow
- **Deliverable:** Service revenue can be recorded and approved

**Phase 8 Validation:** Revenue independently recorded and appears in program P&L

---

## PHASE 9: PROGRAM LIFECYCLE & COMPLETION (Weeks 26-27)

**Goals:** Enable program completion and archival

### 1. Program Completion Workflow (Week 26)
- Completion checklist validation:
  - All payment requests settled? ✓
  - No pending transactions? ✓
  - Research report uploaded? ✓
- Completion data:
  - Completion date
  - Research report (PDF/document)
  - Completion notes
  - Budget summary (Allocated, Spent, Revenue, Net, Surplus)
- Manager marks program as Completed
- AVP final approval of completion:
  - Review program deliverables
  - Verify financial close
  - Approve or request more information
- Budget surplus calculation:
  - Total Budget - Total Spent = Surplus
  - Surplus returned to global budget pool for site/FY
  - Tracked with audit trail
- Program status: Completed (locked from editing except by admin)
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Completion checklist, surplus calculation, approval workflow
- **Deliverable:** Programs can be completed with proper closure

### 2. Program Archival (Week 27)
- Archive individual program:
  - Select reason for archive (Fiscal year-end, Completed, Long-term storage)
  - Archive timestamp and user tracking
  - Confirmation dialog explaining implications
- Bulk archive:
  - Select multiple programs
  - Common reason for all
- Hidden from default views:
  - "Show Archived Programs" checkbox on program list
  - Archive filter in all reports
  - Archived programs not included in active dashboards
- Unarchival capability:
  - Manager or AVP can unarchive if needed
  - Tracked in audit trail with reason
- Data retention:
  - Archived programs retained for 5 years
  - Searchable and reportable
  - Historical analysis capability
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Archive/unarchive workflows, filtering
- **Deliverable:** Programs can be archived and unarchived

**Phase 9 Validation:** Program completion and archival workflows complete

---

## PHASE 10: EMPLOYEE BENEFITS (Weeks 28-29)

**Goals:** Enable employee subsidi and benefit claims

### 1. Subsidi Management (Weeks 28-29)
- Subsidi types:
  - Vehicle Maintenance (IDR 500,000/month)
  - Future-ready structure for Health, Education, etc.
- Rules per subsidi type:
  - Monthly ceiling
  - No carry-over (unused forfeited)
  - Cannot exceed monthly limit
  - Eligibility rules (by role or individual)
- Benefit claim workflow:
  - Employee creates claim (Reimbursement or Direct Payment)
  - No approval required (already pre-approved by rules)
  - FO verification and disbursement
  - Within remaining monthly ceiling
- Evidence requirements:
  - Proof of payment (receipt)
  - Supporting documentation (tachometer photo for fuel, etc.)
- Tracking:
  - Monthly limit per employee
  - Claimed amount (used)
  - Remaining balance (real-time)
  - Monthly auto-reset
- Reports:
  - Subsidi usage by employee
  - Subsidi utilization rate
  - Unclaimed benefits
- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Subsidi limit enforcement, monthly reset, claim workflows
- **Deliverable:** Employees can claim subsidies within limits

**Phase 10 Validation:** Subsidi system operational

---

## PHASE 11: ADMINISTRATIVE & DOMAIN-SPECIFIC FEATURES (Weeks 30-33)

**Parallel tracks - can build simultaneously:**

### TRACK A: System Admin Panel (Weeks 30-31)

- **User & Role Management:**
  - User CRUD (already done, enhance)
  - Role management
  - Permission matrix management
  - Bulk user import

- **System Configuration:**
  - Global settings (annual budget, batch times, settlement SLA)
  - Email configuration (SMTP, templates)
  - Notification templates
  - Subsidi configuration (types, amounts, eligibility)
  - Site management (add, modify, deactivate)

- **Emergency Operations & Overrides:**
  - Unlock locked budgets (with reason)
  - Delete approved transactions (with justification)
  - Bypass approval workflow (with notification)
  - Modify locked settlements (with audit trail)
  - Manual journal entries (with explanation)

- **Admin Monitoring & Audit:**
  - Admin activity log (all admin actions)
  - Dual admin monitoring (Manager + AVP see each other's actions)
  - Critical action alerts (to both admins via email)
  - System health dashboard (active users, performance, errors)
  - Backup & restore (manual trigger, history, restore points)

- **Testing:** Admin functions, overrides, audit logging
- **Owner:** Backend Lead + Frontend Lead
- **Deliverable:** Admin panel fully operational with safety guardrails

### TRACK B: Asset Management (Weeks 30-31)

- **Asset CRUD:**
  - Asset creation (name, type, description, purchase date, cost)
  - Asset categorization (Machinery, Vehicle, Equipment, Furniture, Electronics)
  - Asset details (model, serial number, location, PIC)
  - Warranty and maintenance info
  - Depreciation tracking
  - Asset status (Active, Inactive, Disposed)

- **Asset Tracking:**
  - Location tracking (Site assignment)
  - Usage history
  - Maintenance log
  - Depreciation schedule

- **Disposal Management:**
  - Disposal request (reason, replacement asset)
  - Disposal approval workflow
  - Removed asset archival
  - Track replacement asset acquisition

- **Reporting:**
  - Asset inventory by site
  - Asset depreciation report
  - Asset maintenance history
  - Asset disposal history

- **Testing:** Asset creation, categorization, tracking
- **Owner:** Backend Lead + Frontend Lead
- **Deliverable:** Asset management system operational (independent of payments)

### TRACK C: Construction Project Management (Weeks 31-32)

- **Project Setup:**
  - Project creation (name, scope, location, budget)
  - Milestone definition (% completion, retention %)
  - Milestone dates and deliverables
  - Contractor/vendor assignment

- **Progress Tracking:**
  - Progress verification workflow
  - Site visit report upload
  - Progress photos
  - Manager sign-off per milestone

- **Payment Management:**
  - Payment per milestone (10% retention)
  - Retention release (max 3 months after completion)
  - Milestone status tracking

- **Completion & Closure:**
  - Final acceptance checklist
  - Retention release approval
  - Project archival

- **Testing:** Milestone tracking, retention calculations
- **Owner:** Backend Lead + Frontend Lead
- **Deliverable:** Construction projects can be tracked with milestone-based payments

### TRACK D: Travel Management (Weeks 32-33)

- **Travel Request:**
  - Destination and date
  - Purpose and objective
  - Mode of transport
  - Breakdown fields (Transport, Accommodation, Per Diem, Toll, Parking, Fuel)
  - Per diem rate by city (configuration)

- **Travel Advance:**
  - Advance request (cash or direct payment)
  - Approved amount
  - Accounting entries

- **Settlement:**
  - Actual expense entry
  - Variance handling (excess → return, short → topup)
  - Evidence upload (boarding pass, hotel invoice, odometer photos, receipts)

- **Reporting:**
  - Travel expense by employee
  - Travel cost by destination
  - Advance vs actual variance

- **Testing:** Per diem calculation, settlement, advance reconciliation
- **Owner:** Backend Lead + Frontend Lead
- **Deliverable:** Travel expenses can be tracked and settled

**Phase 11 Validation:** All admin and domain-specific features operational

---

## PHASE 12: REPORTING & DASHBOARDS (Weeks 34-36)

**Goals:** Enable financial visibility and analytics

### 1. Executive Dashboard
- Multi-site consolidated view:
  - Total Revenue (all sites, YTD)
  - Total Expenses (all sites, YTD)
  - Net Income = Revenue - Expenses
  - Budget Utilization %
  - Active Programs count
  - Pending Approvals count
- Site-specific drill-down:
  - Toggle: All Sites / Klaten Only / Magelang Only
  - Site performance metrics
  - Site-specific P&L

### 2. Manager Dashboard
- Pending approvals queue:
  - Programs awaiting approval
  - Payment requests awaiting approval
  - Budget revisions pending
  - Settlement revisions pending
  - Aging indicators (overdue highlighted)
- Program status overview:
  - Active programs count
  - Programs completing this month
  - Programs at risk (budget concerns)
- Budget monitoring:
  - Programs by budget utilization %
  - Budget variance alerts
  - Spending trend analysis

### 3. Finance Dashboard
- Batch processing queue:
  - Payments awaiting disbursement
  - Next batch times (10 AM, 2 PM)
  - Payment method breakdown
- Settlement status:
  - Pending settlements
  - Overdue settlements (auto-blocked users)
  - Settlement variance summary
- Reconciliation:
  - Batch vs bank transfer reconciliation
  - Variance analysis
  - Unreconciled items
- COA usage:
  - Transaction count per account
  - Amount per account
  - Account utilization report

### 4. Officer Dashboard
- Assigned programs:
  - Active programs
  - Program details (budget, activities)
  - Available budget per program
- Payment request status:
  - My pending requests
  - Request aging
  - Approval status
- Settlement reminders:
  - Outstanding settlements
  - Deadline countdown (with warnings)
  - Past due settlements (auto-blocked notice)

### 5. Reports Library

**Program Reports:**
- Program P&L (Revenue - Expenses = Net)
- Program Budget vs Actual
- Program activity-level breakdown
- Program completion report

**Financial Reports:**
- Budget vs Actual by Program
- Budget vs Actual by COA Account
- Revenue by Program and Type
- Expense by COA Account and Program
- Site performance comparison

**Operational Reports:**
- Settlement aging report
- Settlement variance analysis
- Payment approval SLA compliance
- User activity audit log

**Export Options:**
- Excel export (all reports)
- PDF generation (printable reports)
- CSV export (data analysis)
- Scheduled report delivery (email)

### 6. Data Export
- Multi-format export (Excel, PDF, CSV)
- Custom field selection
- Date range filtering
- Site filtering
- Scheduled export delivery

- **Owner:** Backend Lead + Frontend Lead
- **Testing:** Report accuracy, export functionality, performance
- **Deliverable:** All dashboards and reports available

**Phase 12 Validation:** Complete visibility into financial operations

---

## PHASE 13: COMPREHENSIVE TESTING & GO-LIVE (Weeks 37-40)

**Goals:** Validate system readiness and user enablement

### 1. Integration Testing (Weeks 37-38)

- **End-to-End Workflows:**
  - Program creation → Budget approval → Payment request → Settlement → Completion
  - Multi-program payment split with variance handling
  - Revenue recording and program P&L calculation
  - Program archival and historical reporting

- **Scenario Testing:**
  - Approval workflow with multi-level approvals
  - Budget revision and reallocation
  - Settlement extensions and deadline management
  - COA changes and impact analysis
  - User delegation scenarios

- **Edge Case Testing:**
  - Exact match settlements
  - Underpayment variance (new payment request creation)
  - Overpayment variance (return to budget)
  - Multi-program split proportional variance distribution
  - Cross-site program scenarios
  - Timeout and auto-blocking

- **Performance Testing:**
  - Load test with 20+ concurrent users
  - Report generation under load
  - Large dataset handling
  - Query optimization

- **Security Testing:**
  - Access control verification (RBAC)
  - Data isolation by site
  - Audit trail integrity
  - SQL injection prevention
  - CSRF protection

- **Owner:** QA Lead + Tech Lead
- **Acceptance Criteria:** All scenarios pass, no critical bugs
- **Deliverable:** Integration test report signed off

### 2. User Acceptance Testing (Weeks 38-39)

- **Actual User Testing:**
  - Managers test approval workflows
  - Finance tests payment and settlement processing
  - Research Officers test payment request submission
  - Associates test program and budget creation
  - Farm Admin tests document verification

- **Data Migration Testing:**
  - Import historical programs (if applicable)
  - Verify data integrity post-migration
  - Compare Excel vs system calculations

- **Training & Documentation:**
  - User manuals for each role
  - Video tutorials (workflow walkthroughs)
  - Quick reference guides
  - FAQs based on common issues
  - In-app help/tooltips

- **Feedback Collection:**
  - Usability feedback
  - Feature requests
  - Issues and bugs
  - Performance feedback

- **UAT Sign-Off:**
  - Manager approval: "Ready for use by Managers"
  - Finance approval: "Ready for financial operations"
  - Officer approval: "Ready for field use"

- **Owner:** Product Lead + Users
- **Acceptance Criteria:** UAT sign-off from all stakeholder groups
- **Deliverable:** UAT report and sign-off

### 3. Go-Live Preparation (Weeks 39-40)

- **Pre-Launch:**
  - Final system check and smoke tests
  - Database backup
  - Rollback plan documentation
  - Escalation procedures and contact list
  - Support team briefing

- **Launch Window:**
  - Data freeze (no Excel entries)
  - System deployment to production
  - Database migration (if applicable)
  - Smoke tests in production
  - User access activation

- **Parallel Run (Optional):**
  - Run system + Excel simultaneously for 1-2 weeks
  - Compare outputs for verification
  - Build user confidence

- **Go-Live Support:**
  - 24/7 support for first week
  - Daily check-ins for first month
  - Issue escalation procedures
  - Performance monitoring

- **Post-Launch Activities:**
  - User feedback collection
  - Issue resolution and patches
  - Performance optimization
  - Documentation updates

- **Owner:** Tech Lead + Product Lead
- **Acceptance Criteria:** System stable, users productive, issues resolved quickly
- **Deliverable:** Go-live completion report

---

## IMPLEMENTATION SUCCESS FACTORS

### Critical Success Factors

✅ **RBAC configured correctly** - Determines entire permission model
✅ **Approval Workflow validated with managers early** - Gates all subsequent features
✅ **COA structure locked after definition** - Prevents broken references
✅ **Payment/Settlement thoroughly tested** - Critical path for financial operations
✅ **Multi-program splits validated** - Complex variance handling must be bulletproof
✅ **Phased user rollout** - Build confidence before broader deployment
✅ **Comprehensive integration testing** - Catch issues before go-live
✅ **Documentation and training** - Users must understand workflows

### De-Risking Measures

1. **Approval Workflow Validation**
   - Hold workshops with Manager and AVP to define approval rules
   - Document with real scenarios before coding
   - Mock approval workflows in design phase

2. **COA Lock-Down**
   - Comprehensive review with Finance
   - Sign-off required before Programs can be created
   - All COA changes require AVP approval and audit trail

3. **Settlement Complexity**
   - Build settlement incrementally (simple → complex)
   - Test multi-program splits with real scenarios
   - Stress test variance handling

4. **User Adoption**
   - Start with power users (Manager, Finance)
   - Roll out to Associates (RA) next
   - Last to roll out: Officers in field (after system proven)
   - Comprehensive training for each wave

5. **Data Migration**
   - Import historical programs/budgets if needed
   - Verify data integrity post-import
   - Provide reconciliation reports

6. **Monitoring & Support**
   - 24/7 support for first week
   - Daily standups for first month
   - Performance monitoring (queries, logs)
   - User feedback collection and quick response

### Risk Mitigation Timeline

| Risk | Mitigation Strategy | Timing |
|------|---|---|
| Approval logic wrong | Validate with stakeholders before coding | Phase 2, Week 4-5 |
| COA changes break budgets | Lock COA after Programs created | Phase 4, Week 12 |
| Settlement variances untested | Stress test multi-program splits | Phase 7, Weeks 22-23 |
| Users reject system | Phased rollout, comprehensive training | Phase 13, Weeks 38-40 |
| Performance issues | Load testing before go-live | Phase 13, Week 37 |
| Data loss | Automated backups, tested restore procedure | Ongoing |
| Critical bugs in production | Comprehensive UAT and sign-off | Phase 13, Week 39 |

---

## PARALLEL TRACKS & TIMELINE OPTIMIZATION

### Possible Parallelization

**After Phase 2 (Approval Engine complete):**
- Phase 3 (Sites/FY/COA) can happen while Admin Panel is designed

**After Phase 4 (COA locked):**
- Phase 5 (Program) and early Phase 6 (Payment core) can overlap

**After Phase 6 (Payment Request done):**
- Phase 7 (Settlement), Phase 8 (Revenue), Phase 10 (Subsidi) can overlap
- Phase 11 domain-specific features (Asset, Construction, Travel) can happen in parallel

**During Phase 12 (Reporting):**
- Phase 13 (Testing) can begin in parallel with Report completion

### Total Timeline: 40 Weeks (~9-10 Months)

Critical path cannot be significantly compressed without quality risk. Parallelization gains ~2-3 weeks but requires additional team capacity.

---

## DELIVERABLES SUMMARY

| Phase | Deliverable | Status |
|-------|---|---|
| 1 | RBAC + User Management operational | Complete |
| 2 | Approval Workflow configurable and tested | Complete |
| 3 | Sites, Fiscal Years, Global COA established | Complete |
| 4 | Per-Site-Per-FY COA locked and ready | Complete |
| 5 | Programs with budgets and BoQs approved | Complete |
| 6 | Payment Requests end-to-end | Complete |
| 7 | Settlement with variance handling | Complete |
| 8 | Revenue Recording independent | Complete |
| 9 | Program completion and archival | Complete |
| 10 | Employee benefits (subsidi) | Complete |
| 11 | Admin panel, Asset, Construction, Travel | Complete |
| 12 | Dashboards, Reports, Exports | Complete |
| 13 | System tested, trained, deployed | Complete |

---

## CONCLUSION

This roadmap provides a **chronologically correct, de-risked implementation path** for GreenLedger that:

✅ Builds foundation first (RBAC → Approval → Master Data)
✅ Establishes critical structures early (COA locked before use)
✅ Builds core workflows systematically (Program → Payment → Settlement)
✅ Validates complex logic thoroughly (multi-program splits, variance handling)
✅ Enables phased user adoption (power users → broader rollout)
✅ Delivers business value incrementally (visibility increases with each phase)

**Total development effort: 40 weeks / ~9-10 months**
**Total team: 2-3 developers (Backend + Frontend), 1 QA, 1 Product Lead**
**Risk level: Medium (complexity is high, but mitigated through phasing and testing)**
