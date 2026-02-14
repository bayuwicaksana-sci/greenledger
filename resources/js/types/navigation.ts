import type { InertiaLinkProps } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';

export type BreadcrumbItem = {
    title: string;
    href: string;
};

export type NavGroup = {
    title?: string | null;
    navItems: NavItem[];
};

export type NavItem = {
    title: string;
    href?: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
    subItems?: SubNavItem[];
    badge?: number | string;
};

export type SubNavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    isActive?: boolean;
    badge?: number | string;
};
