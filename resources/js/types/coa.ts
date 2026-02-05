import type { Site } from './site';

export type CoaAccount = {
    id: number;
    site_id: number;
    account_code: string;
    account_name: string;
    account_type: 'REVENUE' | 'EXPENSE';
    short_description?: string;
    is_active: boolean;
    hierarchy_level: number;
    parent_account_id: number | null;
    initial_budget: number;
    allocated_budget: number;
    actual_amount: number;
    balance: number;
    category: 'PROGRAM' | 'NON_PROGRAM';
    sub_category: string | null;
    typical_usage: string | null;
    tax_applicable: boolean;
    approval_status: 'draft' | 'pending_approval' | 'approved' | 'rejected' | 'cancelled' | null;
    site?: Site;
};
