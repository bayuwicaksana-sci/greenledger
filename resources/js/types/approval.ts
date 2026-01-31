// Approval System Types

import type { User } from './auth';

export interface ApprovalWorkflow {
    id: number;
    name: string;
    description: string | null;
    model_type: string;
    is_active: boolean;
    configuration: Record<string, any> | null;
    created_at: string;
    updated_at: string;
    versions_count?: number;
    instances_count?: number;
    active_version?: ApprovalWorkflowVersion;
    versions?: ApprovalWorkflowVersion[];
}

export interface ApprovalWorkflowVersion {
    id: number;
    approval_workflow_id: number;
    version_number: number;
    is_active: boolean;
    configuration: Record<string, any> | null;
    created_at: string;
    updated_at: string;
    steps?: ApprovalStep[];
    workflow?: ApprovalWorkflow;
}

export interface ApprovalStep {
    id: number;
    approval_workflow_version_id: number;
    name: string;
    description: string | null;
    step_order: number;
    step_type: 'sequential' | 'parallel';
    approver_type: 'user' | 'role' | 'permission';
    approver_identifiers: (string | number)[];
    required_approvals_count: number | null;
    conditional_rules: Record<string, any> | null;
    created_at: string;
    updated_at: string;
}

export interface ApprovalInstance {
    id: number;
    approval_workflow_id: number;
    approvable_type: string;
    approvable_id: number;
    status: ApprovalInstanceStatus;
    submitted_by: number;
    submitted_at: string | null;
    completed_at: string | null;
    metadata: Record<string, any> | null;
    created_at: string;
    updated_at: string;
    workflow?: ApprovalWorkflow;
    submitter?: User;
    current_step?: ApprovalInstanceStep;
    steps?: ApprovalInstanceStep[];
    actions?: ApprovalAction[];
}

export interface ApprovalInstanceStep {
    id: number;
    approval_instance_id: number;
    approval_step_id: number;
    step_order: number;
    status: ApprovalStepStatus;
    started_at: string | null;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
    step?: ApprovalStep;
    actions?: ApprovalAction[];
}

export interface ApprovalAction {
    id: number;
    approval_instance_id: number;
    approval_instance_step_id: number;
    user_id: number;
    action: ApprovalActionType;
    comments: string | null;
    metadata: Record<string, any> | null;
    created_at: string;
    updated_at: string;
    user?: User;
}

export type ApprovalInstanceStatus =
    | 'draft'
    | 'pending_approval'
    | 'approved'
    | 'rejected'
    | 'changes_requested'
    | 'cancelled';

export type ApprovalStepStatus =
    | 'pending'
    | 'in_progress'
    | 'approved'
    | 'rejected'
    | 'changes_requested'
    | 'skipped';

export type ApprovalActionType = 'approve' | 'reject' | 'request_changes';

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
}
