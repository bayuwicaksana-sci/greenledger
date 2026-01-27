export type * from './auth';
export type * from './navigation';
export type * from './ui';

import type { Auth } from './auth';

export type SharedData = {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    site_code?: string;
    sites: [
        {
            site_name: string;
            site_code: string;
        },
    ];
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
    [key: string]: unknown;
};
