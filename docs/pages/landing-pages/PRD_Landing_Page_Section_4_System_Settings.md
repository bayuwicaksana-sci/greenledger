# PRODUCT REQUIREMENT DOCUMENT

## LANDING PAGE - SECTION 4: SYSTEM SETTINGS

**AgriScience Finance Management System**  
**Version:** 1.0  
**Date:** 08 Februari 2026  
**Document:** 4 of 5

---

## TABLE OF CONTENTS

1. [Overview](#1-overview)
2. [Access Control](#2-access-control)
3. [Module 1: User Management](#3-module-1-user-management)
4. [Module 2: Roles & Permissions](#4-module-2-roles--permissions)
5. [Module 3: Station Configuration](#5-module-3-station-configuration)
6. [Module 4: Global COA](#6-module-4-global-coa)
7. [Module 5: Approval Workflow](#7-module-5-approval-workflow)
8. [Module 6: Announcements](#8-module-6-announcements)
9. [Technical Specifications](#9-technical-specifications)
10. [API Endpoints Summary](#10-api-endpoints-summary)
11. [Database Schemas](#11-database-schemas)

---

## 1. OVERVIEW

### 1.1 Purpose

System Settings Section adalah **administrative control panel** yang exclusively accessible by Manager dan AVP untuk system configuration, user management, dan operational parameter setup.

### 1.2 Display Condition

**Visibility:** Show ONLY if user role = Manager OR AVP

```javascript
function shouldShowSystemSettings(userRole) {
    return ['Manager', 'AVP'].includes(userRole);
}
```

**If user role is NOT Manager/AVP:** Section completely hidden (tidak ada placeholder atau disabled state)

### 1.3 Layout Specifications

| Property            | Specification                                     | Rationale                                    |
| ------------------- | ------------------------------------------------- | -------------------------------------------- |
| **Position**        | Below Program Launcher, above Information Section | Administrative functions grouped together    |
| **Section Title**   | "System Settings"                                 | Clear indication of admin area               |
| **Container Width** | Max 1200px, centered                              | Consistent with other sections               |
| **Section Padding** | 40px top/bottom, 24px left/right                  | Ample separation                             |
| **Background**      | #FAFAFA (Very light gray)                         | Visual distinction from operational sections |
| **Border Top**      | 2px solid #E5E5E5                                 | Strong separator                             |

### 1.4 Module Grid Layout

**Desktop (>1024px):** 3 columns

```
[User Mgmt]  [Roles]      [Stations]
[COA]        [Workflow]   [Announce]
```

**Tablet (768-1024px):** 2 columns

```
[User Mgmt]  [Roles]
[Stations]   [COA]
[Workflow]   [Announce]
```

**Mobile (<768px):** 1 column (stacked)

### 1.5 Module Card Specifications

**Common Card Properties:**

| Property          | Value                                        | CSS                      |
| ----------------- | -------------------------------------------- | ------------------------ |
| **Width**         | ~32% (desktop), ~48% (tablet), 100% (mobile) | Responsive flex          |
| **Min Height**    | 200px                                        | Consistent card size     |
| **Background**    | #FFFFFF                                      | White on gray section bg |
| **Border**        | 1px solid #E5E5E5                            | Subtle boundary          |
| **Border Radius** | 8px                                          | Rounded corners          |
| **Padding**       | 24px                                         | Adequate content spacing |
| **Gap**           | 16px between cards                           | Clear separation         |
| **Cursor**        | pointer                                      | Entire card clickable    |
| **Hover Shadow**  | 0 4px 12px rgba(0,0,0,0.1)                   | Depth on hover           |
| **Transition**    | all 0.2s ease                                | Smooth interaction       |

**CSS Implementation:**

```css
.settings-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 24px;
}

.settings-module-card {
    background: white;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 24px;
    min-height: 200px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
}

.settings-module-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    border-color: #2e5c8a;
}

@media (max-width: 1024px) {
    .settings-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
}
```

---

## 2. ACCESS CONTROL

### 2.1 Role-Based Module Access

| Module                    | Manager Access                      | AVP Access                        | Notes                         |
| ------------------------- | ----------------------------------- | --------------------------------- | ----------------------------- |
| **User Management**       | ✅ Full (Create, Edit, Block, View) | ✅ Full                           | Both can manage all users     |
| **Roles & Permissions**   | ✅ View, Edit approval matrix       | ✅ Full (including role creation) | AVP can modify system roles   |
| **Station Configuration** | ✅ Edit stations, assign FA         | ✅ Full                           | Both manage multi-site setup  |
| **Global COA**            | ✅ Full (Create, Edit, Deactivate)  | ✅ Full                           | Critical for budget mapping   |
| **Approval Workflow**     | ✅ Edit SLA, view workflow          | ✅ Full (modify routing logic)    | AVP controls system workflow  |
| **Announcements**         | ✅ Full (Post, Edit, Delete)        | ✅ Full                           | Both can communicate to staff |

### 2.2 Server-Side Permission Check

**Middleware:**

```javascript
function requireManagerOrAVP(req, res, next) {
    if (!['Manager', 'AVP'].includes(req.user.role)) {
        return res.status(403).json({
            success: false,
            error: 'Access denied. Manager or AVP role required.',
        });
    }
    next();
}

// Apply to all settings routes
app.use('/api/v1/settings/*', authenticateToken, requireManagerOrAVP);
```

### 2.3 Granular Permission Checks

**Example - Delete User:**

```javascript
app.delete('/api/v1/users/:id', async (req, res) => {
    // Additional check: Cannot delete self
    if (req.params.id === req.user.id) {
        return res.status(400).json({
            success: false,
            error: 'Cannot delete your own account',
        });
    }

    // Cannot delete AVP (unless requester is also AVP)
    const targetUser = await User.findById(req.params.id);
    if (targetUser.role === 'AVP' && req.user.role !== 'AVP') {
        return res.status(403).json({
            success: false,
            error: 'Only AVP can delete AVP accounts',
        });
    }

    // Proceed with soft delete
    await User.update({ id: req.params.id }, { deleted_at: new Date() });
    res.json({ success: true });
});
```

---

## 3. MODULE 1: USER MANAGEMENT

### 3.1 Module Card

#### 3.1.1 Card Header

| Element         | Value                                     |
| --------------- | ----------------------------------------- |
| **Icon**        | Users icon (SVG), 48x48px, Color: #2E5C8A |
| **Title**       | "User Management"                         |
| **Description** | "Manage users, roles, and access"         |
| **Quick Stats** | "X active users" (dynamic count)          |

**Structure:**

```html
<div class="settings-module-card" data-module="user-management">
    <svg class="module-icon">{users_icon}</svg>
    <h3 class="module-title">User Management</h3>
    <p class="module-description">Manage users, roles, and access</p>
    <div class="module-stats">
        <span class="stat-badge">{active_users_count} active users</span>
    </div>
    <svg class="arrow-icon">{arrow_right}</svg>
</div>
```

#### 3.1.2 Navigation

**Click Card:** Navigate to `/admin/users`

**URL Structure:**

- `/admin/users` - User list (default view)
- `/admin/users/create` - Create new user form
- `/admin/users/edit/:id` - Edit user form
- `/admin/users/import` - Bulk import interface
- `/admin/users/activity` - Activity log

### 3.2 User List Page

#### 3.2.1 Page Layout

**Header Section:**

```html
<div class="page-header">
    <div class="header-left">
        <h1>User Management</h1>
        <p class="subtitle">Manage system users and access</p>
    </div>
    <div class="header-right">
        <button class="btn-secondary" onclick="openImportModal()">
            <svg class="icon">↑</svg>
            Import Users
        </button>
        <button class="btn-primary" onclick="navigateTo('/admin/users/create')">
            <svg class="icon">+</svg>
            Add New User
        </button>
    </div>
</div>
```

#### 3.2.2 Filters Bar

**Filter Options:**

| Filter           | Type       | Options                                                     | Default |
| ---------------- | ---------- | ----------------------------------------------------------- | ------- |
| **Role**         | Dropdown   | All, AVP, Manager, RA, KA, RO, FS, FO, FA                   | All     |
| **Site**         | Dropdown   | All, Jakarta HQ, Klaten, Magelang                           | All     |
| **Sub-division** | Dropdown   | All, Agronomy, Crops Protection, Product Testing, Knowledge | All     |
| **Status**       | Dropdown   | All, Active, Inactive, Blocked                              | All     |
| **Search**       | Text Input | Name or Email                                               | Empty   |

**HTML Structure:**

```html
<div class="filters-bar">
    <select id="filter-role" onchange="applyFilters()">
        <option value="">All Roles</option>
        <option value="AVP">AVP</option>
        <option value="Manager">Manager</option>
        <option value="RA">RA</option>
        <option value="KA">KA</option>
        <option value="RO">RO</option>
        <option value="FS">FS</option>
        <option value="FO">FO</option>
        <option value="FA">FA</option>
    </select>

    <select id="filter-site" onchange="applyFilters()">
        <option value="">All Sites</option>
        <option value="jakarta">Jakarta HQ</option>
        <option value="klaten">Klaten</option>
        <option value="magelang">Magelang</option>
    </select>

    <select id="filter-subdivision" onchange="applyFilters()">
        <option value="">All Sub-divisions</option>
        <option value="agronomy">Agronomy</option>
        <option value="crops_protection">Crops Protection</option>
        <option value="product_testing">Product Testing</option>
        <option value="knowledge">Knowledge</option>
    </select>

    <select id="filter-status" onchange="applyFilters()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="blocked">Blocked</option>
    </select>

    <input
        type="text"
        id="filter-search"
        placeholder="Search name or email..."
        oninput="debounceSearch(this.value)"
    />

    <button class="btn-secondary" onclick="clearFilters()">
        Clear Filters
    </button>
</div>
```

**Filter Logic:**

```javascript
function applyFilters() {
    const filters = {
        role: document.getElementById('filter-role').value,
        site: document.getElementById('filter-site').value,
        subdivision: document.getElementById('filter-subdivision').value,
        status: document.getElementById('filter-status').value,
        search: document.getElementById('filter-search').value.trim(),
    };

    // Build query params
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
        if (value) params.append(key, value);
    });

    // Fetch filtered users
    fetchUsers(`/api/v1/users?${params.toString()}`);
}

let searchTimeout;
function debounceSearch(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500); // Wait 500ms after typing stops
}
```

#### 3.2.3 User Table

**Table Columns:**

| Column           | Width | Sortable | Description               |
| ---------------- | ----- | -------- | ------------------------- |
| **Avatar**       | 48px  | No       | User photo or initials    |
| **Name**         | 20%   | Yes      | Full name                 |
| **Email**        | 25%   | Yes      | Email address             |
| **Role**         | 12%   | Yes      | User role badge           |
| **Site**         | 12%   | No       | Assigned site(s)          |
| **Sub-division** | 15%   | No       | For RA/RO only            |
| **Status**       | 10%   | Yes      | Active/Inactive/Blocked   |
| **Actions**      | 8%    | No       | Edit, Block, Delete icons |

**HTML Structure:**

```html
<table class="users-table">
    <thead>
        <tr>
            <th></th>
            <th onclick="sortBy('name')" class="sortable">
                Name <svg class="sort-icon">⇅</svg>
            </th>
            <th onclick="sortBy('email')" class="sortable">
                Email <svg class="sort-icon">⇅</svg>
            </th>
            <th onclick="sortBy('role')" class="sortable">
                Role <svg class="sort-icon">⇅</svg>
            </th>
            <th>Site</th>
            <th>Sub-division</th>
            <th onclick="sortBy('status')" class="sortable">
                Status <svg class="sort-icon">⇅</svg>
            </th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- User rows populated via JS -->
    </tbody>
</table>
```

**Table Row Example:**

```html
<tr data-user-id="{user_id}">
    <td>
        <div class="user-avatar" style="background: {color}">{initials}</div>
    </td>
    <td class="user-name">{full_name}</td>
    <td class="user-email">{email}</td>
    <td>
        <span class="role-badge {role_class}">{role}</span>
    </td>
    <td>{site_name}</td>
    <td>{sub_division || '--'}</td>
    <td>
        <span class="status-badge {status_class}">{status}</span>
    </td>
    <td class="actions">
        <button class="icon-btn" onclick="editUser('{user_id}')" title="Edit">
            <svg>✏️</svg>
        </button>
        <button
            class="icon-btn"
            onclick="toggleBlockUser('{user_id}')"
            title="Block/Unblock"
        >
            <svg>🚫</svg>
        </button>
        <button
            class="icon-btn danger"
            onclick="deleteUser('{user_id}')"
            title="Delete"
        >
            <svg>🗑️</svg>
        </button>
    </td>
</tr>
```

**Status Badge Styling:**

```css
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #81c784;
}

.status-badge.inactive {
    background: #f5f5f5;
    color: #666;
    border: 1px solid #cccccc;
}

.status-badge.blocked {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ef9a9a;
}
```

**Role Badge Styling:**

```css
.role-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.role-badge.avp {
    background: #e3f2fd;
    color: #1565c0;
}
.role-badge.manager {
    background: #f3e5f5;
    color: #6a1b9a;
}
.role-badge.ra {
    background: #e8f5e9;
    color: #2e7d32;
}
.role-badge.ka {
    background: #e8f5e9;
    color: #388e3c;
}
.role-badge.ro {
    background: #fff3e0;
    color: #e65100;
}
.role-badge.fs {
    background: #fff3e0;
    color: #ef6c00;
}
.role-badge.fo {
    background: #fce4ec;
    color: #c2185b;
}
.role-badge.fa {
    background: #f3e5f5;
    color: #7b1fa2;
}
```

#### 3.2.4 Pagination

**Display:** Show if total users > 20 (page size)

**Structure:**

```html
<div class="pagination">
    <button class="page-btn" onclick="goToPage(1)" disabled>« First</button>
    <button class="page-btn" onclick="goToPage(currentPage - 1)" disabled>
        ‹ Previous
    </button>

    <span class="page-info">
        Page {currentPage} of {totalPages} ({totalUsers} users)
    </span>

    <button class="page-btn" onclick="goToPage(currentPage + 1)">Next ›</button>
    <button class="page-btn" onclick="goToPage(totalPages)">Last »</button>
</div>
```

**API Pagination:**

```javascript
async function fetchUsers(url = '/api/v1/users', page = 1) {
    const params = new URLSearchParams(url.split('?')[1] || '');
    params.set('page', page);
    params.set('limit', 20);

    const response = await fetch(`/api/v1/users?${params.toString()}`);
    const data = await response.json();

    renderUsersTable(data.data.users);
    updatePagination(data.data.pagination);
}
```

**API Response:**

```json
{
    "success": true,
    "data": {
        "users": [
            /* user objects */
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_count": 52,
            "page_size": 20,
            "has_next": true,
            "has_prev": false
        }
    }
}
```

### 3.3 Create New User

#### 3.3.1 Form Layout

**Page:** `/admin/users/create`

**Form Structure:**

```html
<form id="create-user-form" onsubmit="submitCreateUser(event)">
    <div class="form-section">
        <h2>Basic Information</h2>

        <div class="form-group">
            <label for="full_name"
                >Full Name <span class="required">*</span></label
            >
            <input
                type="text"
                id="full_name"
                name="full_name"
                placeholder="e.g., Budi Santoso"
                required
                minlength="3"
                maxlength="100"
                pattern="^[a-zA-Z\s]+$"
            />
            <span class="field-hint"
                >Letters and spaces only, 3-100 characters</span
            >
            <span class="field-error" id="error-full_name"></span>
        </div>

        <div class="form-group">
            <label for="email"
                >Email Address <span class="required">*</span></label
            >
            <input
                type="email"
                id="email"
                name="email"
                placeholder="budi.santoso@agriscience.com"
                required
            />
            <span class="field-hint"
                >Must be unique and valid email format</span
            >
            <span class="field-error" id="error-email"></span>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input
                type="tel"
                id="phone_number"
                name="phone_number"
                placeholder="+62812XXXXXXXX"
                pattern="^\+62[0-9]{9,13}$"
            />
            <span class="field-hint">Format: +62XXXXXXXXX (10-15 digits)</span>
            <span class="field-error" id="error-phone_number"></span>
        </div>
    </div>

    <div class="form-section">
        <h2>Role & Assignment</h2>

        <div class="form-group">
            <label for="role">Role <span class="required">*</span></label>
            <select
                id="role"
                name="role"
                required
                onchange="handleRoleChange()"
            >
                <option value="">-- Select Role --</option>
                <option value="AVP">AVP of Agri Services</option>
                <option value="Manager">Agriculture Science Manager</option>
                <option value="RA">Research Associate</option>
                <option value="KA">Knowledge Associate</option>
                <option value="RO">Research Officer</option>
                <option value="FS">Farm Supervisor</option>
                <option value="FO">Finance Operation</option>
                <option value="FA">Farm Admin</option>
            </select>
            <span class="field-error" id="error-role"></span>
        </div>

        <div class="form-group" id="site-group">
            <label for="site"
                >Site Assignment <span class="required">*</span></label
            >
            <select id="site" name="site_id" required>
                <option value="">-- Select Site --</option>
                <option value="uuid-jakarta">Jakarta HQ</option>
                <option value="uuid-klaten">Klaten</option>
                <option value="uuid-magelang">Magelang</option>
            </select>
            <span class="field-hint" id="site-hint"
                >Primary site assignment</span
            >
            <span class="field-error" id="error-site_id"></span>
        </div>

        <div class="form-group hidden" id="subdivision-group">
            <label for="sub_division"
                >Sub-division <span class="required">*</span></label
            >
            <select id="sub_division" name="sub_division">
                <option value="">-- Select Sub-division --</option>
                <option value="agronomy">Agronomy</option>
                <option value="crops_protection">Crops Protection</option>
                <option value="product_testing">Product Testing</option>
                <option value="knowledge">Knowledge</option>
            </select>
            <span class="field-error" id="error-sub_division"></span>
        </div>

        <!-- For FA only: Multi-site assignment -->
        <div class="form-group hidden" id="multi-site-group">
            <label>Additional Sites (FA only)</label>
            <div class="checkbox-group">
                <label>
                    <input
                        type="checkbox"
                        name="additional_sites[]"
                        value="uuid-klaten"
                    />
                    Klaten
                </label>
                <label>
                    <input
                        type="checkbox"
                        name="additional_sites[]"
                        value="uuid-magelang"
                    />
                    Magelang
                </label>
            </div>
            <span class="field-hint">FA can manage multiple sites</span>
        </div>
    </div>

    <div class="form-section">
        <h2>Account Settings</h2>

        <div class="form-group">
            <label>Initial Password</label>
            <div class="password-display">
                <input
                    type="text"
                    id="initial_password"
                    name="initial_password"
                    readonly
                    value="{auto_generated_password}"
                />
                <button
                    type="button"
                    class="btn-icon"
                    onclick="regeneratePassword()"
                >
                    🔄 Regenerate
                </button>
            </div>
            <span class="field-hint">
                Auto-generated secure password. User must change on first login.
                <button type="button" class="link-btn" onclick="copyPassword()">
                    📋 Copy to Clipboard
                </button>
            </span>
        </div>

        <div class="form-group">
            <label>
                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    checked
                />
                Active Status
            </label>
            <span class="field-hint"
                >Uncheck to create inactive account (user cannot login)</span
            >
        </div>

        <div class="form-group">
            <label>
                <input
                    type="checkbox"
                    id="send_welcome_email"
                    name="send_welcome_email"
                    checked
                />
                Send Welcome Email
            </label>
            <span class="field-hint"
                >Email with credentials and login instructions</span
            >
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-secondary" onclick="history.back()">
            Cancel
        </button>
        <button type="submit" class="btn-primary">
            <svg class="icon">💾</svg>
            Create User
        </button>
    </div>
</form>
```

#### 3.3.2 Field Validations

**Full Name:**

```javascript
const fullNameRules = {
    required: true,
    minLength: 3,
    maxLength: 100,
    pattern: /^[a-zA-Z\s]+$/,
    errorMessages: {
        required: 'Full name is required',
        minLength: 'Name must be at least 3 characters',
        maxLength: 'Name cannot exceed 100 characters',
        pattern: 'Name can only contain letters and spaces',
    },
};
```

**Email:**

```javascript
const emailRules = {
    required: true,
    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    asyncValidation: async (email) => {
        // Check uniqueness
        const response = await fetch(
            `/api/v1/users/check-email?email=${email}`,
        );
        const data = await response.json();
        return data.available;
    },
    errorMessages: {
        required: 'Email is required',
        pattern: 'Invalid email format',
        asyncValidation: 'Email already exists',
    },
};
```

**Phone Number:**

```javascript
const phoneRules = {
    required: false,
    pattern: /^\+62[0-9]{9,13}$/,
    errorMessages: {
        pattern: 'Invalid phone format. Use: +62XXXXXXXXX',
    },
};
```

**Role:**

```javascript
const roleRules = {
    required: true,
    options: ['AVP', 'Manager', 'RA', 'KA', 'RO', 'FS', 'FO', 'FA'],
    errorMessages: {
        required: 'Role is required',
        options: 'Invalid role selected',
    },
};
```

**Sub-division (Conditional):**

```javascript
function validateSubdivision(formData) {
    const role = formData.role;
    const subdivision = formData.sub_division;

    // Required for RA and RO only
    if (['RA', 'RO'].includes(role)) {
        if (!subdivision) {
            return { valid: false, error: 'Sub-division required for RA/RO' };
        }

        const validOptions = [
            'agronomy',
            'crops_protection',
            'product_testing',
            'knowledge',
        ];
        if (!validOptions.includes(subdivision)) {
            return { valid: false, error: 'Invalid sub-division' };
        }
    }

    return { valid: true };
}
```

#### 3.3.3 Dynamic Form Behavior

**Role Change Handler:**

```javascript
function handleRoleChange() {
    const role = document.getElementById('role').value;
    const subdivisionGroup = document.getElementById('subdivision-group');
    const multiSiteGroup = document.getElementById('multi-site-group');
    const siteHint = document.getElementById('site-hint');

    // Show/hide subdivision based on role
    if (['RA', 'RO'].includes(role)) {
        subdivisionGroup.classList.remove('hidden');
        document.getElementById('sub_division').required = true;
    } else {
        subdivisionGroup.classList.add('hidden');
        document.getElementById('sub_division').required = false;
        document.getElementById('sub_division').value = '';
    }

    // Show multi-site for FA
    if (role === 'FA') {
        multiSiteGroup.classList.remove('hidden');
        siteHint.textContent =
            'Primary site (can select additional sites below)';
    } else {
        multiSiteGroup.classList.add('hidden');
        siteHint.textContent = 'Primary site assignment';
        // Uncheck all additional sites
        document
            .querySelectorAll('input[name="additional_sites[]"]')
            .forEach((cb) => {
                cb.checked = false;
            });
    }
}
```

#### 3.3.4 Password Generation

**Auto-Generate Secure Password:**

```javascript
function generatePassword() {
    const length = 12;
    const charset = {
        uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lowercase: 'abcdefghijklmnopqrstuvwxyz',
        numbers: '0123456789',
        symbols: '!@#$%^&*',
    };

    let password = '';

    // Ensure at least one of each type
    password +=
        charset.uppercase[Math.floor(Math.random() * charset.uppercase.length)];
    password +=
        charset.lowercase[Math.floor(Math.random() * charset.lowercase.length)];
    password +=
        charset.numbers[Math.floor(Math.random() * charset.numbers.length)];
    password +=
        charset.symbols[Math.floor(Math.random() * charset.symbols.length)];

    // Fill remaining length
    const allChars = Object.values(charset).join('');
    for (let i = password.length; i < length; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
    }

    // Shuffle password
    return password
        .split('')
        .sort(() => Math.random() - 0.5)
        .join('');
}

function regeneratePassword() {
    const passwordField = document.getElementById('initial_password');
    passwordField.value = generatePassword();

    // Show regenerate animation
    passwordField.classList.add('regenerated');
    setTimeout(() => passwordField.classList.remove('regenerated'), 500);
}

// Generate on page load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('initial_password').value = generatePassword();
});
```

#### 3.3.5 Form Submission

**Client-Side Validation:**

```javascript
async function submitCreateUser(event) {
    event.preventDefault();

    // Clear previous errors
    document
        .querySelectorAll('.field-error')
        .forEach((el) => (el.textContent = ''));

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    // Validate all fields
    const errors = await validateCreateUserForm(data);

    if (Object.keys(errors).length > 0) {
        // Show errors
        Object.entries(errors).forEach(([field, message]) => {
            document.getElementById(`error-${field}`).textContent = message;
        });

        // Scroll to first error
        const firstError = document.querySelector('.field-error:not(:empty)');
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });

        return;
    }

    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="spinner"></div> Creating...';

    try {
        // Submit to API
        const response = await fetch('/api/v1/users', {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${getAuthToken()}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Failed to create user');
        }

        // Show success message
        showToast('User created successfully!', 'success');

        // Redirect to user list
        setTimeout(() => {
            window.location.href = '/admin/users';
        }, 1000);
    } catch (error) {
        console.error('Create user error:', error);
        showToast(error.message, 'error');

        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg class="icon">💾</svg> Create User';
    }
}
```

**API Endpoint:**

```http
POST /api/v1/users
Headers:
  Authorization: Bearer {jwt_token}
  Content-Type: application/json

Body:
{
  "full_name": "Budi Santoso",
  "email": "budi.santoso@agriscience.com",
  "phone_number": "+628123456789",
  "role": "RA",
  "site_id": "uuid-klaten",
  "sub_division": "agronomy",
  "initial_password": "Abc123!@#xyz",
  "is_active": true,
  "send_welcome_email": true
}
```

**Server Response:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": "uuid-new-user",
            "full_name": "Budi Santoso",
            "email": "budi.santoso@agriscience.com",
            "role": "RA",
            "site": "Klaten",
            "sub_division": "Agronomy",
            "status": "Active",
            "created_at": "2026-02-09T10:00:00Z"
        }
    },
    "message": "User created successfully. Welcome email sent."
}
```

### 3.4 Edit User

**Page:** `/admin/users/edit/:id`

**Form:** Same structure as Create User, but:

- Pre-filled dengan existing data
- Cannot change email (read-only field)
- Password section shows "Reset Password" button instead of auto-generated field
- Additional "Change Role" warning if role is being changed

**Reset Password Flow:**

```javascript
async function resetUserPassword(userId) {
    const confirmed = confirm(
        'This will generate a new password and send it to the user via email. Continue?',
    );

    if (!confirmed) return;

    try {
        const response = await fetch(`/api/v1/users/${userId}/reset-password`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${getAuthToken()}` },
        });

        const result = await response.json();

        if (result.success) {
            showToast(
                'Password reset successfully. Email sent to user.',
                'success',
            );
        }
    } catch (error) {
        showToast('Failed to reset password', 'error');
    }
}
```

### 3.5 Bulk Import

#### 3.5.1 Import Page

**Page:** `/admin/users/import`

**Steps:**

1. Download Excel template
2. Fill template with user data
3. Upload filled template
4. Preview & validate
5. Confirm import

**Template Download:**

```javascript
async function downloadTemplate() {
    const response = await fetch('/api/v1/users/import/template', {
        headers: { Authorization: `Bearer ${getAuthToken()}` },
    });

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'user_import_template.xlsx';
    a.click();
}
```

**Template Structure:**

| Column           | Required    | Format            | Example              | Validation                           |
| ---------------- | ----------- | ----------------- | -------------------- | ------------------------------------ |
| **Full Name**    | Yes         | Text, 3-100 chars | Budi Santoso         | Letters and spaces only              |
| **Email**        | Yes         | Email format      | budi@agriscience.com | Must be unique                       |
| **Role**         | Yes         | Exact match       | RA                   | AVP, Manager, RA, KA, RO, FS, FO, FA |
| **Site**         | Yes         | Exact match       | Klaten               | Jakarta HQ, Klaten, Magelang         |
| **Sub-division** | Conditional | Exact match       | Agronomy             | Required if Role = RA/RO             |
| **Phone Number** | No          | +62XXXXXXXXX      | +628123456789        | 10-15 digits                         |

#### 3.5.2 Upload & Validation

**Upload Interface:**

```html
<div class="import-upload-section">
    <div class="upload-area" id="upload-dropzone">
        <svg class="upload-icon">📁</svg>
        <p class="upload-text">Drag & drop Excel file here</p>
        <p class="upload-subtext">or click to browse</p>
        <input
            type="file"
            id="file-input"
            accept=".xlsx,.xls"
            onchange="handleFileUpload(event)"
            hidden
        />
    </div>

    <div class="upload-requirements">
        <h4>Requirements:</h4>
        <ul>
            <li>Excel format (.xlsx or .xls)</li>
            <li>Maximum 100 users per import</li>
            <li>Use provided template structure</li>
            <li>All required fields must be filled</li>
        </ul>
    </div>
</div>
```

**Validation Preview:**

```javascript
async function handleFileUpload(event) {
    const file = event.target.files[0];

    if (!file) return;

    // Show loading
    showLoading('Validating file...');

    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await fetch('/api/v1/users/import/validate', {
            method: 'POST',
            headers: { Authorization: `Bearer ${getAuthToken()}` },
            body: formData,
        });

        const result = await response.json();

        hideLoading();

        if (result.success) {
            renderValidationPreview(result.data);
        } else {
            showToast('Validation failed: ' + result.error, 'error');
        }
    } catch (error) {
        hideLoading();
        showToast('Upload failed', 'error');
    }
}

function renderValidationPreview(data) {
    const { valid_rows, invalid_rows, summary } = data;

    const html = `
    <div class="validation-summary">
      <h3>Validation Results</h3>
      <div class="summary-stats">
        <div class="stat success">
          <span class="number">${summary.valid_count}</span>
          <span class="label">Valid Users</span>
        </div>
        <div class="stat error">
          <span class="number">${summary.invalid_count}</span>
          <span class="label">Errors</span>
        </div>
        <div class="stat">
          <span class="number">${summary.total_count}</span>
          <span class="label">Total Rows</span>
        </div>
      </div>
    </div>
    
    <div class="validation-table-container">
      <table class="validation-table">
        <thead>
          <tr>
            <th>Row</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Site</th>
            <th>Status</th>
            <th>Errors</th>
          </tr>
        </thead>
        <tbody>
          ${valid_rows
              .map(
                  (row) => `
            <tr class="valid-row">
              <td>${row.row_number}</td>
              <td>${row.full_name}</td>
              <td>${row.email}</td>
              <td>${row.role}</td>
              <td>${row.site}</td>
              <td><span class="badge success">✓ Valid</span></td>
              <td>--</td>
            </tr>
          `,
              )
              .join('')}
          
          ${invalid_rows
              .map(
                  (row) => `
            <tr class="invalid-row">
              <td>${row.row_number}</td>
              <td>${row.full_name || '--'}</td>
              <td>${row.email || '--'}</td>
              <td>${row.role || '--'}</td>
              <td>${row.site || '--'}</td>
              <td><span class="badge error">✗ Invalid</span></td>
              <td>
                <ul class="error-list">
                  ${row.errors.map((err) => `<li>${err}</li>`).join('')}
                </ul>
              </td>
            </tr>
          `,
              )
              .join('')}
        </tbody>
      </table>
    </div>
    
    <div class="import-actions">
      <button class="btn-secondary" onclick="cancelImport()">
        Cancel
      </button>
      <button 
        class="btn-primary" 
        onclick="confirmImport()"
        ${summary.invalid_count > 0 ? 'disabled' : ''}
      >
        Import ${summary.valid_count} Users
      </button>
    </div>
  `;

    document.getElementById('import-preview').innerHTML = html;
}
```

#### 3.5.3 Confirm Import

**Confirmation Modal:**

```javascript
function confirmImport() {
    showModal({
        title: 'Confirm Import',
        message: `You are about to import ${validRowsCount} users. Welcome emails will be sent to all users. Continue?`,
        buttons: [
            {
                text: 'Cancel',
                style: 'secondary',
                onClick: closeModal,
            },
            {
                text: 'Import',
                style: 'primary',
                onClick: executeImport,
            },
        ],
    });
}

async function executeImport() {
    closeModal();
    showLoading('Importing users...');

    try {
        const response = await fetch('/api/v1/users/import/execute', {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${getAuthToken()}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ validated_data: validRows }),
        });

        const result = await response.json();

        hideLoading();

        if (result.success) {
            showToast(
                `Successfully imported ${result.data.created_count} users`,
                'success',
            );

            // Show detailed summary
            showImportSummary(result.data);

            // Redirect after 5 seconds
            setTimeout(() => {
                window.location.href = '/admin/users';
            }, 5000);
        }
    } catch (error) {
        hideLoading();
        showToast('Import failed', 'error');
    }
}
```

### 3.6 Block/Unblock User

**Block User Modal:**

```html
<div class="modal" id="block-user-modal">
    <div class="modal-content">
        <h3>Block User: {user_name}</h3>

        <div class="form-group">
            <label>Block Reason <span class="required">*</span></label>
            <textarea
                id="block_reason"
                placeholder="Explain why this user is being blocked..."
                minlength="10"
                maxlength="500"
                required
            ></textarea>
            <span class="field-hint">10-500 characters</span>
        </div>

        <div class="form-group">
            <label>Block Duration</label>
            <select id="block_duration">
                <option value="manual">Until manually unblocked</option>
                <option value="7">7 days</option>
                <option value="14">14 days</option>
                <option value="30">30 days</option>
            </select>
        </div>

        <div class="warning-box">
            <svg class="icon warning">⚠️</svg>
            <p>
                Blocked users cannot login or access the system. All pending
                requests will remain as-is.
            </p>
        </div>

        <div class="modal-actions">
            <button class="btn-secondary" onclick="closeBlockModal()">
                Cancel
            </button>
            <button class="btn-danger" onclick="confirmBlock()">
                Block User
            </button>
        </div>
    </div>
</div>
```

**Block API:**

```http
POST /api/v1/users/:id/block
Headers:
  Authorization: Bearer {jwt_token}
  Content-Type: application/json

Body:
{
  "reason": "Multiple overdue settlements without resolution",
  "duration_days": 7  // or null for manual unblock
}
```

**Server-Side Logic:**

```javascript
app.post('/api/v1/users/:id/block', async (req, res) => {
    const { reason, duration_days } = req.body;
    const userId = req.params.id;

    // Validate reason
    if (!reason || reason.length < 10) {
        return res.status(400).json({
            success: false,
            error: 'Block reason must be at least 10 characters',
        });
    }

    // Calculate blocked_until date
    const blocked_until = duration_days
        ? new Date(Date.now() + duration_days * 24 * 60 * 60 * 1000)
        : null;

    // Update user
    await User.update(
        { id: userId },
        {
            status: 'Blocked',
            blocked_reason: reason,
            blocked_at: new Date(),
            blocked_by: req.user.id,
            blocked_until: blocked_until,
        },
    );

    // Log action
    await AuditLog.create({
        user_id: req.user.id,
        action: 'User Blocked',
        entity_type: 'User',
        entity_id: userId,
        details: { reason, duration_days },
    });

    // Send notification email to blocked user
    const user = await User.findById(userId);
    await sendEmail(user.email, 'Account Blocked', {
        reason,
        blocked_until: blocked_until
            ? formatDate(blocked_until)
            : 'Until manually unblocked',
        contact: 'support@agriscience.com',
    });

    res.json({ success: true, message: 'User blocked successfully' });
});
```

**Unblock:**

```http
POST /api/v1/users/:id/unblock
Headers:
  Authorization: Bearer {jwt_token}
  Content-Type: application/json

Body:
{
  "justification": "All overdue settlements have been submitted"
}
```

### 3.7 Delete User

**Confirmation Modal:**

```javascript
function deleteUser(userId, userName) {
    showModal({
        title: 'Delete User',
        message: `Are you sure you want to delete ${userName}? This will deactivate their account but preserve historical data.`,
        type: 'danger',
        buttons: [
            {
                text: 'Cancel',
                style: 'secondary',
                onClick: closeModal,
            },
            {
                text: 'Delete User',
                style: 'danger',
                onClick: async () => {
                    closeModal();
                    await executeDeleteUser(userId);
                },
            },
        ],
    });
}

async function executeDeleteUser(userId) {
    try {
        const response = await fetch(`/api/v1/users/${userId}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${getAuthToken()}` },
        });

        if (response.ok) {
            showToast('User deleted successfully', 'success');

            // Remove row from table
            document.querySelector(`tr[data-user-id="${userId}"]`).remove();
        }
    } catch (error) {
        showToast('Failed to delete user', 'error');
    }
}
```

**Soft Delete Implementation:**

```sql
-- Soft delete (set deleted_at timestamp)
UPDATE users
SET deleted_at = NOW(),
    status = 'Inactive',
    updated_at = NOW()
WHERE id = :user_id;

-- Historical data preserved
-- User cannot login
-- User removed from all active lists
```

### 3.8 Activity Log

**Page:** `/admin/users/activity`

**Table Columns:**

| Column         | Description          | Format                                |
| -------------- | -------------------- | ------------------------------------- |
| **Timestamp**  | When action occurred | YYYY-MM-DD HH:mm:ss                   |
| **User**       | Who performed action | Name (clickable to profile)           |
| **Action**     | What was done        | Login, Created RAB, Approved PR, etc. |
| **Entity**     | Target of action     | RAB-00012, PR-00123, User: John Doe   |
| **IP Address** | Source IP            | IPv4/IPv6                             |
| **Details**    | Additional info      | JSON (expandable)                     |

**Filters:**

- Date Range (from/to)
- User (dropdown)
- Action Type (Login, Create, Update, Delete, Approve, Reject)
- Entity Type (RAB, Payment Request, Settlement, User, Vendor)

**Export:**

```javascript
async function exportActivityLog() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);

    window.location.href = `/api/v1/users/activity/export?${params.toString()}`;
}
```

**API generates Excel file with all matching records**

---

## 4. MODULE 2: ROLES & PERMISSIONS

### 4.1 Module Card

| Element         | Value                                     |
| --------------- | ----------------------------------------- |
| **Icon**        | Shield icon (SVG), 48x48px                |
| **Title**       | "Roles & Permissions"                     |
| **Description** | "Configure user roles and access control" |
| **Quick Stats** | "8 roles configured"                      |

**Navigation:** `/admin/roles`

### 4.2 Role Matrix View

**Page Layout:**

**Display:** Table showing all roles with their permissions

**Columns:**

- Role Name
- User Count (how many users have this role)
- Permissions Summary
- Actions (Edit, View Details)

**Table:**

```html
<table class="roles-matrix-table">
    <thead>
        <tr>
            <th>Role</th>
            <th>Users</th>
            <th>Create RAB</th>
            <th>Approve RAB</th>
            <th>Create PR</th>
            <th>Approve PR</th>
            <th>Review PR</th>
            <th>Disburse</th>
            <th>Verify Settlement</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>AVP</strong></td>
            <td>1</td>
            <td>✓</td>
            <td>✓ (All)</td>
            <td>✓</td>
            <td>✓ (>5M)</td>
            <td>--</td>
            <td>--</td>
            <td>--</td>
            <td>
                <button onclick="editRole('AVP')">Edit</button>
            </td>
        </tr>
        <!-- More roles -->
    </tbody>
</table>
```

### 4.3 Approval Matrix Configuration

**Visual Editor:**

**Amount Thresholds:**

- < 5 juta
- > = 5 juta

**Requester Levels:**

- RO/FS
- RA/KA
- Manager

**Approval Flow Diagram:**

```
RO/FS Request (<5M):
  RO/FS → RA/KA (Review) → Manager (Approve) → FO (Disburse)

RO/FS Request (>=5M):
  RO/FS → RA/KA (Review) → Manager + AVP (Both Approve) → FO (Disburse)

RA/KA Request (<5M):
  RA/KA → Manager (Approve) → FO (Disburse)

RA/KA Request (>=5M):
  RA/KA → Manager + AVP (Both Approve) → FO (Disburse)

Manager Request:
  Manager → AVP (Approve) → FO (Disburse)
```

**Editable Parameters:**

- Amount threshold (default: 5,000,000)
- Reviewer role (RA/KA for RO/FS)
- Approver roles
- Parallel vs Sequential approval

### 4.4 Delegation Settings

**Manager Delegation:**

**Form:**

```html
<div class="delegation-form">
    <h3>Delegation Settings</h3>
    <p>When Manager is unavailable, approvals auto-escalate to AVP</p>

    <div class="form-group">
        <label>Manager</label>
        <select id="manager-select">
            <option value="uuid-manager">Dewi Kartika (Manager)</option>
        </select>
    </div>

    <div class="form-group">
        <label>Delegation Period</label>
        <input type="date" id="delegation-start" />
        <span>to</span>
        <input type="date" id="delegation-end" />
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" id="delegation-active" />
            Delegation Active
        </label>
    </div>

    <button class="btn-primary" onclick="saveDelegation()">
        Save Delegation Settings
    </button>
</div>
```

**Effect:**

- During delegation period, Manager's pending approvals auto-route to AVP
- System shows delegation badge on approval requests
- Manager can still approve if they login (delegation is flexible, not hard block)

---

## 5. MODULE 3: STATION CONFIGURATION

### 5.1 Module Card

| Element         | Value                                      |
| --------------- | ------------------------------------------ |
| **Icon**        | Map Pin icon, 48x48px                      |
| **Title**       | "Station Configuration"                    |
| **Description** | "Manage research stations and assignments" |
| **Quick Stats** | "3 stations configured"                    |

**Navigation:** `/admin/stations`

### 5.2 Station List

**Display:**

| Station Name | Site Code | Address            | FA Assigned | Active RABs | Actions |
| ------------ | --------- | ------------------ | ----------- | ----------- | ------- |
| Jakarta HQ   | JKT       | Jl. Sudirman...    | --          | 0           | Edit    |
| Klaten       | KLT       | Jl. Raya Klaten... | Siti Aminah | 12          | Edit    |
| Magelang     | MLG       | Jl. Magelang...    | Siti Aminah | 8           | Edit    |

### 5.3 Edit Station

**Form Fields:**

- Station Name (read-only - cannot change existing stations)
- Site Code (read-only)
- Full Address
- Assigned FA (dropdown - select from FA role users)
- Contact Phone
- Active Status

**FA Multi-Station Assignment:**

- FA can be assigned to multiple stations
- System shows all assigned stations for selected FA
- Manager can reassign FA between stations

---

## 6. MODULE 4: GLOBAL COA

### 6.1 Module Card

| Element         | Value                           |
| --------------- | ------------------------------- |
| **Icon**        | List icon, 48x48px              |
| **Title**       | "Chart of Accounts"             |
| **Description** | "Manage COA for budget mapping" |
| **Quick Stats** | "45 COA codes active"           |

**Navigation:** `/admin/coa`

### 6.2 COA Hierarchy View

**Display:** Tree structure

```
├─ 5000 - Cost of Goods Sold
│  ├─ 5010 - Agricultural Supplies
│  │  ├─ 5010-001 - Pupuk Kimia - NPK
│  │  ├─ 5010-002 - Pupuk Organik
│  │  └─ 5010-003 - Pestisida
│  ├─ 5020 - Labor Costs
│  │  ├─ 5020-001 - Direct Labor - Field
│  │  └─ 5020-002 - Direct Labor - Processing
│  └─ 5030 - Equipment & Tools
│     ├─ 5030-001 - Farm Equipment
│     └─ 5030-002 - Laboratory Equipment
├─ 6000 - Operating Expenses
│  ├─ 6010 - Utilities
│  └─ 6020 - Transportation
```

### 6.3 Add/Edit COA

**Form:**

- COA Code (format: XXXX-XXX, unique)
- Description
- Category (dropdown: COGS, Operating Expense, Capital Expenditure)
- Sub-category
- Active Status

**Usage Report:**

- Show how many times COA used in RABs
- Cannot delete COA if usage count > 0 (only deactivate)

---

## 7. MODULE 5: APPROVAL WORKFLOW

### 7.1 Module Card

| Element         | Value                                |
| --------------- | ------------------------------------ |
| **Icon**        | Git Branch icon, 48x48px             |
| **Title**       | "Approval Workflow"                  |
| **Description** | "Configure approval routing and SLA" |
| **Quick Stats** | "2 workflows active"                 |

**Navigation:** `/admin/workflow`

### 7.2 SLA Configuration

**Settings:**

| Process          | Normal SLA | Urgent SLA | Auto-Escalation                 |
| ---------------- | ---------- | ---------- | ------------------------------- |
| Manager Approval | 24 hours   | 4 hours    | After 24hr → Notify AVP         |
| AVP Approval     | 48 hours   | 6 hours    | After 48hr → Email notification |
| FO Disbursement  | 12 hours   | 5 hours    | After 12hr → Notify Manager     |
| Settlement       | 2x24 hours | N/A        | After deadline → Auto-block     |

**Editable Fields:**

- SLA duration (hours)
- Auto-escalation enabled/disabled
- Escalation action (notify, auto-approve, reassign)
- Reminder frequency

### 7.3 Auto-Escalation Rules

**Rule Builder:**

```
IF approval pending > {SLA_threshold}
THEN {action}
  - Send email to {approver + manager/AVP}
  - Create notification
  - Log escalation event
```

**Example:**

```
IF Manager approval pending > 24 hours
THEN
  - Send email to Manager + AVP
  - Create high-priority notification
  - Flag request as "SLA Breached"
```

---

## 8. MODULE 6: ANNOUNCEMENTS

### 8.1 Module Card

| Element         | Value                            |
| --------------- | -------------------------------- |
| **Icon**        | Megaphone icon, 48x48px          |
| **Title**       | "Announcements"                  |
| **Description** | "Post system-wide announcements" |
| **Quick Stats** | "2 active announcements"         |

**Navigation:** `/admin/announcements`

### 8.2 Create Announcement

**Form:**

- Title (required, max 100 chars)
- Message (required, max 1000 chars, rich text editor)
- Priority (Info, Warning, Critical)
- Target Audience (All Users, Specific Roles, Specific Sites)
- Expiry Date (optional - auto-hide after date)
- Send Email (checkbox - send email notification)
- Pin to Top (checkbox - show at top of Information Section)

**Preview:**

- Live preview of how announcement will appear
- Shows on Information Section
- Shows as notification (if "Send Email" checked)

### 8.3 Announcement List

**Table:**

- Title
- Priority
- Target Audience
- Created By
- Created Date
- Status (Active/Expired)
- Actions (Edit, Delete, Pin/Unpin)

**Actions:**

- Edit: Modify announcement
- Delete: Soft delete (hide from view)
- Pin/Unpin: Toggle pin status

---

## 9. TECHNICAL SPECIFICATIONS

### 9.1 Performance Requirements

| Metric               | Target             |
| -------------------- | ------------------ |
| User List Load       | <1s for 100 users  |
| Create User          | <2s total          |
| Bulk Import          | <30s for 100 users |
| COA Tree Render      | <500ms             |
| Approval Matrix Load | <800ms             |

### 9.2 Security

**Audit Trail:**

- ALL admin actions logged
- Log includes: who, what, when, before/after values
- Logs immutable (cannot be deleted/edited)
- Retention: Permanent

**Permission Checks:**

- Server-side validation on every endpoint
- Client-side checks for UI only (not security)
- Role changes require AVP approval (logged)

### 9.3 Data Validation

**Server-Side Rules:**

- Email uniqueness check
- Role validation against allowed values
- Site/sub-division consistency
- COA code format validation
- Prevent circular delegation

---

## 10. API ENDPOINTS SUMMARY

### 10.1 User Management

| Endpoint                           | Method | Purpose                              |
| ---------------------------------- | ------ | ------------------------------------ |
| `/api/v1/users`                    | GET    | List users with filters & pagination |
| `/api/v1/users`                    | POST   | Create new user                      |
| `/api/v1/users/:id`                | GET    | Get user details                     |
| `/api/v1/users/:id`                | PUT    | Update user                          |
| `/api/v1/users/:id`                | DELETE | Soft delete user                     |
| `/api/v1/users/:id/block`          | POST   | Block user                           |
| `/api/v1/users/:id/unblock`        | POST   | Unblock user                         |
| `/api/v1/users/:id/reset-password` | POST   | Reset user password                  |
| `/api/v1/users/check-email`        | GET    | Check email availability             |
| `/api/v1/users/import/template`    | GET    | Download import template             |
| `/api/v1/users/import/validate`    | POST   | Validate import file                 |
| `/api/v1/users/import/execute`     | POST   | Execute import                       |
| `/api/v1/users/activity`           | GET    | Get activity log                     |
| `/api/v1/users/activity/export`    | GET    | Export activity log                  |

### 10.2 Roles & Permissions

| Endpoint                   | Method | Purpose                 |
| -------------------------- | ------ | ----------------------- |
| `/api/v1/roles`            | GET    | List all roles          |
| `/api/v1/roles/:id`        | GET    | Get role details        |
| `/api/v1/roles/:id`        | PUT    | Update role permissions |
| `/api/v1/roles/matrix`     | GET    | Get approval matrix     |
| `/api/v1/roles/matrix`     | PUT    | Update approval matrix  |
| `/api/v1/roles/delegation` | GET    | Get delegation settings |
| `/api/v1/roles/delegation` | PUT    | Update delegation       |

### 10.3 Stations

| Endpoint               | Method | Purpose             |
| ---------------------- | ------ | ------------------- |
| `/api/v1/stations`     | GET    | List stations       |
| `/api/v1/stations/:id` | GET    | Get station details |
| `/api/v1/stations/:id` | PUT    | Update station      |

### 10.4 COA

| Endpoint                | Method | Purpose                   |
| ----------------------- | ------ | ------------------------- |
| `/api/v1/coa`           | GET    | List COA (tree structure) |
| `/api/v1/coa`           | POST   | Create COA                |
| `/api/v1/coa/:id`       | GET    | Get COA details           |
| `/api/v1/coa/:id`       | PUT    | Update COA                |
| `/api/v1/coa/:id`       | DELETE | Deactivate COA            |
| `/api/v1/coa/:id/usage` | GET    | Get COA usage stats       |

### 10.5 Workflow

| Endpoint                      | Method | Purpose              |
| ----------------------------- | ------ | -------------------- |
| `/api/v1/workflow/sla`        | GET    | Get SLA settings     |
| `/api/v1/workflow/sla`        | PUT    | Update SLA           |
| `/api/v1/workflow/escalation` | GET    | Get escalation rules |
| `/api/v1/workflow/escalation` | PUT    | Update rules         |

### 10.6 Announcements

| Endpoint                        | Method | Purpose             |
| ------------------------------- | ------ | ------------------- |
| `/api/v1/announcements`         | GET    | List announcements  |
| `/api/v1/announcements`         | POST   | Create announcement |
| `/api/v1/announcements/:id`     | GET    | Get details         |
| `/api/v1/announcements/:id`     | PUT    | Update announcement |
| `/api/v1/announcements/:id`     | DELETE | Delete announcement |
| `/api/v1/announcements/:id/pin` | POST   | Pin announcement    |

---

## 11. DATABASE SCHEMAS

### 11.1 Users Table

```sql
CREATE TABLE users (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  phone_number VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('AVP','Manager','RA','KA','RO','FS','FO','FA') NOT NULL,
  site_id UUID REFERENCES sites(id),
  sub_division ENUM('agronomy','crops_protection','product_testing','knowledge'),
  status ENUM('Active','Inactive','Blocked') DEFAULT 'Active',
  blocked_reason TEXT,
  blocked_at TIMESTAMP,
  blocked_by UUID REFERENCES users(id),
  blocked_until TIMESTAMP,
  must_change_password BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP,

  INDEX idx_users_email (email),
  INDEX idx_users_role (role),
  INDEX idx_users_status (status),
  INDEX idx_users_site (site_id)
);
```

### 11.2 User Site Assignments

```sql
CREATE TABLE user_site_assignments (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID REFERENCES users(id) NOT NULL,
  site_id UUID REFERENCES sites(id) NOT NULL,
  is_primary BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP,

  UNIQUE(user_id, site_id),
  INDEX idx_user_site_user (user_id),
  INDEX idx_user_site_site (site_id)
);
```

### 11.3 COA Table

```sql
CREATE TABLE coa (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  coa_code VARCHAR(20) NOT NULL UNIQUE,
  description VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  sub_category VARCHAR(100),
  status ENUM('Active','Inactive') DEFAULT 'Active',
  created_by UUID REFERENCES users(id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP,

  INDEX idx_coa_code (coa_code),
  INDEX idx_coa_category (category),
  INDEX idx_coa_status (status)
);
```

### 11.4 Announcements Table

```sql
CREATE TABLE announcements (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  title VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  priority ENUM('Info','Warning','Critical') DEFAULT 'Info',
  target_audience JSONB,  -- {type: 'all'} or {type: 'roles', roles: [...]} or {type: 'sites', sites: [...]}
  is_pinned BOOLEAN DEFAULT FALSE,
  send_email BOOLEAN DEFAULT FALSE,
  expires_at TIMESTAMP,
  created_by UUID REFERENCES users(id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP,

  INDEX idx_announcements_priority (priority),
  INDEX idx_announcements_expires (expires_at),
  INDEX idx_announcements_pinned (is_pinned)
);
```

---

## CONCLUSION

This completes **Section 4: System Settings** PRD - the largest section with 6 comprehensive sub-modules. This document provides detailed specifications for:

✅ **User Management** - Complete CRUD, bulk import, block/unblock, activity log  
✅ **Roles & Permissions** - Role matrix, approval routing, delegation  
✅ **Station Configuration** - Multi-site setup, FA assignment  
✅ **Global COA** - Hierarchical structure, usage tracking  
✅ **Approval Workflow** - SLA configuration, auto-escalation rules  
✅ **Announcements** - System-wide communication

**All with:**

- Field-level specifications
- Validation rules
- API endpoints
- Database schemas
- Error handling
- Access control

**Next Document:**

- PRD Section 5: Information Section (final section)

---

**Document Control:**

| Version | Date        | Author           | Changes                   |
| ------- | ----------- | ---------------- | ------------------------- |
| 1.0     | 08 Feb 2026 | System Architect | Initial comprehensive PRD |

---

_End of Section 4 PRD_
