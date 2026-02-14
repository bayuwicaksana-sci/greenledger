# PRODUCT REQUIREMENT DOCUMENT

## LANDING PAGE - SECTION 5: INFORMATION SECTION

**AgriScience Finance Management System**  
**Version:** 1.0  
**Date:** 08 Februari 2026  
**Document:** 5 of 5 (Final)

---

## TABLE OF CONTENTS

1. [Overview](#1-overview)
2. [Latest Announcement Component](#2-latest-announcement-component)
3. [Finance Terms & Conditions](#3-finance-terms--conditions)
4. [Help & Support Links](#4-help--support-links)
5. [Footer Information](#5-footer-information)
6. [Visual Design & Styling](#6-visual-design--styling)
7. [Interaction Behaviors](#7-interaction-behaviors)
8. [Technical Specifications](#8-technical-specifications)
9. [API Endpoints Summary](#9-api-endpoints-summary)
10. [Database Queries](#10-database-queries)

---

## 1. OVERVIEW

### 1.1 Purpose

Information Section adalah **informational footer** pada Landing Page yang menyediakan:

- Latest system announcement (if any)
- Finance policy & terms reminders
- Quick access to help resources
- System metadata (version, last updated)

### 1.2 Display Condition

**Always Show:** Information Section displayed untuk ALL users (no role restrictions)

### 1.3 Layout Specifications

| Property            | Specification                                 | Rationale                             |
| ------------------- | --------------------------------------------- | ------------------------------------- |
| **Position**        | Bottom of Landing Page, after System Settings | Footer area for secondary information |
| **Container Width** | Max 1200px, centered                          | Consistent with other sections        |
| **Section Padding** | 32px top/bottom, 24px left/right              | Adequate spacing                      |
| **Background**      | #F8F9FA (Very light gray)                     | Subtle distinction from white         |
| **Border Top**      | 2px solid #E5E5E5                             | Clear section separator               |

### 1.4 Component Layout

**Desktop (>1024px):** 3-column layout

```
[Latest Announcement - 50%]  |  [Finance T&C - 25%]  |  [Help & Support - 25%]
```

**Tablet (768-1024px):** 2-column layout

```
[Latest Announcement - 100%]
[Finance T&C - 50%]  |  [Help & Support - 50%]
```

**Mobile (<768px):** Stacked (1 column)

```
[Latest Announcement]
[Finance T&C]
[Help & Support]
```

---

## 2. LATEST ANNOUNCEMENT COMPONENT

### 2.1 Purpose

Display most recent pinned or active announcement from System Settings > Announcements module.

### 2.2 Data Source

#### 2.2.1 SQL Query

```sql
SELECT
  id,
  title,
  message,
  priority,
  created_by,
  created_at,
  expires_at
FROM announcements
WHERE deleted_at IS NULL
  AND (expires_at IS NULL OR expires_at > NOW())
  AND (
    is_pinned = TRUE
    OR created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  )
ORDER BY
  is_pinned DESC,
  created_at DESC
LIMIT 1;
```

**Query Logic:**

- Active announcements only (not deleted, not expired)
- Pinned announcements take priority
- Or show announcements created within last 7 days
- Order by pinned first, then most recent

#### 2.2.2 API Endpoint

**Endpoint:** `GET /api/v1/announcements/latest`

**Request:**

```http
GET /api/v1/announcements/latest
Headers:
  Authorization: Bearer {jwt_token}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "announcement": {
            "id": "uuid-announcement",
            "title": "System Maintenance Schedule",
            "message": "The system will undergo scheduled maintenance on Feb 15, 2026 from 22:00 to 23:00 WIB. Please complete urgent settlements before this time.",
            "priority": "Warning",
            "created_by": {
                "id": "uuid-manager",
                "name": "Dewi Kartika",
                "role": "Manager"
            },
            "created_at": "2026-02-08T09:00:00Z",
            "expires_at": "2026-02-15T23:00:00Z"
        }
    }
}
```

**If no announcement:**

```json
{
    "success": true,
    "data": {
        "announcement": null
    }
}
```

### 2.3 Display Format

#### 2.3.1 Component Structure

**HTML:**

```html
<div class="info-component announcement-component">
    <div class="component-header">
        <svg class="header-icon">{megaphone_icon}</svg>
        <h3 class="component-title">Latest Announcement</h3>
    </div>

    <div class="announcement-content">
        <!-- If announcement exists -->
        <div class="announcement-card {priority-class}">
            <div class="announcement-header">
                <h4 class="announcement-title">{title}</h4>
                <span class="priority-badge {priority}">{priority}</span>
            </div>

            <p class="announcement-message">{message}</p>

            <div class="announcement-footer">
                <div class="author-info">
                    <svg class="icon user">👤</svg>
                    <span>{created_by_name}</span>
                </div>
                <div class="date-info">
                    <svg class="icon calendar">📅</svg>
                    <span>{relative_date}</span>
                </div>
            </div>
        </div>

        <!-- If no announcement -->
        <div class="no-announcement">
            <svg class="icon info">ℹ️</svg>
            <p>No announcements at this time</p>
        </div>
    </div>
</div>
```

#### 2.3.2 Priority-Based Styling

**Priority Levels:**

| Priority     | Border Color     | Background | Badge Color | Icon |
| ------------ | ---------------- | ---------- | ----------- | ---- |
| **Info**     | #2196F3 (Blue)   | #E3F2FD    | #1976D2     | ℹ️   |
| **Warning**  | #FF9800 (Orange) | #FFF3E0    | #E65100     | ⚠️   |
| **Critical** | #F44336 (Red)    | #FFEBEE    | #C62828     | 🚨   |

**CSS Implementation:**

```css
.announcement-card {
    border-left: 4px solid;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
}

.announcement-card.info {
    border-left-color: #2196f3;
    background: #e3f2fd;
}

.announcement-card.warning {
    border-left-color: #ff9800;
    background: #fff3e0;
}

.announcement-card.critical {
    border-left-color: #f44336;
    background: #ffebee;
}

.priority-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.priority-badge.info {
    background: #1976d2;
    color: white;
}

.priority-badge.warning {
    background: #e65100;
    color: white;
}

.priority-badge.critical {
    background: #c62828;
    color: white;
}
```

#### 2.3.3 Message Truncation

**Display Rules:**

- Show full message if ≤150 characters
- Truncate if >150 characters, add "Read More" link
- Click "Read More" → Expand inline OR open modal with full content

**Truncation Logic:**

```javascript
function formatAnnouncementMessage(message) {
    const maxLength = 150;

    if (message.length <= maxLength) {
        return message;
    }

    return {
        truncated: message.substring(0, maxLength) + '...',
        full: message,
        needsExpansion: true,
    };
}
```

**Expandable UI:**

```html
<p class="announcement-message" id="announcement-msg">
    {truncated_message}
    <button class="read-more-btn" onclick="toggleMessage()">Read More</button>
</p>
```

```javascript
function toggleMessage() {
    const msgElement = document.getElementById('announcement-msg');
    const btn = msgElement.querySelector('.read-more-btn');

    if (msgElement.classList.contains('expanded')) {
        msgElement.textContent = truncatedMessage;
        msgElement.appendChild(btn);
        btn.textContent = 'Read More';
        msgElement.classList.remove('expanded');
    } else {
        msgElement.textContent = fullMessage;
        msgElement.appendChild(btn);
        btn.textContent = 'Show Less';
        msgElement.classList.add('expanded');
    }
}
```

#### 2.3.4 Date Formatting

**Relative Time Display:**

| Time Difference | Display Format  | Example          |
| --------------- | --------------- | ---------------- |
| < 1 hour        | "X minutes ago" | "15 minutes ago" |
| 1-24 hours      | "X hours ago"   | "5 hours ago"    |
| 1-7 days        | "X days ago"    | "3 days ago"     |
| > 7 days        | "MMM DD, YYYY"  | "Feb 08, 2026"   |

**JavaScript Formatter:**

```javascript
function formatRelativeTime(timestamp) {
    const now = new Date();
    const date = new Date(timestamp);
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 60) {
        return `${diffMins} minute${diffMins !== 1 ? 's' : ''} ago`;
    } else if (diffHours < 24) {
        return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
    } else if (diffDays <= 7) {
        return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;
    } else {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        });
    }
}
```

### 2.4 No Announcement State

**Display Condition:** When API returns `announcement: null`

**Structure:**

```html
<div class="no-announcement">
    <svg class="icon info" width="48" height="48">
        <!-- Info icon -->
    </svg>
    <p class="no-announcement-text">No announcements at this time</p>
    <p class="no-announcement-subtext">Check back later for updates</p>
</div>
```

**Styling:**

```css
.no-announcement {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 32px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px dashed #cccccc;
}

.no-announcement .icon {
    opacity: 0.3;
    margin-bottom: 12px;
}

.no-announcement-text {
    font-size: 14px;
    color: #666;
    margin: 0 0 4px 0;
}

.no-announcement-subtext {
    font-size: 12px;
    color: #999;
    margin: 0;
}
```

---

## 3. FINANCE TERMS & CONDITIONS

### 3.1 Purpose

Quick reference untuk key financial policies yang sering dilupakan users.

### 3.2 Content

**Static Content (No API):**

**Displayed Items:**

1. **Settlement Deadline**
    - Icon: ⏱️ Clock
    - Text: "Submit settlement within 2x24 hours after disbursement"
    - Subtext: "Late settlements result in account block"

2. **Budget Allocation**
    - Icon: 💰 Money Bag
    - Text: "Request cannot exceed remaining budget"
    - Subtext: "Check budget availability before submission"

3. **Procurement Threshold**
    - Icon: 🛒 Shopping Cart
    - Text: "Items >1M per item require Procurement review"
    - Subtext: "FO will flag automatically"

4. **Document Retention**
    - Icon: 📁 Folder
    - Text: "Physical documents: 5 years, Digital: 3 years active"
    - Subtext: "Cold storage after 3 years"

### 3.3 Component Structure

**HTML:**

```html
<div class="info-component terms-component">
    <div class="component-header">
        <svg class="header-icon">{clipboard_icon}</svg>
        <h3 class="component-title">Finance Terms</h3>
    </div>

    <div class="terms-content">
        <div class="term-item">
            <div class="term-icon">⏱️</div>
            <div class="term-text">
                <p class="term-title">Settlement Deadline</p>
                <p class="term-description">
                    Submit within 2x24 hours after disbursement
                </p>
            </div>
        </div>

        <div class="term-item">
            <div class="term-icon">💰</div>
            <div class="term-text">
                <p class="term-title">Budget Allocation</p>
                <p class="term-description">Cannot exceed remaining budget</p>
            </div>
        </div>

        <div class="term-item">
            <div class="term-icon">🛒</div>
            <div class="term-text">
                <p class="term-title">Procurement Threshold</p>
                <p class="term-description">
                    Items >1M require Procurement review
                </p>
            </div>
        </div>

        <div class="term-item">
            <div class="term-icon">📁</div>
            <div class="term-text">
                <p class="term-title">Document Retention</p>
                <p class="term-description">
                    Physical: 5yr, Digital: 3yr active
                </p>
            </div>
        </div>
    </div>

    <a href="/policies/finance" class="view-full-link">
        View Full Terms & Conditions →
    </a>
</div>
```

### 3.4 Styling

```css
.terms-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.term-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    background: white;
    border-radius: 6px;
    border: 1px solid #e5e5e5;
    transition: background 0.2s ease;
}

.term-item:hover {
    background: #f8f9fa;
}

.term-icon {
    font-size: 24px;
    flex-shrink: 0;
    width: 32px;
    text-align: center;
}

.term-text {
    flex: 1;
}

.term-title {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin: 0 0 4px 0;
}

.term-description {
    font-size: 12px;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

.view-full-link {
    display: inline-block;
    margin-top: 12px;
    font-size: 13px;
    color: #2e5c8a;
    text-decoration: none;
    font-weight: 500;
}

.view-full-link:hover {
    text-decoration: underline;
}
```

### 3.5 Full Terms Page

**Link:** `/policies/finance`

**Content Structure:**

**Page Sections:**

1. Settlement Policies
    - Deadline requirements
    - Evidence requirements
    - Late settlement penalties
    - Extension procedures

2. Budget Management
    - RAB creation rules
    - Budget reallocation policies
    - Budget addition approval
    - Budget tracking guidelines

3. Payment Request Policies
    - Request types (Cash Advance, Reimbursement, Mixed)
    - Approval matrix
    - SLA commitments
    - Urgent request criteria

4. Procurement Rules
    - Threshold amounts
    - Vendor selection
    - Quotation requirements
    - Contract requirements

5. Document Management
    - Retention periods
    - Storage locations
    - Access policies
    - Disposal procedures

6. Audit & Compliance
    - Audit trail requirements
    - Compliance checks
    - Reporting obligations
    - External audit procedures

**Format:** Long-form HTML page with table of contents, printable PDF version available

---

## 4. HELP & SUPPORT LINKS

### 4.1 Purpose

Quick access to help resources and support channels.

### 4.2 Content

**Static Links (No API):**

**Displayed Items:**

1. **User Guide**
    - Icon: 📖 Book
    - Link: `/help/user-guide`
    - Description: "Complete system documentation"

2. **Video Tutorials**
    - Icon: 🎥 Video Camera
    - Link: `/help/videos`
    - Description: "Step-by-step video guides"

3. **FAQ**
    - Icon: ❓ Question Mark
    - Link: `/help/faq`
    - Description: "Frequently asked questions"

4. **Contact Support**
    - Icon: 💬 Chat Bubble
    - Link: `mailto:support@agriscience.com`
    - Description: "Email: support@agriscience.com"

5. **Report Issue**
    - Icon: 🐛 Bug
    - Link: `/support/report-issue`
    - Description: "Report bugs or issues"

### 4.3 Component Structure

**HTML:**

```html
<div class="info-component help-component">
    <div class="component-header">
        <svg class="header-icon">{help_icon}</svg>
        <h3 class="component-title">Help & Support</h3>
    </div>

    <div class="help-content">
        <a href="/help/user-guide" class="help-link">
            <div class="help-icon">📖</div>
            <div class="help-text">
                <p class="help-title">User Guide</p>
                <p class="help-description">Complete system documentation</p>
            </div>
            <svg class="arrow-icon">→</svg>
        </a>

        <a href="/help/videos" class="help-link">
            <div class="help-icon">🎥</div>
            <div class="help-text">
                <p class="help-title">Video Tutorials</p>
                <p class="help-description">Step-by-step video guides</p>
            </div>
            <svg class="arrow-icon">→</svg>
        </a>

        <a href="/help/faq" class="help-link">
            <div class="help-icon">❓</div>
            <div class="help-text">
                <p class="help-title">FAQ</p>
                <p class="help-description">Frequently asked questions</p>
            </div>
            <svg class="arrow-icon">→</svg>
        </a>

        <a href="mailto:support@agriscience.com" class="help-link">
            <div class="help-icon">💬</div>
            <div class="help-text">
                <p class="help-title">Contact Support</p>
                <p class="help-description">support@agriscience.com</p>
            </div>
            <svg class="arrow-icon">→</svg>
        </a>

        <a href="/support/report-issue" class="help-link">
            <div class="help-icon">🐛</div>
            <div class="help-text">
                <p class="help-title">Report Issue</p>
                <p class="help-description">Report bugs or problems</p>
            </div>
            <svg class="arrow-icon">→</svg>
        </a>
    </div>

    <div class="support-hours">
        <svg class="icon clock">🕐</svg>
        <span>Support Hours: Mon-Fri, 8 AM - 5 PM WIB</span>
    </div>
</div>
```

### 4.4 Styling

```css
.help-content {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.help-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: white;
    border-radius: 6px;
    border: 1px solid #e5e5e5;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.help-link:hover {
    background: #f0f7ff;
    border-color: #2e5c8a;
    transform: translateX(4px);
}

.help-icon {
    font-size: 24px;
    flex-shrink: 0;
    width: 32px;
    text-align: center;
}

.help-text {
    flex: 1;
}

.help-title {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin: 0 0 2px 0;
}

.help-description {
    font-size: 11px;
    color: #666;
    margin: 0;
}

.help-link .arrow-icon {
    width: 16px;
    height: 16px;
    color: #999;
    transition: transform 0.2s ease;
}

.help-link:hover .arrow-icon {
    transform: translateX(4px);
    color: #2e5c8a;
}

.support-hours {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding: 8px 12px;
    background: #fff3e0;
    border-radius: 6px;
    font-size: 12px;
    color: #e65100;
}

.support-hours .icon {
    font-size: 16px;
}
```

### 4.5 Keyboard Shortcut

**F1 Key:** Open help modal

**Implementation:**

```javascript
document.addEventListener('keydown', (e) => {
    if (e.key === 'F1') {
        e.preventDefault();
        openHelpModal();
    }
});

function openHelpModal() {
    // Show help modal with quick actions
    showModal({
        title: 'Quick Help',
        content: `
      <div class="help-modal-content">
        <h4>Common Actions:</h4>
        <ul>
          <li><strong>Create RAB:</strong> Navigate to site → Budget Management → Create RAB</li>
          <li><strong>Submit Payment Request:</strong> Navigate to site → Payment Requests → New Request</li>
          <li><strong>Submit Settlement:</strong> Check Quick Actions for pending settlements</li>
        </ul>
        
        <h4>Need More Help?</h4>
        <div class="help-links">
          <a href="/help/user-guide">User Guide</a>
          <a href="/help/videos">Video Tutorials</a>
          <a href="/help/faq">FAQ</a>
          <a href="mailto:support@agriscience.com">Contact Support</a>
        </div>
      </div>
    `,
        buttons: [{ text: 'Close', style: 'primary', onClick: closeModal }],
    });
}
```

---

## 5. FOOTER INFORMATION

### 5.1 System Metadata

**Display at Bottom:**

```html
<div class="system-footer">
    <div class="footer-left">
        <span class="system-name">AgriScience Finance System</span>
        <span class="system-version">v1.0.0</span>
    </div>

    <div class="footer-center">
        <span class="copyright">© 2026 Agriculture Science Division</span>
    </div>

    <div class="footer-right">
        <span class="last-updated">Last Updated: Feb 08, 2026</span>
        <a href="/privacy-policy" class="footer-link">Privacy Policy</a>
        <a href="/terms-of-service" class="footer-link">Terms of Service</a>
    </div>
</div>
```

### 5.2 Styling

```css
.system-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    margin-top: 32px;
    border-top: 1px solid #e5e5e5;
    font-size: 12px;
    color: #666;
}

.footer-left,
.footer-center,
.footer-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.system-name {
    font-weight: 600;
    color: #333;
}

.system-version {
    padding: 2px 8px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
}

.footer-link {
    color: #2e5c8a;
    text-decoration: none;
}

.footer-link:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .system-footer {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }

    .footer-left,
    .footer-center,
    .footer-right {
        flex-direction: column;
        gap: 4px;
    }
}
```

---

## 6. VISUAL DESIGN & STYLING

### 6.1 Component Header Pattern

**Consistent Across All 3 Components:**

```css
.component-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e5e5;
}

.header-icon {
    width: 24px;
    height: 24px;
    color: #2e5c8a;
}

.component-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
}
```

### 6.2 Info Component Container

```css
.info-component {
    background: white;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e5e5e5;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Grid layout */
.information-section {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 20px;
    margin-bottom: 32px;
}

@media (max-width: 1024px) {
    .information-section {
        grid-template-columns: 1fr;
    }

    .announcement-component {
        grid-column: 1 / -1;
    }

    .information-section {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .information-section {
        grid-template-columns: 1fr;
    }
}
```

### 6.3 Color Palette Summary

| Element                    | Color   | Usage                            |
| -------------------------- | ------- | -------------------------------- |
| **Primary (Links, Icons)** | #2E5C8A | Headings, links, primary actions |
| **Text Primary**           | #333333 | Main text content                |
| **Text Secondary**         | #666666 | Descriptions, subtext            |
| **Text Tertiary**          | #999999 | Hints, metadata                  |
| **Border**                 | #E5E5E5 | Dividers, card borders           |
| **Background**             | #F8F9FA | Section background               |
| **Card Background**        | #FFFFFF | Component cards                  |
| **Info**                   | #2196F3 | Info announcements               |
| **Warning**                | #FF9800 | Warning announcements            |
| **Critical**               | #F44336 | Critical announcements           |

---

## 7. INTERACTION BEHAVIORS

### 7.1 Announcement Expand/Collapse

**For long messages:**

```javascript
let isExpanded = false;

function toggleAnnouncementMessage() {
    const msgElement = document.getElementById('announcement-message');
    const toggleBtn = document.getElementById('toggle-btn');

    if (isExpanded) {
        msgElement.textContent = truncatedMessage + '...';
        toggleBtn.textContent = 'Read More';
        isExpanded = false;
    } else {
        msgElement.textContent = fullMessage;
        toggleBtn.textContent = 'Show Less';
        isExpanded = true;
    }
}
```

### 7.2 External Link Handling

**For help links that open external resources:**

```javascript
document.querySelectorAll('.help-link[target="_blank"]').forEach((link) => {
    link.addEventListener('click', (e) => {
        // Analytics tracking
        trackEvent('Help Link Clicked', {
            link_text: link.textContent,
            link_url: link.href,
        });
    });
});
```

### 7.3 Tooltip for Terms

**Hover over term item shows full detail:**

```html
<div class="term-item" data-tooltip="Full policy explanation appears here...">
    <!-- Term content -->
</div>
```

```css
.term-item[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 8px 12px;
    background: #333;
    color: white;
    font-size: 12px;
    border-radius: 4px;
    white-space: normal;
    max-width: 300px;
    z-index: 100;
    margin-bottom: 8px;
}
```

### 7.4 Loading States

**Announcement Loading:**

```html
<div class="announcement-skeleton">
    <div class="skeleton-header"></div>
    <div class="skeleton-message"></div>
    <div class="skeleton-footer"></div>
</div>
```

```css
.skeleton-header,
.skeleton-message,
.skeleton-footer {
    background: linear-gradient(90deg, #f5f5f5 0px, #eeeeee 40px, #f5f5f5 80px);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
    border-radius: 4px;
}

.skeleton-header {
    width: 60%;
    height: 20px;
    margin-bottom: 12px;
}

.skeleton-message {
    width: 100%;
    height: 60px;
    margin-bottom: 12px;
}

.skeleton-footer {
    width: 40%;
    height: 16px;
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}
```

---

## 8. TECHNICAL SPECIFICATIONS

### 8.1 Performance Requirements

| Metric                  | Target | Measurement                       |
| ----------------------- | ------ | --------------------------------- |
| **Announcement API**    | <300ms | Time to fetch latest announcement |
| **Section Render**      | <200ms | Time to render all 3 components   |
| **Link Click Response** | <50ms  | Navigation to help pages          |
| **Initial Load**        | <500ms | Full section visible              |

### 8.2 Caching Strategy

**Announcement Cache:**

```javascript
const ANNOUNCEMENT_CACHE_DURATION = 10 * 60 * 1000; // 10 minutes

async function fetchLatestAnnouncement(useCache = true) {
    const cacheKey = 'latest_announcement';

    if (useCache) {
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            if (Date.now() - timestamp < ANNOUNCEMENT_CACHE_DURATION) {
                return data;
            }
        }
    }

    const response = await fetch('/api/v1/announcements/latest');
    const result = await response.json();

    sessionStorage.setItem(
        cacheKey,
        JSON.stringify({
            data: result.data,
            timestamp: Date.now(),
        }),
    );

    return result.data;
}
```

**Invalidate Cache:**

- When Manager/AVP posts new announcement
- Manual refresh by user
- After 10 minutes auto-expiry

### 8.3 Accessibility

| Requirement             | Implementation                                     |
| ----------------------- | -------------------------------------------------- |
| **Keyboard Navigation** | All links accessible via Tab                       |
| **Screen Reader**       | Proper ARIA labels on all components               |
| **Focus Indicators**    | Visible focus outline (3px blue)                   |
| **Alt Text**            | All icons have aria-label                          |
| **Semantic HTML**       | Proper heading hierarchy (h3 for component titles) |
| **Color Contrast**      | All text meets WCAG AA (4.5:1)                     |

### 8.4 Responsive Breakpoints

| Device                  | Layout Change                                                |
| ----------------------- | ------------------------------------------------------------ |
| **Desktop (>1024px)**   | 3-column layout (Announcement 50%, Terms 25%, Help 25%)      |
| **Tablet (768-1024px)** | Announcement full-width, then 2-column (Terms 50%, Help 50%) |
| **Mobile (<768px)**     | Stacked 1-column, all components full-width                  |

---

## 9. API ENDPOINTS SUMMARY

| Endpoint                       | Method | Purpose                        | Cache  | Response Time |
| ------------------------------ | ------ | ------------------------------ | ------ | ------------- |
| `/api/v1/announcements/latest` | GET    | Get latest active announcement | 10 min | <300ms        |

**Note:** Finance Terms and Help Links are static content (no API calls)

---

## 10. DATABASE QUERIES

### 10.1 Latest Announcement Query

**Optimized Query:**

```sql
SELECT
  id,
  title,
  message,
  priority,
  created_by,
  created_at,
  expires_at,
  is_pinned
FROM announcements
WHERE deleted_at IS NULL
  AND (expires_at IS NULL OR expires_at > NOW())
  AND (
    is_pinned = TRUE
    OR created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  )
ORDER BY
  is_pinned DESC,
  priority DESC,  -- Critical > Warning > Info
  created_at DESC
LIMIT 1;
```

**Required Index:**

```sql
CREATE INDEX idx_announcements_active
ON announcements(is_pinned, priority, created_at DESC)
WHERE deleted_at IS NULL;
```

**Query Performance:**

- Target: <100ms
- Use covering index
- Limit 1 for efficiency

### 10.2 Query Explanation

**Why This Query:**

1. Filter active announcements (not deleted, not expired)
2. Prioritize pinned announcements
3. Show recent announcements (last 7 days) if no pinned
4. Order by importance: pinned > priority > recency
5. Fetch only 1 result (most important)

**Performance Optimization:**

- Index on (is_pinned, priority, created_at)
- Partial index with WHERE deleted_at IS NULL
- LIMIT 1 to stop after first match
- Avoid SELECT \* (only fetch needed columns)

---

## CONCLUSION

This completes **Section 5: Information Section** - the **FINAL** section of the Landing Page PRD series! 🎉

### What This Document Covers:

✅ **Latest Announcement** - Priority-based display, message truncation, relative dates  
✅ **Finance Terms** - 4 key policy reminders with visual styling  
✅ **Help & Support** - 5 help links with keyboard shortcut (F1)  
✅ **System Footer** - Metadata, version, copyright, links  
✅ **Visual Design** - Consistent styling, responsive layout  
✅ **Interactions** - Expand/collapse, tooltips, loading states  
✅ **Performance** - Caching strategy, API optimization  
✅ **Database** - Optimized query with proper indexing

---

## COMPLETE LANDING PAGE PRD SERIES SUMMARY

**All 5 Documents Completed:**

1. ✅ **Section 1: Header Bar** (50+ pages)
    - Logo, Global Search, Notification Bell, User Profile

2. ✅ **Section 2: Quick Actions** (45+ pages)
    - 4 Alert types with full logic

3. ✅ **Section 3: Program Launcher** (35+ pages)
    - Site selection, session management

4. ✅ **Section 4: System Settings** (80+ pages)
    - 6 administrative modules

5. ✅ **Section 5: Information Section** (15+ pages)
    - Announcements, Terms, Help & Support

**Total Documentation:** ~225 pages of comprehensive PRD

**Coverage:**

- Field-level specifications
- Validation rules
- SQL queries
- API endpoints
- Database schemas
- Business logic
- Error handling
- Performance targets
- Accessibility compliance

---

**Document Control:**

| Version | Date        | Author           | Changes                                   |
| ------- | ----------- | ---------------- | ----------------------------------------- |
| 1.0     | 08 Feb 2026 | System Architect | Initial comprehensive PRD - FINAL SECTION |

---

_End of Section 5 PRD_

🎉 **COMPLETE LANDING PAGE PRD SERIES FINISHED!** 🎉
