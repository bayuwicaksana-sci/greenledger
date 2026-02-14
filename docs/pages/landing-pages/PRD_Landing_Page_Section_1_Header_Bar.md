# PRODUCT REQUIREMENT DOCUMENT

## LANDING PAGE - SECTION 1: HEADER BAR

**AgriScience Finance Management System**  
**Version:** 1.0  
**Date:** 08 Februari 2026  
**Document:** 1 of 5

---

## TABLE OF CONTENTS

1. [Overview](#1-overview)
2. [Logo & Application Name](#2-logo--application-name)
3. [Global Search Bar](#3-global-search-bar)
4. [Notification Bell](#4-notification-bell)
5. [User Profile](#5-user-profile)
6. [Technical Specifications](#6-technical-specifications)
7. [API Endpoints Summary](#7-api-endpoints-summary)
8. [Database Schema](#8-database-schema)

---

## 1. OVERVIEW

### 1.1 Purpose

Header Bar adalah **persistent navigation component** yang selalu visible di top screen sepanjang user berada di sistem (Landing Page maupun Site-Specific System). Berfungsi sebagai:

- Global navigation (logo untuk kembali ke Landing Page)
- Quick search across entities
- Notification center
- User profile access & logout

### 1.2 Position & Layout

| Property          | Specification              | Rationale                                            |
| ----------------- | -------------------------- | ---------------------------------------------------- |
| **Position**      | Fixed top                  | Always accessible, tidak hilang saat scroll          |
| **Width**         | 100% viewport width        | Full screen coverage                                 |
| **Height**        | 80px                       | Optimal untuk touch targets (min 44px per Apple HIG) |
| **Z-index**       | 1000                       | Above all content, below modals (1100)               |
| **Background**    | #FFFFFF (White)            | Clean, professional, high contrast                   |
| **Border Bottom** | 1px solid #E5E5E5          | Subtle separator                                     |
| **Padding**       | 0 32px (left/right)        | Breathing room, responsive: 16px on mobile           |
| **Box Shadow**    | 0 2px 4px rgba(0,0,0,0.08) | Depth perception, lift effect                        |

### 1.3 Component Arrangement

**Layout:** Horizontal Flexbox

```
[Logo + App Name]    [        Global Search Bar        ]    [Bell] [Profile]
     (left)                    (center, flex-grow)           (right)
```

**Responsive Behavior:**

| Breakpoint          | Logo    | App Name           | Search                                 | Bell    | Profile                 |
| ------------------- | ------- | ------------------ | -------------------------------------- | ------- | ----------------------- |
| Desktop (>1024px)   | Visible | Both lines visible | Full width 600px                       | Visible | Full (avatar+name+role) |
| Tablet (768-1024px) | Visible | Primary only       | 400px                                  | Visible | Avatar + name only      |
| Mobile (<768px)     | Visible | Hidden             | Icon only (click → full screen search) | Visible | Avatar only             |

---

## 2. LOGO & APPLICATION NAME

### 2.1 Component Structure

#### 2.1.1 Logo Image

| Property             | Specification                         | Implementation Detail                              |
| -------------------- | ------------------------------------- | -------------------------------------------------- |
| **File Path**        | `/assets/images/logo.png`             | Stored in CDN for fast loading                     |
| **Format**           | PNG with transparency                 | Fallback: SVG version at `/assets/images/logo.svg` |
| **Size**             | 40x40px                               | Consistent with Material Design icon size          |
| **Alt Text**         | "AgriScience Logo"                    | For screen readers (accessibility)                 |
| **Loading Strategy** | Eager loading                         | Critical above-the-fold content                    |
| **Error Handling**   | Show placeholder div with brand color | Background: #2E5C8A, no broken image icon          |
| **Load Timeout**     | 500ms                                 | If not loaded, show fallback SVG                   |

**HTML Structure:**

```html
<a href="/landing" class="logo-container">
    <img
        src="/assets/images/logo.png"
        alt="AgriScience Logo"
        width="40"
        height="40"
        onerror="this.src='/assets/images/logo.svg'"
    />
    <div class="app-name">
        <span class="app-name-primary">AgriScience</span>
        <span class="app-name-secondary">Finance System</span>
    </div>
</a>
```

#### 2.1.2 Application Name

**Primary Name:**
| Property | Value | CSS |
|----------|-------|-----|
| Text | "AgriScience" | - |
| Font Family | Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif | Standard system font stack |
| Font Size | 18pt (24px) | `font-size: 1.5rem` |
| Font Weight | 700 (Bold) | `font-weight: 700` |
| Color | #2E5C8A | Primary brand color |
| Line Height | 1.2 | `line-height: 1.2` |
| Letter Spacing | -0.01em | Tighter for better readability |

**Secondary Name:**
| Property | Value |
|----------|-------|
| Text | "Finance System" |
| Font Size | 12pt (16px) |
| Font Weight | 400 (Regular) |
| Color | #666666 |
| Display | Block (new line below primary) |

**Responsive Display:**

- Desktop (>1024px): Both primary + secondary visible
- Tablet (768-1024px): Primary only
- Mobile (<768px): Both hidden (logo only)

#### 2.1.3 Click Behavior

**Scenario 1: User in Landing Page**

- **Action:** Click logo/name
- **Result:** Reload Landing Page (scroll to top if already at bottom)
- **API Call:** None
- **Navigation:** `window.location.href = '/landing'` OR `router.push('/landing')`

**Scenario 2: User in Site-Specific System (Klaten/Magelang)**

- **Action:** Click logo/name
- **Confirmation Modal:**
    - Title: "Leave Current Site?"
    - Message: "You will return to Landing Page and need to re-select your site. Continue?"
    - Buttons: "Cancel" (secondary), "Leave" (primary)
- **On Confirm:**
    1. API Call: `DELETE /api/v1/session/site` (clear selected site session)
    2. Navigate: `window.location.href = '/landing'`
- **On Cancel:** Stay on current page, close modal

**Scenario 3: User Has Unsaved Changes**

- **Action:** Click logo/name
- **Browser Prompt:** "You have unsaved changes. Are you sure you want to leave?"
- **Note:** Use `beforeunload` event listener

**Error Handling:**
| Error Scenario | User Experience | Technical Handling |
|----------------|-----------------|-------------------|
| Network error during API call | Show error toast: "Unable to navigate. Check connection.", Stay on current page | Catch API error, display toast, log to console |
| Session expired | Redirect directly to login page | HTTP 401 response → clear local storage → redirect `/login` |
| Server error (500) | Show error toast: "Something went wrong. Please try again." | Catch error, log to error tracking service (Sentry) |

### 2.2 Accessibility

| Requirement             | Implementation                                       |
| ----------------------- | ---------------------------------------------------- |
| **ARIA Label**          | `aria-label="Navigate to Landing Page"` on `<a>` tag |
| **Keyboard Navigation** | Tab to focus, Enter/Space to activate                |
| **Focus Indicator**     | Blue outline 3px, `outline: 3px solid #2E5C8A`       |
| **Screen Reader**       | Announces "Navigate to Landing Page, link"           |

### 2.3 Performance Metrics

| Metric                         | Target | Measurement Method          |
| ------------------------------ | ------ | --------------------------- |
| Logo Load Time                 | <500ms | Chrome DevTools Network tab |
| First Contentful Paint (FCP)   | <1.0s  | Lighthouse                  |
| Largest Contentful Paint (LCP) | <1.5s  | Lighthouse                  |
| Cumulative Layout Shift (CLS)  | <0.1   | Lighthouse                  |

---

## 3. GLOBAL SEARCH BAR

### 3.1 Purpose & Scope

**Purpose:** Enable users untuk quickly find entities across system tanpa navigating through menus.

**Search Scope:** RAB, Payment Request, Vendor, COA (Manager/AVP only), User (Manager/AVP only)

### 3.2 Input Field Specifications

#### 3.2.1 Field Properties

| Property                 | Type    | Value                             | Validation                                   |
| ------------------------ | ------- | --------------------------------- | -------------------------------------------- |
| **ID**                   | String  | `global-search-input`             | Unique ID for JS targeting                   |
| **Name**                 | String  | `q`                               | Query parameter name                         |
| **Type**                 | Text    | `<input type="text">`             | -                                            |
| **Placeholder**          | String  | "Search RAB, PR, Vendor, User..." | Dynamic based on user permissions            |
| **Max Length**           | Integer | 100 characters                    | Prevent excessively long queries             |
| **Min Length (Trigger)** | Integer | 2 characters                      | Debounce starts after 2 chars                |
| **Autocomplete**         | String  | `off`                             | Disable browser autocomplete                 |
| **Spellcheck**           | Boolean | `false`                           | Disable browser spellcheck (technical terms) |
| **Debounce Delay**       | Integer | 300ms                             | Wait time before API call                    |

#### 3.2.2 Visual Styling

| Property              | Value                                     | CSS                             |
| --------------------- | ----------------------------------------- | ------------------------------- |
| **Width**             | 600px max, 100% responsive                | `max-width: 600px; width: 100%` |
| **Height**            | 40px                                      | `height: 40px`                  |
| **Padding**           | 8px 16px 8px 44px (left padding for icon) | `padding: 8px 16px 8px 44px`    |
| **Border**            | 1px solid #CCCCCC                         | `border: 1px solid #CCCCCC`     |
| **Border Radius**     | 8px                                       | `border-radius: 8px`            |
| **Background**        | #FFFFFF                                   | `background: #FFF`              |
| **Font Size**         | 14pt (18.67px)                            | `font-size: 0.875rem`           |
| **Font Color**        | #333333                                   | `color: #333`                   |
| **Placeholder Color** | #999999                                   | `::placeholder { color: #999 }` |

**Focus State:**

```css
#global-search-input:focus {
    outline: none;
    border-color: #2e5c8a;
    box-shadow: 0 0 0 3px rgba(46, 92, 138, 0.1);
}
```

**Hover State:**

```css
#global-search-input:hover {
    border-color: #999999;
}
```

#### 3.2.3 Search Icon

| Property     | Value                                                    |
| ------------ | -------------------------------------------------------- |
| **Icon**     | Magnifying glass (FontAwesome `fa-search` or custom SVG) |
| **Size**     | 20x20px                                                  |
| **Color**    | #666666                                                  |
| **Position** | Absolute, left 12px, vertically centered                 |
| **Hover**    | No interaction (decoration only)                         |

**SVG Code:**

```html
<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
    <path
        d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35"
        stroke="#666666"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
</svg>
```

#### 3.2.4 Clear Button

**Display Condition:** Show when `input.value.length > 0`

| Property         | Value                                            |
| ---------------- | ------------------------------------------------ |
| **Icon**         | X circle (clear icon)                            |
| **Size**         | 18x18px                                          |
| **Color**        | #999999                                          |
| **Hover Color**  | #333333                                          |
| **Position**     | Absolute, right 12px, vertically centered        |
| **Click Action** | Clear input value + close dropdown + focus input |
| **Cursor**       | Pointer                                          |

### 3.3 Search Functionality

#### 3.3.1 Debounce Logic

**Purpose:** Prevent excessive API calls while user is typing.

**Implementation:**

```javascript
let debounceTimer;

function handleSearchInput(event) {
    const query = event.target.value.trim();

    // Clear previous timer
    clearTimeout(debounceTimer);

    // Minimum 2 characters
    if (query.length < 2) {
        closeDropdown();
        return;
    }

    // Wait 300ms before executing
    debounceTimer = setTimeout(() => {
        executeSearch(query);
    }, 300);
}

document
    .getElementById('global-search-input')
    .addEventListener('input', handleSearchInput);
```

**User Experience:**

- User types "pup" → Wait 300ms → API call
- User continues typing "pupuk" → Timer resets → Wait 300ms → API call with "pupuk"
- Result: Only 1 API call instead of 5

#### 3.3.2 Search Endpoint

**API Endpoint:** `GET /api/v1/search`

**Request:**

```http
GET /api/v1/search?query=pupuk&limit=5
Headers:
  Authorization: Bearer {jwt_token}
  Content-Type: application/json
```

**Query Parameters:**

| Parameter | Type     | Required | Default | Description                                 |
| --------- | -------- | -------- | ------- | ------------------------------------------- |
| `query`   | String   | Yes      | -       | Search query (2-100 chars)                  |
| `limit`   | Integer  | No       | 5       | Max results per entity type                 |
| `types`   | String[] | No       | All     | Filter entity types: rab,pr,vendor,coa,user |

**Response Format:**

```json
{
    "success": true,
    "data": {
        "results": [
            {
                "type": "RAB",
                "id": "550e8400-e29b-41d4-a716-446655440000",
                "code": "RAB-2026-02-00012",
                "title": "Penelitian Pupuk Organik Q2",
                "site": {
                    "id": "uuid",
                    "name": "Klaten",
                    "code": "KLT"
                },
                "status": "Active",
                "created_at": "2026-02-01T08:00:00Z"
            },
            {
                "type": "Vendor",
                "id": "uuid",
                "name": "PT Pupuk Indonesia",
                "npwp": "01.234.567.8-901.000",
                "type": "Corporate"
            }
        ],
        "recent_searches": ["pupuk", "budi santoso", "klaten"],
        "total_count": 12
    }
}
```

**Error Responses:**

| Status Code | Error Message                 | User Display                        |
| ----------- | ----------------------------- | ----------------------------------- |
| 400         | Query too short (min 2 chars) | "Enter at least 2 characters"       |
| 401         | Unauthorized                  | Redirect to login                   |
| 429         | Too many requests             | "Too many searches. Please wait..." |
| 500         | Internal server error         | "Search temporarily unavailable"    |
| 503         | Service unavailable           | "Search temporarily unavailable"    |

#### 3.3.3 Search Scope Per Entity

**3.3.3.1 RAB (Rancangan Anggaran Biaya)**

**Permission Check:**

- User can view RAB if:
    - User is creator (users.id = rabs.created_by)
    - User is Manager/AVP (all RABs)
    - User's sub-division matches RAB sub-division (RA Agronomy sees Agronomy RABs)

**SQL Query:**

```sql
SELECT
  r.id,
  r.rab_code AS code,
  r.title,
  r.status,
  r.created_at,
  s.name AS site_name,
  s.site_code AS site_code,
  u.name AS creator_name
FROM rabs r
JOIN sites s ON r.site_id = s.id
JOIN users u ON r.created_by = u.id
WHERE
  (r.rab_code LIKE :query OR r.title LIKE :query)
  AND r.deleted_at IS NULL
  AND (
    r.created_by = :current_user_id
    OR :current_user_role IN ('Manager', 'AVP')
    OR (
      :current_user_sub_division = (
        SELECT sub_division FROM users WHERE id = r.created_by
      )
    )
  )
ORDER BY
  CASE
    WHEN r.rab_code = :query THEN 1
    WHEN r.rab_code LIKE CONCAT(:query, '%') THEN 2
    WHEN r.title LIKE CONCAT(:query, '%') THEN 3
    ELSE 4
  END,
  r.created_at DESC
LIMIT 5;
```

**Result Format:**

```json
{
    "type": "RAB",
    "id": "uuid",
    "code": "RAB-2026-02-00012",
    "title": "Penelitian Pupuk Organik Q2",
    "site": {
        "name": "Klaten",
        "code": "KLT"
    },
    "status": "Active",
    "created_at": "2026-02-01T08:00:00Z"
}
```

**Navigation:** Click result → `/rab/{id}` (RAB detail page)

---

**3.3.3.2 Payment Request**

**Permission Check:**

- User can view PR if:
    - User is requester (payment_requests.requester_id = current_user_id)
    - User is approver (exists in approval_chain where pr_id = payment_requests.id AND approver_id = current_user_id)
    - User is FO/FA (can see all PRs)

**SQL Query:**

```sql
SELECT
  pr.id,
  pr.pr_code AS code,
  pr.type,
  pr.total_amount,
  pr.status,
  pr.is_urgent,
  pr.created_at,
  s.name AS site_name,
  s.site_code,
  u.name AS requester_name,
  u.role AS requester_role
FROM payment_requests pr
JOIN sites s ON pr.site_id = s.id
JOIN users u ON pr.requester_id = u.id
WHERE
  pr.pr_code LIKE :query
  AND pr.deleted_at IS NULL
  AND (
    pr.requester_id = :current_user_id
    OR :current_user_role IN ('FO', 'FA', 'Manager', 'AVP')
    OR EXISTS (
      SELECT 1 FROM approval_chain ac
      WHERE ac.pr_id = pr.id AND ac.approver_id = :current_user_id
    )
  )
ORDER BY
  CASE WHEN pr.pr_code = :query THEN 1 ELSE 2 END,
  pr.created_at DESC
LIMIT 5;
```

**Result Format:**

```json
{
    "type": "PR",
    "id": "uuid",
    "code": "PR-2026-02-00123",
    "type": "Cash Advance",
    "amount": 3500000,
    "site": {
        "name": "Magelang",
        "code": "MLG"
    },
    "status": "In Approval",
    "is_urgent": false,
    "requester": {
        "name": "Budi Santoso",
        "role": "RO"
    }
}
```

**Navigation:** Click result → `/pr/{id}` (PR detail page)

---

**3.3.3.3 Vendor**

**Permission Check:** All users can search vendors (no restriction)

**SQL Query:**

```sql
SELECT
  v.id,
  v.vendor_type,
  CASE
    WHEN v.vendor_type = 'Corporate' THEN v.company_name
    ELSE v.full_name
  END AS name,
  v.npwp,
  v.nik,
  v.status,
  v.created_at
FROM vendors v
WHERE
  v.deleted_at IS NULL
  AND (
    v.company_name LIKE :query
    OR v.full_name LIKE :query
    OR v.npwp LIKE :query
    OR v.nik LIKE :query
  )
ORDER BY
  CASE
    WHEN v.company_name = :query OR v.full_name = :query THEN 1
    WHEN v.company_name LIKE CONCAT(:query, '%') OR v.full_name LIKE CONCAT(:query, '%') THEN 2
    ELSE 3
  END,
  v.created_at DESC
LIMIT 5;
```

**Result Format:**

```json
{
    "type": "Vendor",
    "id": "uuid",
    "vendor_type": "Corporate",
    "name": "PT Agro Makmur Sejahtera",
    "npwp": "01.234.567.8-901.000",
    "status": "Active"
}
```

**Navigation:** Click result → `/vendors/{id}` (Vendor detail page)

---

**3.3.3.4 COA (Chart of Accounts)**

**Permission Check:** Manager and AVP only

**SQL Query:**

```sql
SELECT
  c.id,
  c.coa_code AS code,
  c.description,
  c.category,
  c.sub_category,
  c.status,
  COUNT(DISTINCT ri.rab_id) AS usage_count
FROM coa c
LEFT JOIN rab_items ri ON c.id = ri.coa_id
WHERE
  :current_user_role IN ('Manager', 'AVP')
  AND c.deleted_at IS NULL
  AND (
    c.coa_code LIKE :query
    OR c.description LIKE :query
  )
GROUP BY c.id
ORDER BY
  CASE
    WHEN c.coa_code = :query THEN 1
    WHEN c.coa_code LIKE CONCAT(:query, '%') THEN 2
    ELSE 3
  END,
  c.coa_code ASC
LIMIT 5;
```

**Result Format:**

```json
{
    "type": "COA",
    "id": "uuid",
    "code": "5010-001",
    "description": "Pupuk Kimia - NPK",
    "category": "Cost of Goods Sold",
    "sub_category": "Agricultural Supplies",
    "status": "Active",
    "usage_count": 12
}
```

**Navigation:** Click result → `/admin/coa?highlight={code}` (COA management page with highlight)

---

**3.3.3.5 User**

**Permission Check:** Manager and AVP only

**SQL Query:**

```sql
SELECT
  u.id,
  u.name,
  u.email,
  u.role,
  u.sub_division,
  u.status,
  s.name AS site_name
FROM users u
LEFT JOIN sites s ON u.site_id = s.id
WHERE
  :current_user_role IN ('Manager', 'AVP')
  AND u.deleted_at IS NULL
  AND (
    u.name LIKE :query
    OR u.email LIKE :query
  )
ORDER BY
  CASE
    WHEN u.name = :query THEN 1
    WHEN u.name LIKE CONCAT(:query, '%') THEN 2
    ELSE 3
  END,
  u.name ASC
LIMIT 5;
```

**Result Format:**

```json
{
    "type": "User",
    "id": "uuid",
    "name": "Budi Santoso",
    "email": "budi.santoso@agriscience.com",
    "role": "RA",
    "sub_division": "Agronomy",
    "site": "Klaten",
    "status": "Active"
}
```

**Navigation:** Click result → `/admin/users/{id}` (User profile/management page)

---

### 3.4 Auto-Suggest Dropdown

#### 3.4.1 Dropdown Container

**Trigger Conditions:**

1. Input has focus AND
2. Query length >= 2 characters AND
3. (Results exist OR recent searches exist)

**Position:** Absolute positioning below search input

```css
.search-dropdown {
    position: absolute;
    top: calc(100% + 4px); /* 4px gap below input */
    left: 0;
    width: 100%; /* Match input width */
    max-width: 600px;
    max-height: 400px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1001;
}
```

#### 3.4.2 Recent Searches Section

**Display Condition:** Recent searches exist (max 3 shown)

**Data Source:** User session `recent_searches` array (stored server-side)

**Structure:**

```html
<div class="search-section recent-searches">
    <div class="section-header">Recent</div>
    <div class="search-item recent" data-query="pupuk">
        <svg class="icon clock">...</svg>
        <span class="text">pupuk</span>
    </div>
    <div class="search-item recent" data-query="budi santoso">
        <svg class="icon clock">...</svg>
        <span class="text">budi santoso</span>
    </div>
</div>
```

**Styling:**
| Element | Style |
|---------|-------|
| Section Header | 12pt gray (#999), uppercase, padding 12px 16px, border-bottom 1px solid #F5F5F5 |
| Search Item | Padding 10px 16px, cursor pointer, hover background #F8F9FA |
| Clock Icon | 16x16px, gray (#999), margin-right 8px |
| Text | 14pt regular (#333) |

**Click Behavior:**

1. Populate input with clicked query
2. Execute search with that query
3. Close dropdown after results load

#### 3.4.3 Results Section

**Display Condition:** API returned results

**Structure:**

```html
<div class="search-section results">
    <div class="section-header">Results</div>

    <!-- RAB Result -->
    <a href="/rab/uuid" class="search-result-item">
        <span class="type-badge rab">RAB</span>
        <div class="content">
            <div class="primary-text">
                RAB-2026-02-00012 - Penelitian Pupuk Organik Q2
            </div>
            <div class="secondary-text">
                <span class="site-badge klt">KLT</span>
                <span class="status">Active</span>
            </div>
        </div>
    </a>

    <!-- PR Result -->
    <a href="/pr/uuid" class="search-result-item">
        <span class="type-badge pr">PR</span>
        <div class="content">
            <div class="primary-text">PR-2026-02-00123</div>
            <div class="secondary-text">
                <span class="site-badge mlg">MLG</span>
                <span class="amount">Rp 3,500,000</span>
                <span class="status urgent">Urgent</span>
            </div>
        </div>
    </a>

    <!-- More results... -->
</div>
```

**Type Badge Styling:**

| Type   | Background             | Text Color | Icon        |
| ------ | ---------------------- | ---------- | ----------- |
| RAB    | #E3F2FD (Light Blue)   | #2E5C8A    | Document    |
| PR     | #E8F5E9 (Light Green)  | #4CAF50    | Receipt     |
| Vendor | #F3E5F5 (Light Purple) | #9C27B0    | Building    |
| COA    | #FFF3E0 (Light Orange) | #FF9800    | Chart       |
| User   | #EEEEEE (Light Gray)   | #666666    | User Circle |

**Result Item Styling:**

```css
.search-result-item {
    display: flex;
    align-items: flex-start;
    padding: 12px 16px;
    border-bottom: 1px solid #f5f5f5;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.search-result-item:hover {
    background: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.type-badge {
    flex-shrink: 0;
    width: 44px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    margin-right: 12px;
}

.primary-text {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin-bottom: 4px;
    /* Truncate after 2 lines */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.secondary-text {
    font-size: 12px;
    color: #666;
    display: flex;
    gap: 8px;
    align-items: center;
}

.site-badge {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.site-badge.klt {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #1976d2;
}

.site-badge.mlg {
    background: #e8f5e9;
    color: #388e3c;
    border: 1px solid #388e3c;
}
```

#### 3.4.4 Empty State

**Display Condition:** Query >= 2 chars AND API returned 0 results AND no recent searches

**Structure:**

```html
<div class="search-empty-state">
    <svg class="icon magnifying-glass-x" width="64" height="64">...</svg>
    <div class="message">No results found for <strong>"{query}"</strong></div>
    <div class="suggestion">Try different keywords</div>
</div>
```

**Styling:**

```css
.search-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
}

.search-empty-state .icon {
    opacity: 0.3;
    margin-bottom: 16px;
}

.search-empty-state .message {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
}

.search-empty-state .suggestion {
    font-size: 12px;
    color: #999;
}
```

#### 3.4.5 Loading State

**Display Condition:** API request in progress

**Structure:**

```html
<div class="search-loading-state">
    <div class="skeleton-item"></div>
    <div class="skeleton-item"></div>
    <div class="skeleton-item"></div>
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

.skeleton-item {
    height: 60px;
    margin: 8px 16px;
    border-radius: 8px;
    background: linear-gradient(90deg, #f5f5f5 0px, #eeeeee 40px, #f5f5f5 80px);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
}
```

**Timeout:** If API takes >5 seconds:

```html
<div class="search-timeout-state">
    <svg class="icon clock" width="48" height="48">...</svg>
    <div class="message">Search taking longer than usual...</div>
    <button class="retry-button">Retry</button>
</div>
```

#### 3.4.6 Error State

**Display Condition:** API returned error (500, 503, network error)

**Structure:**

```html
<div class="search-error-state">
    <svg class="icon alert-triangle" width="48" height="48">...</svg>
    <div class="message">Unable to load search results</div>
    <div class="suggestion">Please check your connection</div>
    <button class="retry-button">Retry</button>
</div>
```

**Error Handling by Status Code:**

| Status                    | Display Message                             | Action Button         |
| ------------------------- | ------------------------------------------- | --------------------- |
| 429 (Rate Limit)          | "Too many searches. Please wait..."         | Timer countdown (30s) |
| 500 (Server Error)        | "Search temporarily unavailable"            | Retry button          |
| 503 (Service Unavailable) | "Search service is down for maintenance"    | Close button only     |
| Network Error             | "Unable to connect. Check your connection." | Retry button          |

#### 3.4.7 Keyboard Navigation

**Supported Keys:**

| Key            | Action                                     | State Change                       |
| -------------- | ------------------------------------------ | ---------------------------------- |
| **Arrow Down** | Move highlight to next result              | `selected_index++`                 |
| **Arrow Up**   | Move highlight to previous result          | `selected_index--`                 |
| **Enter**      | Navigate to highlighted result             | Call `result.href`                 |
| **Escape**     | Close dropdown                             | Hide dropdown, keep input focus    |
| **Tab**        | Close dropdown, move focus to next element | Hide dropdown, native tab behavior |

**Implementation:**

```javascript
let selectedIndex = -1; // -1 means no selection
const resultItems = document.querySelectorAll('.search-result-item');

document
    .getElementById('global-search-input')
    .addEventListener('keydown', (e) => {
        if (!dropdownVisible) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedIndex = Math.min(
                    selectedIndex + 1,
                    resultItems.length - 1,
                );
                updateHighlight();
                break;

            case 'ArrowUp':
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateHighlight();
                break;

            case 'Enter':
                e.preventDefault();
                if (selectedIndex >= 0) {
                    resultItems[selectedIndex].click();
                }
                break;

            case 'Escape':
                e.preventDefault();
                closeDropdown();
                break;
        }
    });

function updateHighlight() {
    resultItems.forEach((item, index) => {
        item.classList.toggle('highlighted', index === selectedIndex);
    });

    // Scroll highlighted item into view
    if (selectedIndex >= 0) {
        resultItems[selectedIndex].scrollIntoView({
            block: 'nearest',
            behavior: 'smooth',
        });
    }
}
```

**Highlighted Item Style:**

```css
.search-result-item.highlighted {
    background: #e3f2fd;
    border-left: 3px solid #2e5c8a;
    padding-left: 13px; /* Compensate for border */
}
```

#### 3.4.8 Click Outside to Close

**Behavior:** Clicking anywhere outside search input or dropdown closes dropdown

**Implementation:**

```javascript
document.addEventListener('click', (e) => {
    const searchContainer = document.querySelector('.search-container');

    if (!searchContainer.contains(e.target)) {
        closeDropdown();
    }
});
```

---

### 3.5 Recent Searches Management

#### 3.5.1 Storage Strategy

**Server-Side Session Storage** (NOT localStorage)

- Why: Sync across devices, secure, controlled by backend
- Storage: Redis session store
- Key: `user:{user_id}:recent_searches`
- Data Structure: Array of strings (max 10 items)

#### 3.5.2 Save Recent Search

**Trigger:** User clicks a search result OR presses Enter on highlighted result

**API Endpoint:** `POST /api/v1/search/recent`

**Request:**

```json
{
    "query": "pupuk"
}
```

**Server Logic:**

```javascript
async function saveRecentSearch(userId, query) {
    const key = `user:${userId}:recent_searches`;
    let recentSearches = await redis.lrange(key, 0, -1);

    // Remove query if already exists (move to front)
    recentSearches = recentSearches.filter((q) => q !== query);

    // Add to front
    recentSearches.unshift(query);

    // Keep max 10 items
    if (recentSearches.length > 10) {
        recentSearches = recentSearches.slice(0, 10);
    }

    // Save back to Redis
    await redis.del(key);
    await redis.rpush(key, ...recentSearches);

    // Set expiry: 30 days
    await redis.expire(key, 30 * 24 * 60 * 60);

    return recentSearches.slice(0, 3); // Return top 3 for display
}
```

#### 3.5.3 Clear Recent Search

**User Action:** Click "Clear" button in recent searches section

**API Endpoint:** `DELETE /api/v1/search/recent`

**Effect:** Clear all recent searches for current user

---

## 4. NOTIFICATION BELL

### 4.1 Purpose

Notify users about actions requiring immediate attention:

- Approval requests assigned to them
- Settlement deadlines approaching
- Revision requests on their submissions
- System announcements
- Block warnings/notifications

### 4.2 Badge Counter

#### 4.2.1 Data Source

**SQL Query:**

```sql
SELECT COUNT(*) as unread_count
FROM notifications
WHERE user_id = :current_user_id
  AND is_read = false
  AND deleted_at IS NULL;
```

**API Endpoint:** `GET /api/v1/notifications/unread-count`

**Response:**

```json
{
    "success": true,
    "data": {
        "count": 5
    }
}
```

#### 4.2.2 Display Logic

| Count | Display             | Badge Style     |
| ----- | ------------------- | --------------- |
| 0     | Hide badge entirely | `display: none` |
| 1-9   | Show actual number  | `content: "5"`  |
| 10+   | Show "9+"           | `content: "9+"` |

**CSS Implementation:**

```css
.notification-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    background: #f44336;
    color: white;
    font-size: 11px;
    font-weight: 700;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
```

#### 4.2.3 Refresh Mechanisms

**1. On Page Load:**

```javascript
async function loadUnreadCount() {
    try {
        const response = await fetch('/api/v1/notifications/unread-count');
        const data = await response.json();
        updateBadge(data.data.count);
    } catch (error) {
        console.error('Failed to load notification count:', error);
    }
}

window.addEventListener('load', loadUnreadCount);
```

**2. WebSocket Real-Time Updates (Preferred):**

```javascript
const ws = new WebSocket('wss://api.agriscience.com/ws/notifications');

ws.onopen = () => {
    // Send authentication
    ws.send(
        JSON.stringify({
            type: 'auth',
            token: getAuthToken(),
        }),
    );
};

ws.onmessage = (event) => {
    const message = JSON.parse(event.data);

    if (message.type === 'notification_created') {
        // Increment counter
        incrementBadge();

        // Show toast notification
        showToast(message.data.title);

        // Play sound (if enabled in user preferences)
        playNotificationSound();
    }

    if (message.type === 'notification_read') {
        // Decrement counter
        decrementBadge();
    }
};

ws.onerror = (error) => {
    console.error('WebSocket error:', error);
    // Fallback to polling
    startPolling();
};
```

**3. Polling Fallback (if WebSocket fails):**

```javascript
let pollingInterval;

function startPolling() {
    // Poll every 60 seconds
    pollingInterval = setInterval(loadUnreadCount, 60000);
}

function stopPolling() {
    clearInterval(pollingInterval);
}
```

**4. Manual Refresh (after user interaction):**

```javascript
function markNotificationAsRead(notificationId) {
    fetch(`/api/v1/notifications/${notificationId}/read`, { method: 'PATCH' })
        .then(() => {
            // Optimistically decrement counter
            decrementBadge();
        })
        .catch((error) => {
            console.error('Failed to mark as read:', error);
            // Revert counter
            incrementBadge();
        });
}
```

#### 4.2.4 Animation Effects

**Pulse Animation (New Notification Arrives):**

```css
@keyframes pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
}

.notification-badge.new-notification {
    animation: pulse 0.6s ease-in-out;
}
```

**Trigger:**

```javascript
function incrementBadge() {
    const badge = document.querySelector('.notification-badge');
    const currentCount = parseInt(badge.textContent) || 0;
    const newCount = currentCount + 1;

    updateBadge(newCount);

    // Add pulse animation
    badge.classList.add('new-notification');
    setTimeout(() => {
        badge.classList.remove('new-notification');
    }, 600);
}
```

### 4.3 Notification Types & Generation

#### 4.3.1 Type Definitions

| Type ID                | Type Name           | Priority | Icon           | Color          |
| ---------------------- | ------------------- | -------- | -------------- | -------------- |
| `approval_request`     | Approval Request    | High     | Document Check | Blue #2E5C8A   |
| `settlement_reminder`  | Settlement Reminder | Medium   | Clock          | Yellow #FFC107 |
| `settlement_overdue`   | Settlement Overdue  | Critical | Alert Circle   | Red #F44336    |
| `revision_request`     | Revision Request    | High     | Edit           | Orange #FB923C |
| `announcement`         | System Announcement | Low      | Megaphone      | Green #4CAF50  |
| `block_warning`        | Block Warning       | Critical | Alert Triangle | Red #F44336    |
| `block_notification`   | User Blocked        | Critical | Ban            | Red #F44336    |
| `unblock_notification` | User Unblocked      | Medium   | Check Circle   | Green #4CAF50  |

#### 4.3.2 Generation Rules

**4.3.2.1 Approval Request**

**Trigger:** Payment Request or RAB status changes to "In Approval" AND assigned to user

**Generation Logic:**

```javascript
// Triggered by database trigger or application event
async function createApprovalNotification(prId, approverId) {
    const pr = await PaymentRequest.findById(prId);
    const approver = await User.findById(approverId);

    await Notification.create({
        user_id: approverId,
        type: 'approval_request',
        title: `${pr.pr_code} needs your approval`,
        message: `${pr.requester.name} submitted ${pr.type} for Rp ${formatCurrency(pr.total_amount)}`,
        entity_type: 'PaymentRequest',
        entity_id: prId,
        entity_url: `/pr/${prId}/approval`,
        priority: pr.is_urgent ? 'high' : 'medium',
    });

    // Send via WebSocket
    sendWebSocketNotification(approverId, {
        type: 'notification_created',
        data: { title: `${pr.pr_code} needs your approval` },
    });

    // Send email if user has email notifications enabled
    if (approver.preferences.email_notifications) {
        sendEmail(approver.email, 'New Approval Request', emailTemplate);
    }
}
```

---

**4.3.2.2 Settlement Reminder**

**Trigger:** Cron job runs every hour, creates notifications at deadline-24hr AND deadline-6hr

**Cron Job Logic:**

```javascript
// Run every hour: 0 * * * *
async function sendSettlementReminders() {
    const now = new Date();
    const in24Hours = new Date(now.getTime() + 24 * 60 * 60 * 1000);
    const in6Hours = new Date(now.getTime() + 6 * 60 * 60 * 1000);

    // Find settlements with deadline in 24 hours (and not yet reminded)
    const settlements24h = await Settlement.find({
        deadline: { $gte: now, $lte: in24Hours },
        status: 'Pending Settlement',
        reminder_24h_sent: false,
    }).populate('payment_request');

    for (const settlement of settlements24h) {
        await Notification.create({
            user_id: settlement.payment_request.requester_id,
            type: 'settlement_reminder',
            title: `Settlement due in 24 hours`,
            message: `Please submit settlement for ${settlement.payment_request.pr_code}`,
            entity_type: 'Settlement',
            entity_id: settlement.id,
            entity_url: `/settlements/${settlement.id}/submit`,
            priority: 'medium',
        });

        settlement.reminder_24h_sent = true;
        await settlement.save();
    }

    // Find settlements with deadline in 6 hours
    const settlements6h = await Settlement.find({
        deadline: { $gte: now, $lte: in6Hours },
        status: 'Pending Settlement',
        reminder_6h_sent: false,
    }).populate('payment_request');

    for (const settlement of settlements6h) {
        await Notification.create({
            user_id: settlement.payment_request.requester_id,
            type: 'settlement_reminder',
            title: `Settlement due in 6 hours - Urgent!`,
            message: `Please submit settlement for ${settlement.payment_request.pr_code} immediately`,
            entity_type: 'Settlement',
            entity_id: settlement.id,
            entity_url: `/settlements/${settlement.id}/submit`,
            priority: 'high',
        });

        settlement.reminder_6h_sent = true;
        await settlement.save();
    }
}
```

---

**4.3.2.3 Settlement Overdue**

**Trigger:** Cron job runs every hour, detects overdue settlements

**Logic:**

```javascript
async function sendOverdueNotifications() {
    const now = new Date();

    const overdueSettlements = await Settlement.find({
        deadline: { $lt: now },
        status: 'Pending Settlement',
        overdue_notification_sent: false,
    }).populate('payment_request');

    for (const settlement of overdueSettlements) {
        await Notification.create({
            user_id: settlement.payment_request.requester_id,
            type: 'settlement_overdue',
            title: `Settlement OVERDUE for ${settlement.payment_request.pr_code}`,
            message: `Your settlement is now overdue. Submit immediately to avoid account block.`,
            entity_type: 'Settlement',
            entity_id: settlement.id,
            entity_url: `/settlements/${settlement.id}/submit`,
            priority: 'critical',
        });

        settlement.overdue_notification_sent = true;
        await settlement.save();

        // Check if user should be blocked (>3 overdue settlements)
        const overdueCount = await Settlement.countDocuments({
            'payment_request.requester_id':
                settlement.payment_request.requester_id,
            deadline: { $lt: now },
            status: 'Pending Settlement',
        });

        if (overdueCount >= 3) {
            await blockUser(
                settlement.payment_request.requester_id,
                'Multiple overdue settlements',
            );
        }
    }
}
```

---

### 4.4 Database Schema

#### 4.4.1 Notifications Table

**Table Name:** `notifications`

| Column        | Type         | Constraints                                                    | Description                                                                                                                                                        |
| ------------- | ------------ | -------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `id`          | UUID         | PRIMARY KEY, DEFAULT uuid_generate_v4()                        | Unique identifier                                                                                                                                                  |
| `user_id`     | UUID         | NOT NULL, FOREIGN KEY users(id) ON DELETE CASCADE, INDEX       | Target user                                                                                                                                                        |
| `type`        | ENUM         | NOT NULL                                                       | 'approval_request', 'settlement_reminder', 'settlement_overdue', 'revision_request', 'announcement', 'block_warning', 'block_notification', 'unblock_notification' |
| `title`       | VARCHAR(255) | NOT NULL                                                       | Notification title shown in dropdown                                                                                                                               |
| `message`     | TEXT         | NULL                                                           | Detailed message (can be long)                                                                                                                                     |
| `entity_type` | VARCHAR(50)  | NULL                                                           | Related entity type: PaymentRequest, RAB, Settlement, User, Announcement                                                                                           |
| `entity_id`   | UUID         | NULL                                                           | Related entity ID                                                                                                                                                  |
| `entity_url`  | VARCHAR(255) | NULL                                                           | Navigation URL when clicked                                                                                                                                        |
| `priority`    | ENUM         | NOT NULL DEFAULT 'medium'                                      | 'critical', 'high', 'medium', 'low'                                                                                                                                |
| `is_read`     | BOOLEAN      | NOT NULL DEFAULT false, INDEX                                  | Read status                                                                                                                                                        |
| `read_at`     | TIMESTAMP    | NULL                                                           | When user marked as read                                                                                                                                           |
| `created_at`  | TIMESTAMP    | NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX                      | Creation timestamp                                                                                                                                                 |
| `updated_at`  | TIMESTAMP    | NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Last update                                                                                                                                                        |
| `deleted_at`  | TIMESTAMP    | NULL, INDEX                                                    | Soft delete timestamp                                                                                                                                              |

**Indexes:**

```sql
CREATE INDEX idx_notifications_user_unread
ON notifications(user_id, is_read, created_at DESC)
WHERE deleted_at IS NULL;

CREATE INDEX idx_notifications_type
ON notifications(type, created_at DESC)
WHERE deleted_at IS NULL;

CREATE INDEX idx_notifications_entity
ON notifications(entity_type, entity_id)
WHERE deleted_at IS NULL;
```

**Why These Indexes:**

- `idx_notifications_user_unread`: Fast unread count query (most common)
- `idx_notifications_type`: Filter by notification type
- `idx_notifications_entity`: Find notifications for specific entity (e.g., "show all notifications for this PR")

---

### 4.5 Notification Dropdown

#### 4.5.1 Dropdown Trigger

**Click Bell Icon:** Toggle dropdown visibility

**JavaScript:**

```javascript
const bellIcon = document.querySelector('.notification-bell');
const dropdown = document.querySelector('.notification-dropdown');

bellIcon.addEventListener('click', (e) => {
    e.stopPropagation();

    if (dropdown.classList.contains('visible')) {
        closeDropdown();
    } else {
        openDropdown();
    }
});

async function openDropdown() {
    dropdown.classList.add('visible');

    // Load notifications
    await loadNotifications();

    // Mark bell as opened (stop any pulsing animation)
    bellIcon.classList.remove('has-new');
}

function closeDropdown() {
    dropdown.classList.remove('visible');
}
```

#### 4.5.2 Load Notifications API

**Endpoint:** `GET /api/v1/notifications?limit=5&unread_only=true`

**Query Parameters:**

| Parameter     | Type    | Required | Default | Description                    |
| ------------- | ------- | -------- | ------- | ------------------------------ |
| `limit`       | Integer | No       | 5       | Max results to return          |
| `unread_only` | Boolean | No       | false   | Show only unread notifications |
| `offset`      | Integer | No       | 0       | Pagination offset              |
| `type`        | String  | No       | All     | Filter by type                 |

**Response:**

```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": "uuid",
        "type": "approval_request",
        "title": "PR-2026-02-00123 needs your approval",
        "message": "Budi Santoso submitted Cash Advance for Rp 3,500,000",
        "entity_type": "PaymentRequest",
        "entity_id": "uuid",
        "entity_url": "/pr/uuid/approval",
        "priority": "high",
        "is_read": false,
        "created_at": "2026-02-08T10:30:00Z"
      },
      ...
    ],
    "total_count": 12,
    "unread_count": 5
  }
}
```

#### 4.5.3 Dropdown Structure

**HTML:**

```html
<div class="notification-dropdown">
    <div class="dropdown-header">
        <span class="title">Notifications</span>
        <a href="/notifications" class="view-all">View All</a>
    </div>

    <div class="notification-list">
        <!-- Notification items render here -->
    </div>

    <div class="dropdown-footer">
        <button class="mark-all-read">Mark All as Read</button>
    </div>
</div>
```

**Dropdown Styling:**

```css
.notification-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 380px;
    max-height: 500px;
    background: white;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    z-index: 1002;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
}

.notification-dropdown.visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-bottom: 1px solid #e5e5e5;
}

.dropdown-header .title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.dropdown-header .view-all {
    font-size: 14px;
    color: #2e5c8a;
    text-decoration: none;
}

.dropdown-header .view-all:hover {
    text-decoration: underline;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
}

.dropdown-footer {
    padding: 12px 16px;
    border-top: 1px solid #e5e5e5;
}

.mark-all-read {
    width: 100%;
    padding: 8px;
    background: #f5f5f5;
    border: none;
    border-radius: 4px;
    color: #666;
    font-size: 14px;
    cursor: pointer;
}

.mark-all-read:hover {
    background: #eeeeee;
}
```

#### 4.5.4 Notification Item Structure

**HTML Template:**

```html
<div
    class="notification-item {is_read ? '' : 'unread'}"
    data-id="{notification.id}"
>
    <div class="icon-container {type}">{icon_svg}</div>

    <div class="content">
        <div class="title">{notification.title}</div>
        <div class="message">{notification.message}</div>
        <div class="timestamp">
            {formatRelativeTime(notification.created_at)}
        </div>
    </div>

    {!is_read &&
    <div class="unread-dot"></div>
    }
</div>
```

**Notification Item Styling:**

```css
.notification-item {
    display: flex;
    padding: 12px 16px;
    border-bottom: 1px solid #f5f5f5;
    cursor: pointer;
    position: relative;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background: #f0f7ff;
}

.icon-container {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.icon-container.approval_request {
    background: #e3f2fd;
}
.icon-container.settlement_reminder {
    background: #fff3e0;
}
.icon-container.settlement_overdue {
    background: #ffebee;
}
.icon-container.revision_request {
    background: #fbe9e7;
}
.icon-container.announcement {
    background: #e8f5e9;
}

.icon-container svg {
    width: 20px;
    height: 20px;
}

.content {
    flex: 1;
    min-width: 0; /* Allow text truncation */
}

.content .title {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin-bottom: 4px;
    /* Truncate after 2 lines */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-item.unread .content .title {
    font-weight: 600;
}

.content .message {
    font-size: 12px;
    color: #666;
    /* Truncate after 2 lines */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.content .timestamp {
    font-size: 11px;
    color: #999;
    margin-top: 4px;
}

.unread-dot {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 8px;
    height: 8px;
    background: #2e5c8a;
    border-radius: 50%;
}
```

#### 4.5.5 Click Notification Item

**Behavior:**

1. Mark notification as read (API call)
2. Navigate to entity URL
3. Close dropdown

**JavaScript:**

```javascript
async function handleNotificationClick(notificationId, entityUrl, isRead) {
    // Close dropdown immediately for better UX
    closeDropdown();

    // Mark as read if unread
    if (!isRead) {
        try {
            await fetch(`/api/v1/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: { Authorization: `Bearer ${getAuthToken()}` },
            });

            // Optimistically decrement badge
            decrementBadge();
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
            // Continue navigation even if marking failed
        }
    }

    // Navigate to entity
    window.location.href = entityUrl;
}

// Attach click handlers
document.querySelectorAll('.notification-item').forEach((item) => {
    item.addEventListener('click', () => {
        const id = item.dataset.id;
        const url = item.dataset.url;
        const isRead = !item.classList.contains('unread');

        handleNotificationClick(id, url, isRead);
    });
});
```

#### 4.5.6 Mark All as Read

**API Endpoint:** `PATCH /api/v1/notifications/mark-all-read`

**JavaScript:**

```javascript
async function markAllAsRead() {
    const button = document.querySelector('.mark-all-read');
    button.disabled = true;
    button.textContent = 'Marking...';

    try {
        await fetch('/api/v1/notifications/mark-all-read', {
            method: 'PATCH',
            headers: { Authorization: `Bearer ${getAuthToken()}` },
        });

        // Update UI
        document
            .querySelectorAll('.notification-item.unread')
            .forEach((item) => {
                item.classList.remove('unread');
                const dot = item.querySelector('.unread-dot');
                if (dot) dot.remove();
            });

        // Set badge to 0
        updateBadge(0);

        // Show success toast
        showToast('All notifications marked as read', 'success');
    } catch (error) {
        console.error('Failed to mark all as read:', error);
        showToast('Failed to mark notifications as read', 'error');
    } finally {
        button.disabled = false;
        button.textContent = 'Mark All as Read';
    }
}
```

---

## 5. USER PROFILE

### 5.1 Display Components

#### 5.1.1 Avatar

**Data Source:** `GET /api/v1/users/me` → `avatar_url` field

**Display Logic:**

| Condition                                   | Display         | Implementation                                 |
| ------------------------------------------- | --------------- | ---------------------------------------------- |
| `avatar_url` exists AND image loads         | Show user photo | `<img src="{avatar_url}" alt="User avatar" />` |
| `avatar_url` exists BUT image fails to load | Show initials   | Fallback to initials generation                |
| `avatar_url` is NULL                        | Show initials   | Generate initials from `name` field            |

**Avatar Styling:**

```css
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e5e5;
}

.user-avatar-initials {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    color: white;
    /* Background color generated from user ID */
}
```

**Initials Generation Function:**

```javascript
function generateInitials(name) {
    if (!name) return '??';

    const parts = name.trim().split(' ');

    if (parts.length === 1) {
        // Single name: use first 2 letters
        return parts[0].substring(0, 2).toUpperCase();
    }

    // Multiple names: first letter of first name + first letter of last name
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

// Examples:
// "Budi" → "BU"
// "Budi Santoso" → "BS"
// "Budi Santoso Putra" → "BP"
```

**Background Color Generation:**

```javascript
function generateAvatarColor(userId) {
    const colors = [
        '#E91E63', // Pink
        '#9C27B0', // Purple
        '#3F51B5', // Indigo
        '#2196F3', // Blue
        '#009688', // Teal
        '#4CAF50', // Green
        '#FF9800', // Orange
        '#795548', // Brown
    ];

    // Hash user ID to select color
    const hash = userId.split('').reduce((acc, char) => {
        return acc + char.charCodeAt(0);
    }, 0);

    return colors[hash % colors.length];
}
```

**HTML Implementation:**

```html
<!-- If avatar_url exists -->
<img
    src="{user.avatar_url}"
    alt="User avatar"
    class="user-avatar"
    onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';"
/>
<div
    class="user-avatar-initials"
    style="display:none; background: {generateAvatarColor(user.id)};"
>
    {generateInitials(user.name)}
</div>

<!-- If avatar_url is NULL -->
<div
    class="user-avatar-initials"
    style="background: {generateAvatarColor(user.id)};"
>
    {generateInitials(user.name)}
</div>
```

#### 5.1.2 Name Display

**Data Source:** `users.name`

**Styling:**

```css
.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.user-name:hover {
    overflow: visible;
    white-space: normal;
    /* Show full name in tooltip */
}
```

**Truncation Logic:**

- If name length > 20 characters: Truncate with "..."
- Show full name in tooltip on hover

**Tooltip Implementation:**

```html
<span class="user-name" title="{user.name}">
    {user.name.length > 20 ? user.name.substring(0, 20) + '...' : user.name}
</span>
```

#### 5.1.3 Role Display

**Data Source:** `users.role` + `users.sub_division` (if applicable)

**Format Logic:**

| Role    | Sub-division   | Display Format           |
| ------- | -------------- | ------------------------ |
| AVP     | N/A            | "AVP of Agri Services"   |
| Manager | "Agri Science" | "Manager - Agri Science" |
| RA      | "Agronomy"     | "RA - Agronomy"          |
| RO      | "Agronomy"     | "RO - Agronomy"          |
| FO      | N/A            | "FO"                     |
| FA      | N/A            | "FA"                     |

**Styling:**

```css
.user-role {
    font-size: 12px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}
```

**Responsive Display:**

- Desktop (>1024px): Show full role
- Tablet (768-1024px): Show role abbreviation only (e.g., "RA - Agronomy" → "RA")
- Mobile (<768px): Hidden (avatar only)

#### 5.1.4 Chevron Icon

**Purpose:** Visual indicator that profile is clickable and has dropdown

**Icon:** SVG chevron-down

```svg
<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
  <path d="M4 6L8 10L12 6" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
```

**Animation:**

```css
.user-profile-chevron {
    transition: transform 0.2s ease;
}

.user-profile.open .user-profile-chevron {
    transform: rotate(180deg);
}
```

### 5.2 Profile Dropdown Menu

#### 5.2.1 Dropdown Structure

**HTML:**

```html
<div class="profile-dropdown">
    <div class="dropdown-item" data-action="profile">
        <svg class="icon user">...</svg>
        <span>View Profile</span>
    </div>

    <div class="dropdown-item" data-action="settings">
        <svg class="icon settings">...</svg>
        <span>Settings</span>
    </div>

    <div class="dropdown-item" data-action="help">
        <svg class="icon help">...</svg>
        <span>Help & Documentation</span>
        <kbd class="shortcut">F1</kbd>
    </div>

    <div class="dropdown-divider"></div>

    <div class="dropdown-item logout" data-action="logout">
        <svg class="icon logout">...</svg>
        <span>Logout</span>
    </div>
</div>
```

**Dropdown Styling:**

```css
.profile-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 240px;
    background: white;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 8px 0;
    z-index: 1002;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
}

.profile-dropdown.visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 10px 16px;
    cursor: pointer;
    color: #333;
    text-decoration: none;
    transition: background 0.15s ease;
}

.dropdown-item:hover {
    background: #f5f5f5;
}

.dropdown-item .icon {
    width: 20px;
    height: 20px;
    margin-right: 12px;
    color: #666;
}

.dropdown-item span {
    flex: 1;
    font-size: 14px;
}

.dropdown-item .shortcut {
    font-size: 11px;
    padding: 2px 6px;
    background: #f5f5f5;
    border: 1px solid #e5e5e5;
    border-radius: 3px;
    color: #666;
}

.dropdown-divider {
    height: 1px;
    background: #e5e5e5;
    margin: 8px 0;
}

.dropdown-item.logout {
    color: #f44336;
}

.dropdown-item.logout .icon {
    color: #f44336;
}
```

#### 5.2.2 Menu Actions

**5.2.2.1 View Profile**

**Navigation:** `/profile`

**Purpose:** User can view/edit their profile information

**Profile Page Content:**

- Personal Information (Name, Email, Phone)
- Avatar upload
- Role & Sub-division (read-only)
- Site assignment (read-only)
- Change Password form
- Two-Factor Authentication setup

---

**5.2.2.2 Settings**

**Navigation:** `/settings`

**Purpose:** User preferences configuration

**Settings Categories:**

**1. Notification Preferences**

```
Email Notifications:
  ☑ Approval requests
  ☑ Settlement reminders
  ☐ Settlement overdue (always enabled, cannot disable)
  ☑ System announcements
  ☐ Weekly digest

In-App Notifications:
  ☑ All notifications (master toggle)

SMS Notifications:
  ☐ Critical notifications only
  Phone number: [+62 812-3456-7890]
```

**2. Language & Region**

```
Language: [Bahasa Indonesia ▼]
  Options: Bahasa Indonesia, English

Timezone: [Asia/Jakarta (GMT+7) ▼]

Date Format: [DD/MM/YYYY ▼]
  Options: DD/MM/YYYY, MM/DD/YYYY, YYYY-MM-DD

Number Format: [1.234.567,89 ▼]
  Options: 1.234.567,89 (ID), 1,234,567.89 (US)

Currency Display: [Rp 1.234.567 ▼]
  Options: Rp 1.234.567, Rp 1,234,567, IDR 1,234,567
```

**3. Appearance**

```
Theme: [Light ▼]
  Options: Light, Dark, Auto (system preference)

Sidebar: [☑ Always collapsed on load]

Compact Mode: [☐ Enable compact mode (reduce spacing)]
```

**4. Privacy & Security**

```
Session Timeout: [30 minutes ▼]
  Options: 15 min, 30 min, 1 hour, 4 hours, Never

Remember Me: [☐ Remember me on this device]

Activity Log: [View my activity log →]

Connected Devices: [Manage devices →]
```

**5. Email Digest**

```
Digest Frequency: [Weekly ▼]
  Options: Never, Daily, Weekly

Send Time: [Monday 08:00 ▼]

Include:
  ☑ Pending approvals summary
  ☑ Overdue settlements
  ☑ Budget utilization
  ☐ System announcements
```

---

**5.2.2.3 Help & Documentation**

**Navigation:** `/help` OR external URL

**Keyboard Shortcut:** F1

**Purpose:** Access help resources

**Help Center Structure:**

```
Help Center
├─ Getting Started Guide
│  ├─ System Overview
│  ├─ User Roles & Permissions
│  └─ Quick Start Tutorial
├─ How-To Guides
│  ├─ Create RAB
│  ├─ Submit Payment Request
│  ├─ Submit Settlement
│  ├─ Approve Requests
│  └─ Generate Reports
├─ Video Tutorials (5-10 min each)
│  ├─ RAB Creation Walkthrough
│  ├─ Payment Request Process
│  └─ Settlement Submission
├─ FAQ (Frequently Asked Questions)
│  ├─ General
│  ├─ Budget Management
│  ├─ Payment Requests
│  └─ Settlements
├─ Troubleshooting
│  ├─ Login Issues
│  ├─ Upload Problems
│  └─ Approval Workflow Issues
└─ Contact Support
   ├─ Submit Ticket
   ├─ Live Chat (FO hours: 8 AM - 5 PM)
   └─ Email: support@agriscience.com
```

**Search Function:**

- Full-text search across all help articles
- Auto-suggest as user types
- "Popular articles" section on homepage

---

**5.2.2.4 Logout**

**Purpose:** Sign out user from system

**Flow:**

**Option 1: Immediate Logout (No Confirmation)**

```javascript
async function logout() {
    try {
        // Call logout API
        await fetch('/api/v1/auth/logout', {
            method: 'POST',
            headers: { Authorization: `Bearer ${getAuthToken()}` },
        });
    } catch (error) {
        console.error('Logout API failed:', error);
        // Continue logout client-side anyway
    }

    // Clear client-side storage
    localStorage.removeItem('auth_token');
    sessionStorage.clear();

    // Close WebSocket connections
    if (window.notificationWebSocket) {
        window.notificationWebSocket.close();
    }

    // Redirect to login
    window.location.href = '/login';
}
```

**Option 2: Logout with Confirmation (Configurable)**

```javascript
async function initiateLogout() {
    // Check for unsaved changes
    const hasUnsavedChanges = checkUnsavedChanges();

    if (hasUnsavedChanges) {
        const confirmed = confirm(
            'You have unsaved changes. Are you sure you want to logout?',
        );
        if (!confirmed) return;
    }

    // Show logout confirmation modal
    showModal({
        title: 'Confirm Logout',
        message: 'Are you sure you want to logout?',
        buttons: [
            {
                text: 'Cancel',
                style: 'secondary',
                onClick: () => closeModal(),
            },
            {
                text: 'Logout',
                style: 'danger',
                onClick: async () => {
                    closeModal();
                    await logout();
                },
            },
        ],
    });
}
```

**Server-Side Logout API:**

**Endpoint:** `POST /api/v1/auth/logout`

**Request:**

```http
POST /api/v1/auth/logout
Headers:
  Authorization: Bearer {jwt_token}
```

**Server Actions:**

1. Invalidate JWT token (add to blacklist if using JWT)
2. Clear session data (selected site, recent searches, etc.)
3. Log logout action in audit trail:
    ```sql
    INSERT INTO audit_log (user_id, action, ip_address, user_agent, created_at)
    VALUES (?, 'Logout', ?, ?, NOW());
    ```

**Response:**

```json
{
    "success": true,
    "message": "Logout successful"
}
```

---

## 6. TECHNICAL SPECIFICATIONS

### 6.1 Performance Requirements

| Metric                        | Target       | Measurement                                  |
| ----------------------------- | ------------ | -------------------------------------------- |
| **Header Load Time**          | <500ms       | Time from page load to header fully rendered |
| **Search API Response**       | <300ms (p95) | API response time for search queries         |
| **Notification API Response** | <200ms (p95) | Unread count + dropdown load                 |
| **WebSocket Connection**      | <1s          | Time to establish WebSocket connection       |
| **Header Height Stability**   | 0 CLS        | No layout shift during load                  |

### 6.2 Responsive Breakpoints

| Device      | Width Range    | Header Adaptations                                                       |
| ----------- | -------------- | ------------------------------------------------------------------------ |
| **Mobile**  | <768px         | Logo only, search icon (expand to fullscreen), bell, avatar only         |
| **Tablet**  | 768px - 1024px | Logo + primary name, search 400px, bell, avatar + name                   |
| **Desktop** | >1024px        | Full layout: logo + both names, search 600px, bell, avatar + name + role |

### 6.3 Browser Support

**Minimum Supported Versions:**

- Chrome: Latest 2 major versions
- Firefox: Latest 2 major versions
- Safari: Latest 2 major versions
- Edge: Latest 2 major versions

**Graceful Degradation:**

- If WebSocket not supported: Fallback to polling
- If CSS Grid not supported: Fallback to Flexbox
- If localStorage not available: Use sessionStorage

### 6.4 Accessibility (WCAG 2.1 Level A)

| Requirement             | Implementation                                                       |
| ----------------------- | -------------------------------------------------------------------- |
| **Keyboard Navigation** | All interactive elements accessible via Tab, Enter/Space to activate |
| **Focus Indicators**    | Visible 3px blue outline on focus                                    |
| **ARIA Labels**         | All icons have aria-label attributes                                 |
| **Screen Reader**       | Semantic HTML, proper heading hierarchy, alt text for images         |
| **Color Contrast**      | Minimum 4.5:1 ratio for text, 3:1 for large text                     |
| **Skip Links**          | "Skip to main content" link at top of page                           |

---

## 7. API ENDPOINTS SUMMARY

| Endpoint                              | Method    | Purpose                        | Auth Required        |
| ------------------------------------- | --------- | ------------------------------ | -------------------- |
| `/api/v1/search`                      | GET       | Global search across entities  | Yes                  |
| `/api/v1/search/recent`               | POST      | Save recent search             | Yes                  |
| `/api/v1/search/recent`               | DELETE    | Clear recent searches          | Yes                  |
| `/api/v1/notifications/unread-count`  | GET       | Get unread notification count  | Yes                  |
| `/api/v1/notifications`               | GET       | Get notification list          | Yes                  |
| `/api/v1/notifications/{id}/read`     | PATCH     | Mark notification as read      | Yes                  |
| `/api/v1/notifications/mark-all-read` | PATCH     | Mark all notifications as read | Yes                  |
| `/api/v1/users/me`                    | GET       | Get current user profile       | Yes                  |
| `/api/v1/auth/logout`                 | POST      | Logout user                    | Yes                  |
| `/api/v1/session/site`                | DELETE    | Clear selected site session    | Yes                  |
| `/ws/notifications`                   | WebSocket | Real-time notification updates | Yes (via auth token) |

---

## 8. DATABASE SCHEMA

### 8.1 Notifications Table

See section 4.4.1 for complete schema.

### 8.2 Recent Searches (Redis)

**Key:** `user:{user_id}:recent_searches`

**Data Type:** List (LPUSH/LRANGE)

**Value:** Array of search query strings

**TTL:** 30 days

**Max Items:** 10 (trim older items)

---

## CONCLUSION

This completes the **Section 1: Header Bar** PRD. This document provides comprehensive specifications down to the root level, including:

✅ Component structure & styling  
✅ Data sources & API endpoints  
✅ Business logic & validation rules  
✅ Database schemas & indexes  
✅ Error handling & edge cases  
✅ Performance metrics & targets  
✅ Accessibility requirements  
✅ Code implementation examples

**Next Documents:**

- PRD Section 2: Quick Actions
- PRD Section 3: Program Launcher
- PRD Section 4: System Settings (largest section)
- PRD Section 5: Information Section

---

**Document Control:**

| Version | Date        | Author           | Changes                   |
| ------- | ----------- | ---------------- | ------------------------- |
| 1.0     | 08 Feb 2026 | System Architect | Initial comprehensive PRD |

---

_End of Section 1 PRD_
