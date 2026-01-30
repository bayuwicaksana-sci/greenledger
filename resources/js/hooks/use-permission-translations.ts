import { usePage } from '@inertiajs/react';

import type {
    GroupedPermissions,
    PermissionTranslation,
    PermissionWithTranslation,
    SharedData,
} from '@/types';

/**
 * Hook for accessing permission translations from Inertia shared data.
 *
 * @example
 * ```tsx
 * const { getLabel, getDescription, groupByAction } = usePermissionTranslations();
 *
 * // Get human-readable label
 * const label = getLabel('programs.view.all'); // "View All Programs"
 *
 * // Get description
 * const desc = getDescription('programs.view.all'); // "View programs across all sites..."
 *
 * // Group by action type for UI display
 * const grouped = groupByAction();
 * // { view: [...], create: [...], update: [...] }
 * ```
 */
export function usePermissionTranslations() {
    const { permissionTranslations } = usePage<SharedData>().props;

    /**
     * Get complete translation (label + description) for a specific permission.
     *
     * @param permission - The permission key (e.g., 'programs.view.all')
     * @returns Translation object or null if not found
     */
    const getPermission = (
        permission: string,
    ): PermissionTranslation | null => {
        return permissionTranslations?.[permission] || null;
    };

    /**
     * Get human-readable label for a permission.
     *
     * @param permission - The permission key
     * @returns Label string, or the permission key if translation not found
     */
    const getLabel = (permission: string): string => {
        return permissionTranslations?.[permission]?.label || permission;
    };

    /**
     * Get description for a permission.
     *
     * @param permission - The permission key
     * @returns Description string, or empty string if not found
     */
    const getDescription = (permission: string): string => {
        return permissionTranslations?.[permission]?.description || '';
    };

    /**
     * Group permissions by action type (view, create, update, delete, etc.).
     * Useful for organizing permissions in role configuration UI.
     *
     * @returns Object with action types as keys and arrays of permissions as values
     *
     * @example
     * ```tsx
     * const grouped = groupByAction();
     * // {
     * //   view: [
     * //     { permission: 'programs.view.all', label: '...', description: '...' },
     * //     ...
     * //   ],
     * //   create: [...],
     * //   ...
     * // }
     * ```
     */
    const groupByAction = (): GroupedPermissions => {
        const grouped: GroupedPermissions = {};

        Object.entries(permissionTranslations || {}).forEach(
            ([permission, translation]) => {
                // Extract action from permission (e.g., "programs.view.all" -> "view")
                const parts = permission.split('.');
                const action = parts[1] || 'other';

                if (!grouped[action]) {
                    grouped[action] = [];
                }

                grouped[action].push({
                    permission,
                    label: translation.label,
                    description: translation.description,
                });
            },
        );

        return grouped;
    };

    /**
     * Group permissions by resource (dashboard, programs, payments, etc.).
     * Alternative grouping strategy for different UI needs.
     *
     * @returns Object with resource names as keys and arrays of permissions as values
     *
     * @example
     * ```tsx
     * const grouped = groupByResource();
     * // {
     * //   programs: [
     * //     { permission: 'programs.view.all', label: '...', description: '...' },
     * //     { permission: 'programs.create.all', label: '...', description: '...' },
     * //     ...
     * //   ],
     * //   'payment-requests': [...],
     * //   ...
     * // }
     * ```
     */
    const groupByResource = (): GroupedPermissions => {
        const grouped: GroupedPermissions = {};

        Object.entries(permissionTranslations || {}).forEach(
            ([permission, translation]) => {
                // Extract resource from permission (e.g., "programs.view.all" -> "programs")
                const parts = permission.split('.');
                const resource = parts[0] || 'other';

                if (!grouped[resource]) {
                    grouped[resource] = [];
                }

                grouped[resource].push({
                    permission,
                    label: translation.label,
                    description: translation.description,
                });
            },
        );

        return grouped;
    };

    /**
     * Search permissions by keyword in label or description.
     *
     * @param query - Search keyword
     * @returns Array of matching permissions with translations
     *
     * @example
     * ```tsx
     * const results = search('program');
     * // Returns all permissions with 'program' in label or description
     * ```
     */
    const search = (query: string): PermissionWithTranslation[] => {
        if (!query.trim()) {
            return [];
        }

        const lowerQuery = query.toLowerCase();

        return Object.entries(permissionTranslations || {})
            .filter(
                ([permission, translation]) =>
                    permission.toLowerCase().includes(lowerQuery) ||
                    translation.label.toLowerCase().includes(lowerQuery) ||
                    translation.description.toLowerCase().includes(lowerQuery),
            )
            .map(([permission, translation]) => ({
                permission,
                label: translation.label,
                description: translation.description,
            }));
    };

    /**
     * Get all permissions as a flat array with translations.
     *
     * @returns Array of all permissions with their translations
     */
    const getAll = (): PermissionWithTranslation[] => {
        return Object.entries(permissionTranslations || {}).map(
            ([permission, translation]) => ({
                permission,
                label: translation.label,
                description: translation.description,
            }),
        );
    };

    return {
        /**
         * Get complete translation object for a permission
         */
        getPermission,

        /**
         * Get human-readable label for a permission
         */
        getLabel,

        /**
         * Get description for a permission
         */
        getDescription,

        /**
         * Group permissions by action type (view, create, update, etc.)
         */
        groupByAction,

        /**
         * Group permissions by resource (programs, payments, etc.)
         */
        groupByResource,

        /**
         * Search permissions by keyword
         */
        search,

        /**
         * Get all permissions as flat array
         */
        getAll,

        /**
         * Raw permission translations object
         */
        all: permissionTranslations || {},
    };
}
