# PRODUCT REQUIREMENT DOCUMENT

## LANDING PAGE - SECTION 3: PROGRAM LAUNCHER

**AgriScience Finance Management System**  
**Version:** 1.0  
**Date:** 08 Februari 2026  
**Document:** 3 of 5

---

## TABLE OF CONTENTS

1. [Overview](#1-overview)
2. [Site Card Specifications](#2-site-card-specifications)
3. [Data Fetching & Stats](#3-data-fetching--stats)
4. [Site Selection & Navigation](#4-site-selection--navigation)
5. [Session Management](#5-session-management)
6. [Visual Design & Animations](#6-visual-design--animations)
7. [Interaction Behaviors](#7-interaction-behaviors)
8. [Error Handling](#8-error-handling)
9. [Loading States](#9-loading-states)
10. [Technical Specifications](#10-technical-specifications)
11. [API Endpoints Summary](#11-api-endpoints-summary)
12. [Database Queries](#12-database-queries)

---

## 1. OVERVIEW

### 1.1 Purpose

Program Launcher adalah **site selection gateway** yang berfungsi sebagai entry point ke operational system. User MUST explicitly select site (Klaten atau Magelang) sebelum dapat mengakses budget management, payment requests, dan operational features lainnya.

### 1.2 Design Rationale

**Why Explicit Site Selection:**

| Rationale                   | Impact                                                                         |
| --------------------------- | ------------------------------------------------------------------------------ |
| **Prevent Critical Errors** | User tidak accidentally create RAB/PR di wrong site (budget integrity)         |
| **Operational Awareness**   | User always conscious of which site context they're working in                 |
| **Multi-Site Clarity**      | Clear separation untuk users yang handle both Klaten dan Magelang              |
| **Audit Trail**             | Every action explicitly tied to site selection (compliance)                    |
| **No Assumptions**          | System never assumes atau remembers site selection (fresh choice setiap login) |

**Why NO Persistence:**

- Site selection NOT stored in localStorage/cookies
- NOT remembered across sessions
- After logout → User must re-select site on next login
- Forces intentional decision every time

### 1.3 Layout Specifications

| Property            | Specification                              | Rationale                            |
| ------------------- | ------------------------------------------ | ------------------------------------ |
| **Position**        | Below Quick Actions, above System Settings | Primary action after viewing alerts  |
| **Section Title**   | "Select Your Station"                      | Clear instruction, friendly tone     |
| **Container Width** | Max 1000px, centered                       | Optimal for 2-card layout            |
| **Section Padding** | 48px top/bottom, 24px left/right           | Ample breathing room                 |
| **Background**      | #FFFFFF (White)                            | Clean, focus on cards                |
| **Border Top**      | 1px solid #E5E5E5                          | Visual separation from Quick Actions |

### 1.4 Card Layout

**Desktop (>1024px):**

```
[  Klaten Card  ]  [ Magelang Card ]
    45% width          45% width
         ← 5% gap →
```

**Tablet (768-1024px):**

```
[  Klaten Card  ]  [ Magelang Card ]
    48% width          48% width
         ← 4% gap →
```

**Mobile (<768px):**

```
[  Klaten Card   ]
   100% width

[  Magelang Card ]
   100% width

   ↕ 24px gap
```

### 1.5 Display Conditions

**Always Show:** Program Launcher displayed untuk ALL users pada Landing Page (tidak ada role restrictions)

**Card Visibility:**

- If user assigned to only 1 site → Show only that site card (centered, larger)
- If user assigned to both sites → Show both cards (side by side)

**Site Assignment Check:**

```sql
SELECT site_id
FROM user_site_assignments
WHERE user_id = :current_user_id
AND deleted_at IS NULL;
```

---

## 2. SITE CARD SPECIFICATIONS

### 2.1 Card Container

#### 2.1.1 Visual Properties

| Property                 | Value                                      | CSS                          |
| ------------------------ | ------------------------------------------ | ---------------------------- |
| **Width**                | 45% (desktop), 48% (tablet), 100% (mobile) | `width: 45%` with flex-basis |
| **Height**               | 300px fixed                                | `height: 300px`              |
| **Background**           | #FFFFFF                                    | `background: #fff`           |
| **Border**               | 2px solid #E5E5E5                          | `border: 2px solid #E5E5E5`  |
| **Border Radius**        | 12px                                       | `border-radius: 12px`        |
| **Padding**              | 32px                                       | `padding: 32px`              |
| **Box Shadow (Default)** | 0 2px 8px rgba(0,0,0,0.08)                 | Subtle depth                 |
| **Box Shadow (Hover)**   | 0 8px 24px rgba(0,0,0,0.12)                | Elevated on hover            |
| **Cursor**               | pointer                                    | Entire card clickable        |
| **Transition**           | all 300ms ease                             | Smooth hover effect          |

**CSS Implementation:**

```css
.site-card {
    width: 45%;
    height: 300px;
    background: white;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

.site-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
    border-color: #2e5c8a;
}

.site-card:active {
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1024px) {
    .site-card {
        width: 48%;
    }
}

@media (max-width: 768px) {
    .site-card {
        width: 100%;
        margin-bottom: 24px;
    }

    .site-card:last-child {
        margin-bottom: 0;
    }
}
```

#### 2.1.2 Hover Effects

**Background Gradient on Hover:**

```css
.site-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(46, 92, 138, 0.02) 0%,
        rgba(46, 92, 138, 0.05) 100%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.site-card:hover::before {
    opacity: 1;
}
```

### 2.2 Card Content Structure

#### 2.2.1 Site Icon

**Specifications:**

| Property              | Value                          | Implementation              |
| --------------------- | ------------------------------ | --------------------------- |
| **Size**              | 64x64px                        | SVG viewBox="0 0 64 64"     |
| **Background Circle** | 80px diameter, #E3F2FD         | Container for icon          |
| **Icon Color**        | #2E5C8A                        | Primary brand color         |
| **Icon Type**         | Factory/Building icon          | Represents research station |
| **Position**          | Top center, margin-bottom 20px | Visual hierarchy            |

**HTML Structure:**

```html
<div class="site-icon-container">
    <svg class="site-icon" width="64" height="64" viewBox="0 0 64 64">
        <!-- Factory/Building icon path -->
        <path d="M..." fill="#2E5C8A" />
    </svg>
</div>
```

**Styling:**

```css
.site-icon-container {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #e3f2fd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    transition:
        transform 0.3s ease,
        background 0.3s ease;
}

.site-card:hover .site-icon-container {
    transform: scale(1.1);
    background: #bbdefb;
}

.site-icon {
    width: 64px;
    height: 64px;
}
```

**Icon Variations:**

| Site         | Icon Variation          | Rationale                   |
| ------------ | ----------------------- | --------------------------- |
| **Klaten**   | Factory with smokestack | Industrial/production focus |
| **Magelang** | Farm building with silo | Agricultural/research focus |

#### 2.2.2 Site Name

**Specifications:**

| Property           | Value                                  |
| ------------------ | -------------------------------------- |
| **Text**           | "KLATEN" or "MAGELANG" (all uppercase) |
| **Font Size**      | 24pt (32px)                            |
| **Font Weight**    | 700 (Bold)                             |
| **Color**          | #2E5C8A                                |
| **Letter Spacing** | 0.05em (slightly spaced)               |
| **Position**       | Below icon, margin-bottom 24px         |

**HTML:**

```html
<h2 class="site-name">KLATEN</h2>
```

**Styling:**

```css
.site-name {
    font-size: 2rem; /* 32px */
    font-weight: 700;
    color: #2e5c8a;
    letter-spacing: 0.05em;
    margin: 0 0 24px 0;
    text-align: center;
}
```

#### 2.2.3 Quick Stats

**Purpose:** Provide at-a-glance operational overview untuk help user prioritize which site to enter.

**Three Metrics:**

1. **Active Programs** - Count of active RABs for this site
2. **Pending Actions** - Count of urgent tasks for current user at this site
3. **Budget** - Total budget dari all active RABs (abbreviated)

**Structure:**

```html
<div class="site-stats">
    <div class="stat-item">
        <span class="stat-label">Programs</span>
        <span class="stat-value">8 programs</span>
    </div>

    <div class="stat-item">
        <span class="stat-label">Pending</span>
        <span class="stat-value pending-highlight">3 pending</span>
    </div>

    <div class="stat-item">
        <span class="stat-label">Budget</span>
        <span class="stat-value">Rp 700M</span>
    </div>
</div>
```

**Styling:**

```css
.site-stats {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 24px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.stat-value.pending-highlight {
    color: #ffc107; /* Yellow if > 0 */
}

.stat-value.pending-highlight.has-pending {
    color: #ff9800; /* Orange if > 3 */
}

.stat-value.pending-highlight.critical {
    color: #f44336; /* Red if > 5 */
}
```

**Color Logic for Pending Actions:**

```javascript
function getPendingColor(count) {
    if (count === 0) return '#4CAF50'; // Green (no pending)
    if (count <= 3) return '#FFC107'; // Yellow (few pending)
    if (count <= 5) return '#FF9800'; // Orange (moderate)
    return '#F44336'; // Red (many pending)
}
```

#### 2.2.4 Enter Button

**Specifications:**

| Property          | Value                                     |
| ----------------- | ----------------------------------------- |
| **Text**          | "Enter Site →"                            |
| **Width**         | 100% (full width of card)                 |
| **Height**        | 48px                                      |
| **Background**    | #2E5C8A (Primary brand)                   |
| **Text Color**    | White                                     |
| **Font Size**     | 16px                                      |
| **Font Weight**   | 600 (Semi-bold)                           |
| **Border Radius** | 8px                                       |
| **Transition**    | background 0.2s ease, transform 0.2s ease |

**HTML:**

```html
<button class="enter-button" data-site-id="{site_id}">
    Enter Site
    <svg class="arrow-icon" width="20" height="20" viewBox="0 0 20 20">
        <path
            d="M7 4 L13 10 L7 16"
            stroke="white"
            stroke-width="2"
            fill="none"
        />
    </svg>
</button>
```

**Styling:**

```css
.enter-button {
    width: 100%;
    height: 48px;
    background: #2e5c8a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.enter-button:hover {
    background: #234566;
}

.enter-button:active {
    transform: scale(0.98);
}

.enter-button .arrow-icon {
    transition: transform 0.2s ease;
}

.enter-button:hover .arrow-icon {
    transform: translateX(4px);
}
```

**Note:** Entire card is clickable (not just button), button is visual CTA only

---

## 3. DATA FETCHING & STATS

### 3.1 Active Programs Metric

#### 3.1.1 Definition

**Active Programs** = Count of RABs dengan status "Active" untuk selected site

#### 3.1.2 SQL Query

```sql
SELECT COUNT(*) as active_programs_count
FROM rabs
WHERE site_id = :site_id
  AND status = 'Active'
  AND deleted_at IS NULL;
```

#### 3.1.3 API Endpoint

**Endpoint:** `GET /api/v1/sites/:site_id/stats/active-programs`

**Request:**

```http
GET /api/v1/sites/uuid-klaten/stats/active-programs
Headers:
  Authorization: Bearer {jwt_token}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "count": 8,
        "formatted": "8 programs"
    }
}
```

#### 3.1.4 Display Format

| Count | Display Format | Example    |
| ----- | -------------- | ---------- |
| 0     | "No programs"  | Grey color |
| 1     | "1 program"    | Singular   |
| 2-999 | "X programs"   | Plural     |

**Formatter Function:**

```javascript
function formatProgramsCount(count) {
    if (count === 0) return 'No programs';
    if (count === 1) return '1 program';
    return `${count} programs`;
}
```

### 3.2 Pending Actions Metric

#### 3.2.1 Definition

**Pending Actions** = SUM of urgent tasks for current user at this specific site:

- Overdue settlements (requester = current user, site = this site)
- Due soon settlements (same criteria)
- Pending approvals (approver = current user, request site = this site)
- Revision requests (requester = current user, site = this site)

#### 3.2.2 SQL Query

```sql
SELECT
  (
    -- Overdue settlements
    SELECT COUNT(*)
    FROM settlements s
    JOIN payment_requests pr ON s.pr_id = pr.id
    WHERE pr.requester_id = :current_user_id
      AND pr.site_id = :site_id
      AND s.status = 'Pending Settlement'
      AND s.deadline < NOW()
      AND s.deleted_at IS NULL
  ) +
  (
    -- Due soon settlements
    SELECT COUNT(*)
    FROM settlements s
    JOIN payment_requests pr ON s.pr_id = pr.id
    WHERE pr.requester_id = :current_user_id
      AND pr.site_id = :site_id
      AND s.status = 'Pending Settlement'
      AND s.deadline > NOW()
      AND s.deadline <= DATE_ADD(NOW(), INTERVAL 24 HOUR)
      AND s.deleted_at IS NULL
  ) +
  (
    -- Pending approvals
    SELECT COUNT(*)
    FROM approval_chain ac
    LEFT JOIN payment_requests pr ON ac.entity_id = pr.id AND ac.entity_type = 'PaymentRequest'
    LEFT JOIN rabs r ON ac.entity_id = r.id AND ac.entity_type = 'RAB'
    WHERE ac.approver_id = :current_user_id
      AND ac.status = 'Pending'
      AND COALESCE(pr.site_id, r.site_id) = :site_id
      AND ac.deleted_at IS NULL
  ) +
  (
    -- Revision needed
    SELECT COUNT(*)
    FROM approval_chain ac
    LEFT JOIN payment_requests pr ON ac.entity_id = pr.id AND ac.entity_type = 'PaymentRequest'
    LEFT JOIN rabs r ON ac.entity_id = r.id AND ac.entity_type = 'RAB'
    WHERE COALESCE(pr.requester_id, r.created_by) = :current_user_id
      AND ac.status = 'Revision Required'
      AND COALESCE(pr.site_id, r.site_id) = :site_id
      AND ac.deleted_at IS NULL
  ) AS total_pending_actions;
```

#### 3.2.3 API Endpoint

**Endpoint:** `GET /api/v1/sites/:site_id/stats/pending-actions`

**Response:**

```json
{
    "success": true,
    "data": {
        "count": 3,
        "breakdown": {
            "overdue_settlements": 1,
            "due_soon_settlements": 1,
            "pending_approvals": 1,
            "revision_needed": 0
        },
        "formatted": "3 pending"
    }
}
```

#### 3.2.4 Display Format & Color

| Count | Display      | Color            | Urgency Level |
| ----- | ------------ | ---------------- | ------------- |
| 0     | "No pending" | #4CAF50 (Green)  | All clear     |
| 1-3   | "X pending"  | #FFC107 (Yellow) | Low           |
| 4-5   | "X pending"  | #FF9800 (Orange) | Moderate      |
| 6+    | "X pending"  | #F44336 (Red)    | High          |

### 3.3 Budget Metric

#### 3.3.1 Definition

**Budget** = SUM of total_budget dari all active RABs untuk this site

#### 3.3.2 SQL Query

```sql
SELECT SUM(total_budget) as total_budget
FROM rabs
WHERE site_id = :site_id
  AND status = 'Active'
  AND deleted_at IS NULL;
```

#### 3.3.3 API Endpoint

**Endpoint:** `GET /api/v1/sites/:site_id/stats/budget`

**Response:**

```json
{
    "success": true,
    "data": {
        "total": 700000000,
        "formatted": "Rp 700M"
    }
}
```

#### 3.3.4 Display Format (Abbreviated)

**Abbreviation Rules:**

| Amount Range            | Format      | Example           |
| ----------------------- | ----------- | ----------------- |
| 0                       | "Rp 0"      | No budget         |
| 1 - 999,999             | "Rp XXX rb" | Rp 500 rb         |
| 1,000,000 - 999,999,999 | "Rp X.XM"   | Rp 5.5M, Rp 700M  |
| 1,000,000,000+          | "Rp X.XB"   | Rp 1.2B, Rp 15.8B |

**Formatter Function:**

```javascript
function formatBudget(amount) {
    if (amount === 0) return 'Rp 0';

    if (amount < 1000000) {
        // Ribu
        return `Rp ${Math.round(amount / 1000)} rb`;
    } else if (amount < 1000000000) {
        // Juta (Million)
        const millions = amount / 1000000;
        if (millions >= 100) {
            return `Rp ${Math.round(millions)}M`;
        } else {
            return `Rp ${millions.toFixed(1)}M`;
        }
    } else {
        // Miliar (Billion)
        const billions = amount / 1000000000;
        return `Rp ${billions.toFixed(1)}B`;
    }
}

// Examples:
formatBudget(500000); // "Rp 500 rb"
formatBudget(5500000); // "Rp 5.5M"
formatBudget(700000000); // "Rp 700M"
formatBudget(1250000000); // "Rp 1.3B"
```

### 3.4 Combined Stats Fetching

**Option 1: Separate API Calls (Parallel)**

```javascript
async function fetchSiteStats(siteId) {
    const [programs, pending, budget] = await Promise.all([
        fetch(`/api/v1/sites/${siteId}/stats/active-programs`),
        fetch(`/api/v1/sites/${siteId}/stats/pending-actions`),
        fetch(`/api/v1/sites/${siteId}/stats/budget`),
    ]);

    return {
        programs: await programs.json(),
        pending: await pending.json(),
        budget: await budget.json(),
    };
}
```

**Option 2: Combined Endpoint (Recommended)**

**Endpoint:** `GET /api/v1/sites/:site_id/stats/summary`

**Response:**

```json
{
    "success": true,
    "data": {
        "site_id": "uuid-klaten",
        "site_name": "Klaten",
        "site_code": "KLT",
        "active_programs": {
            "count": 8,
            "formatted": "8 programs"
        },
        "pending_actions": {
            "count": 3,
            "urgency": "low",
            "formatted": "3 pending"
        },
        "budget": {
            "total": 700000000,
            "formatted": "Rp 700M"
        },
        "last_updated": "2026-02-09T08:00:00Z"
    }
}
```

---

## 4. SITE SELECTION & NAVIGATION

### 4.1 Click Behavior

**Entire Card Clickable:**

- Not just button, entire card area is clickable
- Improves UX (larger touch target)
- Consistent behavior across card

**JavaScript Event:**

```javascript
document.querySelectorAll('.site-card').forEach((card) => {
    card.addEventListener('click', async (e) => {
        e.preventDefault();

        const siteId = card.dataset.siteId;
        const siteName = card.dataset.siteName;

        await selectSite(siteId, siteName);
    });
});
```

### 4.2 Selection Flow

**Step-by-Step Process:**

1. **User Clicks Card**
    - Add loading state to clicked card
    - Disable other card (prevent double-click)
    - Show loading spinner

2. **Set Site Session**
    - API Call: `POST /api/v1/session/set-site`
    - Payload: `{ site_id: "uuid-klaten" }`
    - Server stores `selected_site_id` in session

3. **Session Response**
    - Success: Proceed to navigation
    - Error: Show error message, re-enable cards

4. **Navigate to Dashboard**
    - Redirect: `window.location.href = '/dashboard?site=KLT'`
    - Or: `router.push('/dashboard', { query: { site: 'KLT' } })`

5. **Dashboard Loads**
    - Dashboard reads `selected_site_id` from session
    - All subsequent pages use this site context
    - Site context persists until logout/session expiry

**Implementation:**

```javascript
async function selectSite(siteId, siteName) {
    const card = document.querySelector(`.site-card[data-site-id="${siteId}"]`);

    // Show loading state
    card.classList.add('loading');
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    card.appendChild(spinner);

    // Disable other cards
    document.querySelectorAll('.site-card').forEach((c) => {
        if (c !== card) c.style.pointerEvents = 'none';
    });

    try {
        // Set site session
        const response = await fetch('/api/v1/session/set-site', {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${getAuthToken()}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ site_id: siteId }),
        });

        if (!response.ok) {
            throw new Error('Failed to set site session');
        }

        // Navigate to dashboard
        window.location.href = `/dashboard?site=${siteName.substring(0, 3).toUpperCase()}`;
    } catch (error) {
        console.error('Site selection error:', error);

        // Remove loading state
        card.classList.remove('loading');
        card.removeChild(spinner);

        // Re-enable cards
        document.querySelectorAll('.site-card').forEach((c) => {
            c.style.pointerEvents = 'auto';
        });

        // Show error message
        showToast('Unable to enter site. Please try again.', 'error');
    }
}
```

### 4.3 Set Site Session API

**Endpoint:** `POST /api/v1/session/set-site`

**Request:**

```http
POST /api/v1/session/set-site
Headers:
  Authorization: Bearer {jwt_token}
  Content-Type: application/json

Body:
{
  "site_id": "uuid-klaten"
}
```

**Server-Side Logic:**

```javascript
// Express.js example
app.post('/api/v1/session/set-site', authenticateToken, async (req, res) => {
    const { site_id } = req.body;
    const user_id = req.user.id;

    // Validate site_id
    const site = await Site.findById(site_id);
    if (!site) {
        return res.status(404).json({
            success: false,
            error: 'Site not found',
        });
    }

    // Check user has access to this site
    const hasAccess = await UserSiteAssignment.exists({
        user_id: user_id,
        site_id: site_id,
        deleted_at: null,
    });

    if (!hasAccess) {
        return res.status(403).json({
            success: false,
            error: 'Access denied to this site',
        });
    }

    // Store in session
    req.session.selected_site_id = site_id;
    req.session.selected_site_name = site.name;
    req.session.selected_site_code = site.site_code;

    // Log action for audit
    await AuditLog.create({
        user_id: user_id,
        action: 'Site Selected',
        entity_type: 'Site',
        entity_id: site_id,
        details: { site_name: site.name },
        ip_address: req.ip,
        user_agent: req.headers['user-agent'],
    });

    res.json({
        success: true,
        data: {
            site_id: site.id,
            site_name: site.name,
            site_code: site.site_code,
        },
    });
});
```

**Response:**

```json
{
    "success": true,
    "data": {
        "site_id": "uuid-klaten",
        "site_name": "Klaten",
        "site_code": "KLT"
    }
}
```

**Error Responses:**

| Status Code | Error          | User Message                                 |
| ----------- | -------------- | -------------------------------------------- |
| 404         | Site not found | "Site not available. Contact administrator." |
| 403         | Access denied  | "You don't have access to this site."        |
| 500         | Server error   | "Unable to enter site. Please try again."    |

---

## 5. SESSION MANAGEMENT

### 5.1 Session Storage Strategy

**Server-Side Session (Redis)**

**Why Server-Side:**

- Security: Sensitive site context not exposed in client
- Sync: Session shared across browser tabs
- Control: Server can invalidate session anytime
- Audit: All session changes logged server-side

**Session Schema:**

```javascript
// Redis key: session:{session_id}
{
  user_id: "uuid-user-123",
  email: "user@agriscience.com",
  role: "RA",
  selected_site_id: "uuid-klaten",      // Set by site selection
  selected_site_name: "Klaten",          // Set by site selection
  selected_site_code: "KLT",             // Set by site selection
  created_at: "2026-02-09T08:00:00Z",
  last_active: "2026-02-09T10:30:00Z",
  expires_at: "2026-02-09T16:00:00Z"    // 8 hours from login
}
```

**Session TTL:** 8 hours from last activity (sliding window)

### 5.2 Site Context Middleware

**Purpose:** Inject selected site context into all API requests

**Express.js Middleware:**

```javascript
function injectSiteContext(req, res, next) {
    // Skip for non-authenticated routes
    if (!req.session || !req.session.user_id) {
        return next();
    }

    // Inject site context if available
    if (req.session.selected_site_id) {
        req.site = {
            id: req.session.selected_site_id,
            name: req.session.selected_site_name,
            code: req.session.selected_site_code,
        };
    }

    next();
}

// Apply to all routes after authentication
app.use(authenticateToken);
app.use(injectSiteContext);
```

**Usage in Route Handlers:**

```javascript
app.get('/api/v1/rabs', async (req, res) => {
    // Site context automatically available
    if (!req.site) {
        return res.status(400).json({
            success: false,
            error: 'No site selected. Please select site from Landing Page.',
        });
    }

    // Use site context in query
    const rabs = await RAB.find({
        site_id: req.site.id,
        deleted_at: null,
    });

    res.json({ success: true, data: rabs });
});
```

### 5.3 Session Invalidation

**Logout:**

```javascript
app.post('/api/v1/auth/logout', async (req, res) => {
    // Clear site selection
    delete req.session.selected_site_id;
    delete req.session.selected_site_name;
    delete req.session.selected_site_code;

    // Destroy entire session
    req.session.destroy((err) => {
        if (err) {
            return res.status(500).json({ success: false });
        }
        res.json({ success: true, message: 'Logged out' });
    });
});
```

**Session Timeout:**

- Automatic: Session expires after 8 hours inactivity
- User sees login page on next request
- Site selection cleared automatically

### 5.4 Site Context Persistence Rules

**CRITICAL: NO Persistence Across Login Sessions**

| Action                              | Site Selection Status               |
| ----------------------------------- | ----------------------------------- |
| **User logs in**                    | No site selected (null)             |
| **User selects Klaten**             | Site = Klaten (stored in session)   |
| **User navigates in system**        | Site = Klaten (persists in session) |
| **User clicks logo → Landing Page** | Site cleared (prompt re-selection)  |
| **User logs out**                   | Site cleared                        |
| **User logs in again**              | No site selected (must re-select)   |
| **Session expires**                 | Site cleared                        |

**Implementation:**

```javascript
// When returning to Landing Page
app.delete('/api/v1/session/site', (req, res) => {
    delete req.session.selected_site_id;
    delete req.session.selected_site_name;
    delete req.session.selected_site_code;

    res.json({ success: true, message: 'Site context cleared' });
});
```

---

## 6. VISUAL DESIGN & ANIMATIONS

### 6.1 Card Hover Animation

**Transform Effect:**

```css
.site-card {
    transition:
        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
        box-shadow 0.3s ease,
        border-color 0.3s ease;
}

.site-card:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    border-color: #2e5c8a;
}
```

**Icon Bounce:**

```css
@keyframes icon-bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

.site-card:hover .site-icon-container {
    animation: icon-bounce 0.6s ease;
}
```

### 6.2 Loading State Animation

**Spinner Overlay:**

```html
<div class="loading-spinner-overlay">
    <div class="spinner"></div>
    <p class="loading-text">Entering {site_name}...</p>
</div>
```

**Styling:**

```css
.site-card.loading {
    pointer-events: none;
    opacity: 0.6;
    position: relative;
}

.loading-spinner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    z-index: 10;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e5e5;
    border-top-color: #2e5c8a;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.loading-text {
    margin-top: 16px;
    font-size: 14px;
    color: #666;
}
```

### 6.3 Success Transition

**Brief Success State Before Navigation:**

```javascript
async function selectSite(siteId, siteName) {
    // ... selection logic ...

    // Show success state briefly
    card.classList.add('success');
    card.innerHTML = `
    <svg class="checkmark" width="64" height="64">
      <circle cx="32" cy="32" r="30" stroke="#4CAF50" stroke-width="4" fill="none"/>
      <path d="M16 32 L28 44 L48 20" stroke="#4CAF50" stroke-width="4" fill="none"/>
    </svg>
    <p>Entering ${siteName}...</p>
  `;

    // Wait 500ms before navigation (user sees success feedback)
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Navigate
    window.location.href = `/dashboard?site=${siteCode}`;
}
```

---

## 7. INTERACTION BEHAVIORS

### 7.1 Keyboard Navigation

**Tab Order:**

1. Tab to Klaten card
2. Tab to Magelang card
3. Tab to next section

**Keyboard Activation:**

- Enter or Space: Activate card (same as click)
- Focus indicator visible (3px blue outline)

**Implementation:**

```javascript
document.querySelectorAll('.site-card').forEach((card) => {
    // Make card keyboard accessible
    card.setAttribute('tabindex', '0');
    card.setAttribute('role', 'button');
    card.setAttribute('aria-label', `Enter ${card.dataset.siteName} site`);

    card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            card.click();
        }
    });
});
```

### 7.2 Touch Interactions (Mobile)

**Active State:**

```css
.site-card:active {
    transform: scale(0.98);
    background: #f8f9fa;
}
```

**Prevent Double Tap Zoom:**

```css
.site-card {
    touch-action: manipulation;
}
```

### 7.3 Disabled State

**Scenario:** If site is under maintenance or user doesn't have access

**Visual:**

```css
.site-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
    filter: grayscale(0.8);
}

.site-card.disabled::after {
    content: 'Unavailable';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(244, 67, 54, 0.9);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}
```

---

## 8. ERROR HANDLING

### 8.1 Stats Loading Errors

**Scenario:** API fails to load site stats

**Fallback Display:**

```javascript
// If stats API fails, show generic message
{
  active_programs: { formatted: '-- programs' },
  pending_actions: { formatted: '-- pending' },
  budget: { formatted: 'Rp --' }
}
```

**Visual Indicator:**

```html
<div class="stat-item error">
    <span class="stat-label">Programs</span>
    <span class="stat-value error-value">
        --
        <svg class="warning-icon" width="16" height="16">...</svg>
    </span>
</div>
```

**Error Tooltip:**

```css
.stat-value.error-value {
    color: #999;
    cursor: help;
}

.stat-value.error-value:hover::after {
    content: 'Unable to load stats';
    position: absolute;
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 4px;
}
```

### 8.2 Site Selection Errors

**Error Types:**

| Error                  | User Message                                 | Action                           |
| ---------------------- | -------------------------------------------- | -------------------------------- |
| **Network Error**      | "Unable to connect. Check your connection."  | Show retry button                |
| **403 Forbidden**      | "You don't have access to this site."        | Disable card, show contact admin |
| **404 Site Not Found** | "Site not available. Contact administrator." | Disable card                     |
| **500 Server Error**   | "Something went wrong. Please try again."    | Show retry button                |

**Error Display:**

```javascript
function showSelectionError(card, message) {
    // Remove loading state
    card.classList.remove('loading');

    // Show error overlay
    const errorOverlay = document.createElement('div');
    errorOverlay.className = 'error-overlay';
    errorOverlay.innerHTML = `
    <svg class="error-icon" width="48" height="48">...</svg>
    <p class="error-message">${message}</p>
    <button class="retry-button" onclick="retrySelection('${card.dataset.siteId}')">
      Try Again
    </button>
  `;

    card.appendChild(errorOverlay);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        card.removeChild(errorOverlay);
        card.style.pointerEvents = 'auto';
    }, 5000);
}
```

### 8.3 Session Expiry Handling

**Scenario:** User tries to select site but session expired

**Detection:**

```javascript
async function selectSite(siteId, siteName) {
    try {
        const response = await fetch('/api/v1/session/set-site', {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${getAuthToken()}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ site_id: siteId }),
        });

        if (response.status === 401) {
            // Session expired
            showSessionExpiredModal();
            return;
        }

        // ... continue selection
    } catch (error) {
        // Handle error
    }
}

function showSessionExpiredModal() {
    showModal({
        title: 'Session Expired',
        message: 'Your session has expired. Please login again.',
        buttons: [
            {
                text: 'Login',
                style: 'primary',
                onClick: () => {
                    window.location.href = '/login';
                },
            },
        ],
        closeOnClickOutside: false,
    });
}
```

---

## 9. LOADING STATES

### 9.1 Initial Page Load

**Skeleton Loaders for Cards:**

```html
<div class="site-cards-container loading">
    <div class="site-card-skeleton">
        <div class="skeleton-icon"></div>
        <div class="skeleton-title"></div>
        <div class="skeleton-stats">
            <div class="skeleton-stat"></div>
            <div class="skeleton-stat"></div>
            <div class="skeleton-stat"></div>
        </div>
        <div class="skeleton-button"></div>
    </div>
    <!-- Repeat for second card -->
</div>
```

**Shimmer Animation:**

```css
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.skeleton-icon,
.skeleton-title,
.skeleton-stat,
.skeleton-button {
    background: linear-gradient(90deg, #f5f5f5 0px, #eeeeee 40px, #f5f5f5 80px);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
    border-radius: 4px;
}

.skeleton-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 20px;
}

.skeleton-title {
    width: 120px;
    height: 32px;
    margin: 0 auto 24px;
}

.skeleton-stat {
    height: 20px;
    margin-bottom: 12px;
}

.skeleton-button {
    width: 100%;
    height: 48px;
    margin-top: 24px;
}
```

### 9.2 Stats Refresh Loading

**Inline Spinner for Stats:**

```html
<div class="stat-item loading">
    <span class="stat-label">Programs</span>
    <span class="stat-value">
        <div class="mini-spinner"></div>
    </span>
</div>
```

```css
.mini-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #e5e5e5;
    border-top-color: #2e5c8a;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    display: inline-block;
}
```

---

## 10. TECHNICAL SPECIFICATIONS

### 10.1 Performance Requirements

| Metric                 | Target    | Measurement            |
| ---------------------- | --------- | ---------------------- |
| **Stats API Response** | <500ms    | All 3 metrics combined |
| **Card Render Time**   | <200ms    | After data received    |
| **Site Selection**     | <2s total | Click to navigation    |
| **Hover Animation**    | 60fps     | Smooth transform       |
| **Loading Skeleton**   | <100ms    | Appears immediately    |

### 10.2 Caching Strategy

**Stats Data Cache:**

```javascript
const STATS_CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

async function fetchSiteStats(siteId, useCache = true) {
    const cacheKey = `site_stats_${siteId}`;

    if (useCache) {
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            if (Date.now() - timestamp < STATS_CACHE_DURATION) {
                return data;
            }
        }
    }

    // Fetch fresh data
    const response = await fetch(`/api/v1/sites/${siteId}/stats/summary`);
    const data = await response.json();

    // Cache
    sessionStorage.setItem(
        cacheKey,
        JSON.stringify({
            data: data.data,
            timestamp: Date.now(),
        }),
    );

    return data.data;
}
```

**Cache Invalidation:**

- On logout: Clear all cache
- On manual refresh: Force fresh fetch
- After 5 minutes: Auto-expire

### 10.3 Accessibility (WCAG 2.1 Level A)

| Requirement             | Implementation                            |
| ----------------------- | ----------------------------------------- |
| **Keyboard Navigation** | Full keyboard support (Tab, Enter, Space) |
| **Focus Indicators**    | Visible 3px blue outline                  |
| **ARIA Labels**         | role="button", aria-label on cards        |
| **Screen Reader**       | Stats announced properly                  |
| **Color Contrast**      | All text minimum 4.5:1 ratio              |
| **Touch Targets**       | Min 44x44px (entire card)                 |

### 10.4 Browser Support

**Tested Browsers:**

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

**Polyfills Required:**

- None (uses standard ES6+)

---

## 11. API ENDPOINTS SUMMARY

| Endpoint                                  | Method | Purpose                      | Cache    | Response Time |
| ----------------------------------------- | ------ | ---------------------------- | -------- | ------------- |
| `/api/v1/sites/:id/stats/summary`         | GET    | Get all stats for site       | 5 min    | <500ms        |
| `/api/v1/sites/:id/stats/active-programs` | GET    | Active programs count        | 5 min    | <200ms        |
| `/api/v1/sites/:id/stats/pending-actions` | GET    | Pending actions count        | No cache | <300ms        |
| `/api/v1/sites/:id/stats/budget`          | GET    | Total budget                 | 5 min    | <200ms        |
| `/api/v1/session/set-site`                | POST   | Set selected site in session | No cache | <300ms        |
| `/api/v1/session/site`                    | DELETE | Clear site selection         | No cache | <100ms        |

---

## 12. DATABASE QUERIES

### 12.1 Query Optimization

**Required Indexes:**

```sql
-- Sites
CREATE INDEX idx_sites_active ON sites(id, deleted_at);

-- RABs
CREATE INDEX idx_rabs_site_status ON rabs(site_id, status, deleted_at);
CREATE INDEX idx_rabs_site_budget ON rabs(site_id, status, total_budget) WHERE deleted_at IS NULL;

-- Settlements
CREATE INDEX idx_settlements_requester_site_deadline
ON settlements(requester_id, site_id, status, deadline)
WHERE deleted_at IS NULL;

-- Approval Chain
CREATE INDEX idx_approval_chain_approver_site
ON approval_chain(approver_id, entity_type, entity_id, status)
WHERE deleted_at IS NULL;

-- User Site Assignments
CREATE INDEX idx_user_site_assignments_user
ON user_site_assignments(user_id, site_id)
WHERE deleted_at IS NULL;
```

### 12.2 Query Performance Monitoring

**Explain Analyze for Key Queries:**

```sql
-- Active Programs Count
EXPLAIN ANALYZE
SELECT COUNT(*) FROM rabs
WHERE site_id = 'uuid-klaten'
  AND status = 'Active'
  AND deleted_at IS NULL;

-- Expected: Index Scan on idx_rabs_site_status, <50ms
```

**Slow Query Threshold:** 500ms

**Optimization Tips:**

- Use covering indexes
- Avoid OR conditions (use UNION instead)
- Pre-aggregate where possible
- Use materialized views for complex stats (if needed)

---

## CONCLUSION

This completes **Section 3: Program Launcher** PRD. This document provides comprehensive specifications including:

✅ Site card visual design & hover effects  
✅ Three metrics with detailed calculations (Programs, Pending, Budget)  
✅ Data fetching strategies (parallel & combined)  
✅ Site selection flow & session management  
✅ NO persistence rules (intentional re-selection)  
✅ Loading states & error handling  
✅ Animations & transitions  
✅ Performance targets & caching  
✅ Accessibility compliance  
✅ Database optimization

**Next Documents:**

- PRD Section 4: System Settings (largest, 6 sub-modules)
- PRD Section 5: Information Section

---

**Document Control:**

| Version | Date        | Author           | Changes                   |
| ------- | ----------- | ---------------- | ------------------------- |
| 1.0     | 08 Feb 2026 | System Architect | Initial comprehensive PRD |

---

_End of Section 3 PRD_
