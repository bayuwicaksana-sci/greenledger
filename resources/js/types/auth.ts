import type { PaginatedResponse } from '@/types';

export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    primarySite: string | null;
    roles: string[];
    allPermissions: string[];
    notifications: PaginatedResponse<Notification>;
    unreadCount: number;
};

export type Notification = {
    id: string;
    type: string;
    data: {
        message: string;
        url?: string;
        type?: string;
    };
    read_at: string | null;
    created_at: string;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};
