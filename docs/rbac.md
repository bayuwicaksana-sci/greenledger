# Role-Based Access Control (RBAC) Permissions Matrix

---

## Permission Legend

**Actions:**

- **C** = Create
- **R** = Read/View
- **U** = Update/Edit
- **D** = Delete
- **A** = Approve
- **X** = Archive
- **E** = Export
- **O** = Override/Emergency Action

**Scope Modifiers:**

- **Own** = Only their own records
- **Assigned** = Only assigned programs/entities
- **Site** = All records in their site(s)
- **All** = All records across all sites
- **None** = No access

---

# 1. DASHBOARD & OVERVIEW

| Feature                    | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| -------------------------- | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Dashboard             | R-Own      | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Site Selector              | Assigned   | Assigned   | All     | All   | All        | All        |
| Key Metrics Widget         | R-Own      | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Budget Utilization Chart   | R-Assigned | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Program P&L Summary        | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Pending Approvals Widget   | None       | None       | R-All   | R-All | R-All      | None       |
| Settlement Deadline Alerts | R-Own      | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Revenue Summary            | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |

---

# 2. SITES MANAGEMENT

| Feature                     | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| --------------------------- | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Sites                  | R-Assigned | R-Assigned | R-All   | R-All | R-All      | R-All      |
| Create Site                 | None       | None       | C       | C     | None       | None       |
| Edit Site                   | None       | None       | U       | U     | None       | None       |
| Activate/Deactivate Site    | None       | None       | U       | U     | None       | None       |
| Site Performance Comparison | None       | R-Assigned | R-All   | R-All | R-All      | None       |

---

# 3. USERS & ACCESS MANAGEMENT

| Feature             | RO     | RA     | Manager | AVP   | Finance Op | Farm Admin |
| ------------------- | ------ | ------ | ------- | ----- | ---------- | ---------- |
| View Users          | R-Site | R-Site | R-All   | R-All | R-All      | R-Site     |
| Create User         | None   | None   | C       | C     | None       | None       |
| Edit User           | None   | None   | U       | U     | None       | None       |
| Deactivate User     | None   | None   | U       | U     | None       | None       |
| Assign Roles        | None   | None   | C,U,D   | C,U,D | None       | None       |
| Assign Site Access  | None   | None   | C,U,D   | C,U,D | None       | None       |
| Reset Password      | None   | None   | U       | U     | None       | None       |
| View Access Logs    | None   | None   | R-All   | R-All | None       | None       |
| Change Own Password | U      | U      | U       | U     | U          | U          |
| Edit Own Profile    | U      | U      | U       | U     | U          | U          |

---

# 4. PROGRAMS & BUDGETS

| Feature                     | RO         | RA         | Manager   | AVP       | Finance Op | Farm Admin |
| --------------------------- | ---------- | ---------- | --------- | --------- | ---------- | ---------- |
| View Programs (Own Site)    | R-Site     | R-Site     | R-All     | R-All     | R-All      | R-Site     |
| View Programs (Other Sites) | None       | None       | R-All     | R-All     | R-All      | None       |
| Create Program              | None       | C-Draft    | C-Draft   | C-Draft   | None       | None       |
| Edit Program (Draft)        | None       | U-Assigned | U-All     | U-All     | None       | None       |
| Edit Program (Active)       | None       | None       | U-Limited | U-Limited | None       | None       |
| Delete Program (Draft only) | None       | D-Assigned | D-All     | D-All     | None       | None       |
| Submit Program for Approval | None       | C-Assigned | C-All     | None      | None       | None       |
| Approve Program Budget      | None       | None       | A         | A         | None       | None       |
| Reject Program Budget       | None       | None       | A         | A         | None       | None       |
| View Program Budget         | R-Assigned | R-Assigned | R-All     | R-All     | R-All      | None       |
| View Program P&L            | R-Assigned | R-Assigned | R-All     | R-All     | R-All      | None       |
| Mark Program as Completed   | None       | None       | C         | None      | None       | None       |
| Approve Program Completion  | None       | None       | None      | A         | None       | None       |
| Archive Program             | None       | None       | X         | X         | None       | None       |
| Unarchive Program           | None       | None       | X         | X         | None       | None       |
| View Archived Programs      | R-Assigned | R-Assigned | R-All     | R-All     | R-All      | None       |
| Export Program Data         | E-Assigned | E-Assigned | E-All     | E-All     | E-All      | None       |

---

# 5. PROGRAM ASSIGNMENTS

| Feature                  | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| ------------------------ | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Program Assignments | R-Assigned | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Assign User to Program   | None       | None       | C       | C     | None       | None       |
| Remove User from Program | None       | None       | D       | D     | None       | None       |
| Change Assignment Role   | None       | None       | U       | U     | None       | None       |

---

# 6. ACTIVITIES

| Feature                           | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| --------------------------------- | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Activities                   | R-Assigned | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Create Activity                   | C-Assigned | C-Assigned | C-All   | C-All | None       | None       |
| Edit Activity                     | U-Assigned | U-Assigned | U-All   | U-All | None       | None       |
| Delete Activity (no transactions) | None       | D-Assigned | D-All   | D-All | None       | None       |
| Set Activity Status               | U-Assigned | U-Assigned | U-All   | U-All | None       | None       |
| View Activity Budget              | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| View Activity Financials          | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Export Activity Data              | E-Assigned | E-Assigned | E-All   | E-All | E-All      | None       |

---

# 7. CHART OF ACCOUNTS (COA)

| Feature                      | RO     | RA     | Manager | AVP   | Finance Op | Farm Admin |
| ---------------------------- | ------ | ------ | ------- | ----- | ---------- | ---------- |
| View COA                     | R-Site | R-Site | R-All   | R-All | R-All      | R-Site     |
| Create COA Account           | None   | None   | C       | C     | C          | None       |
| Edit COA Account Name        | None   | None   | U       | U     | U          | None       |
| Edit COA Account Code        | None   | None   | None    | O     | O          | None       |
| Deactivate COA Account       | None   | None   | U       | U     | U          | None       |
| View COA Usage Report        | None   | R-Site | R-All   | R-All | R-All      | None       |
| View COA Transaction History | None   | R-Site | R-All   | R-All | R-All      | None       |
| Bulk Import COA              | None   | None   | C       | C     | C          | None       |
| Export COA                   | None   | E-Site | E-All   | E-All | E-All      | None       |

---

# 8. PAYMENT REQUESTS

| Feature                                | RO    | RA         | Manager | AVP   | Finance Op | Farm Admin |
| -------------------------------------- | ----- | ---------- | ------- | ----- | ---------- | ---------- |
| View Payment Requests                  | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Create Payment Request (Single)        | C     | C          | C       | C     | None       | None       |
| Create Payment Request (Multi-Program) | C     | C          | C       | C     | None       | None       |
| Edit Payment Request (Draft)           | U-Own | U-Assigned | U-All   | U-All | None       | None       |
| Delete Payment Request (Draft)         | D-Own | D-Assigned | D-All   | D-All | None       | None       |
| Submit Payment Request                 | C-Own | C-Assigned | C-All   | C-All | None       | None       |
| Approve Payment Request                | None  | None       | A       | A     | None       | None       |
| Reject Payment Request                 | None  | None       | A       | A     | None       | None       |
| Process Payment (Mark as Paid)         | None  | None       | None    | None  | C          | None       |
| Cancel Payment Request                 | None  | None       | U-All   | U-All | None       | None       |
| View Payment Request Splits            | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Edit Splits (Before Approval)          | U-Own | U-Assigned | None    | None  | None       | None       |
| Upload Quotation                       | C-Own | C-Assigned | C-All   | C-All | None       | C-Site     |
| Download Payment Receipt               | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Export Payment Requests                | E-Own | E-Assigned | E-All   | E-All | E-All      | None       |
| Bypass Approval (Emergency)            | None  | None       | None    | O     | None       | None       |

---

# 9. SETTLEMENTS

| Feature                     | RO    | RA         | Manager | AVP   | Finance Op | Farm Admin |
| --------------------------- | ----- | ---------- | ------- | ----- | ---------- | ---------- |
| View Settlements            | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Submit Settlement           | C-Own | C-Assigned | C-All   | C-All | None       | None       |
| Edit Settlement (Pending)   | U-Own | U-Assigned | None    | None  | None       | None       |
| Upload Receipt              | C-Own | C-Assigned | C-All   | C-All | None       | C-Site     |
| Delete Settlement (Pending) | D-Own | D-Assigned | D-All   | D-All | None       | None       |
| Review Settlement           | None  | None       | None    | None  | R-All      | R-Site     |
| Request Settlement Revision | None  | None       | None    | None  | C          | C-Site     |
| Approve Settlement          | None  | None       | None    | None  | A          | None       |
| Reject Settlement           | None  | None       | None    | None  | A          | None       |
| View Settlement Splits      | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| View Overdue Settlements    | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Export Settlements          | E-Own | E-Assigned | E-All   | E-All | E-All      | None       |
| Modify Locked Settlement    | None  | None       | None    | O     | O          | None       |

---

# 10. REVENUE - HARVEST

| Feature                     | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| --------------------------- | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Harvest Revenue        | R-Assigned | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Record Harvest Revenue      | C-Assigned | C-Assigned | C-All   | C-All | None       | None       |
| Edit Harvest (Within 48hrs) | U-Own      | U-Assigned | U-All   | U-All | None       | None       |
| Edit Harvest (After 48hrs)  | None       | None       | None    | None  | U          | None       |
| Delete Harvest (Draft)      | D-Own      | D-Assigned | D-All   | D-All | None       | None       |
| Review Harvest              | None       | None       | R-All   | R-All | R-All      | None       |
| Request Harvest Correction  | None       | None       | C       | C     | C          | None       |
| View Harvest by Program     | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| View Harvest by Buyer       | None       | R-Assigned | R-All   | R-All | R-All      | None       |
| View Harvest Cycle Report   | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Export Harvest Data         | E-Assigned | E-Assigned | E-All   | E-All | E-All      | None       |

---

# 11. REVENUE - TESTING SERVICES

| Feature                        | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| ------------------------------ | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Testing Services          | R-Assigned | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Record Testing Service         | None       | C-Assigned | C-All   | C-All | None       | None       |
| Edit Testing Service (Draft)   | None       | U-Assigned | U-All   | U-All | None       | None       |
| Delete Testing Service (Draft) | None       | D-Assigned | D-All   | D-All | None       | None       |
| Submit Testing Service         | None       | C-Assigned | C-All   | C-All | None       | None       |
| Approve Testing Service        | None       | None       | A       | A     | None       | None       |
| Reject Testing Service         | None       | None       | A       | A     | None       | None       |
| Update Payment Status          | None       | U-Assigned | U-All   | U-All | U          | None       |
| Upload Contract Document       | None       | C-Assigned | C-All   | C-All | None       | None       |
| View Service by Client         | None       | R-Assigned | R-All   | R-All | R-All      | None       |
| Export Testing Services        | None       | E-Assigned | E-All   | E-All | E-All      | None       |

---

# 12. BUYERS & CLIENTS

| Feature                         | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| ------------------------------- | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| View Buyers                     | R-All      | R-All      | R-All   | R-All | R-All      | R-All      |
| Create Buyer                    | C          | C          | C       | C     | C          | None       |
| Edit Buyer                      | None       | U          | U       | U     | U          | None       |
| Deactivate Buyer                | None       | None       | U       | U     | U          | None       |
| View Buyer Transaction History  | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| View Clients                    | R-All      | R-All      | R-All   | R-All | R-All      | R-All      |
| Create Client                   | None       | C          | C       | C     | C          | None       |
| Edit Client                     | None       | U          | U       | U     | U          | None       |
| Deactivate Client               | None       | None       | U       | U     | U          | None       |
| View Client Transaction History | None       | R-Assigned | R-All   | R-All | R-All      | None       |
| Export Buyers/Clients           | None       | E-All      | E-All   | E-All | E-All      | None       |

---

# 13. SUBSIDI

| Feature                      | RO    | RA    | Manager | AVP   | Finance Op | Farm Admin |
| ---------------------------- | ----- | ----- | ------- | ----- | ---------- | ---------- |
| View My Subsidi Eligibility  | R-Own | R-Own | R-Own   | R-Own | R-Own      | R-Own      |
| View Subsidi Types           | R-All | R-All | R-All   | R-All | R-All      | R-All      |
| Submit Subsidi Claim         | C-Own | C-Own | C-Own   | C-Own | C-Own      | C-Own      |
| Edit Subsidi Claim (Draft)   | U-Own | U-Own | U-Own   | U-Own | U-Own      | U-Own      |
| Delete Subsidi Claim (Draft) | D-Own | D-Own | D-Own   | D-Own | D-Own      | D-Own      |
| View My Claims               | R-Own | R-Own | R-Own   | R-Own | R-Own      | R-Own      |
| View All Claims              | None  | None  | R-All   | R-All | None       | None       |
| Approve Subsidi Claim        | None  | None  | A       | A     | None       | None       |
| Reject Subsidi Claim         | None  | None  | A       | A     | None       | None       |
| Process Subsidi Payment      | None  | None  | None    | None  | C          | None       |
| Create Subsidi Type          | None  | None  | C       | C     | None       | None       |
| Edit Subsidi Type            | None  | None  | U       | U     | None       | None       |
| Deactivate Subsidi Type      | None  | None  | U       | U     | None       | None       |
| Manage Eligibility           | None  | None  | C,U,D   | C,U,D | None       | None       |
| Export Subsidi Data          | None  | None  | E-All   | E-All | None       | None       |

---

# 14. REPORTS & ANALYTICS

## Financial Reports

| Feature                       | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| ----------------------------- | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| Program P&L Report            | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Budget vs Actual Report       | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Budget Utilization Report     | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Variance Analysis             | None       | R-Assigned | R-All   | R-All | R-All      | None       |
| Revenue Analysis (Harvest)    | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Revenue Analysis (Testing)    | None       | R-Assigned | R-All   | R-All | R-All      | None       |
| Expense by COA Report         | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Expense by Program Report     | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Expense by Activity Report    | R-Assigned | R-Assigned | R-All   | R-All | R-All      | None       |
| Site Performance Comparison   | None       | None       | R-All   | R-All | R-All      | None       |
| Consolidated Financial Report | None       | None       | R-All   | R-All | R-All      | None       |

## Operational Reports

| Feature                      | RO    | RA         | Manager | AVP   | Finance Op | Farm Admin |
| ---------------------------- | ----- | ---------- | ------- | ----- | ---------- | ---------- |
| Payment Request Register     | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Settlement Status Report     | R-Own | R-Assigned | R-All   | R-All | R-All      | R-Site     |
| Settlement Compliance Report | None  | None       | R-All   | R-All | R-All      | R-Site     |
| Approval Cycle Time Report   | None  | None       | R-All   | R-All | R-All      | None       |
| Transaction Volume Report    | None  | None       | R-All   | R-All | R-All      | None       |
| User Activity Report         | None  | None       | R-All   | R-All | None       | None       |

## Compliance & Audit Reports

| Feature                 | RO   | RA   | Manager | AVP   | Finance Op | Farm Admin |
| ----------------------- | ---- | ---- | ------- | ----- | ---------- | ---------- |
| Audit Trail Report      | None | None | R-All   | R-All | R-All      | None       |
| Digital Signature Log   | None | None | R-All   | R-All | R-All      | None       |
| Admin Activity Log      | None | None | R-All   | R-All | None       | None       |
| Critical Actions Report | None | None | R-All   | R-All | None       | None       |
| Data Change History     | None | None | R-All   | R-All | R-All      | None       |

## Export Capabilities

| Feature                  | RO         | RA         | Manager | AVP   | Finance Op | Farm Admin |
| ------------------------ | ---------- | ---------- | ------- | ----- | ---------- | ---------- |
| Export to Excel          | E-Assigned | E-Assigned | E-All   | E-All | E-All      | None       |
| Export to PDF            | E-Assigned | E-Assigned | E-All   | E-All | E-All      | None       |
| Export to CSV            | E-Assigned | E-Assigned | E-All   | E-All | E-All      | None       |
| Schedule Report Delivery | None       | None       | C,U,D   | C,U,D | C,U,D      | None       |

---

# 15. SYSTEM SETTINGS

| Feature                          | RO   | RA   | Manager | AVP   | Finance Op | Farm Admin |
| -------------------------------- | ---- | ---- | ------- | ----- | ---------- | ---------- |
| View System Settings             | None | None | R-All   | R-All | None       | None       |
| Edit Annual Budget Ceiling       | None | None | U       | U     | None       | None       |
| Edit Batch Processing Times      | None | None | U       | U     | None       | None       |
| Edit Settlement Deadline         | None | None | U       | U     | None       | None       |
| Edit Fiscal Year Dates           | None | None | U       | U     | None       | None       |
| Configure Email Settings         | None | None | U       | U     | None       | None       |
| Configure Notification Templates | None | None | U       | U     | None       | None       |
| Configure Workflow Rules         | None | None | U       | U     | None       | None       |
| Configure Approval Hierarchies   | None | None | U       | U     | None       | None       |

---

# 16. NOTIFICATIONS

| Feature                            | RO    | RA    | Manager | AVP   | Finance Op | Farm Admin |
| ---------------------------------- | ----- | ----- | ------- | ----- | ---------- | ---------- |
| View Own Notifications             | R-Own | R-Own | R-Own   | R-Own | R-Own      | R-Own      |
| Mark as Read/Unread                | U-Own | U-Own | U-Own   | U-Own | U-Own      | U-Own      |
| Delete Notification                | D-Own | D-Own | D-Own   | D-Own | D-Own      | D-Own      |
| Configure Notification Preferences | U-Own | U-Own | U-Own   | U-Own | U-Own      | U-Own      |
| Manage Notification Settings       | None  | None  | U-All   | U-All | None       | None       |

---

# 17. EMERGENCY OPERATIONS (System Admin Only)

| Feature                        | RO   | RA   | Manager | AVP | Finance Op | Farm Admin |
| ------------------------------ | ---- | ---- | ------- | --- | ---------- | ---------- |
| Unlock Budget                  | None | None | O       | O   | None       | None       |
| Delete Transaction             | None | None | O       | O   | None       | None       |
| Bypass Approval                | None | None | O       | O   | None       | None       |
| Modify Locked Data             | None | None | O       | O   | None       | None       |
| Manual Journal Entry           | None | None | O       | O   | None       | None       |
| Force Archive/Unarchive        | None | None | O       | O   | None       | None       |
| Reset User Password (Any User) | None | None | O       | O   | None       | None       |
| Override Settlement            | None | None | O       | O   | None       | None       |

---

# 18. AUDIT & MONITORING

| Feature                      | RO   | RA   | Manager | AVP   | Finance Op | Farm Admin |
| ---------------------------- | ---- | ---- | ------- | ----- | ---------- | ---------- |
| View Audit Logs              | None | None | R-All   | R-All | R-All      | None       |
| View Admin Activity Log      | None | None | R-All   | R-All | None       | None       |
| View User Access Logs        | None | None | R-All   | R-All | None       | None       |
| View Critical Action Alerts  | None | None | R-All   | R-All | None       | None       |
| View System Health Dashboard | None | None | R-All   | R-All | None       | None       |
| Export Audit Data            | None | None | E-All   | E-All | E-All      | None       |

---

# 19. DATA MANAGEMENT

| Feature               | RO   | RA   | Manager | AVP   | Finance Op | Farm Admin |
| --------------------- | ---- | ---- | ------- | ----- | ---------- | ---------- |
| Trigger Manual Backup | None | None | C       | C     | None       | None       |
| View Backup History   | None | None | R-All   | R-All | None       | None       |
| Restore from Backup   | None | None | O       | O     | None       | None       |
| Run Year-End Process  | None | None | C       | C     | None       | None       |
| Bulk Archive Programs | None | None | C       | C     | None       | None       |
| Data Export Tools     | None | None | E-All   | E-All | E-All      | None       |

---

# 20. PROGRAM LIFECYCLE ACTIONS

| Feature                    | RO   | RA         | Manager | AVP   | Finance Op | Farm Admin |
| -------------------------- | ---- | ---------- | ------- | ----- | ---------- | ---------- |
| Create Draft Program       | None | C          | C       | C     | None       | None       |
| Submit for Budget Approval | None | C-Assigned | C-All   | C-All | None       | None       |
| Approve Budget (Activate)  | None | None       | A       | A     | None       | None       |
| Reject Budget              | None | None       | A       | A     | None       | None       |
| Mark as Completed          | None | None       | C       | None  | None       | None       |
| Approve Completion         | None | None       | None    | A     | None       | None       |
| Archive Program            | None | None       | X       | X     | None       | None       |
| Unarchive Program          | None | None       | X       | X     | None       | None       |

---

# SPECIAL PERMISSIONS NOTES

## Cross-Site Access

- **RO/RA**: Only see assigned site(s) unless explicitly granted cross-site access
- **Manager/AVP/Finance**: Default access to all sites
- **Farm Admin**: Access to both Klaten and Magelang

## Program Assignment Override

- **Manager/AVP**: Can assign users from any site to any program
- **RA**: Cannot assign themselves to programs without Manager approval

## Budget Thresholds

- Implement multi-level approvals for budgets exceeding configured thresholds
- Manager: Up to 500M per program
- AVP: Unlimited

## Settlement Review Hierarchy

1. **Farm Admin**: Preliminary documentation check
2. **Finance Operation**: Final review and approval
3. Both can request revisions

## Emergency Override Justification

All emergency operations (O) require:

- Mandatory justification text (minimum 50 characters)
- Dual admin notification (Manager + AVP)
- Critical action alert generation
- Detailed audit log entry

## Fiscal Year Boundary

- During year-end process (Dec 15-31), restrict budget modifications
- Only System Admins can override year-end locks

## Multi-Role Users

Users with multiple roles get union of permissions:

- Example: User with both RO + RA roles gets RA permissions (higher privilege)
- Example: Manager role always includes lower-level permissions

---

# IMPLEMENTATION RECOMMENDATIONS

## Database Level

- Store permissions in `role_permissions` table
- Check permissions via JOIN queries on user_roles
- Cache frequent permission checks

## Application Level

- Implement middleware for permission checks
- Use permission decorators/guards on API endpoints
- Frontend: Hide/disable UI elements based on permissions

## API Endpoints

- All endpoints validate user permissions before processing
- Return 403 Forbidden if permission denied
- Log all permission denials for security monitoring

## Row-Level Security

- Implement site_id filtering in database queries
- Use user's primary_site_id + additional_sites for scoping
- Program assignments override site restrictions

---

**Total Unique Permissions**: ~250+ granular permission checks across 6 roles
**Permission Inheritance**: Manager + AVP inherit all lower-level permissions
**Override Permissions**: 8 emergency operations require special justification
