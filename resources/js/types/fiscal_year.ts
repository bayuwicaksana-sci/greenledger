export type FiscalYear = {
    id: number;
    year: number;
    start_date: string;
    end_date: string;
    is_closed: boolean;
    programs_count?: number;
    created_at?: string;
    updated_at?: string;
};
