# PRODUCT REQUIREMENT DOCUMENT

## LANDING PAGE - SECTION 2: QUICK ACTIONS

**AgriScience Finance Management System**  
**Version:** 1.0  
**Date:** 08 Februari 2026  
**Document:** 2 of 5

---

## TABLE OF CONTENTS

1. [Overview](#1-overview)
2. [Alert Card Structure](#2-alert-card-structure)
3. [Overdue Settlements Alert](#3-overdue-settlements-alert)
4. [Due Soon Settlements Alert](#4-due-soon-settlements-alert)
5. [Pending Approvals Alert](#5-pending-approvals-alert)
6. [Revision Needed Alert](#6-revision-needed-alert)
7. [Empty State](#7-empty-state)
8. [Per-Site Breakdown](#8-per-site-breakdown)
9. [Interaction Behaviors](#9-interaction-behaviors)
10. [Technical Specifications](#10-technical-specifications)
11. [API Endpoints Summary](#11-api-endpoints-summary)
12. [Database Queries](#12-database-queries)

---

## 1. OVERVIEW

### 1.1 Purpose

Quick Actions Section adalah **priority alert system** yang menampilkan urgent tasks requiring immediate user attention. Section ini positioned directly below Header Bar dan berfungsi sebagai:

- Early warning system untuk overdue/approaching deadlines
- Centralized approval queue
- Revision request tracker
- Operational dashboard untuk daily actions

### 1.2 Design Principles

| Principle                  | Implementation                                                                 |
| -------------------------- | ------------------------------------------------------------------------------ |
| **Urgency-First**          | Most critical alerts displayed first (overdue > due soon > pending > revision) |
| **Action-Oriented**        | Every alert has clear CTA button (Submit Now, Review Now, Fix Now)             |
| **Contextual Information** | Show just enough info untuk decision-making tanpa overwhelming                 |
| **Site Awareness**         | Per-site breakdown untuk multi-site operations clarity                         |
| **Visual Hierarchy**       | Color-coding by urgency level (red > yellow > blue > orange)                   |

### 1.3 Layout Specifications

| Property            | Specification                                        | Rationale                                                 |
| ------------------- | ---------------------------------------------------- | --------------------------------------------------------- |
| **Position**        | Immediately below Header Bar, above Program Launcher | Priority visibility for daily operations                  |
| **Container Width** | Max 1200px, centered, padding 0 24px                 | Consistent with header, readable line length              |
| **Section Padding** | 32px top/bottom, 0 left/right                        | Breathing room without excessive spacing                  |
| **Min Height**      | 200px (if alerts exist), 150px (empty state)         | Sufficient space for content, avoid excessive whitespace  |
| **Background**      | #F8F9FA (Very light gray)                            | Subtle distinction from white body, group alerts visually |
| **Border Top**      | 1px solid #E5E5E5                                    | Separate from header                                      |
| **Card Spacing**    | 16px vertical gap between alert cards                | Clear separation, scannable                               |

### 1.4 Display Conditions

**Section Visibility:**

- **Show Section:** If ANY alert type has count > 0 for current user
- **Hide Section:** If ALL alert types return count = 0 (show empty state instead)

**Alert Card Visibility by Role:**

| Role        | Overdue Settlements | Due Soon Settlements | Pending Approvals | Revision Needed |
| ----------- | ------------------- | -------------------- | ----------------- | --------------- |
| **AVP**     | ❌ No               | ❌ No                | ✅ Yes            | ❌ No           |
| **Manager** | ❌ No               | ❌ No                | ✅ Yes            | ❌ No           |
| **RA/KA**   | ✅ Yes              | ✅ Yes               | ✅ Yes (reviewer) | ✅ Yes          |
| **RO/FS**   | ✅ Yes              | ✅ Yes               | ❌ No             | ✅ Yes          |
| **FO**      | ❌ No               | ❌ No                | ❌ No             | ❌ No           |
| **FA**      | ❌ No               | ❌ No                | ❌ No             | ❌ No           |

**Notes:**

- FO/FA tidak melihat Quick Actions karena mereka bekerja dengan dedicated queues di site-specific system
- RA/KA melihat Pending Approvals karena mereka review RO/FS requests

### 1.5 Refresh Strategy

| Trigger               | Frequency       | Method                                                          |
| --------------------- | --------------- | --------------------------------------------------------------- |
| **Page Load**         | Once            | API call on mount                                               |
| **WebSocket Updates** | Real-time       | New approval assigned, settlement deadline passed               |
| **Manual Refresh**    | User-initiated  | Pull-to-refresh on mobile, refresh button                       |
| **Auto Refresh**      | Every 5 minutes | Silent background refresh                                       |
| **After User Action** | Immediate       | After submitting settlement, approving request, fixing revision |

---

## 2. ALERT CARD STRUCTURE

### 2.1 Common Alert Card Components

Every alert card shares common structural elements dengan variations based on alert type.

#### 2.1.1 Card Container

**CSS Specifications:**

```css
.alert-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
    border-left: 4px solid; /* Color varies by type */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    transition:
        box-shadow 0.2s ease,
        transform 0.2s ease;
}

.alert-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.alert-card:last-child {
    margin-bottom: 0;
}
```

#### 2.1.2 Card Header

**Structure:**

```html
<div class="alert-header">
    <div class="header-left">
        <svg class="alert-icon">{icon_svg}</svg>
        <h3 class="alert-title">{title}</h3>
        <span class="count-badge">{count}</span>
    </div>
    <button class="expand-toggle" aria-label="Expand/Collapse">
        <svg class="chevron">...</svg>
    </button>
</div>
```

**Styling:**

```css
.alert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-icon {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
}

.alert-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 8px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    color: white;
}

.expand-toggle {
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    border-radius: 4px;
    transition: background 0.15s ease;
}

.expand-toggle:hover {
    background: #f5f5f5;
}

.expand-toggle .chevron {
    width: 20px;
    height: 20px;
    transition: transform 0.2s ease;
}

.alert-card.collapsed .chevron {
    transform: rotate(-90deg);
}
```

#### 2.1.3 Site Breakdown Row

**Display Condition:** Show if items exist from multiple sites (Klaten AND Magelang)

**Structure:**

```html
<div class="site-breakdown">
    <span class="breakdown-label">Sites:</span>
    <button class="site-filter" data-site="klaten" data-count="2">
        Klaten: <strong>2</strong>
    </button>
    <span class="separator">|</span>
    <button class="site-filter" data-site="magelang" data-count="1">
        Magelang: <strong>1</strong>
    </button>
    <button class="site-filter active" data-site="all">View All</button>
</div>
```

**Styling:**

```css
.site-breakdown {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 13px;
}

.breakdown-label {
    color: #666;
    font-weight: 500;
}

.site-filter {
    background: none;
    border: none;
    color: #2e5c8a;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 3px;
    transition: background 0.15s ease;
}

.site-filter:hover {
    background: #e3f2fd;
    text-decoration: underline;
}

.site-filter.active {
    background: #2e5c8a;
    color: white;
    font-weight: 600;
}

.site-filter strong {
    font-weight: 700;
}

.separator {
    color: #cccccc;
    user-select: none;
}
```

**Interaction:**

- Click site filter → Show only items from that site
- Click "View All" → Reset filter, show all items
- Active filter highlighted dengan blue background

#### 2.1.4 Item List

**Structure:**

```html
<div class="alert-items">
    <!-- Show max 3 items by default -->
    <div class="alert-item">{item_content}</div>
    <div class="alert-item">{item_content}</div>
    <div class="alert-item">{item_content}</div>

    <!-- If >3 items, show expand link -->
    <button class="view-all-items" data-total="8">
        View All 8 Items
        <svg class="arrow-down">...</svg>
    </button>
</div>
```

**Styling:**

```css
.alert-items {
    margin-bottom: 16px;
}

.alert-item {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 8px;
    transition: background 0.15s ease;
}

.alert-item:hover {
    background: #f0f2f5;
}

.alert-item:last-child {
    margin-bottom: 0;
}

.view-all-items {
    width: 100%;
    padding: 10px;
    background: none;
    border: 1px dashed #cccccc;
    border-radius: 6px;
    color: #2e5c8a;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.15s ease;
}

.view-all-items:hover {
    background: #f8f9fa;
    border-color: #2e5c8a;
}

.view-all-items .arrow-down {
    width: 16px;
    height: 16px;
    transition: transform 0.2s ease;
}

.alert-card.expanded .view-all-items .arrow-down {
    transform: rotate(180deg);
}
```

**Expand Behavior:**

- Default: Show top 3 items
- Click "View All X Items" → Expand to show all items
- Button text changes to "Collapse" with upward arrow
- Smooth height animation (CSS transition)

#### 2.1.5 Action Button

**Structure:**

```html
<button class="alert-action-button {type}">
    <svg class="icon">{icon_svg}</svg>
    <span>{button_text}</span>
    <svg class="arrow-right">→</svg>
</button>
```

**Styling:**

```css
.alert-action-button {
    width: 100%;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.alert-action-button .icon {
    width: 20px;
    height: 20px;
}

.alert-action-button .arrow-right {
    width: 16px;
    height: 16px;
    transition: transform 0.2s ease;
}

.alert-action-button:hover .arrow-right {
    transform: translateX(4px);
}

/* Type-specific colors */
.alert-action-button.overdue {
    background: #f44336;
}

.alert-action-button.overdue:hover {
    background: #d32f2f;
}

.alert-action-button.due-soon {
    background: #ffc107;
    color: #333;
}

.alert-action-button.due-soon:hover {
    background: #ffb300;
}

.alert-action-button.approval {
    background: #2e5c8a;
}

.alert-action-button.approval:hover {
    background: #234566;
}

.alert-action-button.revision {
    background: #fb923c;
}

.alert-action-button.revision:hover {
    background: #f97316;
}

.alert-action-button:active {
    transform: scale(0.98);
}
```

### 2.2 Alert Type Color Specifications

| Alert Type               | Border Left       | Background (Badge) | Icon Color | Button Color |
| ------------------------ | ----------------- | ------------------ | ---------- | ------------ |
| **Overdue Settlements**  | 4px solid #F44336 | #F44336 (Red)      | #F44336    | #F44336      |
| **Due Soon Settlements** | 4px solid #FFC107 | #FFC107 (Yellow)   | #FFC107    | #FFC107      |
| **Pending Approvals**    | 4px solid #2E5C8A | #2E5C8A (Blue)     | #2E5C8A    | #2E5C8A      |
| **Revision Needed**      | 4px solid #FB923C | #FB923C (Orange)   | #FB923C    | #FB923C      |

---

## 3. OVERDUE SETTLEMENTS ALERT

### 3.1 Purpose & Trigger

**Purpose:** Alert requesters about settlements yang sudah melewati deadline.

**Display Condition:** Show if user has ANY settlement dengan status "Pending Settlement" AND deadline < current time

**Target Users:** RO, FS, RA, KA (roles yang dapat create payment requests)

### 3.2 Data Source

#### 3.2.1 SQL Query

```sql
SELECT
  s.id AS settlement_id,
  pr.id AS pr_id,
  pr.pr_code,
  pr.type AS payment_type,
  pr.total_amount,
  site.id AS site_id,
  site.name AS site_name,
  site.site_code,
  s.deadline,
  s.created_at AS settlement_created_at,
  TIMESTAMPDIFF(HOUR, s.deadline, NOW()) AS overdue_hours,
  TIMESTAMPDIFF(DAY, s.deadline, NOW()) AS overdue_days
FROM settlements s
JOIN payment_requests pr ON s.pr_id = pr.id
JOIN sites site ON pr.site_id = site.id
WHERE
  pr.requester_id = :current_user_id
  AND s.status = 'Pending Settlement'
  AND s.deadline < NOW()
  AND s.deleted_at IS NULL
  AND pr.deleted_at IS NULL
ORDER BY
  s.deadline ASC  -- Oldest overdue first
LIMIT 50;  -- Reasonable limit untuk UI performance
```

**Query Explanation:**

- Filter by current user (only show their own overdue settlements)
- Status must be "Pending Settlement" (not submitted yet)
- Deadline in the past (< NOW())
- Soft delete check (deleted_at IS NULL)
- Sort by oldest overdue first (most urgent)
- Limit 50 items (prevent performance issues jika user has many overdue)

#### 3.2.2 API Endpoint

**Endpoint:** `GET /api/v1/quick-actions/overdue-settlements`

**Request:**

```http
GET /api/v1/quick-actions/overdue-settlements
Headers:
  Authorization: Bearer {jwt_token}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "total_count": 3,
        "site_breakdown": {
            "klaten": 2,
            "magelang": 1
        },
        "items": [
            {
                "settlement_id": "uuid-1",
                "pr_id": "uuid-pr-1",
                "pr_code": "PR-2026-02-00123",
                "payment_type": "Cash Advance",
                "total_amount": 3500000,
                "site": {
                    "id": "uuid-site-klaten",
                    "name": "Klaten",
                    "code": "KLT"
                },
                "deadline": "2026-02-06T17:00:00Z",
                "overdue_hours": 42,
                "overdue_days": 1,
                "settlement_url": "/settlements/uuid-1/submit"
            },
            {
                "settlement_id": "uuid-2",
                "pr_id": "uuid-pr-2",
                "pr_code": "PR-2026-02-00118",
                "payment_type": "Reimbursement",
                "total_amount": 1200000,
                "site": {
                    "id": "uuid-site-klaten",
                    "name": "Klaten",
                    "code": "KLT"
                },
                "deadline": "2026-02-05T12:00:00Z",
                "overdue_hours": 70,
                "overdue_days": 2,
                "settlement_url": "/settlements/uuid-2/submit"
            },
            {
                "settlement_id": "uuid-3",
                "pr_id": "uuid-pr-3",
                "pr_code": "PR-2026-02-00115",
                "payment_type": "Mixed",
                "total_amount": 5000000,
                "site": {
                    "id": "uuid-site-magelang",
                    "name": "Magelang",
                    "code": "MLG"
                },
                "deadline": "2026-02-04T09:00:00Z",
                "overdue_hours": 104,
                "overdue_days": 4,
                "settlement_url": "/settlements/uuid-3/submit"
            }
        ]
    }
}
```

### 3.3 Alert Card Specifications

#### 3.3.1 Card Header

| Element         | Value                                              |
| --------------- | -------------------------------------------------- |
| **Icon**        | Alert Circle (SVG), Color: #F44336                 |
| **Title**       | "Overdue Settlements"                              |
| **Count Badge** | Background: #F44336, Text: White, Show total count |
| **Border Left** | 4px solid #F44336                                  |

#### 3.3.2 Item Display Format

**Single Item Structure:**

```html
<div class="alert-item overdue-settlement" data-id="{settlement_id}">
    <div class="item-header">
        <span class="pr-code">{pr_code}</span>
        <span class="site-badge {site_code}">{site_code}</span>
        <span class="overdue-indicator">{overdue_text}</span>
    </div>

    <div class="item-details">
        <span class="payment-type">{payment_type}</span>
        <span class="amount">Rp {formatted_amount}</span>
    </div>

    <div class="item-footer">
        <span class="deadline-info">Deadline: {formatted_deadline}</span>
    </div>
</div>
```

**Field Formatting:**

| Field                 | Format Logic                                             | Example              |
| --------------------- | -------------------------------------------------------- | -------------------- |
| **PR Code**           | Direct display                                           | "PR-2026-02-00123"   |
| **Site Badge**        | 3-letter code, uppercase                                 | "KLT" or "MLG"       |
| **Overdue Indicator** | If <24hr: "X hours overdue", If >=24hr: "X days overdue" | "2 days overdue"     |
| **Payment Type**      | Direct display                                           | "Cash Advance"       |
| **Amount**            | Indonesian currency format                               | "Rp 3,500,000"       |
| **Deadline**          | Format: DD MMM YYYY, HH:mm                               | "06 Feb 2026, 17:00" |

**Overdue Text Calculation:**

```javascript
function formatOverdueText(overdueHours, overdueDays) {
    if (overdueDays === 0) {
        return `${overdueHours} hours overdue`;
    } else if (overdueDays === 1) {
        return '1 day overdue';
    } else {
        return `${overdueDays} days overdue`;
    }
}
```

#### 3.3.3 Visual Styling

**Overdue Indicator Styling:**

```css
.overdue-indicator {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    background: #ffebee;
    color: #c62828;
    font-size: 12px;
    font-weight: 600;
    border-radius: 12px;
    border: 1px solid #ef9a9a;
}

/* Pulse animation for critical overdue (>3 days) */
@keyframes pulse-red {
    0%,
    100% {
        background: #ffebee;
    }
    50% {
        background: #ffcdd2;
    }
}

.overdue-indicator.critical {
    animation: pulse-red 2s infinite;
}
```

**Apply Critical Style:**

```javascript
function isOverdueCritical(overdueDays) {
    return overdueDays > 3;
}
```

### 3.4 Action Button

**Button Text:** "Submit Now"

**Button Icon:** Upload icon (SVG)

**Click Behavior:**

1. If only 1 item: Navigate directly to settlement form `/settlements/{settlement_id}/submit`
2. If multiple items: Show modal with list, user selects which settlement to submit
3. After selection: Navigate to selected settlement form

**Modal Structure (Multiple Items):**

```html
<div class="settlement-selection-modal">
    <div class="modal-header">
        <h3>Select Settlement to Submit</h3>
        <button class="close-modal">×</button>
    </div>

    <div class="modal-body">
        <div class="settlement-option" data-id="{settlement_id}">
            <input
                type="radio"
                name="settlement"
                value="{settlement_id}"
                id="settlement-{id}"
            />
            <label for="settlement-{id}">
                <strong>{pr_code}</strong>
                <span class="site-badge">{site}</span>
                <span class="overdue">{overdue_text}</span>
            </label>
        </div>
        <!-- Repeat for each settlement -->
    </div>

    <div class="modal-footer">
        <button class="btn-secondary" onclick="closeModal()">Cancel</button>
        <button class="btn-primary" onclick="submitSelected()">Continue</button>
    </div>
</div>
```

### 3.5 Business Rules

| Rule                   | Implementation                                                                                            |
| ---------------------- | --------------------------------------------------------------------------------------------------------- |
| **Auto-Block Trigger** | If user has 3+ overdue settlements, system auto-blocks user from creating new requests                    |
| **Block Warning**      | If user has 2 overdue settlements, show warning banner: "Warning: 1 more overdue will block your account" |
| **Escalation Email**   | If settlement overdue >7 days, auto-send email to Manager AND user                                        |
| **Priority Sorting**   | Oldest overdue first (most critical)                                                                      |
| **Grace Period**       | Deadline +1 hour grace period before marking as overdue (account for timezone/clock differences)          |

---

## 4. DUE SOON SETTLEMENTS ALERT

### 4.1 Purpose & Trigger

**Purpose:** Proactive reminder untuk settlements yang akan due dalam waktu dekat.

**Display Condition:** Show if user has ANY settlement dengan deadline dalam <24 hours (but not yet overdue)

**Target Users:** RO, FS, RA, KA

### 4.2 Data Source

#### 4.2.1 SQL Query

```sql
SELECT
  s.id AS settlement_id,
  pr.id AS pr_id,
  pr.pr_code,
  pr.type AS payment_type,
  pr.total_amount,
  site.id AS site_id,
  site.name AS site_name,
  site.site_code,
  s.deadline,
  TIMESTAMPDIFF(HOUR, NOW(), s.deadline) AS hours_remaining,
  TIMESTAMPDIFF(MINUTE, NOW(), s.deadline) AS minutes_remaining
FROM settlements s
JOIN payment_requests pr ON s.pr_id = pr.id
JOIN sites site ON pr.site_id = site.id
WHERE
  pr.requester_id = :current_user_id
  AND s.status = 'Pending Settlement'
  AND s.deadline > NOW()  -- Not yet overdue
  AND s.deadline <= DATE_ADD(NOW(), INTERVAL 24 HOUR)  -- Due within 24 hours
  AND s.deleted_at IS NULL
  AND pr.deleted_at IS NULL
ORDER BY
  s.deadline ASC  -- Soonest deadline first
LIMIT 50;
```

#### 4.2.2 API Endpoint

**Endpoint:** `GET /api/v1/quick-actions/due-soon-settlements`

**Response:**

```json
{
    "success": true,
    "data": {
        "total_count": 2,
        "site_breakdown": {
            "klaten": 1,
            "magelang": 1
        },
        "items": [
            {
                "settlement_id": "uuid-4",
                "pr_id": "uuid-pr-4",
                "pr_code": "PR-2026-02-00130",
                "payment_type": "Reimbursement",
                "total_amount": 2100000,
                "site": {
                    "id": "uuid-site-klaten",
                    "name": "Klaten",
                    "code": "KLT"
                },
                "deadline": "2026-02-09T14:00:00Z",
                "hours_remaining": 6,
                "minutes_remaining": 345,
                "settlement_url": "/settlements/uuid-4/submit"
            },
            {
                "settlement_id": "uuid-5",
                "pr_id": "uuid-pr-5",
                "pr_code": "PR-2026-02-00128",
                "payment_type": "Cash Advance",
                "total_amount": 4500000,
                "site": {
                    "id": "uuid-site-magelang",
                    "name": "Magelang",
                    "code": "MLG"
                },
                "deadline": "2026-02-10T09:00:00Z",
                "hours_remaining": 18,
                "minutes_remaining": 1080,
                "settlement_url": "/settlements/uuid-5/submit"
            }
        ]
    }
}
```

### 4.3 Alert Card Specifications

#### 4.3.1 Card Header

| Element         | Value                                                          |
| --------------- | -------------------------------------------------------------- |
| **Icon**        | Clock icon (SVG), Color: #FFC107                               |
| **Title**       | "Due Soon Settlements"                                         |
| **Count Badge** | Background: #FFC107, Text: #333 (dark, not white for contrast) |
| **Border Left** | 4px solid #FFC107                                              |

#### 4.3.2 Item Display Format

**Time Remaining Display Logic:**

| Hours Remaining | Display Format       | Example              |
| --------------- | -------------------- | -------------------- |
| < 1 hour        | "Due in X minutes"   | "Due in 45 minutes"  |
| 1-6 hours       | "Due in X hours"     | "Due in 6 hours"     |
| 6-24 hours      | "Due today at HH:mm" | "Due today at 14:00" |

**JavaScript Formatter:**

```javascript
function formatTimeRemaining(hoursRemaining, minutesRemaining, deadline) {
    if (hoursRemaining < 1) {
        return `Due in ${minutesRemaining} minutes`;
    } else if (hoursRemaining <= 6) {
        return `Due in ${hoursRemaining} hours`;
    } else {
        const deadlineTime = new Date(deadline);
        const timeString = deadlineTime.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        });
        return `Due today at ${timeString}`;
    }
}
```

**Urgency Indicator Color:**

```javascript
function getUrgencyColor(hoursRemaining) {
    if (hoursRemaining < 2) return '#F44336'; // Red (critical)
    if (hoursRemaining < 6) return '#FF9800'; // Orange (urgent)
    return '#FFC107'; // Yellow (warning)
}
```

#### 4.3.3 Visual Styling

**Time Remaining Badge:**

```css
.time-remaining {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
}

.time-remaining.critical {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ef9a9a;
}

.time-remaining.urgent {
    background: #fff3e0;
    color: #e65100;
    border: 1px solid #ffcc80;
}

.time-remaining.warning {
    background: #fff9c4;
    color: #f57f17;
    border: 1px solid #fff59d;
}
```

### 4.4 Action Button

**Button Text:** "Submit Now"

**Button Color:** #FFC107 (Yellow), Text: #333 (Dark for contrast)

**Click Behavior:** Same as Overdue Settlements (navigate to form or show selection modal)

---

## 5. PENDING APPROVALS ALERT

### 5.1 Purpose & Trigger

**Purpose:** Show approval requests assigned to current user.

**Display Condition:** Show if user has ANY pending approval tasks

**Target Users:**

- **Manager/AVP:** Approval requests at their level
- **RA/KA:** Review requests (RO/FS submissions in their sub-division)

### 5.2 Data Source

#### 5.2.1 SQL Query (Approvers: Manager/AVP)

```sql
SELECT
  pr.id AS pr_id,
  pr.pr_code,
  COALESCE(r.rab_code, 'N/A') AS rab_code,
  CASE
    WHEN pr.pr_code IS NOT NULL THEN 'Payment Request'
    WHEN r.id IS NOT NULL THEN 'RAB'
  END AS entity_type,
  pr.type AS payment_type,
  pr.total_amount,
  pr.is_urgent,
  site.id AS site_id,
  site.name AS site_name,
  site.site_code,
  requester.id AS requester_id,
  requester.name AS requester_name,
  requester.role AS requester_role,
  ac.created_at AS assigned_at,
  TIMESTAMPDIFF(HOUR, ac.created_at, NOW()) AS hours_pending
FROM approval_chain ac
LEFT JOIN payment_requests pr ON ac.entity_id = pr.id AND ac.entity_type = 'PaymentRequest'
LEFT JOIN rabs r ON ac.entity_id = r.id AND ac.entity_type = 'RAB'
JOIN sites site ON COALESCE(pr.site_id, r.site_id) = site.id
JOIN users requester ON COALESCE(pr.requester_id, r.created_by) = requester.id
WHERE
  ac.approver_id = :current_user_id
  AND ac.status = 'Pending'
  AND ac.deleted_at IS NULL
ORDER BY
  pr.is_urgent DESC,  -- Urgent first
  ac.created_at ASC   -- Then oldest first
LIMIT 50;
```

#### 5.2.2 SQL Query (Reviewers: RA/KA)

```sql
SELECT
  pr.id AS pr_id,
  pr.pr_code,
  'Payment Request' AS entity_type,
  pr.type AS payment_type,
  pr.total_amount,
  pr.is_urgent,
  site.id AS site_id,
  site.name AS site_name,
  site.site_code,
  requester.id AS requester_id,
  requester.name AS requester_name,
  requester.role AS requester_role,
  pr.created_at AS submitted_at,
  TIMESTAMPDIFF(HOUR, pr.created_at, NOW()) AS hours_pending
FROM payment_requests pr
JOIN rabs r ON pr.rab_id = r.id
JOIN sites site ON pr.site_id = site.id
JOIN users requester ON pr.requester_id = requester.id
WHERE
  pr.status = 'Submitted'
  AND requester.role IN ('RO', 'FS')
  AND r.created_by = :current_user_id  -- RA/KA only sees requests in their RABs
  AND pr.deleted_at IS NULL
ORDER BY
  pr.is_urgent DESC,
  pr.created_at ASC
LIMIT 50;
```

#### 5.2.3 API Endpoint

**Endpoint:** `GET /api/v1/quick-actions/pending-approvals`

**Response:**

```json
{
    "success": true,
    "data": {
        "total_count": 5,
        "urgent_count": 1,
        "site_breakdown": {
            "klaten": 3,
            "magelang": 2
        },
        "items": [
            {
                "entity_id": "uuid-pr-6",
                "entity_type": "Payment Request",
                "code": "PR-2026-02-00135",
                "payment_type": "Cash Advance",
                "total_amount": 7500000,
                "is_urgent": true,
                "site": {
                    "id": "uuid-site-klaten",
                    "name": "Klaten",
                    "code": "KLT"
                },
                "requester": {
                    "id": "uuid-user-1",
                    "name": "Budi Santoso",
                    "role": "RO"
                },
                "submitted_at": "2026-02-08T10:00:00Z",
                "hours_pending": 6,
                "approval_url": "/pr/uuid-pr-6/approval"
            },
            {
                "entity_id": "uuid-rab-2",
                "entity_type": "RAB",
                "code": "RAB-2026-02-00024",
                "total_amount": 25000000,
                "is_urgent": false,
                "site": {
                    "id": "uuid-site-magelang",
                    "name": "Magelang",
                    "code": "MLG"
                },
                "requester": {
                    "id": "uuid-user-2",
                    "name": "Siti Aminah",
                    "role": "RA"
                },
                "submitted_at": "2026-02-07T14:30:00Z",
                "hours_pending": 26,
                "approval_url": "/rab/uuid-rab-2/approval"
            }
        ]
    }
}
```

### 5.3 Alert Card Specifications

#### 5.3.1 Card Header

| Element         | Value                                              |
| --------------- | -------------------------------------------------- |
| **Icon**        | Document Check icon (SVG), Color: #2E5C8A          |
| **Title**       | "Pending Approvals"                                |
| **Count Badge** | Background: #2E5C8A, Text: White, Show total count |
| **Border Left** | 4px solid #2E5C8A                                  |

**Special Badge (If Urgent Exists):**

```html
<span class="count-badge urgent-indicator">
    {total_count} <span class="urgent-count">({urgent_count} Urgent)</span>
</span>
```

#### 5.3.2 Item Display Format

**Item Structure:**

```html
<div class="alert-item pending-approval" data-id="{entity_id}">
    <div class="item-header">
        <span class="entity-code">{code}</span>
        <span class="site-badge {site_code}">{site_code}</span>
        {#if is_urgent}
        <span class="urgent-badge">URGENT</span>
        {/if}
    </div>

    <div class="item-details">
        <div class="requester-info">
            <svg class="user-icon">...</svg>
            <span>{requester_name} ({requester_role})</span>
        </div>
        <div class="amount-info">
            <svg class="money-icon">...</svg>
            <span>Rp {formatted_amount}</span>
        </div>
    </div>

    <div class="item-footer">
        <span class="time-pending">Submitted {relative_time_ago}</span>
        {#if hours_pending > 12}
        <span class="sla-warning">⚠ Approaching SLA</span>
        {/if}
    </div>
</div>
```

**Field Formatting:**

| Field              | Format                                         | Example                                   |
| ------------------ | ---------------------------------------------- | ----------------------------------------- |
| **Entity Code**    | Direct display, bold                           | "PR-2026-02-00135" or "RAB-2026-02-00024" |
| **Urgent Badge**   | Show if is_urgent = true                       | "URGENT" in red                           |
| **Requester Info** | Name + Role                                    | "Budi Santoso (RO)"                       |
| **Amount**         | Currency format                                | "Rp 7,500,000"                            |
| **Time Pending**   | Relative time                                  | "6 hours ago", "1 day ago"                |
| **SLA Warning**    | Show if hours_pending > 12 (Normal SLA = 24hr) | "⚠ Approaching SLA"                       |

#### 5.3.3 Urgent Badge Styling

```css
.urgent-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    background: #f44336;
    color: white;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 3px;
    letter-spacing: 0.5px;
    animation: pulse-urgent 1.5s infinite;
}

@keyframes pulse-urgent {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}
```

#### 5.3.4 SLA Warning

**Display Condition:**

- Normal requests: Show warning if pending >12 hours (SLA = 24hr, 50% threshold)
- Urgent requests: Show warning if pending >2 hours (SLA = 5hr, 40% threshold)

**Styling:**

```css
.sla-warning {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    background: #fff3e0;
    color: #e65100;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    border: 1px solid #ffe0b2;
}
```

### 5.4 Action Button

**Button Text:** "Review Now"

**Button Icon:** Eye icon (SVG)

**Click Behavior:**

1. If only 1 item: Navigate directly to approval page `/pr/{id}/approval` or `/rab/{id}/approval`
2. If multiple items: Show selection modal (similar to settlements)
3. After selection: Navigate to selected approval page

### 5.5 Business Rules

| Rule                    | Implementation                                                                                                |
| ----------------------- | ------------------------------------------------------------------------------------------------------------- |
| **Auto-Escalation**     | If approval pending >24hr (Normal) or >5hr (Urgent), auto-escalate to next level + notify Manager             |
| **Urgent Priority**     | Urgent items always sorted first, regardless of submission time                                               |
| **Self-Approval Block** | System prevents showing requests where requester_id = current_user_id (should never happen, but double-check) |
| **Delegation Handling** | If Manager on delegation, approval auto-routes to AVP (reflected in approval_chain)                           |

---

## 6. REVISION NEEDED ALERT

### 6.1 Purpose & Trigger

**Purpose:** Notify requesters about rejected requests requiring revision.

**Display Condition:** Show if user has ANY request dengan status "Revision Required"

**Target Users:** RO, FS, RA, KA (roles yang dapat submit requests)

### 6.2 Data Source

#### 6.2.1 SQL Query

```sql
SELECT
  pr.id AS pr_id,
  pr.pr_code,
  COALESCE(r.rab_code, 'N/A') AS rab_code,
  CASE
    WHEN pr.pr_code IS NOT NULL THEN 'Payment Request'
    WHEN r.id IS NOT NULL THEN 'RAB'
  END AS entity_type,
  pr.type AS payment_type,
  pr.total_amount,
  site.id AS site_id,
  site.name AS site_name,
  site.site_code,
  ac.approver_id,
  approver.name AS rejected_by_name,
  approver.role AS rejected_by_role,
  ac.notes AS rejection_reason,
  ac.updated_at AS rejected_at,
  TIMESTAMPDIFF(DAY, ac.updated_at, NOW()) AS days_since_rejection
FROM approval_chain ac
LEFT JOIN payment_requests pr ON ac.entity_id = pr.id AND ac.entity_type = 'PaymentRequest'
LEFT JOIN rabs r ON ac.entity_id = r.id AND ac.entity_type = 'RAB'
JOIN sites site ON COALESCE(pr.site_id, r.site_id) = site.id
JOIN users approver ON ac.approver_id = approver.id
WHERE
  COALESCE(pr.requester_id, r.created_by) = :current_user_id
  AND ac.status = 'Revision Required'
  AND ac.deleted_at IS NULL
ORDER BY
  ac.updated_at DESC  -- Most recent rejection first
LIMIT 50;
```

#### 6.2.2 API Endpoint

**Endpoint:** `GET /api/v1/quick-actions/revision-needed`

**Response:**

```json
{
    "success": true,
    "data": {
        "total_count": 2,
        "site_breakdown": {
            "klaten": 1,
            "magelang": 1
        },
        "items": [
            {
                "entity_id": "uuid-pr-7",
                "entity_type": "Payment Request",
                "code": "PR-2026-02-00132",
                "payment_type": "Reimbursement",
                "total_amount": 1800000,
                "site": {
                    "id": "uuid-site-klaten",
                    "name": "Klaten",
                    "code": "KLT"
                },
                "rejected_by": {
                    "id": "uuid-manager",
                    "name": "Dewi Kartika",
                    "role": "Manager"
                },
                "rejection_reason": "Missing supporting documents. Please attach vendor quotation and proof of work.",
                "rejected_at": "2026-02-08T09:15:00Z",
                "days_since_rejection": 0,
                "edit_url": "/pr/uuid-pr-7/edit"
            },
            {
                "entity_id": "uuid-rab-3",
                "entity_type": "RAB",
                "code": "RAB-2026-02-00025",
                "total_amount": 18000000,
                "site": {
                    "id": "uuid-site-magelang",
                    "name": "Magelang",
                    "code": "MLG"
                },
                "rejected_by": {
                    "id": "uuid-avp",
                    "name": "Ahmad Fauzi",
                    "role": "AVP"
                },
                "rejection_reason": "Budget allocation exceeds sub-division quota for Q2. Please reallocate or provide justification.",
                "rejected_at": "2026-02-07T16:30:00Z",
                "days_since_rejection": 1,
                "edit_url": "/rab/uuid-rab-3/edit"
            }
        ]
    }
}
```

### 6.3 Alert Card Specifications

#### 6.3.1 Card Header

| Element         | Value                                  |
| --------------- | -------------------------------------- |
| **Icon**        | Edit/Pencil icon (SVG), Color: #FB923C |
| **Title**       | "Revision Needed"                      |
| **Count Badge** | Background: #FB923C, Text: White       |
| **Border Left** | 4px solid #FB923C                      |

#### 6.3.2 Item Display Format

**Item Structure:**

```html
<div class="alert-item revision-needed" data-id="{entity_id}">
    <div class="item-header">
        <span class="entity-code">{code}</span>
        <span class="site-badge {site_code}">{site_code}</span>
        <span class="entity-type-badge">{entity_type}</span>
    </div>

    <div class="rejection-info">
        <div class="rejected-by">
            <svg class="user-icon">...</svg>
            <span>Rejected by {rejected_by_name}</span>
        </div>
    </div>

    <div class="rejection-reason">
        <svg class="info-icon">...</svg>
        <p>{rejection_reason}</p>
    </div>

    <div class="item-footer">
        <span class="time-info">Requested {relative_time_ago}</span>
        {#if days_since_rejection > 3}
        <span class="stale-warning">⚠ Needs attention</span>
        {/if}
    </div>
</div>
```

#### 6.3.3 Rejection Reason Display

**Text Truncation:**

- Default: Show first 100 characters
- If longer: Add "..." and "Read More" link
- Click "Read More" → Expand full text inline

**Styling:**

```css
.rejection-reason {
    display: flex;
    gap: 8px;
    padding: 10px;
    background: #fff7ed;
    border-left: 3px solid #fb923c;
    border-radius: 4px;
    margin: 8px 0;
}

.rejection-reason .info-icon {
    width: 18px;
    height: 18px;
    color: #fb923c;
    flex-shrink: 0;
    margin-top: 2px;
}

.rejection-reason p {
    margin: 0;
    font-size: 13px;
    color: #333;
    line-height: 1.5;
}

.rejection-reason.truncated p {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.read-more {
    color: #fb923c;
    font-weight: 500;
    cursor: pointer;
    text-decoration: underline;
}
```

#### 6.3.4 Stale Warning

**Display Condition:** Show if days_since_rejection > 3

**Purpose:** Encourage user to fix revision sooner

**Styling:**

```css
.stale-warning {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    background: #fef3c7;
    color: #92400e;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    border: 1px solid #fde68a;
}
```

### 6.4 Action Button

**Button Text:** "Fix Now"

**Button Icon:** Wrench/Tool icon (SVG)

**Click Behavior:**

1. Navigate to edit page `/pr/{id}/edit` or `/rab/{id}/edit`
2. Edit page pre-filled dengan existing data
3. Show rejection reason prominently at top of edit form
4. After resubmit: Remove from Revision Needed list, add to Pending Approvals (for approver)

### 6.5 Business Rules

| Rule                 | Implementation                                                                            |
| -------------------- | ----------------------------------------------------------------------------------------- |
| **Auto-Reminder**    | If revision not addressed after 7 days, send reminder email to requester                  |
| **Expiry**           | If revision not addressed after 30 days, auto-archive request (status = Expired)          |
| **Re-approval Flow** | After revision resubmitted, goes back to same approver who rejected it (for verification) |
| **Revision History** | System tracks revision count (v1, v2, v3...) for audit trail                              |

---

## 7. EMPTY STATE

### 7.1 Display Condition

Show empty state when **ALL** alert types return count = 0 for current user:

- Overdue Settlements: 0
- Due Soon Settlements: 0
- Pending Approvals: 0
- Revision Needed: 0

### 7.2 Visual Design

**Structure:**

```html
<div class="quick-actions-empty-state">
    <svg class="checkmark-circle" width="80" height="80">
        <!-- Animated checkmark -->
    </svg>

    <h3 class="title">All caught up!</h3>
    <p class="message">No urgent actions required at this time.</p>
    <p class="subtext">Great job staying on top of things!</p>
</div>
```

**Styling:**

```css
.quick-actions-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    padding: 40px 20px;
    background: linear-gradient(135deg, #e8f5e9 0%, #f0fdf4 100%);
    border-radius: 12px;
    text-align: center;
}

.checkmark-circle {
    margin-bottom: 20px;
    /* Animated entrance */
    animation: checkmark-appear 0.6s ease-out;
}

@keyframes checkmark-appear {
    0% {
        opacity: 0;
        transform: scale(0.5);
    }
    60% {
        transform: scale(1.1);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.quick-actions-empty-state .title {
    font-size: 24px;
    font-weight: 600;
    color: #2e7d32;
    margin: 0 0 8px 0;
}

.quick-actions-empty-state .message {
    font-size: 16px;
    color: #333;
    margin: 0 0 8px 0;
}

.quick-actions-empty-state .subtext {
    font-size: 14px;
    color: #666;
    margin: 0;
}
```

**Checkmark SVG with Animation:**

```svg
<svg width="80" height="80" viewBox="0 0 80 80">
  <!-- Circle background -->
  <circle cx="40" cy="40" r="38" fill="#4CAF50" opacity="0.1"/>

  <!-- Checkmark -->
  <path
    d="M25 40 L35 50 L55 30"
    stroke="#4CAF50"
    stroke-width="4"
    fill="none"
    stroke-linecap="round"
    stroke-linejoin="round"
    stroke-dasharray="50"
    stroke-dashoffset="50"
    style="animation: checkmark-draw 0.5s ease-out 0.2s forwards;"
  />

  <style>
    @keyframes checkmark-draw {
      to {
        stroke-dashoffset: 0;
      }
    }
  </style>
</svg>
```

### 7.3 Alternative Content (Optional)

**Motivational Messages (Random Display):**

- "All caught up! No urgent actions required."
- "You're on top of everything! Great work!"
- "No pending tasks. Time to plan ahead!"
- "Everything's under control. Well done!"
- "Clean slate! All your tasks are complete."

**Implementation:**

```javascript
const emptyStateMessages = [
    {
        title: 'All caught up!',
        message: 'No urgent actions required at this time.',
        subtext: 'Great job staying on top of things!',
    },
    {
        title: "You're on top of everything!",
        message: 'All tasks completed.',
        subtext: 'Keep up the excellent work!',
    },
    // ... more messages
];

function getRandomEmptyStateMessage() {
    const index = Math.floor(Math.random() * emptyStateMessages.length);
    return emptyStateMessages[index];
}
```

---

## 8. PER-SITE BREAKDOWN

### 8.1 Purpose

For users operating across multiple sites (Klaten AND Magelang), provide clear breakdown of where action items are located.

### 8.2 Display Logic

**Show Breakdown IF:**

- User has access to multiple sites (site_assignments includes >1 site)
- AND alert has items from >1 site

**Hide Breakdown IF:**

- User only has access to 1 site
- OR all alert items from same site

### 8.3 Breakdown Component

**Structure:**

```html
<div class="site-breakdown">
    <span class="label">Sites:</span>
    <button class="site-filter" data-site="klaten" data-count="2">
        Klaten: <strong>2</strong>
    </button>
    <span class="separator">|</span>
    <button class="site-filter" data-site="magelang" data-count="1">
        Magelang: <strong>1</strong>
    </button>
    <button class="site-filter all active" data-site="all">View All</button>
</div>
```

### 8.4 Filter Behavior

**Default State:** "View All" selected, show all items

**Click Site Filter (e.g., "Klaten"):**

1. Hide items from other sites (display: none)
2. Update active state (add "active" class to clicked filter)
3. Update item count display (e.g., "Showing 2 of 3 items")
4. Scroll to first visible item

**JavaScript Implementation:**

```javascript
function filterBySite(site) {
    const items = document.querySelectorAll('.alert-item');

    items.forEach((item) => {
        const itemSite = item.dataset.site;

        if (site === 'all' || itemSite === site) {
            item.style.display = ''; // Show
        } else {
            item.style.display = 'none'; // Hide
        }
    });

    // Update active state
    document.querySelectorAll('.site-filter').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.site === site);
    });

    // Update count display
    const visibleCount = document.querySelectorAll(
        '.alert-item:not([style*="display: none"])',
    ).length;
    const totalCount = items.length;

    if (site !== 'all') {
        updateCountDisplay(`Showing ${visibleCount} of ${totalCount} items`);
    } else {
        updateCountDisplay(`Showing all ${totalCount} items`);
    }
}
```

### 8.5 Persistence

**Filter State:** NOT persisted across page reloads (always reset to "View All")

**Rationale:** Prevent confusion dari stale filters; user always sees complete picture first

---

## 9. INTERACTION BEHAVIORS

### 9.1 Card Expand/Collapse

**Default State:** Expanded (show top 3 items)

**Click "View All X Items":**

1. Expand to show all items (remove max-height restriction)
2. Button text changes to "Collapse"
3. Arrow icon rotates 180deg
4. Smooth CSS transition (height animation)

**Implementation:**

```javascript
function toggleExpand(alertCard) {
    const itemsContainer = alertCard.querySelector('.alert-items');
    const button = alertCard.querySelector('.view-all-items');
    const allItems = alertCard.querySelectorAll('.alert-item');

    if (alertCard.classList.contains('expanded')) {
        // Collapse
        allItems.forEach((item, index) => {
            if (index >= 3) item.style.display = 'none';
        });
        button.textContent = `View All ${allItems.length} Items`;
        alertCard.classList.remove('expanded');
    } else {
        // Expand
        allItems.forEach((item) => (item.style.display = ''));
        button.textContent = 'Collapse';
        alertCard.classList.add('expanded');
    }
}
```

### 9.2 Loading States

**Initial Load:**

```html
<div class="quick-actions-loading">
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
    <div class="skeleton-card"></div>
</div>
```

**Skeleton Animation:**

```css
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.skeleton-card {
    height: 180px;
    margin-bottom: 16px;
    border-radius: 8px;
    background: linear-gradient(90deg, #f5f5f5 0px, #eeeeee 40px, #f5f5f5 80px);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
}
```

### 9.3 Error States

**API Error:**

```html
<div class="quick-actions-error">
    <svg class="error-icon" width="48" height="48">...</svg>
    <h3>Unable to load quick actions</h3>
    <p>Please check your connection and try again.</p>
    <button class="retry-button" onclick="retryLoadQuickActions()">
        <svg class="refresh-icon">...</svg>
        Retry
    </button>
</div>
```

**Retry Logic:**

```javascript
let retryCount = 0;
const MAX_RETRIES = 3;

async function retryLoadQuickActions() {
    if (retryCount >= MAX_RETRIES) {
        showToast('Unable to load. Please refresh page.', 'error');
        return;
    }

    retryCount++;
    showLoadingState();

    try {
        await loadQuickActions();
        retryCount = 0; // Reset on success
    } catch (error) {
        showErrorState();
    }
}
```

### 9.4 Pull-to-Refresh (Mobile)

**Implementation:**

```javascript
let touchStartY = 0;
let pullDistance = 0;

document.addEventListener('touchstart', (e) => {
    if (window.scrollY === 0) {
        touchStartY = e.touches[0].clientY;
    }
});

document.addEventListener('touchmove', (e) => {
    if (touchStartY === 0) return;

    pullDistance = e.touches[0].clientY - touchStartY;

    if (pullDistance > 0 && pullDistance < 100) {
        // Show pull indicator
        showPullIndicator(pullDistance);
    }
});

document.addEventListener('touchend', () => {
    if (pullDistance > 80) {
        // Trigger refresh
        refreshQuickActions();
    }

    // Reset
    hidePullIndicator();
    touchStartY = 0;
    pullDistance = 0;
});
```

---

## 10. TECHNICAL SPECIFICATIONS

### 10.1 Performance Requirements

| Metric                        | Target       | Measurement                             |
| ----------------------------- | ------------ | --------------------------------------- |
| **API Response Time**         | <500ms (p95) | All 4 quick action endpoints combined   |
| **Initial Render**            | <1s          | Time from API response to DOM render    |
| **Expand/Collapse Animation** | 300ms        | Smooth CSS transition                   |
| **Filter Switch**             | <100ms       | Instant site filter response            |
| **Skeleton Load Time**        | <200ms       | Skeleton appears quickly while fetching |

### 10.2 Caching Strategy

**Client-Side Cache:**

- Cache quick action data for 2 minutes
- Invalidate cache on user action (submit settlement, approve, etc.)
- Use sessionStorage (not localStorage - fresh data per session)

**Cache Implementation:**

```javascript
const CACHE_DURATION = 2 * 60 * 1000; // 2 minutes

async function loadQuickActions() {
    const cacheKey = 'quick_actions_data';
    const cached = sessionStorage.getItem(cacheKey);

    if (cached) {
        const { data, timestamp } = JSON.parse(cached);
        const age = Date.now() - timestamp;

        if (age < CACHE_DURATION) {
            renderQuickActions(data);
            return;
        }
    }

    // Fetch fresh data
    const data = await fetchQuickActions();

    sessionStorage.setItem(
        cacheKey,
        JSON.stringify({
            data,
            timestamp: Date.now(),
        }),
    );

    renderQuickActions(data);
}

function invalidateCache() {
    sessionStorage.removeItem('quick_actions_data');
}
```

### 10.3 Accessibility

| Requirement             | Implementation                                                    |
| ----------------------- | ----------------------------------------------------------------- |
| **Keyboard Navigation** | All buttons/links accessible via Tab, Enter/Space to activate     |
| **ARIA Labels**         | Alert cards have role="region" aria-labelledby="{alert_title_id}" |
| **Screen Reader**       | Announce count badges: "3 overdue settlements"                    |
| **Focus Management**    | Focus indicator visible, logical tab order                        |
| **Color Contrast**      | All text meets WCAG AA (4.5:1 minimum)                            |

### 10.4 Responsive Breakpoints

**Desktop (>1024px):**

- Alert cards: Full width
- Show 3 items by default
- Site breakdown inline

**Tablet (768-1024px):**

- Alert cards: Full width
- Show 3 items by default
- Site breakdown inline

**Mobile (<768px):**

- Alert cards: Full width, increased padding
- Show 2 items by default (conserve space)
- Site breakdown stacked vertically
- Larger touch targets (min 44x44px)

---

## 11. API ENDPOINTS SUMMARY

| Endpoint                                     | Method | Purpose                                 | Response Time Target |
| -------------------------------------------- | ------ | --------------------------------------- | -------------------- |
| `/api/v1/quick-actions/overdue-settlements`  | GET    | Overdue settlements list                | <500ms               |
| `/api/v1/quick-actions/due-soon-settlements` | GET    | Due soon settlements list               | <500ms               |
| `/api/v1/quick-actions/pending-approvals`    | GET    | Pending approvals list                  | <500ms               |
| `/api/v1/quick-actions/revision-needed`      | GET    | Revision required items list            | <500ms               |
| `/api/v1/quick-actions/summary`              | GET    | All quick actions aggregated (optional) | <800ms               |

**Parallel Fetching:**

```javascript
async function fetchAllQuickActions() {
    const [overdue, dueSoon, approvals, revisions] = await Promise.all([
        fetch('/api/v1/quick-actions/overdue-settlements'),
        fetch('/api/v1/quick-actions/due-soon-settlements'),
        fetch('/api/v1/quick-actions/pending-approvals'),
        fetch('/api/v1/quick-actions/revision-needed'),
    ]);

    return {
        overdue: await overdue.json(),
        dueSoon: await dueSoon.json(),
        approvals: await approvals.json(),
        revisions: await revisions.json(),
    };
}
```

---

## 12. DATABASE QUERIES

### 12.1 Query Optimization

**Indexes Required:**

```sql
-- Settlements
CREATE INDEX idx_settlements_requester_status_deadline
ON settlements(requester_id, status, deadline)
WHERE deleted_at IS NULL;

-- Approval Chain
CREATE INDEX idx_approval_chain_approver_status
ON approval_chain(approver_id, status, created_at DESC)
WHERE deleted_at IS NULL;

-- Payment Requests
CREATE INDEX idx_payment_requests_requester_status
ON payment_requests(requester_id, status)
WHERE deleted_at IS NULL;
```

### 12.2 Query Performance Monitoring

**Slow Query Threshold:** 500ms

**Monitoring:**

```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 0.5;

-- Analyze query performance
EXPLAIN ANALYZE
SELECT ...;
```

**Optimization Tips:**

- Use covering indexes (include all SELECT columns)
- Avoid SELECT \* (only select needed columns)
- Use appropriate JOINs (INNER vs LEFT)
- Limit results (50 items max)
- Add WHERE deleted_at IS NULL to all queries

---

## CONCLUSION

This completes **Section 2: Quick Actions** PRD. This document provides comprehensive specifications including:

✅ 4 Alert types with complete breakdown  
✅ Data sources (SQL queries + API endpoints)  
✅ Visual specifications (colors, layouts, animations)  
✅ Business rules & validation logic  
✅ Empty state & error handling  
✅ Per-site breakdown functionality  
✅ Performance requirements & caching  
✅ Accessibility compliance  
✅ Database optimization strategies

**Next Documents:**

- PRD Section 3: Program Launcher
- PRD Section 4: System Settings (largest)
- PRD Section 5: Information Section

---

**Document Control:**

| Version | Date        | Author           | Changes                   |
| ------- | ----------- | ---------------- | ------------------------- |
| 1.0     | 08 Feb 2026 | System Architect | Initial comprehensive PRD |

---

_End of Section 2 PRD_
