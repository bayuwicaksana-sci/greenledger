# Project Goals

**GreenLedger** - Agriculture Science Division Financial Management System

# **I. EXECUTIVE SUMMARY**

## **1.1 Background**

The Agriculture Science Division is currently facing significant challenges in managing operational finances. As a highly mobile research division with field teams spread across multiple sites (Klaten and Magelang), a manual Excel-based system is no longer adequate to support the operational complexity and high accountability requirements.

## **1.2 Pain Points**

- The manual Excel system is not scalable for mobile teams and multi-site operations
- Documentation is unstructured and difficult to archive with a proper audit trail
- Workflow approvals via email cause bottlenecks and loss of documentation
- There is no real-time budget monitoring or visibility for decision-making
- The settlement process is not properly controlled (no timeout, no tracking)
- Coordination between Jakarta (FO) and Sites (FA Klaten/Magelang) is not efficient

## **1.3 Proposed Solutions**

Development of an integrated financial management system specifically designed for the needs of a legally established research foundation organization with the following characteristics:

- Configurable multi-level approval workflow
- Real-time budget monitoring per research topic and sub-division
- Automated settlement tracking with SLA enforcement (2-7 days)
- Flexible payment mechanisms (Cash Advance, Reimbursement, Direct Payment)
- Asset tracking for fixed assets and non-consumable items
- Comprehensive audit trail for compliance and accountability
- Integrated communication (in-app chat) between requester and finance team

## **1.4 Expected Benefits**

| Aspect                     | Benefit                                                                                    |
| :------------------------- | :----------------------------------------------------------------------------------------- |
| **Operational Efficiency** | Reduction in approval process time by up to 70% through automated workflow                 |
| **Visibility & Control**   | Real-time budget monitoring per topic and sub-division for decision making                 |
| **Compliance**             | Complete audit trail and automated document archiving in accordance with retention policy  |
| **Accountability**         | Enforced settlement SLA with auto-blocking mechanism for non-compliance                    |
| **Collaboration**          | Seamless coordination between FO (Jakarta) and FA (Sites) through integrated communication |

# **II. BUSINESS CONTEXT**

## **2.1 Organization Structure**

The Agriculture Science Division consists of 13 positions with a clear reporting hierarchy:

| No     | Position                             | Role Level   | Reports To  | Sub-Division | Call Sign |
| :----- | :----------------------------------- | :----------- | :---------- | :----------- | :-------- |
| **1**  | AVP of Agri Services                 | Top Approver | COO         | Dept Level   | AVP       |
| **2**  | Agriculture Science Manager          | Manager      | AVP         | Div Level    | Manager   |
| **3**  | Research Associate - Agronomy        | Associate    | AS Manager  | Agronomy     | RA-Agro   |
| **4**  | Research Associate - Crop Protection | Associate    | AS Manager  | Crops Prot   | RA-CP     |
| **5**  | Research Associate - Product Testing | Associate    | AS Manager  | Prod Test    | RA-PT     |
| **6**  | Knowledge Associate                  | Associate    | AS Manager  | Knowledge    | KA        |
| **7**  | Research Officer - Agronomy          | Officer      | RA Agronomy | Agronomy     | RO-Agro   |
| **8**  | Research Officer - Crops Protection  | Officer      | RA CP       | Crops Prot   | RO-CP     |
| **9**  | Research Officer - Product Testing   | Officer      | RA PT       | Prod Test    | RO-PT     |
| **10** | Knowledge Officer                    | Officer      | KA          | Knowledge    | KO        |
| **11** | Farm Supervisor                      | Supervisor   | RA Agronomy | Agronomy     | FS        |
| **12** | Farm Admin                           | Admin        | AS Manager  | Div Level    | FA        |
| **13** | Finance Operation                    | Officer      | AS Manager  | Div Level    | FO        |

## **2.2 Multi-Site Operations**

The division's operations are spread across three locations with the following functional distribution:

| Location              | Function                                                        | Team                                   |
| :-------------------- | :-------------------------------------------------------------- | :------------------------------------- |
| **Jakarta (HQ)**      | Finance coordination, approval, and physical document archiving | FO (Finance Operation), AVP, Manager   |
| **Klaten (Site 1)**   | Research execution and local verification                       | FA, RA, RO, FS (various sub-divisions) |
| **Magelang (Site 2)** | Research execution and local verification                       | FA, RA, RO, FS (various sub-divisions) |

**Important Note:** FO and FA coordinate to manage operations at both sites. The budget is assigned per research site to ensure better visibility and control.

## **2.3 Regulatory Context (Foundation)**

As a legal entity of the Foundation, there are special provisions that must be adhered to in financial management:

- **Separation of Program vs Non-Program Budget**

- Program: Core activities (Research & Knowledge Development)
- Non-Program: Supporting activities (procurement, travel, amenities, etc.)

- **Budget Cycle Management**

- Fiscal year: January - December (calendar year)
- No budget carry-over across fiscal years
- Budget reallocation allowed within approved total (no limit on frequency)

- **Document Retention Policy**

- Physical documents: Minimum of 5 years
- Digital documents (active): 3 years
- Digital documents (cold storage): After 3 years, accessible only with AVP approval
- Auto-archive: Files older than 1 year are automatically moved to the archive

**Procurement Compliance**

- Items over 1 million per item MUST go through the Procurement team
- FO determines the need for procurement involvement during review (manual decision)

# **III. BUSINESS PROCESS DESIGN**

This section describes the end-to-end workflow for all key business processes within the system. Each workflow is designed with consideration for segregation of duties, approval hierarchy, and enforcement mechanisms.

## **3.1 Program (Research & Knowledge)**

### **3.1.1 Budget Planning & Approval**

Workflow for the preparation and approval of the Budget Plan (RAB) for research or knowledge development topics:

1. **Initiation by Associate Level**

- RA/KA prepares a budget plan (RAB) for the research topic with itemized breakdowns and estimated costs.
- The RAB includes: topic title, objectives, timeline, budget items with quantity and unit price.
- The system automatically assigns an RAB ID and version (v1.0 for initial).

2. **Manager Review & COA Mapping**

- The Manager reviews the feasibility of the budget and maps the Chart of Accounts (COA) for each item.
- COA mapping is done at the beginning to facilitate execution (requesters do not need to map again).
- Manager options: Approve (proceed to AVP), Request Revision (return to RA/KA with notes), Hard Reject (RAB rejected, not continued).

3. **AVP Final Approval**

- The AVP conducts the final review and approval.
- AVP options: Approve (RAB becomes active), Request Revision (return to Manager & RA/KA), Hard Reject.
- Once approved by AVP, the RAB status = ACTIVE and the budget is available for execution.

4. **Budget Availability**

- All employees who are the person in charge (PIC) of a topic can submit payment requests for items in the Budget Plan (RAB).
- System tracking: Planned amount (RAB), Committed amount (approved requests), Actual spent (settled), Remaining budget

### **3.1.2 Budget Reallocation**

The process for reallocating budget between items within a single research topic:

- Associates (RA/KA) can perform reallocation with Manager approval only.
- AVPs are automatically notified of any reallocation (for awareness purposes, no approval needed).
- The total budget remains unchanged (only transfers between items).
- The system automatically increments the RAB version (v1.1, v1.2, etc.) for audit trail purposes.
- There is no limit on reallocation frequency as long as the total approved budget is not exceeded.
- Historical versions remain accessible for audit purposes.

### **3.1.3 Budget Addition (Top-up)**

If additional budget is needed during mid-research:

- RA/KA submits an additional budget proposal (Addition Request) with justification
- Approval flow remains the same: Manager → AVP (both must approve)
- Once approved, the old and new budgets are merged in the system into a single consolidated budget
- System tracking: Original Budget + Addition(s) = Total Available Budget
- Version numbering: major version increment (v2.0, v3.0) for budget additions

### **3.1.4 Payment Request Workflow**

End-to-end workflow for payment requests (budget realization):

5. **Request Creation**

- The requester (RO/FS/RA/KA) creates a payment request
- Select items ONLY from the approved budget plan (RAB)
- Choose payment type: Cash Advance, Reimbursement, or Mixed
- Choose payment method: Bank Transfer, Cash, Virtual Account
- Input bank details if transferring (account number, account holder name, bank name)
- Mark urgency: Normal (12-hour SLA) or Urgent (5-hour SLA - requires Manager approval)
- For Reimbursement: upload proof of payment at the time of request creation

6. **Multi-level Approval**

The approval flow depends on the requester's level and amount:

| Requester Level | Amount      | Reviewed By | Approved By   |
| :-------------- | :---------- | :---------- | :------------ |
| **RO / FS**     | < 5 million | RA / KA     | Manager       |
| **RO / FS**     | > 5 million | RA / KA     | Manager + AVP |
| **RA / KA**     | < 5 million | -           | Manager       |
| **RA / KA**     | > 5 million | -           | Manager + AVP |
| **Manager**     | Any amount  | -           | AVP           |

**Note:** Self-approval is not allowed. Requests made by a Manager must be approved by an AVP.

7. **FO/FA Review**

- After approval, the request goes to FO for review
- FA conducts initial verification (physical document check)
- FO performs the final review and decision:
    - Direct Payment (processed by FO itself)
    - Via Procurement (flag items > 1 million per item, hand-off to Procurement team)
- FO can override the payment method if it is not appropriate (with notification to the requester)

8. **Disbursement**

- FO disburses the funds according to the payment method
- SLA: Normal = 12 hours, Urgent = 5 hours (from final approval)
- System auto-generates: Purchase Requisition (PR) and Purchase Order (PO) for compliance
- Status update: DISBURSED

### **3.1.5 Settlement Workflow**

The accountability process after funds have been disbursed:

9. **Settlement Submission (default 2x24 hours)**

- The requester MUST upload settlement proof within 2x24 hours after disbursement.
- The requester may request an extension from the FO (maximum 7 days in total).
- The FO can adjust the deadline via the UI (3x24 hours or custom, without approval).
- If the timeout is reached: The system will AUTO-BLOCK the requester (unable to create new requests).

10. **Evidence Upload**

- Requester upload:
    - Proof of payment (receipt, invoice)
    - Proof of work: Photos of goods (if purchasing items), Photos of process (if service), or both if applicable
    - Official Report (if the vendor cannot provide a formal invoice)
- File limit: 5-10 MB per upload (configurable), maximum 50 MB total
- Requests over 10 MB: Requester submits request via the system, Manager approval required

11. **FA Verification**

- FA verifies physical evidence (original documents)
- FA cross-checks digital files against physical documents
- FA options: Approve (proceed to FO), Request Revision (return to requester with notes)

12. **FO Final Verification**

- FO verifies the nominal amount, compliance, and completeness
- FO checks variance: Actual vs Disbursed amount
- FO options: Approve & Close, Request Revision

13. **Variance Handling**

The system automatically handles variance scenarios:

| Scenario         | System Action                                                                                                                                                                             |
| :--------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Exact Match**  | Status = CLOSED. No action required.                                                                                                                                                      |
| **Overpayment**  | The system automatically notifies the requester to return the excess funds to the topic budget. Funds are returned to the same topic budget pool.                                         |
| **Underpayment** | The system automatically creates a NEW payment request for the shortfall. The new request remains within the same topic (funds from the related topic). The normal approval flow applies. |

### **3.1.6 Communication & Dispute Resolution**

- **In-App Chat Feature**
    - Attached to every Payment Request and Settlement
    - Visible to: Requester, all Approvers (Manager, AVP), Reviewers (RA/KA), FO, FA
    - Conversation history = part of the audit trail
    - Use case: Clarification, discussion of evidence, revision requests
- **Dispute Escalation**
    - FA or FO can raise a dispute if there is a discrepancy
    - Dispute = a flag for Manager and AVP attention
    - Manager/AVP can override FO/FA decisions if justified
    - System logs all disputes and resolutions for audit purposes

## **3.2 Non-Program (Supporting Activities)**

Non-Program includes all supporting activities that do not fall under research/knowledge development. The basic workflow is the same as the Program, but with additional information requirements and specialized handling.

### **3.2.1 Non-Program Categories**

- **Asset Purchase**
    - Agricultural machinery, equipment, vehicles, furniture, electronics (>1 year economic life)
    - Additional info: Technical specifications, justification, expected lifespan, location assignment, PIC, warranty, disposal plan for replaced assets
    - Entered into the Asset Tracking Module after purchase

- **Construction/Renovation**
    - Greenhouse, lab facility, office renovation, infrastructure
    - Additional info: Detailed scope of work, site location, completion date, milestone breakdown
    - Multi-stage payment: 10% retention per milestone, released no later than 3 months after completion
    - Progress verification: Site visit report + photos, Manager sign-off per milestone
- **Operational Expenses**
    - Electricity, water, internet, office/land rent
    - Additional info: Billing period, meter reading (if applicable), comparison with previous month
    - Auto-reminder for recurring payments (monthly)
- **Travel**
    - Business trips, external training, conference attendance
    - Additional info: Destination, date, purpose, mode of transport, breakdown (ticket/hotel/per diem/toll/parking/fuel)
    - Hotel & vehicle handled by corporate account (paid directly by the Foundation Finance - outside system scope)
    - Cash advance for: Per diem (configurable rate per city), toll, parking, fuel
    - Settlement: Boarding pass, hotel invoice, odometer photo (if renting a car), toll/parking receipts
- **Consultant/Expert**
    - External expertise, training facilitator, advisor
    - Additional info: Name, area of expertise, scope of work/TOR, duration, payment scheme (per hour/day/deliverable/retainer)
    - Consultants are included in the Vendor Database (separate section)
    - Timesheet required for per-hour payment scheme
    - Deliverables must be accepted by the Manager before payment
- **Employee Development**
    - Training, certification, seminars, workshops, books, online courses, team building, outings
    - Scope: Individual, group, or an entire division
    - Additional info: Relevance to job, expected outcome
    - Settlement: Certificate, invoice, attendance photos
    - No post-training requirement (sharing session optional)
- **Amenities (Employee Welfare)**
    - Drinking water, sugar, coffee, tea, snacks
    - Safety equipment: PPE, first aid kit, tissues, cleaning supplies
    - Recurring and ad-hoc purchases
- **Office Supplies/Stationery**
    - Stationery, printer supplies, forms, labels
    - Simple workflow, no complex tracking

### **3.2.2 Multi-Stage Payment (Construction/Major Projects)**

For large projects with multiple milestones:

14. **Budget Plan (RAB) Creation with Milestone Breakdown**

- Manager and vendor agree on milestone structure
- Example: Down Payment 30%, Progress 1 (40%), Completion (30%)
- Each milestone = separate line item in the budget (RAB) with % retention (10% per milestone)

15. **Payment Request per Milestone**

- Requester prepares a payment request for a specific milestone
- Normal approval flow (Manager/AVP based on amount)
- Finance Office disburses 90% of the milestone amount (10% retention)

16. **Progress Verification**

- Site visit by the Manager (or delegated PIC)
- Upload: Site visit report + progress photos to the system
- Manager sign-off in the system to release the next milestone

17. **Retention Release**

- After project completion & final acceptance:
- System auto-creates a payment request for accumulated retention (10% x all milestones)
- Manager approval required
- Maximum 3 months after final completion, or as per contract terms

## **3.3 Claim Benefit (Employee Subsidy)**

The foundation provides a vehicle maintenance subsidy benefit of IDR 500,000 per month for eligible employees. The system is designed to accommodate additional benefits in the future (future-ready).

### **3.3.1 Vehicle Maintenance Subsidy**

- **Ceiling & Rules**
    - IDR 500,000 per employee per month
    - No carry-over (unused portion from the previous month is forfeited)
    - Cannot exceed the monthly ceiling
- **Workflow**
    - Employees create a benefit claim request (separate menu, not a regular payment request)
    - Primary option: Reimbursement (pay first, claim later)
    - Alternative: Direct payment by the office (for services at partner workshops – handled by the Foundation's Finance outside the system scope)
    - NO approval required (as there is already a ceiling, auto-approved if within the limit)
    - Front Office verifies & disburses (within remaining ceiling)
- **Evidence Required**
    - Proof of payment (workshop or gas station receipt)
    - Tachometer photo (before & after) if refueling
    - Photo of completed work if service
    - Official report if spare parts are replaced
- **System Tracking**
    - Monthly limit per employee
    - Claimed amount (used)
    - Remaining balance (real-time)
    - Auto-reset at the beginning of the following month

### **3.3.2 Future Benefits**

The system is designed to accommodate additional benefit types in the future (e.g., health subsidies, child education allowances, etc.). The database structure and UI should be modular to allow easy addition without major refactoring.

# **IV. APPROVAL MATRIX & ROLE DEFINITION**

## **4.1 Role Hierarchy & Permissions**

| Role                  | Permissions                                                                                                                                                                          | Data Visibility                                              |
| :-------------------- | :----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :----------------------------------------------------------- |
| **AVP**               | Approve RAB (all amounts), Approve payment requests (>5 million), Grant access to archived files, Full system admin                                                                  | ALL divisions, ALL topics, ALL budgets                       |
| **Manager**           | Approve RAB, COA mapping, Approve payment requests (<5 million directly, >5 million with AVP), Approve reallocation, Milestone sign-off, Deliverable acceptance, File >10MB approval | ALL divisions, ALL topics, ALL budgets                       |
| **Associate (RA/KA)** | Create RAB, Request budget addition, Request budget reallocation, Create payment request, Review payment request from RO/FS, Access chat                                             | OWN sub-division only (e.g., RA Agronomy → Agronomy sub-div) |
| **Officer (RO)**      | Create payment request, Submit settlement, View historical requests in assigned topic                                                                                                | Only topics where assigned as PIC                            |
| **Supervisor (FS)**   | Same as RO                                                                                                                                                                           | Only topics where assigned as PIC                            |
| **FO**                | Review payment requests, Disburse funds, Verify settlements, Override payment method, Adjust settlement deadline, Maintain vendor DB, Raise disputes, Access chat                    | ALL (for verification purposes)                              |
| **FA**                | Verify physical documents, Verify settlements, Flag procurement involvement, Maintain vendor DB, Raise disputes, Access chat                                                         | ALL (for verification purposes)                              |

## **4.2 Delegation Rules**

- **Manager Delegation**
    - If the Manager is on leave/unavailable: approvals automatically escalate to the AVP
    - The Manager can set a delegation period in the system (from-to date)
    - The system automatically routes approvals to the AVP during the delegation period
- **AVP Delegation**
    - The AVP does not have a delegation mechanism (must approve personally)
    - The AVP usually makes time to approve even during leave
    - The system sends urgent notifications if there is a pending approval >24 hours

## **4.3 SLA & Escalation**

| Process                   | Normal SLA | Urgent SLA | Escalation    |
| :------------------------ | :--------- | :--------- | :------------ |
| **Approval (Manager)**    | 24 hours   | 4 hours    | Email + SMS   |
| **Approval (AVP)**        | 48 hours   | 6 hours    | Email + SMS   |
| **Disbursement (FO)**     | 12 hours   | 5 hours    | Manager alert |
| **Settlement**            | 2x24 hours | N/A        | Auto-block    |
| **Settlement (Extended)** | Max 7 days | N/A        | Auto-block    |

**Notes:** Urgent requests require Manager approval along with the payment request approval. FO can manually adjust the settlement deadline without approval.

**--- END OF DOCUMENT ---**
