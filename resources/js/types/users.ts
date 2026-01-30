export type User = {
    id: number;
    name: string;
    email: string;
    full_name: string | null;
    is_active: boolean;
    must_change_password: boolean;
    primary_site_id: number | null;
    primary_site_name: string | null;
    sites: UserSite[];
    roles: UserRole[];
    last_login_at: string | null;
    last_login_ip: string | null;
    email_verified_at: string | null;
    created_at: string;
};

export type UserSite = {
    id: number;
    name: string;
    code: string;
    granted_at: string;
    granted_by: number;
};

export type UserRole = {
    id: number;
    name: string;
};

export type UserInvitation = {
    id: number;
    email: string;
    full_name: string;
    primary_site_name: string;
    additional_sites: string[];
    roles: string[];
    invited_by_name: string;
    expires_at: string;
    is_expired: boolean;
    accepted_at: string | null;
    created_at: string;
};

export type UserAccessLog = {
    id: number;
    user_id: number;
    user: {
        id: number;
        name: string;
        email: string;
    };
    action: string;
    ip_address: string;
    user_agent: string | null;
    details: Record<string, any> | null;
    created_at: string;
};

export type UserFormData = {
    email: string;
    full_name: string;
    primary_site_id: number;
    additional_site_ids: number[];
    role_ids: number[];
};

export type BulkInviteData = {
    users: UserFormData[];
};

export type Site = {
    id: number;
    site_name: string;
    site_code: string;
};

export type Role = {
    id: number;
    name: string;
    guard_name: string;
    permissions_count?: number;
    users_count?: number;
};
