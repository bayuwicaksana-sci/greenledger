import type { FiscalYear } from './fiscal_year';

export interface Program {
    id: number;
    site_id: number;
    program_code: string;
    program_name: string;
    description: string | null;
    fiscal_year: FiscalYear;
    status: 'DRAFT' | 'ACTIVE' | 'COMPLETED' | 'ARCHIVED';
    total_budget: number;
    start_date: string | null;
    end_date: string | null;
    actual_start_date: string | null;
    actual_end_date: string | null;
    completion_reason: string | null;
    research_report_url: string | null;
    created_at: string;
    updated_at: string;
    created_by: number;
    updated_by: number;

    // PRD fields
    program_type: 'SINGLE_YEAR' | 'MULTI_YEAR' | null;
    program_category: 'RESEARCH' | 'TRIAL' | 'PRODUCTION' | null;
    commodity_id: number | null;
    research_associate_id: number | null;
    research_officer_id: number | null;
    planting_start_date: string | null;
    estimated_duration_days: number | null;
    harvest_type: 'single' | 'multiple' | null;
    estimated_harvest_date: string | null;
    harvest_frequency_value: number | null;
    harvest_frequency_unit: 'days' | 'weeks' | null;
    harvest_event_count: number | null;
    first_harvest_date: string | null;
    last_harvest_date: string | null;
    prerequisite_program_id: number | null;
    dependency_note: string | null;
    background_text: string | null;
    problem_statement: string | null;
    hypothesis: string | null;
    objectives: string[] | null;
    journal_references: string | null;
    trial_design: string | null;
    trial_design_other: string | null;
    num_treatments: number | null;
    num_replications: number | null;
    num_samples_per_replication: number | null;
    plot_width_m: number | null;
    plot_length_m: number | null;
    google_maps_url: string | null;

    // Loaded relations
    treatments?: ProgramTreatment[];
    budget_items?: ProgramBudgetItem[];
}

export interface Activity {
    id: number;
    program_id: number;
    activity_name: string;
    description: string | null;
    budget_allocation: number;
    planned_start_date: string | null;
    planned_end_date: string | null;
    actual_start_date: string | null;
    actual_end_date: string | null;
    status: 'PLANNED' | 'ACTIVE' | 'COMPLETED' | 'CANCELLED';
    sort_order: number | null;
    created_at: string;
    updated_at: string;
}

export interface ProgramWithRelations extends Omit<Program, 'created_by'> {
    created_by: { id: number; name: string; email: string };
    activities: Activity[];
}

export interface Commodity {
    id: number;
    commodity_code: string;
    commodity_name: string;
    description: string | null;
    is_active: boolean;
}

export interface ProgramTreatment {
    id: number;
    program_id: number;
    treatment_code: string;
    treatment_description: string | null;
    specification: string | null;
    sort_order: number;
}

export interface ProgramBudgetCategory {
    id: number;
    category_name: string;
    sort_order: number;
}

export interface ProgramBudgetPhase {
    id: number;
    phase_name: string;
    sort_order: number;
}

export interface ProgramBudgetItem {
    id: number;
    program_id: number;
    category_id: number;
    phase_id: number;
    item_description: string;
    specification: string | null;
    unit: string;
    qty: number;
    unit_price: number;
    subtotal: number;
    notes: string | null;
    sort_order: number;
}

export interface MediaItem {
    id: number;
    original_name: string;
    url: string;
}

// Form-level types (used in create/edit state)
export interface TreatmentFormData {
    treatment_code: string;
    treatment_description: string;
    specification: string;
}

export interface BudgetItemFormData {
    category_id: string;
    phase_id: string;
    item_description: string;
    specification: string;
    unit: string;
    qty: string;
    unit_price: string;
    subtotal: number;
    notes: string;
}
