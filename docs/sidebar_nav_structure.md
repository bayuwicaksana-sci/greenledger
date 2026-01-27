# Sidebar Navigation Structure

---

## MAIN NAVIGATION

### 📊 Dashboard

- **Overview** (default landing page)
    - Site selector: All Sites / Klaten / Magelang
    - Key metrics: Budget utilization, pending approvals, program P&L
    - Quick actions widget
    - Alerts & notifications summary

---

### 💰 Financial Management

#### Programs & Budgets

- **My Programs** (RO, RA view - assigned programs)
- **All Programs** (Manager, AVP, Finance - all programs)
- **Create New Program**
- **Archived Programs** (toggle/filter option)

#### Transactions

- **Payment Requests**
    - All Requests (list view with filters)
    - Create Payment Request
    - Multi-Program Split Entry
    - Pending Approvals (Manager badge counter)
    - My Requests (RO, RA filter)
- **Settlements**
    - Pending Settlements (deadline countdown)
    - Settlement Review (Finance, Farm Admin)
    - All Settlements
    - Overdue Settlements (alert badge)

#### Revenue

- **Harvest Revenue**
    - Record Harvest
    - Harvest History
    - Buyer Management
- **Testing Services**
    - Record Service
    - Service History
    - Client Management

---

### 📋 Activities

- **Activity Planning** (RA, Manager)
- **Activity Tracking** (budget vs actual by activity)
- **Activity Calendar** (timeline view)

---

### 💵 Subsidi

- **My Subsidi** (eligible users)
- **Submit Claim**
- **Claim History**
- **Subsidi Administration** (Manager, AVP)
    - Manage Subsidi Types
    - Review Claims
    - Eligibility Management

---

### 📈 Reports & Analytics

#### Financial Reports

- **Program P&L**
    - By Program
    - By Site
    - Consolidated View
- **Budget Reports**
    - Budget vs Actual
    - Budget Utilization
    - Variance Analysis
- **Revenue Reports**
    - Harvest Revenue Analysis
    - Testing Service Revenue
    - Buyer/Client Analysis
- **Expense Reports**
    - By COA Account
    - By Program
    - By Activity

#### Operational Reports

- **Transaction Reports**
    - Payment Request Register
    - Settlement Status Report
    - Approval Cycle Time
- **Compliance Reports**
    - Settlement Compliance
    - Audit Trail Report
    - Digital Signature Log

---

### ⚙️ Configuration (Manager, AVP, Finance Operation)

#### Master Data

- **Chart of Accounts**
    - Manage COA
    - Site-Specific COA
    - Account Usage Report
- **Sites**
    - Site Configuration
    - Site Performance Comparison
- **Buyers & Clients**
    - Buyer Directory
    - Client Directory

#### System Settings

- **General Settings**
    - Annual Budget Configuration
    - Batch Processing Times
    - Settlement Deadline (48hrs)
    - Fiscal Year Settings
- **Workflow Configuration**
    - Approval Hierarchies
    - Auto-routing Rules
    - Notification Settings

---

### 👥 Administration (System Admin: Manager, AVP only)

#### User Management

- **Users**
    - User List
    - Create User
    - Role Assignment
    - Site Access Management
    - Deactivate Users
- **Access Control**
    - Program Assignments
    - Cross-Site Permissions
    - Role Configuration

#### System Administration

- **Emergency Operations**
    - Unlock Budget
    - Delete Transaction (with justification)
    - Bypass Approval
    - Manual Adjustments
- **Audit & Monitoring**
    - Admin Activity Log
    - Critical Action Alerts
    - System Health Dashboard
    - User Access Logs
- **Data Management**
    - Backup & Restore
    - Year-End Process
    - Archive Management
    - Data Export Tools

---

### 🔔 Notifications

- **All Notifications** (with unread badge)
- **Approval Requests**
- **Settlement Reminders**
- **Budget Alerts**
- **System Alerts**

---

### 👤 User Profile Menu (top-right dropdown)

- Profile Settings
- Change Password
- User Preferences
- Help & Documentation
- Logout

---

## ROLE-BASED NAVIGATION VISIBILITY

### Research Officer (RO)

**Visible:**

- Dashboard (filtered to assigned programs)
- Financial Management > Programs & Budgets > My Programs
- Financial Management > Transactions > Payment Requests (create, my requests)
- Financial Management > Transactions > Settlements (my settlements)
- Financial Management > Revenue > Harvest Revenue (record)
- Activities (assigned activities)
- Subsidi (if eligible)
- Reports (limited to assigned programs)
- Notifications
- User Profile

**Hidden:**

- All admin sections
- Approval queues
- Configuration
- Archive management

---

### Research Associate (RA)

**Visible:**

- Dashboard (assigned programs + site view)
- Financial Management > Programs & Budgets (My Programs, Create)
- Financial Management > Transactions > All sections (create, view)
- Financial Management > Revenue > All sections (record, manage)
- Activities (create, manage)
- Subsidi (if eligible)
- Reports (assigned programs + site level)
- Notifications
- User Profile

**Hidden:**

- Admin sections
- Configuration
- COA management
- System settings

---

### Manager (also System Admin)

**Visible:**

- Everything except Finance-specific functions
- Dashboard (all sites, consolidated view)
- All Financial Management sections
- All Activities sections
- Subsidi > Administration
- All Reports (full access)
- Full Configuration access
- Full Administration access
- Notifications (including admin alerts)

**Hidden:**

- Finance Operation specific review queues (shared visibility)

---

### AVP (also System Admin)

**Visible:**

- Everything
- Same as Manager with additional:
    - Executive-level dashboards
    - Multi-site strategic views
    - Final approval sections
    - Program completion approvals

---

### Finance Operation

**Visible:**

- Dashboard (financial operations view)
- Financial Management > Transactions (process payments)
- Financial Management > Transactions > Settlements (review, approve)
- Financial Management > Revenue (verify, correct)
- Configuration > Master Data > Chart of Accounts
- Reports > All financial reports
- Notifications (payment/settlement related)

**Hidden:**

- Program creation/editing
- Activity management
- Budget approval
- Admin sections (except COA)
- Subsidi

---

### Farm Admin

**Visible:**

- Dashboard (documentation view)
- Financial Management > Transactions (view, flag issues)
- Financial Management > Transactions > Settlements (preliminary review)
- Financial Management > Revenue (view)
- Reports (operational reports)
- Notifications (documentation reminders)

**Hidden:**

- All admin sections
- Configuration
- Subsidi
- Program management
- Approvals

---

## NAVIGATION FEATURES

### Global Features (all pages)

1. **Site Selector** (top navigation bar)
    - All Sites
    - Klaten
    - Magelang
    - Persists across navigation

2. **Search Bar** (top navigation)
    - Search programs by code/name
    - Search payment requests by number
    - Search users
    - Jump to entity by ID

3. **Quick Actions Button** (floating/fixed)
    - Create Payment Request
    - Record Harvest
    - Submit Settlement
    - Quick links based on role

4. **Notification Bell** (top-right)
    - Unread count badge
    - Dropdown preview
    - Link to full notification center

5. **Breadcrumbs**
    - Current location hierarchy
    - Quick navigation back

### Navigation Badges & Indicators

- **Red badge**: Urgent/overdue items
- **Orange badge**: Pending approvals
- **Blue badge**: Unread notifications
- **Green dot**: Active/in-progress

### Collapsible Sections

- Each major section can collapse/expand
- User preference persisted
- Mobile: Hamburger menu

---

## MOBILE NAVIGATION PRIORITY

(Sidebar collapses to hamburger menu on mobile)

### Priority Order:

1. Dashboard
2. My Programs
3. Create Payment Request
4. Record Harvest
5. Pending Settlements
6. Notifications
7. More... (access to other sections)

---

## CONTEXTUAL ACTIONS (Right-Click / Action Menu)

### Program List Context Menu:

- View Program Details
- Edit Program (if permitted)
- Create Activity
- Create Payment Request
- Record Revenue
- View P&L
- Archive Program (if completed)

### Payment Request Context Menu:

- View Details
- Edit (if draft)
- Approve/Reject (if pending)
- Download PDF
- View Settlement Status
- Cancel Request

---

## NAVIGATION STATES

### Active State

- Highlight current page in sidebar
- Expand parent section
- Breadcrumb shows full path

### Pending Items

- Badge on "Pending Approvals"
- Badge on "Overdue Settlements"
- Dashboard widget summary

### Filters Preserved

- Site selection persists
- Date range filters remembered
- Status filters saved per user

---

**Total Navigation Items:** ~40-50 unique pages/views
**Navigation Depth:** Maximum 3 levels (Section > Subsection > Page)
**Role-Based Views:** 6 distinct navigation experiences
