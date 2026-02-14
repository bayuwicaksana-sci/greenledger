export type Site = {
    id: number;
    site_name: string;
    site_code: string;
    address: string;
    contact_info?: {
        phone: string;
        email: string;
    };
    is_active?: boolean;
    users_count?: number;
    programs_count?: number;
    coa_accounts_count?: number;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string | null;
};
