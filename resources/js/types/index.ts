export type * from './approval';
export type * from './auth';
export type * from './coa';
export type * from './fiscal_year';
export type * from './navigation';
export type * from './permissions';
export type * from './programs';
export type * from './roles';
export type * from './site';
export type * from './ui';

import type { Auth } from './auth';
import type { PermissionTranslations } from './permissions';
import type { Site } from './site';

export type SharedData = {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    site_code?: string;
    sites: Site[];
    navigation: Array<{
        title?: string | null;
        items: Array<{
            title: string;
            route: string;
            icon: string;
            badge?: number | string;
            badgeCount?: number;
        }>;
    }>;
    permissionTranslations: PermissionTranslations;
    [key: string]: unknown;
};
