# Product Requirements Document: Role-Based Dashboards

## Document Purpose

Define dashboard layouts, metrics, widgets, and functionality for each user role to provide relevant, actionable insights based on responsibilities and access levels.

---

# 1. DASHBOARD DESIGN PRINCIPLES

## Global Design Standards

### Layout Structure

- **Header Bar**: Site selector, search, notifications, user menu
- **Quick Stats Row**: 4-6 KPI cards with trend indicators
- **Main Content Area**: Widgets arranged in 2-column responsive grid
- **Action Panel**: Context-aware quick actions (floating or sidebar)

### Visual Hierarchy

- **Priority 1 (Top)**: Critical actions and alerts
- **Priority 2 (Middle)**: Key metrics and operational data
- **Priority 3 (Bottom)**: Secondary information and trends

### Interaction Patterns

- Click KPI card → Drill down to detailed report
- Hover on chart → Show data tooltip
- Click widget header → Expand/collapse
- All data refreshes every 30 seconds (configurable)

### Responsive Behavior

- Desktop: 2-column grid
- Tablet: 1-column with collapsible sections
- Mobile: Stacked priority cards with scroll

---

# 2. RESEARCH OFFICER (RO) DASHBOARD

## Overview

**Purpose**: Enable ROs to manage day-to-day field operations, submit requests, track settlements, and monitor assigned program budgets.

**Data Scope**: Assigned programs only, own transactions

## 2.1 Header Section

### Site Selector

- Display: Primary site (fixed if single-site user)
- Behavior: If cross-site access granted, show dropdown
- Persistent across navigation

### Greeting & Context

```
Good morning, [Name]
Research Officer | [Site Name]
Last login: [Date Time]
```

## 2.2 Quick Stats Row (4 KPI Cards)

### Card 1: My Active Programs

```
┌─────────────────────────┐
│ Active Programs         │
│ 3                       │
│ ↑ 1 from last month     │
└─────────────────────────┘
```

- **Value**: Count of assigned active programs
- **Trend**: Comparison to previous period
- **Click Action**: Navigate to My Programs list

### Card 2: Pending Settlements

```
┌─────────────────────────┐
│ Pending Settlements     │
│ 5                       │
│ ⚠ 2 overdue             │
└─────────────────────────┘
```

- **Value**: Count of own unsettled payment requests
- **Alert**: Red highlight if any overdue (>48hrs)
- **Click Action**: Navigate to Pending Settlements

### Card 3: Available Budget

```
┌─────────────────────────┐
│ Available Budget        │
│ Rp 45,000,000           │
│ 60% remaining           │
└─────────────────────────┘
```

- **Value**: Sum of available budget across assigned programs
- **Percentage**: Total spent vs total budget
- **Click Action**: Show budget breakdown by program

### Card 4: This Month Activity

```
┌─────────────────────────┐
│ This Month              │
│ 8 Requests | 3 Harvests │
│ Rp 12,500,000           │
└─────────────────────────┘
```

- **Value**: Transaction count and total amount
- **Breakdown**: Payment requests + harvest revenue
- **Click Action**: Navigate to transaction history

## 2.3 Main Widgets (Priority Order)

### Widget 1: Settlement Alerts (Priority: Critical)

```
┌────────────────────────────────────────────────┐
│ ⚠ Settlement Deadlines                         │
├────────────────────────────────────────────────┤
│ • PR-2026-001 | Fertilizer Purchase            │
│   Due: Jan 28, 3:00 PM (18 hours)              │
│   [Upload Receipt]                             │
│                                                │
│ • PR-2026-005 | Pesticide                      │
│   OVERDUE by 6 hours                           │
│   [Upload Receipt - URGENT]                    │
└────────────────────────────────────────────────┘
```

- **Content**: List of pending settlements sorted by urgency
- **Display**: Show up to 5, link to view all if more
- **Actions**: Direct upload button per settlement
- **Alerts**: Red for overdue, orange for <12hrs remaining
- **Empty State**: "All settlements up to date ✓"

### Widget 2: My Programs Summary

```
┌────────────────────────────────────────────────┐
│ My Programs                       [View All →] │
├────────────────────────────────────────────────┤
│ Rice Trial 2026 | Active                       │
│ Budget: 50M | Spent: 30M (60%)                 │
│ Revenue: 15M | Net: -15M                       │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━    │
│                                                │
│ Chili Harvest Cycle | Active                   │
│ Budget: 40M | Spent: 38M (95%)                 │
│ Revenue: 42M | Net: +4M   ⚠ Budget Alert       │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━    │
│                                                │
│ Corn Research | Active                         │
│ Budget: 35M | Spent: 12M (34%)                 │
│ Revenue: 5M | Net: -7M                         │
└────────────────────────────────────────────────┘
```

- **Content**: List of assigned programs with financial summary
- **Display**: Show 3 programs, sorted by budget utilization desc
- **Click**: Expand to show activity breakdown
- **Alerts**: Warning icon if budget >90% utilized

### Widget 3: Recent Transactions

```
┌────────────────────────────────────────────────┐
│ Recent Activity                                │
├────────────────────────────────────────────────┤
│ Jan 27 | Payment Request | PR-2026-012        │
│         Fertilizer | Rp 5,500,000             │
│         Status: Pending Approval               │
│                                                │
│ Jan 26 | Harvest Revenue | HRV-2026-045       │
│         Chili 250kg | Rp 5,000,000            │
│         Status: Posted                         │
│                                                │
│ Jan 25 | Settlement | PR-2026-008             │
│         Pesticide | Rp 3,200,000              │
│         Status: Approved                       │
└────────────────────────────────────────────────┘
```

- **Content**: Last 5 transactions (any type)
- **Display**: Date, type, amount, status
- **Click**: Navigate to transaction detail
- **Filter**: Last 7 days by default

### Widget 4: Budget Utilization Chart

```
┌────────────────────────────────────────────────┐
│ Budget Utilization by Program                  │
├────────────────────────────────────────────────┤
│ Rice Trial 2026      ████████░░ 60%           │
│ Chili Harvest        █████████▓ 95%  ⚠        │
│ Corn Research        ███░░░░░░░ 34%           │
└────────────────────────────────────────────────┘
```

- **Content**: Horizontal bar chart per program
- **Colors**: Green (<70%), Yellow (70-90%), Red (>90%)
- **Interactive**: Hover shows exact amounts

### Widget 5: Upcoming Harvest Schedule (if applicable)

```
┌────────────────────────────────────────────────┐
│ Upcoming Harvests                              │
├────────────────────────────────────────────────┤
│ Jan 30 | Chili #4 | Expected 300kg            │
│ Feb 05 | Rice | Expected 800kg                 │
│ Feb 12 | Chili #5 | Expected 280kg            │
└────────────────────────────────────────────────┘
```

- **Content**: Planned harvest dates for assigned programs
- **Display**: Next 3 harvests within 30 days
- **Click**: Quick record harvest when date arrives

## 2.4 Quick Actions Panel

### Always Visible Actions

- **Create Payment Request** (Primary button)
- **Record Harvest Revenue**
- **Upload Settlement**
- **View My Programs**

### Contextual Actions (Based on State)

- If settlements pending: "Complete Settlements (5)"
- If programs >90% budget: "Review Budget Status"

## 2.5 Filters & Settings

### Available Filters

- Time period: Today, This Week, This Month, Custom Range
- Program filter: All My Programs, [Program 1], [Program 2], etc.
- Transaction type: All, Payments, Revenue, Settlements

### User Preferences

- Default landing view (Summary / Transactions)
- Auto-refresh interval (30s / 1m / 5m / off)
- Email digest frequency (Daily / Weekly)

---

# 3. RESEARCH ASSOCIATE (RA) DASHBOARD

## Overview

**Purpose**: Enable RAs to supervise programs, manage activities, track team performance, and monitor program profitability.

**Data Scope**: Assigned programs (as lead or member), site-level visibility

## 3.1 Quick Stats Row (6 KPI Cards)

### Card 1: Programs Under Supervision

```
┌─────────────────────────┐
│ My Programs             │
│ 4 Active | 1 Draft      │
│ Budget: Rp 180M         │
└─────────────────────────┘
```

### Card 2: Program Profitability

```
┌─────────────────────────┐
│ Net Program Income      │
│ Rp 12,500,000          │
│ ↑ +2.5M from last month │
└─────────────────────────┘
```

- **Value**: Sum of (Revenue - Expenses) across assigned programs
- **Trend**: Month-over-month comparison

### Card 3: Team Pending Actions

```
┌─────────────────────────┐
│ Team Pending            │
│ 8 Settlements           │
│ 3 Payment Requests      │
└─────────────────────────┘
```

- **Value**: Count of pending actions from team members on assigned programs

### Card 4: Revenue This Month

```
┌─────────────────────────┐
│ Revenue (MTD)           │
│ Rp 45,000,000          │
│ 15 Harvests | 2 Services│
└─────────────────────────┘
```

### Card 5: Budget Utilization

```
┌─────────────────────────┐
│ Budget Utilization      │
│ 68%                     │
│ Rp 122M / 180M          │
└─────────────────────────┘
```

### Card 6: Activities Status

```
┌─────────────────────────┐
│ Activities              │
│ 12 Active | 3 Completed │
│ 2 Behind Schedule       │
└─────────────────────────┘
```

## 3.2 Main Widgets

### Widget 1: Programs Performance Dashboard

```
┌────────────────────────────────────────────────────────────────┐
│ Program Performance                          [View All →]      │
├────────────────────────────────────────────────────────────────┤
│ Program        Budget    Spent   Revenue   Net      Status     │
│ Rice Trial     50M       30M     15M        -15M    Active     │
│ Chili Cycle    40M       38M     42M        +4M ✓   Active     │
│ Corn Study     35M       12M     5M         -7M     Active     │
│ Testing Svc    25M       18M     28M        +10M ✓  Active     │
│ New Variety    30M       5M      0M         -5M     Draft      │
└────────────────────────────────────────────────────────────────┘
```

- **Content**: Table with financial summary per program
- **Sorting**: By net income (profitability) by default
- **Highlight**: Green for profitable, red for loss-making
- **Click**: Navigate to program detail

### Widget 2: Team Activity & Settlements

```
┌────────────────────────────────────────────────┐
│ Team Members Activity                          │
├────────────────────────────────────────────────┤
│ Budi (RO) | Rice Trial                         │
│ • 2 pending settlements (1 overdue)            │
│ • Last activity: 2 hours ago                   │
│                                                │
│ Siti (RO) | Chili Cycle                        │
│ • All settlements complete ✓                   │
│ • Last activity: 30 minutes ago                │
│                                                │
│ Andi (RO) | Corn Study                         │
│ • 1 pending settlement (due in 8hrs)           │
│ • Last activity: 1 day ago  ⚠                  │
└────────────────────────────────────────────────┘
```

- **Content**: Team members on assigned programs
- **Display**: Settlement status and last activity
- **Alerts**: Flag inactive members (>24hrs) and overdue settlements
- **Action**: Click to view member details or send reminder

### Widget 3: Activity Timeline

```
┌────────────────────────────────────────────────┐
│ Activities Timeline                            │
├────────────────────────────────────────────────┤
│ Jan [████████▓░░░░░░░░░░] Feb                 │
│ Land Prep     [Complete]                       │
│ Planting      [In Progress] 60%                │
│ Maintenance   [Planned]                        │
│ Harvest       [Planned]                        │
│                                                │
│ Budget: 12M | Spent: 7.2M (60%)               │
└────────────────────────────────────────────────┘
```

- **Content**: Gantt-style timeline for selected program
- **Display**: Activity progress bars with budget tracking
- **Interactive**: Click activity to see expense detail

### Widget 4: Revenue Breakdown

```
┌────────────────────────────────────────────────┐
│ Revenue Sources (This Month)                   │
├────────────────────────────────────────────────┤
│ 🌾 Harvest Revenue      Rp 35,000,000 (78%)   │
│    • Chili: 25M                                │
│    • Rice: 10M                                 │
│                                                │
│ 🔬 Testing Services     Rp 10,000,000 (22%)   │
│    • Soil Analysis: 6M                         │
│    • Product Testing: 4M                       │
│                                                │
│ Total Revenue           Rp 45,000,000          │
└────────────────────────────────────────────────┘
```

### Widget 5: Budget Alerts & Recommendations

```
┌────────────────────────────────────────────────┐
│ ⚠ Alerts & Recommendations                     │
├────────────────────────────────────────────────┤
│ 🔴 Chili Cycle: Budget 95% utilized           │
│    Consider requesting budget increase         │
│                                                │
│ 🟡 Corn Study: Low utilization (34%)          │
│    Review activity schedule and accelerate     │
│                                                │
│ 🟢 Testing Services: Profitable (+10M)        │
│    Consider expanding capacity                 │
└────────────────────────────────────────────────┘
```

- **Content**: AI-driven insights and alerts
- **Categories**: Critical (red), Warning (yellow), Success (green)
- **Action**: Click for detailed recommendation

### Widget 6: Pending Approvals (For Testing Services)

```
┌────────────────────────────────────────────────┐
│ Pending Your Approval                          │
├────────────────────────────────────────────────┤
│ Testing Service | TS-2026-008                  │
│ Client: PT Agri Indonesia | Value: Rp 8M      │
│ [Approve] [Reject]                             │
└────────────────────────────────────────────────┘
```

- **Content**: Items awaiting RA approval
- **Display**: Only if RA has approval authority
- **Actions**: Quick approve/reject buttons

## 3.3 Quick Actions Panel

- **Create New Program**
- **Create Payment Request**
- **Record Harvest Revenue**
- **Record Testing Service**
- **Create Activity**
- **Generate Program P&L Report**

---

# 4. MANAGER DASHBOARD

## Overview

**Purpose**: Strategic oversight of all programs, budget management, approval workflows, and cross-site performance monitoring.

**Data Scope**: All programs across all sites, consolidated and comparative views

## 4.1 Quick Stats Row (6 KPI Cards)

### Card 1: Total Active Programs

```
┌─────────────────────────┐
│ Active Programs         │
│ 15 Total                │
│ KLT: 8 | MGL: 7        │
└─────────────────────────┘
```

- **Click**: Toggle site filter (All/KLT/MGL)

### Card 2: Pending Approvals

```
┌─────────────────────────┐
│ Pending Approvals       │
│ 12 Total                │
│ 8 Payments | 4 Programs │
└─────────────────────────┘
```

- **Alert**: Red badge if any >24hrs old
- **Click**: Navigate to approval queue

### Card 3: Organization Budget

```
┌─────────────────────────┐
│ Budget Utilization      │
│ 1.8B / 3B (60%)        │
│ Available: 1.2B         │
└─────────────────────────┘
```

- **Consolidated**: Across all sites
- **Breakdown**: Click for site-level detail

### Card 4: Net Program Income

```
┌─────────────────────────┐
│ Net Income (YTD)        │
│ + Rp 150,000,000       │
│ KLT: +80M | MGL: +70M  │
└─────────────────────────┘
```

- **Value**: Total revenue - expenses across all programs

### Card 5: Settlement Compliance

```
┌─────────────────────────┐
│ Settlement Compliance   │
│ 87%                     │
│ 3 Overdue | 5 Pending  │
└─────────────────────────┘
```

- **Target**: 100% compliance
- **Alert**: Red if <90%

### Card 6: System Health

```
┌─────────────────────────┐
│ System Status           │
│ ✓ All Systems Normal    │
│ 18 Active Users         │
└─────────────────────────┘
```

## 4.2 Main Widgets

### Widget 1: Approval Queue (Priority)

```
┌─────────────────────────────────────────────────────────────┐
│ ⚠ Pending Approvals                          [Batch View →] │
├─────────────────────────────────────────────────────────────┤
│ Type        ID          Amount       Requestor    Age       │
│ 🔴 Payment   PR-001     Rp 8.5M     Budi (RO)   36 hours   │
│             Fertilizer - 3 Programs                         │
│             [Approve] [Reject] [View Details]               │
│                                                             │
│ 🟡 Payment   PR-008     Rp 3.2M     Siti (RO)   12 hours   │
│             Pesticide - Rice Trial                          │
│             [Approve] [Reject] [View Details]               │
│                                                             │
│ 🟢 Program   Rice V2    Rp 50M      Ahmad (RA)  2 hours    │
│             Budget Approval                                 │
│             [Approve] [Reject] [View Details]               │
│                                                             │
│ [Select All] [Batch Approve Selected]                       │
└─────────────────────────────────────────────────────────────┘
```

- **Content**: All pending approvals sorted by age
- **Priority**: Color-coded by urgency
- **Actions**: Individual or batch approval
- **Filters**: Type, Site, Age, Amount

### Widget 2: Site Performance Comparison

```
┌────────────────────────────────────────────────┐
│ Site Performance Comparison                    │
├────────────────────────────────────────────────┤
│                    Klaten        Magelang      │
│ Active Programs    8             7             │
│ Budget Allocated   1.5B          1.5B          │
│ Budget Utilized    900M (60%)    950M (63%)    │
│ Revenue (YTD)      450M          380M          │
│ Expenses (YTD)     370M          310M          │
│ Net Income         +80M          +70M          │
│ Avg Program ROI    +21%          +23%          │
│                                                │
│ [View Detailed Comparison Report →]            │
└────────────────────────────────────────────────┘
```

- **Content**: Side-by-side metrics for both sites
- **Highlight**: Better performing metrics in green
- **Click**: Navigate to detailed comparison

### Widget 3: Programs by Status

```
┌────────────────────────────────────────────────┐
│ Program Status Overview                        │
├────────────────────────────────────────────────┤
│ 📊 Status Distribution                         │
│                                                │
│   Draft      [███░░] 3 programs (15%)         │
│   Active     [████████████░] 15 (75%)         │
│   Completed  [██░] 2 (10%)                    │
│   Archived   0                                 │
│                                                │
│ Action Required:                               │
│ • 3 Draft programs awaiting budget approval    │
│ • 2 Completed programs ready for archive       │
└────────────────────────────────────────────────┘
```

### Widget 4: Budget Utilization by Category

```
┌────────────────────────────────────────────────┐
│ Budget Utilization by COA Category             │
├────────────────────────────────────────────────┤
│ Category         Allocated    Spent    %       │
│ Fertilizer       450M         380M     84% 🟡  │
│ Seeds            280M         150M     54% 🟢  │
│ Labor            520M         390M     75% 🟢  │
│ Equipment        180M         165M     92% 🔴  │
│ Testing Svcs     200M         85M      43% 🟢  │
│                                                │
│ [View Full COA Report →]                       │
└────────────────────────────────────────────────┘
```

### Widget 5: Revenue Trends

```
┌────────────────────────────────────────────────┐
│ Revenue Trends (Last 6 Months)                 │
├────────────────────────────────────────────────┤
│ 100M│     ╱╲                                   │
│  80M│    ╱  ╲      ╱╲                         │
│  60M│   ╱    ╲    ╱  ╲    ╱                   │
│  40M│  ╱      ╲  ╱    ╲  ╱                    │
│  20M│ ╱        ╲╱      ╲╱                     │
│    └─────────────────────────────────          │
│     Aug Sep Oct Nov Dec Jan                    │
│                                                │
│ — Harvest Revenue  — Testing Services          │
│                                                │
│ Insight: Testing services showing consistent   │
│ growth. Harvest cyclical per season.           │
└────────────────────────────────────────────────┘
```

### Widget 6: Team Performance & Activity

```
┌────────────────────────────────────────────────┐
│ Team Performance Summary                       │
├────────────────────────────────────────────────┤
│ Research Associates (5 total)                  │
│ • Ahmad (KLT): 3 programs, 95% budget util ⚠   │
│ • Dewi (MGL): 2 programs, 68% budget util ✓    │
│ • More... [View All →]                         │
│                                                │
│ Research Officers (10 total)                   │
│ • Settlement compliance: 87% (3 overdue)       │
│ • Avg approval time: 4.2 hours ✓               │
│ • Active users today: 8/10                     │
└────────────────────────────────────────────────┘
```

### Widget 7: Critical Alerts

```
┌────────────────────────────────────────────────┐
│ 🚨 Critical Alerts                             │
├────────────────────────────────────────────────┤
│ 🔴 3 Overdue settlements (>48hrs)              │
│    Action: Send reminders to ROs              │
│                                                │
│ 🟡 8 Payment requests pending >24hrs           │
│    Action: Review approval queue               │
│                                                │
│ 🟡 2 Programs exceeding 90% budget             │
│    Programs: Rice Trial, Chili Cycle          │
│    Action: Review budget status                │
└────────────────────────────────────────────────┘
```

### Widget 8: Recent Admin Actions

```
┌────────────────────────────────────────────────┐
│ Recent Admin Actions                           │
├────────────────────────────────────────────────┤
│ Jan 27 10:30 | Budget Unlock                   │
│              Rice Trial | By: Manager          │
│              Reason: Emergency fertilizer      │
│                                                │
│ Jan 26 14:15 | User Created                    │
│              New RO: Andi | Site: Klaten      │
│              By: Manager                       │
└────────────────────────────────────────────────┘
```

- **Content**: Recent admin/override actions
- **Purpose**: Transparency and audit visibility

## 4.3 Quick Actions Panel

- **Approve All Eligible Requests**
- **Create New Program**
- **Assign User to Program**
- **Generate Consolidated Report**
- **Run Year-End Process** (seasonal)
- **Access Admin Panel**

---

# 5. AVP DASHBOARD

## Overview

**Purpose**: Executive-level oversight, strategic decision-making, budget approval authority, and organizational performance monitoring.

**Data Scope**: Full organizational visibility with executive analytics

## 5.1 Quick Stats Row (6 KPI Cards)

### Card 1: Organizational Budget Health

```
┌─────────────────────────┐
│ Budget Position         │
│ 1.85B / 3B (62%)       │
│ ▼ -2% vs target        │
└─────────────────────────┘
```

- **Target**: 65% by end of Jan
- **Trend**: Behind/on track/ahead

### Card 2: Net Organizational Profit

```
┌─────────────────────────┐
│ Net Profit (YTD)        │
│ + Rp 150,000,000       │
│ ↑ +18% YoY             │
└─────────────────────────┘
```

### Card 3: Program Portfolio Health

```
┌─────────────────────────┐
│ Program Portfolio       │
│ 12 Profitable | 3 Loss  │
│ ROI: +12%               │
└─────────────────────────┘
```

### Card 4: Critical Approvals

```
┌─────────────────────────┐
│ Awaiting AVP Approval   │
│ 2 Budget | 1 Completion │
│ Total Value: Rp 120M    │
└─────────────────────────┘
```

### Card 5: Compliance Score

```
┌─────────────────────────┐
│ Compliance Score        │
│ 91%                     │
│ Target: 95%             │
└─────────────────────────┘
```

- **Factors**: Settlement compliance, approval times, audit

### Card 6: Revenue Growth

```
┌─────────────────────────┐
│ Revenue Growth          │
│ ↑ +24% YoY             │
│ Harvest: +18% | Test: +35% │
└─────────────────────────┘
```

## 5.2 Main Widgets

### Widget 1: Executive Summary

```
┌──────────────────────────────────────────────────────────────┐
│ Executive Performance Summary                                │
├──────────────────────────────────────────────────────────────┤
│ Financial Performance (YTD)                                  │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Total Revenue          Rp 830,000,000                        │
│ Total Expenses         Rp 680,000,000                        │
│ Net Profit             Rp 150,000,000  (+22% margin)         │
│                                                              │
│ Program Portfolio                                            │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Active Programs        15  (Budget: 1.85B)                   │
│ Profitable Programs    12  (80% success rate)                │
│ Loss-Making Programs   3   (Require review)                  │
│ Avg Program ROI        +12%                                  │
│                                                              │
│ Operational Efficiency                                       │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Settlement Compliance  87%  (Target: 100%)                   │
│ Avg Approval Time      4.2 hours  (Target: <8hrs)           │
│ Budget Utilization     62%  (On track)                       │
│                                                              │
│ [Download Executive Report PDF →]                            │
└──────────────────────────────────────────────────────────────┘
```

### Widget 2: Strategic Priorities & Actions

```
┌────────────────────────────────────────────────┐
│ 🎯 Strategic Priorities                        │
├────────────────────────────────────────────────┤
│ 1. Budget Approvals Required (2 pending)       │
│    • Rice Variety 2.0: Rp 50M                  │
│    • Testing Lab Expansion: Rp 70M             │
│    [Review & Approve →]                        │
│                                                │
│ 2. Program Completion Reviews (1 pending)      │
│    • Corn Trial 2025 - Awaiting final approval │
│    [Review Completion →]                       │
│                                                │
│ 3. Loss-Making Programs Review                 │
│    3 programs requiring intervention           │
│    [View Analysis →]                           │
│                                                │
│ 4. Year-End Planning                           │
│    Q4 approaching - begin FY2027 planning      │
│    [Access Planning Tools →]                   │
└────────────────────────────────────────────────┘
```

### Widget 3: Site Performance Scorecard

```
┌────────────────────────────────────────────────┐
│ Site Performance Scorecard                     │
├────────────────────────────────────────────────┤
│ Metric              Klaten    Magelang  Winner │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Revenue             450M      380M      🏆 KLT │
│ Profit Margin       18%       23%       🏆 MGL │
│ Budget Efficiency   60%       63%       🏆 MGL │
│ Settlement Comp.    92%       82%       🏆 KLT │
│ Avg Program ROI     +21%      +23%      🏆 MGL │
│                                                │
│ Overall Score       A-        B+               │
│                                                │
│ Recommendation: Share best practices from      │
│ Magelang's profit optimization with Klaten.    │
└────────────────────────────────────────────────┘
```

### Widget 4: Portfolio Analysis

```
┌────────────────────────────────────────────────┐
│ Program Portfolio Analysis                     │
├────────────────────────────────────────────────┤
│        High Profit                             │
│         ↑                                      │
│   ┌─────┼─────┐                               │
│   │  🟢 │ 🟢  │ Stars (5 programs)            │
│   │ 🟢🟢│     │ High profit, high growth      │
│ ──┼─────┼─────┼──→ Growth                     │
│   │  🟡 │ 🔴  │ Dogs (3 programs)             │
│   │     │ 🔴🔴│ Low profit, low growth        │
│   └─────┼─────┘                               │
│         ↓                                      │
│                                                │
│ 🟢 Stars: Continue investment                  │
│ 🟡 Question: Monitor closely                   │
│ 🔴 Dogs: Review/restructure/terminate          │
│                                                │
│ [View Detailed Portfolio Analysis →]           │
└────────────────────────────────────────────────┘
```

### Widget 5: Financial Trends & Forecasting

```
┌────────────────────────────────────────────────┐
│ Financial Trends & Forecast                    │
├────────────────────────────────────────────────┤
│ Revenue Trend (12 Months)                      │
│ 100M│                           ╱╲ ← Actual    │
│  80M│              ╱╲         ╱                │
│  60M│          ╱╲╱  ╲       ╱                  │
│  40M│        ╱       ╲    ╱                    │
│  20M│      ╱          ╲  ╱                     │
│    └──────────────────────────── ← Forecast    │
│                                                │
│ FY2026 Projection:                             │
│ • Revenue: Rp 1.2B (+15% from FY2025)         │
│ • Expenses: Rp 980M                            │
│ • Net Profit: Rp 220M (+17% margin)           │
│                                                │
│ Confidence Level: 85%                          │
└────────────────────────────────────────────────┘
```

### Widget 6: Compliance & Risk Dashboard

```
┌────────────────────────────────────────────────┐
│ Compliance & Risk Status                       │
├────────────────────────────────────────────────┤
│ Compliance Score: 91/100                       │
│ ━━━━━━━━━━━━━━━━━━━━░░ 91%                    │
│                                                │
│ ✓ Settlement Compliance    87%  ⚠             │
│ ✓ Approval Timeliness      94%  ✓             │
│ ✓ Budget Adherence         96%  ✓             │
│ ✓ Audit Trail Completeness 100% ✓             │
│                                                │
│ Risk Factors:                                  │
│ 🟡 Medium: 3 programs >90% budget              │
│ 🟡 Medium: Settlement compliance <90%          │
│ 🟢 Low: All other risk factors normal          │
│                                                │
│ [View Full Compliance Report →]                │
└────────────────────────────────────────────────┘
```

### Widget 7: Team & Resource Overview

```
┌────────────────────────────────────────────────┐
│ Team & Resources Overview                      │
├────────────────────────────────────────────────┤
│ Personnel                                      │
│ • Research Associates: 5 (Utilization: 85%)    │
│ • Research Officers: 10 (Utilization: 92%)     │
│ • Finance Operations: 3 (Workload: Normal)     │
│                                                │
│ Top Performers (This Quarter)                  │
│ 🏆 Ahmad (RA): 3 profitable programs, +25% ROI │
│ 🏆 Dewi (RA): Highest revenue generation       │
│ 🏆 Budi (RO): 100% settlement compliance       │
│                                                │
│ Resource Allocation                            │
│ • Programs per RA: 2.5 avg (Optimal: 2-3)     │
│ • Programs per RO: 1.5 avg (Optimal: 1-2)     │
│                                                │
│ [View Team Performance Details →]              │
└────────────────────────────────────────────────┘
```

## 5.3 Quick Actions Panel

- **Review Critical Approvals**
- **Generate Executive Report**
- **Strategic Planning Tools**
- **Access Admin Panel**
- **View Audit Logs**
- **Financial Forecast Modeling**

---

# 6. FINANCE OPERATION DASHBOARD

## Overview

**Purpose**: Process payments, review settlements, manage COA, ensure financial accuracy and compliance.

**Data Scope**: All financial transactions across all sites

## 6.1 Quick Stats Row (6 KPI Cards)

### Card 1: Payment Processing Queue

```
┌─────────────────────────┐
│ Payments to Process     │
│ Morning: 8 | Afternoon: 5│
│ Total: Rp 45,000,000   │
└─────────────────────────┘
```

### Card 2: Pending Settlements

```
┌─────────────────────────┐
│ Settlements to Review   │
│ 12 Pending              │
│ 3 Revisions Requested   │
└─────────────────────────┘
```

### Card 3: Today's Processed

```
┌─────────────────────────┐
│ Processed Today         │
│ 15 Payments             │
│ Rp 67,500,000          │
└─────────────────────────┘
```

### Card 4: Settlement Compliance

```
┌─────────────────────────┐
│ Settlement Status       │
│ 87% Compliant           │
│ 3 Overdue | 8 Pending  │
└─────────────────────────┘
```

### Card 5: Revenue Verification

```
┌─────────────────────────┐
│ Revenue Records         │
│ 5 Pending Verification  │
│ 2 Corrections Needed    │
└─────────────────────────┘
```

### Card 6: COA Usage

```
┌─────────────────────────┐
│ COA Accounts            │
│ 45 Active               │
│ 3 Pending Review        │
└─────────────────────────┘
```

## 6.2 Main Widgets

### Widget 1: Payment Processing Queue (Priority)

```
┌──────────────────────────────────────────────────────────┐
│ 💳 Payment Processing Queue                              │
├──────────────────────────────────────────────────────────┤
│ Morning Batch (10:00 AM) - 8 requests                    │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ PR-2026-001 | Fertilizer      | Rp 8.5M  | [Process]   │
│ PR-2026-003 | Seeds           | Rp 3.2M  | [Process]   │
│ PR-2026-005 | Labor Payment   | Rp 5.0M  | [Process]   │
│ ... 5 more                                               │
│ [Process All Morning Batch] Total: Rp 32M               │
│                                                          │
│ Afternoon Batch (2:00 PM) - 5 requests                  │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ PR-2026-007 | Pesticide       | Rp 4.2M  | [Schedule]  │
│ PR-2026-009 | Equipment Rental| Rp 6.8M  | [Schedule]  │
│ ... 3 more                                               │
│ [Schedule Afternoon Batch] Total: Rp 23M                │
└──────────────────────────────────────────────────────────┘
```

- **Organization**: By batch time (morning/afternoon)
- **Actions**: Individual or batch processing
- **Validation**: Auto-check for COA mapping, budget availability

### Widget 2: Settlement Review Queue

```
┌────────────────────────────────────────────────┐
│ 📄 Settlements Pending Review                  │
├────────────────────────────────────────────────┤
│ PR-2026-001 | Fertilizer | Requested: 8.5M    │
│ Actual: 8.3M | Surplus: 200K                   │
│ Receipt: ✓ Uploaded | Farm Admin: ✓ Reviewed  │
│ [Approve] [Request Revision] [View Details]    │
│                                                │
│ PR-2026-005 | Labor | Requested: 5.0M         │
│ Actual: 5.0M | Surplus: 0                      │
│ Receipt: ✓ Uploaded | Farm Admin: Pending     │
│ [Approve] [Request Revision] [View Details]    │
│                                                │
│ PR-2026-008 | Pesticide | Requested: 3.2M     │
│ Actual: 3.5M | OVERBUDGET ⚠                   │
│ Receipt: ✓ Uploaded | Note: Price increase    │
│ [Approve] [Reject] [Contact Requester]         │
│                                                │
│ Revisions Requested (3):                       │
│ • PR-2026-002: Receipt unclear                 │
│ • PR-2026-004: Missing vendor invoice          │
│ [View Revision Queue →]                        │
└────────────────────────────────────────────────┘
```

### Widget 3: Revenue Verification

```
┌────────────────────────────────────────────────┐
│ 🌾 Revenue Records to Verify                   │
├────────────────────────────────────────────────┤
│ HRV-2026-045 | Chili 250kg | Rp 5M            │
│ Buyer: PT Sayur Segar | Payment: ✓ Bank       │
│ COA: KLT-4-1100 | Status: Pending Verification│
│ [Approve] [Request Correction]                 │
│                                                │
│ TS-2026-008 | Soil Testing | Rp 8M            │
│ Client: PT Agri | Payment: Pending             │
│ COA: MGL-4-2100 | Status: Awaiting Payment    │
│ [Mark Paid] [View Contract]                    │
└────────────────────────────────────────────────┘
```

### Widget 4: Batch Processing Summary

```
┌────────────────────────────────────────────────┐
│ Batch Processing Summary (Today)               │
├────────────────────────────────────────────────┤
│ Morning Batch (10:00 AM)                       │
│ • Processed: 12 requests                       │
│ • Total Amount: Rp 48,500,000                 │
│ • Bank Transfer Ref: TRF-20260127-001         │
│ • Status: Complete ✓                          │
│                                                │
│ Afternoon Batch (2:00 PM)                      │
│ • Scheduled: 8 requests                        │
│ • Total Amount: Rp 32,000,000                 │
│ • Status: Awaiting batch time                  │
│                                                │
│ [Download Batch Report] [Bank Reconciliation]  │
└────────────────────────────────────────────────┘
```

### Widget 5: COA Management

```
┌────────────────────────────────────────────────┐
│ Chart of Accounts Management                   │
├────────────────────────────────────────────────┤
│ Account Usage Summary                          │
│ • Total Active Accounts: 45                    │
│ • Klaten COA: 23 accounts                      │
│ • Magelang COA: 22 accounts                    │
│                                                │
│ Recent Activity                                │
│ • KLT-5-2500: New account created              │
│   "Testing Equipment" | Type: Expense          │
│ • MGL-4-1150: Modified                         │
│   Updated description                          │
│                                                │
│ Accounts Needing Review                        │
│ • 3 accounts with zero transactions (90 days)  │
│ • 2 accounts with unusual volume               │
│                                                │
│ [Manage COA] [Create Account] [Usage Report]   │
└────────────────────────────────────────────────┘
```

### Widget 6: Compliance Monitoring

```
┌────────────────────────────────────────────────┐
│ Financial Compliance Monitor                   │
├────────────────────────────────────────────────┤
│ Settlement Compliance: 87%                     │
│ ━━━━━━━━━━━━━━━━━━░░░ Target: 100%            │
│                                                │
│ Overdue Settlements (3):                       │
│ • PR-2026-001: 8 hours overdue                 │
│ • PR-2026-003: 15 hours overdue                │
│ • PR-2026-005: 24 hours overdue ⚠             │
│ [Send Reminders] [Escalate to Manager]         │
│                                                │
│ Reconciliation Status                          │
│ • Last reconciliation: Jan 26, 5:00 PM ✓      │
│ • Discrepancies: 0                            │
│ • Next scheduled: Jan 27, 5:00 PM             │
└────────────────────────────────────────────────┘
```

### Widget 7: Cross-Site Transaction Summary

```
┌────────────────────────────────────────────────┐
│ Cross-Site Financial Summary                   │
├────────────────────────────────────────────────┤
│                    Klaten      Magelang        │
│ Payments Today     8           7               │
│ Amount             28M         25M             │
│ Settlements Due    5           6               │
│ Revenue (Today)    12M         8M              │
│ Pending Actions    3           5               │
│                                                │
│ [View Site Details] [Generate Report]          │
└────────────────────────────────────────────────┘
```

## 6.3 Quick Actions Panel

- **Process Morning Batch**
- **Process Afternoon Batch**
- **Review Pending Settlements**
- **Verify Revenue Records**
- **Manage COA**
- **Bank Reconciliation**
- **Generate Financial Reports**

---

# 7. FARM ADMIN DASHBOARD

## Overview

**Purpose**: Document verification, preliminary settlement review, cross-site coordination between field and finance.

**Data Scope**: All transactions at both sites (Klaten and Magelang)

## 7.1 Quick Stats Row (4 KPI Cards)

### Card 1: Documents to Review

```
┌─────────────────────────┐
│ Pending Review          │
│ 8 Settlements           │
│ KLT: 5 | MGL: 3        │
└─────────────────────────┘
```

### Card 2: Receipts Uploaded Today

```
┌─────────────────────────┐
│ Uploaded Today          │
│ 12 Documents            │
│ All sites               │
└─────────────────────────┘
```

### Card 3: Issues Flagged

```
┌─────────────────────────┐
│ Issues Flagged          │
│ 3 Total                 │
│ 2 KLT | 1 MGL          │
└─────────────────────────┘
```

### Card 4: Compliance Status

```
┌─────────────────────────┐
│ Compliance Today        │
│ 92%                     │
│ On Track ✓              │
└─────────────────────────┘
```

## 7.2 Main Widgets

### Widget 1: Settlement Documentation Queue

```
┌────────────────────────────────────────────────────────┐
│ 📋 Settlement Documentation Review                     │
├────────────────────────────────────────────────────────┤
│ Site Filter: [All Sites ▼] [Klaten] [Magelang]       │
│                                                        │
│ Klaten Site (5 pending)                               │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ PR-2026-001 | Fertilizer | Rp 8.3M                    │
│ Receipt: ✓ Clear | Vendor Invoice: ✓                  │
│ Notes: All documentation complete                      │
│ [Approve for Finance] [Flag Issue]                     │
│                                                        │
│ PR-2026-003 | Seeds | Rp 3.2M                         │
│ Receipt: ⚠ Unclear | Vendor Invoice: ✓                │
│ Notes: Receipt photo quality poor                      │
│ [Request Better Photo] [Call RO]                       │
│                                                        │
│ Magelang Site (3 pending)                             │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ PR-2026-005 | Labor | Rp 5.0M                         │
│ Receipt: ✓ Clear | Attendance: ✓                      │
│ [Approve for Finance]                                  │
│ ... 2 more                                             │
└────────────────────────────────────────────────────────┘
```

### Widget 2: Document Quality Checklist

```
┌────────────────────────────────────────────────┐
│ Documentation Quality Checklist                │
├────────────────────────────────────────────────┤
│ For Each Settlement, Verify:                   │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ ✓ Receipt uploaded and legible                 │
│ ✓ Receipt shows: Date, Vendor, Amount, Items   │
│ ✓ Amount matches request (or surplus explained)│
│ ✓ Vendor invoice (if applicable)               │
│ ✓ Additional docs (contracts, agreements)      │
│ ✓ Photo quality sufficient for audit           │
│                                                │
│ Common Issues & Solutions:                     │
│ • Blurry photo → Request re-upload             │
│ • Missing vendor name → Contact RO             │
│ • Amount mismatch → Require explanation        │
│                                                │
│ [View Documentation Guidelines →]              │
└────────────────────────────────────────────────┘
```

### Widget 3: Site Activity Monitor

```
┌────────────────────────────────────────────────┐
│ Multi-Site Activity Monitor                    │
├────────────────────────────────────────────────┤
│ Klaten Station                                 │
│ • Active ROs: 5                                │
│ • Settlements due today: 3                     │
│ • Documentation complete: 4/5 (80%)            │
│ • Last receipt upload: 15 minutes ago          │
│                                                │
│ Magelang Station                               │
│ • Active ROs: 5                                │
│ • Settlements due today: 2                     │
│ • Documentation complete: 2/2 (100%) ✓         │
│ • Last receipt upload: 2 hours ago             │
│                                                │
│ [Contact Klaten Team] [Contact Magelang Team]  │
└────────────────────────────────────────────────┘
```

### Widget 4: Issues & Communications Log

```
┌────────────────────────────────────────────────┐
│ Issues Flagged & Communications                │
├────────────────────────────────────────────────┤
│ Jan 27 11:30 | PR-2026-003 | Klaten           │
│ Issue: Receipt unclear                         │
│ Action: Sent message to Budi (RO)             │
│ Status: Awaiting response                      │
│                                                │
│ Jan 27 10:15 | PR-2026-007 | Magelang         │
│ Issue: Missing vendor invoice                  │
│ Action: Called Siti (RO)                       │
│ Status: Resolved - invoice uploaded            │
│                                                │
│ Jan 26 16:45 | General                         │
│ Note: Reminded all ROs about settlement policy │
│ Via: WhatsApp group                            │
│                                                │
│ [Add New Issue] [Contact RO] [View All →]     │
└────────────────────────────────────────────────┘
```

### Widget 5: Recent Uploads

```
┌────────────────────────────────────────────────┐
│ Recent Document Uploads (Last 24 Hours)        │
├────────────────────────────────────────────────┤
│ Time    | Site | RO   | Request  | Status     │
│ 13:45   | KLT  | Budi | PR-001   | ✓ Complete │
│ 13:20   | MGL  | Siti | PR-005   | ✓ Complete │
│ 12:50   | KLT  | Andi | PR-008   | ⚠ Review   │
│ 11:30   | MGL  | Dewi | PR-012   | ✓ Complete │
│ 10:15   | KLT  | Budi | PR-015   | ✓ Complete │
│ ... 7 more uploads                             │
│                                                │
│ [View All Uploads] [Filter by Site]            │
└────────────────────────────────────────────────┘
```

### Widget 6: Physical Archive Tracking

```
┌────────────────────────────────────────────────┐
│ Physical Receipt Archive Management            │
├────────────────────────────────────────────────┤
│ Klaten Archive                                 │
│ • This Month: 45 receipts filed                │
│ • Archive Status: Up to date ✓                 │
│ • Last archiving: Jan 26                       │
│                                                │
│ Magelang Archive                               │
│ • This Month: 38 receipts filed                │
│ • Archive Status: 2 pending filing             │
│ • Last archiving: Jan 25                       │
│                                                │
│ Monthly Reconciliation Due: Jan 31             │
│ • Digital vs Physical count verification       │
│ [Schedule Reconciliation]                      │
└────────────────────────────────────────────────┘
```

### Widget 7: Coordination with Finance

```
┌────────────────────────────────────────────────┐
│ Finance Operation Coordination                 │
├────────────────────────────────────────────────┤
│ Approved for Finance Review Today:             │
│ • 8 settlements | Total: Rp 42M               │
│ • Klaten: 5 | Magelang: 3                     │
│                                                │
│ Pending Finance Feedback:                      │
│ • 2 settlements awaiting final approval        │
│ • 1 settlement - revision requested by Finance │
│                                                │
│ Next Coordination Call: Jan 27, 3:00 PM        │
│ Agenda: Discuss flagged issues                 │
│                                                │
│ [Message Finance Team] [View Shared Queue]     │
└────────────────────────────────────────────────┘
```

## 7.3 Quick Actions Panel

- **Review Pending Settlement**
- **Flag Documentation Issue**
- **Upload Receipt (on behalf)**
- **Contact RO**
- **Contact Finance Team**
- **Generate Archive Report**

---

# 8. CROSS-CUTTING FEATURES (All Dashboards)

## 8.1 Global Header Components

### Site Selector

```
┌─────────────────────┐
│ 🏢 All Sites    ▼   │
│   • All Sites       │
│   • Klaten         │
│   • Magelang       │
└─────────────────────┘
```

- **Persistent**: Selection maintained across pages
- **Role-based**: RO/RA limited to assigned sites
- **Badge**: Show alert count per site

### Global Search

```
┌──────────────────────────────────┐
│ 🔍 Search programs, payments...  │
└──────────────────────────────────┘
```

- **Search scope**: Programs, Payment Requests, Users, COA
- **Results**: Type-ahead with recent searches
- **Jump to**: Quick navigation to entity

### Notification Bell

```
┌────────┐
│ 🔔 (5) │
└────────┘
```

- **Badge**: Unread count
- **Dropdown**: Last 5 notifications with preview
- **Priority**: Color-coded (red=urgent, yellow=important)
- **Action**: Mark read, view all, clear all

### User Menu

```
┌──────────────────┐
│ 👤 Budi (RO)  ▼  │
│   • Profile      │
│   • Settings     │
│   • Help         │
│   • Logout       │
└──────────────────┘
```

## 8.2 Common Widget Features

### Widget Header Controls

- **Collapse/Expand**: Toggle widget visibility
- **Refresh**: Manual data refresh
- **Settings**: Configure widget (refresh rate, data range)
- **Export**: Export widget data (CSV/PDF)
- **Help**: Context-sensitive help tooltip

### Empty States

- **No Data**: Friendly message + call-to-action
- **No Permissions**: Explain access limitation
- **Loading**: Skeleton screen while fetching

### Error Handling

- **Connection Lost**: Offline indicator with retry
- **Data Error**: Error message with support contact
- **Timeout**: Automatic retry with exponential backoff

## 8.3 Responsive Design Breakpoints

### Desktop (>1200px)

- 2-column widget grid
- Full sidebar visible
- All quick actions shown

### Tablet (768px - 1200px)

- 1-column widget grid
- Collapsible sidebar
- Priority quick actions

### Mobile (<768px)

- Stacked priority cards
- Hamburger menu
- Bottom navigation bar
- 3-4 priority widgets only

## 8.4 Accessibility Features

- **Keyboard Navigation**: Full tab support
- **Screen Reader**: ARIA labels on all interactive elements
- **High Contrast**: Toggle high contrast mode
- **Font Size**: Adjustable (Small/Medium/Large)
- **Focus Indicators**: Clear focus states

## 8.5 Performance Optimization

- **Lazy Loading**: Widgets load progressively
- **Data Caching**: Cache frequent queries (5-minute TTL)
- **Pagination**: Show 10-20 items, load more on demand
- **Debouncing**: Search input debounced 300ms
- **Prefetching**: Preload likely next actions

---

# 9. IMPLEMENTATION GUIDELINES

## 9.1 Technical Architecture

### Frontend Framework

- **Recommended**: React/Vue with component library
- **State Management**: Redux/Vuex for dashboard state
- **Charting**: Recharts/Chart.js for visualizations
- **Real-time**: WebSocket for live updates

### Backend APIs

- **Dashboard API**: GET /api/dashboard/{role}
- **Widget API**: GET /api/widgets/{widget_id}?filters=...
- **Metrics API**: GET /api/metrics/{metric_type}
- **Export API**: POST /api/export/{format}

### Data Refresh Strategy

- **Critical Data**: 30-second polling (settlements, approvals)
- **Metrics**: 5-minute caching (budget, revenue)
- **Historical**: 1-hour caching (trends, reports)
- **Real-time**: WebSocket for urgent notifications

## 9.2 Widget Priority Loading

### Priority 1 (Immediate - <1s)

- Quick stats row
- Critical alerts
- Approval queues

### Priority 2 (Secondary - <3s)

- Main data widgets
- Charts and visualizations
- Recent activity

### Priority 3 (Deferred - <5s)

- Trend analysis
- Historical data
- Secondary metrics

## 9.3 User Preferences Storage

### Saved Preferences

- Dashboard layout (widget positions)
- Default site filter
- Time period defaults
- Collapsed/expanded widgets
- Theme (light/dark)
- Refresh intervals

### Storage: Browser localStorage + User profile in DB

## 9.4 Testing Requirements

### Unit Tests

- Widget data calculations
- Filter logic
- Permission checks

### Integration Tests

- API data fetching
- Real-time updates
- Cross-widget interactions

### User Testing

- Usability testing per role
- Performance testing (50+ users)
- Accessibility compliance

---

**Total Dashboard Variants:** 6 role-specific dashboards  
**Total Widgets:** ~45 unique widgets across all roles  
**Update Frequency:** 30s - 5min depending on data criticality  
**Target Load Time:** <3 seconds for complete dashboard  
**Mobile Support:** Full responsive design with priority content
