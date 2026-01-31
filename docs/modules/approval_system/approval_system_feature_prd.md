# Product Requirements Document (PRD)

## Feature Name

Universal Approval Workflow System

---

## 1. Overview

### 1.1 Problem Statement

Many business processes require approvals (e.g., content publishing, financial requests, HR actions). Existing approval solutions are often tightly coupled to specific models, lack configurability, or are difficult to audit and extend.

### 1.2 Objective

Build a **model-agnostic, configurable, auditable approval workflow system** in Laravel 12 that supports **multi-step, conditional, parallel, and linear approvals**, configurable entirely from the frontend.

### 1.3 Success Criteria

- Can attach approval workflows to any Eloquent model
- Non-developers can configure workflows via UI
- Supports complex approval logic without code changes
- Full audit trail of every approval action
- Scales to enterprise-level usage

---

## 2. Scope

### In Scope

- Approval workflow definition & execution engine
- Frontend-configurable workflow builder (API support)
- Role- and permission-based approvers
- Conditional rules per step
- Parallel & sequential approvals
- Audit logging and traceability

### Out of Scope

- UI/UX implementation details
- Notification delivery mechanisms (email, Slack, etc.)
- Third-party approval integrations

---

## 3. Stakeholders

| Role               | Responsibility                       |
| ------------------ | ------------------------------------ |
| Product Owner      | Feature vision & prioritization      |
| Backend Engineers  | Workflow engine & APIs               |
| Frontend Engineers | Workflow configuration UI            |
| QA                 | Validation of workflows & edge cases |

---

## 4. User Personas

### 4.1 Admin / Workflow Designer

- Defines approval workflows
- Assigns approvers & rules
- Audits approval history

### 4.2 Approver

- Reviews and approves/rejects items
- Can act individually or as part of a group

### 4.3 Requester

- Submits items for approval
- Tracks approval status

---

## 5. Functional Requirements

### 5.1 Model-Agnostic Approval

- The system MUST support attaching approval workflows to any Eloquent model
- Models opt-in via a trait (e.g., `RequiresApproval`)
- Polymorphic relationship between workflows and models

### 5.2 Workflow Configuration

#### Workflow

- Name
- Target Model (class reference)
- Versioned (supports updates without breaking history)
- Active / Inactive state

#### Approval Step

- Order (for sequential steps)
- Type: `sequential | parallel`
- Required approvals count (for parallel)
- Assigned approvers (user, role, or permission)

### 5.3 Conditional Rules

Each approval step MAY define rules such as:

- Model attribute comparison (e.g., `amount > 10000`)
- Requester role or permission
- Custom expressions (JSON-based rule DSL)

Rules are evaluated:

- At workflow initialization
- Before entering each step

### 5.4 Approval Actions

Approvers can:

- Approve
- Reject
- Request changes (optional future extension)

Each action records:

- Actor
- Timestamp
- Step
- Comments (optional)

### 5.5 Workflow Execution States

Using **spatie/laravel-model-states**:

- `Draft`
- `PendingApproval`
- `Approved`
- `Rejected`
- `Cancelled`

State transitions MUST be explicit and auditable.

### 5.6 Parallel Approval Logic

- Multiple approvers can act concurrently
- Workflow proceeds when:
    - Required approval count is met
    - OR all approvers respond (configurable)

### 5.7 Permissions & Access Control

Using **spatie/laravel-permission**:

- Only authorized users can approve
- Admin-only workflow management
- Permission checks enforced at API and policy level

---

## 6. Non-Functional Requirements

### 6.1 Auditability

Using **spatie/laravel-activitylog**:

- Log all state transitions
- Log rule evaluations
- Log approval actions

Audit logs MUST be immutable and queryable.

### 6.2 Performance

- Workflow evaluation < 100ms under normal load
- Parallel approvals handled asynchronously where possible

### 6.3 Scalability

- Support thousands of concurrent approval instances
- Support workflow versioning

### 6.4 Security

- Strict authorization checks
- Prevent approval replay attacks
- All actions traceable to authenticated users

---

## 7. Data Model (High-Level)

### Core Tables

- `approval_workflows`
- `approval_workflow_versions`
- `approval_steps`
- `approval_instances`
- `approval_actions`

All relationships must be indexed and optimized.

---

## 8. API Requirements (High-Level)

### Admin APIs

- Create / Update Workflow
- Activate / Deactivate Workflow
- Define Steps & Rules

### Runtime APIs

- Submit Model for Approval
- Get Approval Status
- Approve / Reject Step
- Fetch Audit Log

---

## 9. Error Handling & Edge Cases

- Approver removed mid-workflow
- Workflow updated while instances exist
- Conditional rule failure
- Concurrent approval race conditions

System MUST fail gracefully and log all errors.

---

## 10. Dependencies & Libraries

| Package                     | Purpose       | Docs References                                                                |
| --------------------------- | ------------- | ------------------------------------------------------------------------------ |
| spatie/laravel-model-states | State machine | Use context7 mcp tools with "spatie/laravel-model-states" library              |
| spatie/laravel-activitylog  | Auditing      | Use context7 mcp tools with "spatie/laravel-activitylog" library               |
| spatie/laravel-permission   | RBAC          | Use context7 mcp tools with "websites/spatie_be_laravel-permission_v6" library |
| spatie/eloquent-sortable    | Sorting       | N/A                                                                            |

Additional packages MAY be introduced if:

- Actively maintained
- Laravel 12 compatible

---

## 11. Technical Constraints

- Laravel 12 best practices only
- Clean Architecture / Domain-driven boundaries
- No hard-coded model dependencies

---

## 12. Future Enhancements

- Visual workflow builder
- SLA & escalation rules
- Notifications & reminders
- Delegated approvals
- Approval templates

---

## 13. Open Questions

- Rule DSL format standardization
- Notification strategy
- UI ownership boundaries

---

## 14. Acceptance Criteria

- A workflow can be attached to any model
- Parallel and sequential approvals function correctly
- Conditional rules dynamically enable/skip steps
- Complete audit trail available
- No workflow configuration requires code changes
