import { usePage } from '@inertiajs/react';
import type { SharedData } from '@/types';

export function usePermissions() {
    const { auth } = usePage<SharedData>().props;

    const hasPermission = (permission: string): boolean => {
        return auth.allPermissions?.includes(permission) ?? false;
    };

    const hasAnyPermission = (permissions: string[]): boolean => {
        return permissions.some((p) => hasPermission(p));
    };

    const hasAllPermissions = (permissions: string[]): boolean => {
        return permissions.every((p) => hasPermission(p));
    };

    const hasRole = (role: string): boolean => {
        return auth.roles?.includes(role) ?? false;
    };

    const hasAnyRole = (roles: string[]): boolean => {
        return roles.some((r) => hasRole(r));
    };

    return {
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        hasRole,
        hasAnyRole,
        permissions: auth.allPermissions ?? [],
        roles: auth.roles ?? [],
    };
}
