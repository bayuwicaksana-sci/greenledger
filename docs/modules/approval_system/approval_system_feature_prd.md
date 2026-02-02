# Product Requirements Document (PRD)

## Feature Name

Universal Approval Workflow System

---

## Current Implementation Status

**Status:** ✅ **Active Development - Core Features Implemented**
**Last Updated:** 2026-02-02

### What's Working

- ✅ Workflow creation and management (CRUD)
- ✅ Multi-step approval configuration (sequential/parallel)
- ✅ Role, user, and permission-based approvers
- ✅ Step purpose distinction (Approval vs Action)
- ✅ Model-agnostic architecture via `RequiresApproval` trait
- ✅ Frontend workflow builder with drag-and-drop
- ✅ Shadcn/UI dialogs for delete, duplicate, activate/deactivate

### Recently Completed

- ✅ Workflow execution engine (WorkflowEngine service)
- ✅ Approval instance state management
- ✅ Approval action processing
- ✅ Conditional rules evaluation (ApprovalRuleEvaluator)
- ✅ Auto-discovery of approvable models (ModelDiscoveryService)
- ✅ Dynamic model field discovery for rule building (ModelFieldDiscoveryService)

### Pending

- ⏸️ Notification system integration
- ⏸️ SLA-based escalation
- ⏸️ Delegated approvals

---

## 1. System Architecture Overview

### 1.1 High-Level Flow

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐     ┌──────────────┐
│   Admin     │────▶│   Workflow   │────▶│   Model     │────▶│  Approval    │
│ Configures  │     │  Definition  │     │  Submitted  │     │  Processing  │
│  Workflow   │     │              │     │             │     │              │
└─────────────┘     └──────────────┘     └─────────────┘     └──────────────┘
      │                    │                     │                    │
      │                    │                     │                    │
      ▼                    ▼                     ▼                    ▼
  Create Steps      Store in DB         Create Instance      Execute Steps
  Set Approvers     Link to Model       Track Status         Log Actions
```

### 1.2 Core Components

1. **ApprovalWorkflow** - Workflow definition (name, model type, active status)
2. **ApprovalStep** - Individual steps with order, type, approvers, rules
3. **ApprovalInstance** - Runtime instance of workflow for specific model
4. **ApprovalAction** - Individual approval/reject actions by users
5. **RequiresApproval Trait** - Makes models approvable
6. **ApprovalWorkflowService** - Business logic for workflow management

---

## 2. Database Schema (Implemented)

### 2.1 approval_workflows

Stores workflow definitions.

| Column               | Type    | Description                                     |
| -------------------- | ------- | ----------------------------------------------- |
| `id`                 | bigint  | Primary key                                     |
| `name`               | string  | Workflow name (e.g., "Payment Approval")        |
| `description`        | text    | Optional description                            |
| `model_type`         | string  | Target model class (e.g., `App\Models\Program`) |
| `is_active`          | boolean | Only one active workflow per model_type         |
| `current_version_id` | bigint  | (Reserved for future versioning)                |
| `timestamps`         | -       | created_at, updated_at                          |
| `soft_deletes`       | -       | deleted_at                                      |

**Constraints:**

- Unique active workflow per `model_type` (enforced by database constraint)

---

### 2.2 approval_steps

Defines the steps in a workflow.

| Column                     | Type    | Description                        |
| -------------------------- | ------- | ---------------------------------- |
| `id`                       | bigint  | Primary key                        |
| `approval_workflow_id`     | bigint  | FK to `approval_workflows`         |
| `name`                     | string  | Step name (e.g., "Finance Review") |
| `description`              | text    | Optional description               |
| `step_order`               | integer | Order of execution (0-indexed)     |
| `step_type`                | enum    | `sequential` or `parallel`         |
| `step_purpose`             | enum    | `approval` or `action`             |
| `required_approvals_count` | integer | For parallel steps (default: 1)    |
| `approver_type`            | string  | `user`, `role`, or `permission`    |
| `approver_identifiers`     | json    | Array of IDs/names                 |
| `conditional_rules`        | json    | Rule DSL (nullable)                |
| `timestamps`               | -       | created_at, updated_at             |

**Step Type:**

- **Sequential:** Steps execute one after another
- **Parallel:** Multiple approvers can act simultaneously

**Step Purpose:**

- **Approval:** Requires approval (can be auto-skipped if requester is approver)
- **Action:** Requires action (e.g., upload transfer receipt) - always executes

**Approver Type:**

- **user:** Specific users (IDs in `approver_identifiers`)
- **role:** Users with specific roles (role names in `approver_identifiers`)
- **permission:** Users with specific permissions (permission names in `approver_identifiers`)

---

### 2.3 approval_instances

Runtime instances of workflows attached to specific models.

| Column                 | Type      | Description                                                      |
| ---------------------- | --------- | ---------------------------------------------------------------- |
| `id`                   | bigint    | Primary key                                                      |
| `approval_workflow_id` | bigint    | FK to `approval_workflows`                                       |
| `approvable_id`        | bigint    | Polymorphic ID                                                   |
| `approvable_type`      | string    | Polymorphic type (model class)                                   |
| `status`               | string    | `draft`, `pending_approval`, `approved`, `rejected`, `cancelled` |
| `current_step_id`      | bigint    | FK to `approval_steps` (nullable)                                |
| `submitted_by`         | bigint    | FK to `users` (nullable)                                         |
| `submitted_at`         | timestamp | When submitted                                                   |
| `completed_at`         | timestamp | When workflow completed                                          |
| `timestamps`           | -         | created_at, updated_at                                           |
| `soft_deletes`         | -         | deleted_at                                                       |

**Status Flow:**

```
draft ─▶ pending_approval ─▶ approved
                           └─▶ rejected
                           └─▶ cancelled
```

---

### 2.4 approval_actions

Immutable log of all approval actions.

| Column                 | Type   | Description                              |
| ---------------------- | ------ | ---------------------------------------- |
| `id`                   | bigint | Primary key                              |
| `approval_instance_id` | bigint | FK to `approval_instances`               |
| `approval_step_id`     | bigint | FK to `approval_steps`                   |
| `action_type`          | string | `approve`, `reject`, `request_changes`   |
| `actor_id`             | bigint | FK to `users` - who performed the action |
| `comments`             | text   | Optional comments                        |
| `metadata`             | json   | Additional context (e.g., IP, browser)   |
| `timestamps`           | -      | created_at, updated_at (immutable)       |

**Note:** Records are never updated or deleted - full audit trail.

---

## 3. Models & Relationships

### 3.1 ApprovalWorkflow

```php
class ApprovalWorkflow extends Model
{
    // Relationships
    public function steps(): HasMany // → ApprovalStep
    public function instances(): HasMany // → ApprovalInstance

    // Methods
    public function setActive(): void // Activates this workflow
    public function deactivate(): void // Deactivates this workflow
    public function duplicate(string $newName): ApprovalWorkflow
}
```

### 3.2 ApprovalStep

```php
class ApprovalStep extends Model
{
    use Sortable; // Spatie package for ordering

    // Relationships
    public function workflow(): BelongsTo // → ApprovalWorkflow
    public function actions(): HasMany // → ApprovalAction

    // Casts
    protected function casts(): array
    {
        'step_type' => ApprovalStepType::class,
        'step_purpose' => ApprovalStepPurpose::class,
        'approver_type' => ApproverType::class,
        'approver_identifiers' => 'array',
        'conditional_rules' => 'array',
    }
}
```

### 3.3 ApprovalInstance

```php
class ApprovalInstance extends Model
{
    // Relationships
    public function workflow(): BelongsTo // → ApprovalWorkflow
    public function approvable(): MorphTo // → Any model using RequiresApproval
    public function currentStep(): BelongsTo // → ApprovalStep
    public function submittedBy(): BelongsTo // → User
    public function actions(): HasMany // → ApprovalAction

    // Casts
    protected function casts(): array
    {
        'status' => ApprovalInstanceStatus::class,
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    }
}
```

### 3.4 ApprovalAction

```php
class ApprovalAction extends Model
{
    // Relationships
    public function instance(): BelongsTo // → ApprovalInstance
    public function step(): BelongsTo // → ApprovalStep
    public function actor(): BelongsTo // → User

    // Casts
    protected function casts(): array
    {
        'action_type' => ApprovalActionType::class,
        'metadata' => 'array',
    }
}
```

---

## 4. RequiresApproval Trait

Models opt-in to approval workflows by using this trait.

### 4.1 Usage

```php
use App\Models\Traits\RequiresApproval;

class Program extends Model
{
    use RequiresApproval;

    const APPROVAL_DISPLAY_NAME = 'Research Program';
}
```

### 4.2 Provided Methods

```php
// Get all approval instances for this model
$program->approvalInstances(): MorphMany

// Get active approval instance
$program->getActiveApprovalInstance(): ?ApprovalInstance

// Check if has active approval
$program->hasActiveApproval(): bool

// Get approval status
$program->getApprovalStatus(): ?ApprovalInstanceStatus

// Check if approved/pending
$program->isApproved(): bool
$program->isPendingApproval(): bool

// Get display name for UI
Program::getApprovalDisplayName(): string
```

---

## 5. ApprovalWorkflowService

Business logic for workflow management.

### 5.1 Key Methods

```php
class ApprovalWorkflowService
{
    // Create new workflow with steps
    public function createWorkflow(
        string $name,
        string $modelType,
        array $steps,
        ?string $description = null,
        bool $setActive = false
    ): ApprovalWorkflow

    // Update workflow steps (deletes old, creates new)
    public function updateSteps(
        ApprovalWorkflow $workflow,
        array $steps
    ): bool

    // Set a workflow as active (deactivates others for same model)
    public function setActiveWorkflow(
        ApprovalWorkflow $workflow
    ): void

    // Deactivate a workflow
    public function deactivateWorkflow(
        ApprovalWorkflow $workflow
    ): void

    // Duplicate a workflow
    public function duplicateWorkflow(
        ApprovalWorkflow $workflow,
        string $newName
    ): ApprovalWorkflow

    // Get active workflow for a model type
    public function getActiveWorkflowForModel(
        string $modelType
    ): ?ApprovalWorkflow
}
```

---

## 6. Frontend Implementation

### 6.1 Workflow Management UI

**Location:** `resources/js/pages/admin/approval-workflows/`

**Pages:**

- `index.tsx` - List all workflows with actions (edit, duplicate, delete, activate, deactivate)
- `create.tsx` - Create new workflow with StepBuilder
- `edit.tsx` - Edit existing workflow and steps

**Components:**

- `StepBuilder.tsx` - Drag-and-drop step configuration
- `StepCard.tsx` - Individual step card with expand/collapse
- `ConfirmationDialog.tsx` - Reusable confirmation dialog (shadcn AlertDialog)
- `DuplicateDialog.tsx` - Workflow duplication dialog with name input (shadcn Dialog)

### 6.2 StepBuilder Features

- ✅ Drag-and-drop reordering
- ✅ Add/remove steps
- ✅ Expand/collapse step details
- ✅ Configure step properties:
    - Name and description
    - Step type (sequential/parallel)
    - Step purpose (approval/action)
    - Approver type (user/role/permission)
    - Approver selection
    - Required approvals count (for parallel)

### 6.3 UI/UX Enhancements

- ✅ Replaced native `confirm()` with shadcn AlertDialog
- ✅ Replaced native `prompt()` with shadcn Dialog + Input
- ✅ Modern, accessible dialogs with keyboard support
- ✅ Proper focus management and animations

---

## 7. How The System Works

### 7.1 Workflow Configuration (Admin)

1. **Admin creates a workflow:**
    - Selects target model type (e.g., `App\Models\Program`)
    - Defines workflow name and description
    - Adds approval steps using StepBuilder

2. **Configuring each step:**
    - Sets step name (e.g., "Finance Review")
    - Chooses step type (sequential/parallel)
    - Chooses step purpose (approval/action)
    - Selects approver type (user/role/permission)
    - Picks specific approvers from dropdown
    - For parallel steps, sets required approval count

3. **Activating the workflow:**
    - Only one workflow can be active per model type
    - Activating a new workflow deactivates the previous one
    - Active workflows are used for new submissions

### 7.2 Workflow Execution (Runtime)

**Current Status:** 🔄 In Development

**Planned Flow:**

1. **User submits a model for approval:**

    ```php
    // In controller or action
    $program = Program::create([...]);

    // Initialize approval workflow
    $instance = WorkflowEngine::initializeWorkflow($program, auth()->user());
    ```

2. **WorkflowEngine creates an ApprovalInstance:**
    - Links to the active workflow for `Program`
    - Sets status to `draft`
    - Records submitter

3. **User calls submitForApproval:**

    ```php
    $instance->submit();
    ```

4. **Status changes to `pending_approval`:**
    - Sets `submitted_at` timestamp
    - Identifies first step based on `step_order`
    - Sets `current_step_id`

5. **Approvers receive notification** (future):
    - System identifies approvers for current step
    - Sends notifications via configured channels

6. **Approver acts on the step:**

    ```php
    WorkflowEngine::approve($instance, $step, auth()->user(), 'Approved');
    // or
    WorkflowEngine::reject($instance, $step, auth()->user(), 'Needs revision');
    ```

7. **System creates ApprovalAction record:**
    - Logs who, when, what, and why
    - Immutable audit trail

8. **Workflow progresses:**
    - **Sequential:** Moves to next step if approved
    - **Parallel:** Checks if required approval count met
    - **Rejection:** Sets status to `rejected`, workflow ends
    - **All steps complete:** Sets status to `approved`, sets `completed_at`

### 7.3 Step Purpose Logic

**Approval Steps:**

- If requester is in the approver list → Auto-skip step
- Otherwise → Requires explicit approval

**Action Steps:**

- Always executes, cannot be auto-skipped
- Used for mandatory actions (e.g., upload receipt, verify documents)
- Requester must complete the action even if they're in the approver list

### 7.4 Parallel Approval Logic

For parallel steps with `required_approvals_count = 2`:

```
Step: "Management Review" (Parallel)
Approvers: [User A, User B, User C]
Required count: 2

Scenario 1: User A approves, User B approves → Step complete ✓
Scenario 2: User A rejects → Step fails, workflow rejected ✗
Scenario 3: User A approves, User B approves, User C approves → Still complete ✓
```

### 7.5 Dynamic Field Discovery

**Problem:** Different models have different fields available for conditional rules.

**Solution:** `ModelFieldDiscoveryService` introspects models and passes fields via Inertia page props.

**Flow:**

1. Admin visits create/edit page
2. Backend uses `ModelFieldDiscoveryService` to discover fields for model(s)
3. Fields passed as Inertia page props (`modelFieldsMap` for create, `modelFields` for edit)
4. `RuleBuilder` receives fields via prop chain and dynamically populates dropdown
5. When `model_type` changes in create form, frontend selects different fields from `modelFieldsMap` (no HTTP request needed)

**Supported Field Types:**

- **Scalar:** string, number, date, boolean
- **Relationships:** BelongsTo, HasOne (dot notation, e.g., `site.name`)

**Example:**

- `PaymentRequest` shows: `total_amount`, `vendor_name`, `batch_time`, `site.site_name`
- `Program` shows: `total_budget`, `fiscal_year`, `status`, `site.site_name`
- `Settlement` shows: `actual_amount`, `surplus_amount`, `payment_request.total_amount`

**Implementation Details:**

- Service caches field discovery results for 5 minutes
- Fields exclude foreign keys ending in `_id` or `_by`
- Relationship fields are prefixed with relationship name using dot notation
- Human-readable labels generated from field names (e.g., `total_amount` → "Total Amount")

---

## 8. Enums

### 8.1 ApprovalStepType

```php
enum ApprovalStepType: string
{
    case Sequential = 'sequential';
    case Parallel = 'parallel';
}
```

### 8.2 ApprovalStepPurpose

```php
enum ApprovalStepPurpose: string
{
    case Approval = 'approval'; // Can be auto-skipped
    case Action = 'action'; // Always executes
}
```

### 8.3 ApproverType

```php
enum ApproverType: string
{
    case User = 'user';
    case Role = 'role';
    case Permission = 'permission';
}
```

### 8.4 ApprovalInstanceStatus

```php
enum ApprovalInstanceStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
```

### 8.5 ApprovalActionType

```php
enum ApprovalActionType: string
{
    case Approve = 'approve';
    case Reject = 'reject';
    case RequestChanges = 'request_changes'; // Future
}
```

---

## 9. Controllers & Routes

### 9.1 ApprovalWorkflowController

**Routes:** `routes/web.php` (under `admin` prefix)

| Method | URI                                         | Action       | Description         |
| ------ | ------------------------------------------- | ------------ | ------------------- |
| GET    | `/admin/approval-workflows`                 | `index`      | List all workflows  |
| GET    | `/admin/approval-workflows/create`          | `create`     | Show create form    |
| POST   | `/admin/approval-workflows`                 | `store`      | Create workflow     |
| GET    | `/admin/approval-workflows/{id}/edit`       | `edit`       | Show edit form      |
| PUT    | `/admin/approval-workflows/{id}`            | `update`     | Update workflow     |
| DELETE | `/admin/approval-workflows/{id}`            | `destroy`    | Delete workflow     |
| POST   | `/admin/approval-workflows/{id}/duplicate`  | `duplicate`  | Duplicate workflow  |
| POST   | `/admin/approval-workflows/{id}/activate`   | `setActive`  | Activate workflow   |
| POST   | `/admin/approval-workflows/{id}/deactivate` | `deactivate` | Deactivate workflow |

**Permissions:** All routes protected by `admin` middleware

---

## 10. Dependencies & Libraries

| Package                      | Purpose                             | Version                    |
| ---------------------------- | ----------------------------------- | -------------------------- |
| `spatie/laravel-permission`  | Role and permission-based approvers | Compatible with Laravel 12 |
| `spatie/laravel-activitylog` | Audit logging (future)              | Compatible with Laravel 12 |
| `spatie/eloquent-sortable`   | Step ordering                       | Compatible with Laravel 12 |
| `@dnd-kit/core`              | Drag-and-drop in StepBuilder        | Latest                     |
| `@dnd-kit/sortable`          | Sortable items                      | Latest                     |
| `shadcn/ui`                  | UI components (Dialog, AlertDialog) | Latest                     |

---

## 11. Key Design Decisions

### 11.1 Why No Versioning?

**Initial Design:** Workflows had versions, instances linked to versions.

**Problem:** Complexity with minimal benefit. Most workflows evolve slowly.

**Solution:** Removed versioning. Workflows are directly editable. Active instances reference the workflow at their creation time via immutable `ApprovalAction` records.

**Trade-off:** Editing a workflow doesn't affect in-flight instances (they reference steps by ID). If a step is deleted, in-flight instances may encounter issues. This is acceptable for the current use case.

### 11.2 Why Step Purpose?

**Need:** Distinguish between steps that require approval vs. steps that require action.

**Use Case:**

- Program submission requires approval from PI, Finance
- Once approved, Finance must upload transfer receipt (action step)

**Benefit:**

- Approval steps can be auto-skipped if requester is approver
- Action steps always execute, ensuring mandatory actions are completed

### 11.3 Why Polymorphic Approvable?

**Flexibility:** Any model can require approval without coupling to a specific table.

**Scalability:** Easy to add approval to new models - just use the trait.

**Example:** `Program`, `PaymentRequest`, `PurchaseOrder` all use `RequiresApproval` trait.

---

## 12. Security Considerations

### 12.1 Authorization

- ✅ All workflow management routes require admin permissions
- ✅ Workflow execution will check user permissions via policies
- ✅ Approver type validation (user exists, role exists, permission exists)

### 12.2 Audit Trail

- ✅ All actions logged with actor, timestamp, and comments
- ✅ Immutable `approval_actions` table - no updates/deletes
- 🔄 Integration with `spatie/laravel-activitylog` (planned)

### 12.3 Data Integrity

- ✅ Foreign key constraints with appropriate cascade/nullOnDelete
- ✅ Soft deletes on workflows and instances (recoverable)
- ✅ Transaction wrapping for workflow create/update

---

## 13. Performance Considerations

### 13.1 Database Indexes

```sql
-- approval_workflows: No specific index needed (small table)
-- approval_steps: Will add composite index when needed
-- approval_instances:
  INDEX (status, submitted_by)
  INDEX (approvable_type, approvable_id)
-- approval_actions:
  INDEX (approval_instance_id, created_at)
  INDEX (actor_id, action_type)
```

### 13.2 Eager Loading

```php
// Load workflow with steps
$workflow = ApprovalWorkflow::with('steps')->find($id);

// Load instance with relationships
$instance = ApprovalInstance::with([
    'workflow.steps',
    'approvable',
    'actions.actor',
])->find($id);
```

---

## 14. Testing Strategy

### 14.1 Unit Tests (Implemented)

- ✅ ModelFieldDiscoveryService field extraction and type detection
- ✅ Field discovery for PaymentRequest, Program, Settlement models
- ✅ Relationship field discovery (BelongsTo/HasOne)
- ✅ Caching behavior verification
- ⏸️ ApprovalRuleEvaluator condition evaluation (pending)
- ⏸️ WorkflowEngine state transitions (pending)

### 14.2 Feature Tests (Implemented)

- ✅ Approval workflow model creation and scoping
- ✅ ApprovalWorkflowService methods (activate, deactivate, duplicate)
- ✅ ApprovalStepPurpose enum labels and methods
- ✅ Dynamic field props passed to create page (modelFieldsMap)
- ✅ Dynamic field props passed to edit page (modelFields)
- ✅ Field structure validation (value, label, type properties)
- ✅ Authorization requirements for create/edit pages
- ⏸️ Complete workflow execution flow (pending)
- ⏸️ Parallel approval logic (pending)
- ⏸️ Auto-skip approval steps (pending)

### 14.3 Browser Tests (Planned)

- ⏸️ StepBuilder drag-and-drop
- ⏸️ Workflow create/edit UI
- ⏸️ Dialog interactions
- ⏸️ RuleBuilder dynamic field selection

---

## 15. Future Enhancements

### 15.1 Conditional Rules (✅ Implemented)

**Status:** ✅ **Completed** - See section 7.5 for implementation details.

**Features:**

- Rule-based step execution (skip/enable steps based on model attributes)
- Support for scalar fields and relationships (dot notation)
- Logical operators (AND/OR) for combining multiple conditions
- Comparison operators: =, !=, >, >=, <, <=, contains, starts_with, ends_with, in, is_null, is_not_null
- Dynamic field discovery per model type
- Frontend RuleBuilder component with UI for building rules

**Example:** CEO approval only required if `total_amount > 10000`.

### 15.2 Notifications

**Goal:** Notify approvers when action is needed.

**Channels:** Email, Slack, In-app

**Events:**

- Approval needed
- Approval reminder (SLA)
- Approval approved/rejected

### 15.3 Delegated Approvals

**Goal:** Allow approvers to delegate to others.

**Use Case:** Manager delegates to deputy while on vacation.

### 15.4 Visual Workflow Builder

**Goal:** Flowchart-style workflow configuration.

**Library:** React Flow or similar

### 15.5 Approval Templates

**Goal:** Predefined workflow templates for common use cases.

**Examples:**

- Standard Purchase Approval
- Financial Request Approval
- Content Publishing Workflow

---

## 16. Known Limitations

### 16.1 In-Flight Workflow Updates

**Issue:** Editing a workflow may affect in-flight instances if steps are deleted.

**Mitigation:**

- Warn admins when editing active workflows
- Consider duplicating workflow instead of editing
- Future: Implement soft workflow versioning

### 16.2 No Workflow Branching

**Current:** Linear or parallel steps only.

**Future:** Conditional branching (if X, go to Step A, else Step B).

### 16.3 No Escalation

**Current:** No automatic escalation if approvers don't respond.

**Future:** SLA-based escalation to higher authority.

### 16.4 Relationship Depth Limit

**Current:** Only BelongsTo/HasOne relationships supported for conditional rules (e.g., `site.name`, `program.budget`).

**Limitation:** HasMany/BelongsToMany relationships not supported as they return collections, not scalar values.

**Rationale:** Rule comparisons work on scalar values, not arrays. Supporting collection-based rules would require complex aggregation logic (e.g., "sum of all items > 100").

### 16.5 Field Discovery Performance

**Impact:** Initial field discovery takes ~50-100ms per model.

**Mitigation:**

- Results cached for 5 minutes
- Cache hit reduces overhead to <1ms
- Minimal impact on user experience (fields loaded with page props)

**Future:** Consider pre-generating field metadata during deployment or using persistent cache.

---

## 17. Acceptance Criteria

### Core Features (Implemented)

- ✅ A workflow can be attached to any model via trait
- ✅ Workflows can be created, edited, deleted via UI
- ✅ Steps can be configured with approvers, type, purpose
- ✅ Drag-and-drop step ordering
- ✅ Only one active workflow per model type
- ✅ Duplicate workflow functionality
- ✅ Modern UI with shadcn dialogs

### Runtime Features (In Progress)

- 🔄 Submit model for approval
- 🔄 Approve/reject steps
- 🔄 Track approval status
- 🔄 Complete audit trail

### Advanced Features (Pending)

- ⏸️ Conditional rules evaluation
- ⏸️ Parallel approval logic
- ⏸️ Auto-skip approval steps
- ⏸️ Notifications

---

## 18. Open Questions

### 18.1 Rule DSL Standardization

**Question:** What format for conditional rules?

**Options:**

- JSON-based (current)
- Laravel's query builder syntax
- Third-party rule engine (Ruler, Business Rules)

**Decision:** JSON-based for MVP, evaluate others later.

### 18.2 Notification Strategy

**Question:** How to handle notifications?

**Options:**

- Laravel notifications (email, database)
- Third-party (SendGrid, Twilio)
- Event-driven (broadcast to frontend)

**Decision:** Pending product owner input.

### 18.3 Frontend State Management

**Question:** Use React context or global state (Zustand/Redux)?

**Decision:** Current approach (component state) is fine for MVP. Re-evaluate if complexity grows.

---

## 19. Changelog

| Date       | Change                                                   | Author        |
| ---------- | -------------------------------------------------------- | ------------- |
| 2026-01-31 | Initial implementation: Database schema, models, service | Backend Team  |
| 2026-01-31 | Added `step_purpose` field (approval/action)             | Backend Team  |
| 2026-01-31 | Removed workflow versioning (simplification)             | Backend Team  |
| 2026-02-01 | Frontend: StepBuilder, create/edit pages                 | Frontend Team |
| 2026-02-01 | Fixed: Step purpose not populating in edit mode          | Frontend Team |
| 2026-02-01 | Enhanced: Replaced native alerts with shadcn dialogs     | Frontend Team |
| 2026-02-01 | **Updated PRD with comprehensive system documentation**  | AI Assistant  |

---

## 20. References

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6)
- [Spatie Eloquent Sortable](https://github.com/spatie/eloquent-sortable)
- [DnD Kit Documentation](https://dndkit.com/)
- [Shadcn UI Components](https://ui.shadcn.com/)

---

**Document Owner:** Product Team  
**Last Reviewed:** 2026-02-01  
**Next Review:** 2026-02-15
