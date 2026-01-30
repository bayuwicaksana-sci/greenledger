import { type SortingState } from '@tanstack/react-table';
import axios from 'axios';
import {
    ChevronDown,
    ChevronRight,
    Loader2,
    Save,
    Search,
    X,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { usePermissionTranslations } from '@/hooks/use-permission-translations';
import { useToast } from '@/hooks/use-toast';
import type { PermissionWithTranslation, Role } from '@/types';

interface RolePermissionsDrawerProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    role: Role | null;
    onSuccess: () => void;
}

export function RolePermissionsDrawer({
    open,
    onOpenChange,
    role,
    onSuccess,
}: RolePermissionsDrawerProps) {
    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);
    const [selectedPermissions, setSelectedPermissions] = useState<Set<string>>(
        new Set(),
    );
    const [originalPermissions, setOriginalPermissions] = useState<Set<string>>(
        new Set(),
    );
    const [searchQuery, setSearchQuery] = useState('');
    const [expandedGroups, setExpandedGroups] = useState<Set<string>>(
        // new Set(['view', 'create', 'update', 'delete']),
        new Set(),
    );
    const [sorting, setSorting] = useState<SortingState>([]);
    const [allPermissions, setAllPermissions] = useState<
        Array<{ id: number; name: string }>
    >([]);

    const { toast } = useToast();
    const { getPermission } = usePermissionTranslations();

    // Load all permissions when drawer opens
    useEffect(() => {
        if (open && role) {
            loadAllPermissions();
            loadRolePermissions();
        } else if (!open) {
            setSelectedPermissions(new Set());
            setOriginalPermissions(new Set());
            setSearchQuery('');
        }
    }, [open, role]);

    const loadAllPermissions = async () => {
        try {
            const response = await axios.get('/api/permissions');
            setAllPermissions(response.data.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load permissions',
                variant: 'destructive',
            });
        }
    };

    const loadRolePermissions = async () => {
        if (!role) return;

        setLoading(true);
        try {
            const response = await axios.get(`/api/roles/${role.id}`);
            const roleData = response.data.data;
            const permissionNames = new Set(
                roleData.permissions.map((p: any) => p.name),
            );
            setSelectedPermissions(permissionNames);
            setOriginalPermissions(new Set(permissionNames));
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load role permissions',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    // Helper function to format permission name when no translation exists
    const formatPermissionName = (permissionName: string) => {
        // Convert "programs.view.all" to "View All Programs"
        const parts = permissionName.split('.');
        if (parts.length >= 2) {
            const resource = parts[0].replace(/-/g, ' ');
            const action = parts[1].replace(/-/g, ' ');
            const scope = parts[2] ? parts[2].replace(/-/g, ' ') : '';
            return `${action} ${scope} ${resource}`
                .split(' ')
                .filter(Boolean)
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }
        return permissionName
            .split('.')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    // Helper function to format module name for group headers
    const formatModuleName = (module: string) => {
        return module
            .split('-')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    // Prepare permissions data with translations
    const permissionsData: PermissionWithTranslation[] = useMemo(() => {
        return allPermissions.map((perm) => {
            const parts = perm.name.split('.');
            const resource = parts[0] || 'other'; // Group by resource/module
            const action = parts[1] || '';

            // Try to get translation, fall back to formatted name
            const translation = getPermission(perm.name);
            const label = translation?.label || formatPermissionName(perm.name);
            const description =
                translation?.description ||
                `Permission to ${action} ${resource}`;

            return {
                name: perm.name,
                label,
                description,
                selected: selectedPermissions.has(perm.name),
                action: resource, // Use resource as the grouping key
            };
        });
    }, [allPermissions, selectedPermissions, getPermission]);

    // Filter permissions by search
    const filteredPermissions = useMemo(() => {
        if (!searchQuery) return permissionsData;
        const query = searchQuery.toLowerCase();
        return permissionsData.filter(
            (p) =>
                p.name.toLowerCase().includes(query) ||
                p.label.toLowerCase().includes(query) ||
                p.description.toLowerCase().includes(query),
        );
    }, [permissionsData, searchQuery]);

    // Group permissions by action
    const groupedPermissions = useMemo(() => {
        const groups: Record<string, PermissionWithTranslation[]> = {};
        filteredPermissions.forEach((perm) => {
            if (!groups[perm.action]) {
                groups[perm.action] = [];
            }
            groups[perm.action].push(perm);
        });
        return groups;
    }, [filteredPermissions]);

    // Define priority order for common modules/features, rest will be alphabetically sorted
    const priorityModules = [
        'dashboard',
        'programs',
        'activities',
        'payments',
        'payment-requests',
        'budget',
        'revenue',
        'expenses',
        'sites',
        'users',
        'roles',
        'audit',
        'reports',
    ];

    // Get all module groups sorted by priority first, then alphabetically
    const sortedModules = useMemo(() => {
        const allModules = Object.keys(groupedPermissions);

        // Separate priority modules from others
        const priority = allModules.filter(module => priorityModules.includes(module));
        const others = allModules.filter(module => !priorityModules.includes(module)).sort();

        // Sort priority modules according to priorityModules order
        const sortedPriority = priorityModules.filter(module => priority.includes(module));

        return [...sortedPriority, ...others];
    }, [groupedPermissions]);

    const isDirty = useMemo(() => {
        if (selectedPermissions.size !== originalPermissions.size) return true;
        for (const perm of selectedPermissions) {
            if (!originalPermissions.has(perm)) return true;
        }
        return false;
    }, [selectedPermissions, originalPermissions]);

    const togglePermission = (permissionName: string) => {
        setSelectedPermissions((prev) => {
            const newSet = new Set(prev);
            if (newSet.has(permissionName)) {
                newSet.delete(permissionName);
            } else {
                newSet.add(permissionName);
            }
            return newSet;
        });
    };

    const toggleGroup = (action: string) => {
        setExpandedGroups((prev) => {
            const newSet = new Set(prev);
            if (newSet.has(action)) {
                newSet.delete(action);
            } else {
                newSet.add(action);
            }
            return newSet;
        });
    };

    const selectAllInGroup = (action: string) => {
        const groupPerms = groupedPermissions[action] || [];
        setSelectedPermissions((prev) => {
            const newSet = new Set(prev);
            groupPerms.forEach((p) => newSet.add(p.name));
            return newSet;
        });
    };

    const deselectAllInGroup = (action: string) => {
        const groupPerms = groupedPermissions[action] || [];
        setSelectedPermissions((prev) => {
            const newSet = new Set(prev);
            groupPerms.forEach((p) => newSet.delete(p.name));
            return newSet;
        });
    };

    const handleSave = async () => {
        if (!role) return;

        setSaving(true);
        try {
            await axios.put(`/api/roles/${role.id}/permissions`, {
                permissions: Array.from(selectedPermissions),
            });
            toast({
                title: 'Success',
                description: 'Permissions updated successfully',
            });
            setOriginalPermissions(new Set(selectedPermissions));
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to update permissions',
                variant: 'destructive',
            });
        } finally {
            setSaving(false);
        }
    };

    const handleClose = () => {
        if (isDirty) {
            const confirmed = window.confirm(
                'You have unsaved changes. Are you sure you want to close?',
            );
            if (!confirmed) return;
        }
        onOpenChange(false);
    };

    return (
        <Drawer open={open} onOpenChange={handleClose}>
            <DrawerContent className="min-h-[90vh]!">
                <DrawerHeader>
                    <DrawerTitle>Manage Permissions: {role?.name}</DrawerTitle>
                    <DrawerDescription>
                        Select permissions to assign to this role
                    </DrawerDescription>
                </DrawerHeader>

                {/* Search */}
                <div className="flex items-center gap-2 px-4 pb-4">
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            placeholder="Search permissions..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-9"
                        />
                        {searchQuery && (
                            <Button
                                variant="ghost"
                                size="sm"
                                className="absolute top-1/2 right-1 h-7 w-7 -translate-y-1/2 p-0"
                                onClick={() => setSearchQuery('')}
                            >
                                <X className="h-4 w-4" />
                            </Button>
                        )}
                    </div>
                </div>

                <div className="flex flex-1 flex-col overflow-y-scroll px-4">
                    {loading ? (
                        <div className="flex h-full items-center justify-center">
                            <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
                        </div>
                    ) : (
                        <ScrollArea className="-mx-4 flex-1 px-4 pb-4">
                            <div className="space-y-4">
                                {sortedModules.map((module) => {
                                        const permissions =
                                            groupedPermissions[module] || [];
                                        const selectedCount =
                                            permissions.filter((p) =>
                                                selectedPermissions.has(p.name),
                                            ).length;
                                        const isExpanded =
                                            expandedGroups.has(module);

                                        return (
                                            <ScrollArea
                                                key={module}
                                                className="max-h-125 overflow-y-scroll rounded-lg border"
                                            >
                                                <div
                                                    className="sticky top-0 left-0 flex cursor-pointer items-center justify-between bg-muted/50 p-3 hover:bg-muted"
                                                    onClick={() =>
                                                        toggleGroup(module)
                                                    }
                                                >
                                                    <div className="flex items-center gap-2">
                                                        {isExpanded ? (
                                                            <ChevronDown className="h-4 w-4" />
                                                        ) : (
                                                            <ChevronRight className="h-4 w-4" />
                                                        )}
                                                        <span className="font-semibold">
                                                            {formatModuleName(module)}
                                                        </span>
                                                        <Badge
                                                            variant="secondary"
                                                            className="ml-2"
                                                        >
                                                            {selectedCount}/
                                                            {permissions.length}
                                                        </Badge>
                                                    </div>
                                                    <div
                                                        className="flex gap-1"
                                                        onClick={(e) =>
                                                            e.stopPropagation()
                                                        }
                                                    >
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            onClick={() =>
                                                                selectAllInGroup(
                                                                    module,
                                                                )
                                                            }
                                                        >
                                                            Select All
                                                        </Button>
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            onClick={() =>
                                                                deselectAllInGroup(
                                                                    module,
                                                                )
                                                            }
                                                        >
                                                            Clear
                                                        </Button>
                                                    </div>
                                                </div>

                                                {isExpanded && (
                                                    <div className="divide-y">
                                                        {permissions.map(
                                                            (perm) => (
                                                                <div
                                                                    key={
                                                                        perm.name
                                                                    }
                                                                    className="flex items-start gap-3 p-3 hover:bg-muted/50"
                                                                >
                                                                    <Checkbox
                                                                        id={
                                                                            perm.name
                                                                        }
                                                                        checked={selectedPermissions.has(
                                                                            perm.name,
                                                                        )}
                                                                        onCheckedChange={() =>
                                                                            togglePermission(
                                                                                perm.name,
                                                                            )
                                                                        }
                                                                        className="mt-1"
                                                                    />
                                                                    <div className="flex-1 space-y-1">
                                                                        <label
                                                                            htmlFor={
                                                                                perm.name
                                                                            }
                                                                            className="cursor-pointer text-sm leading-none font-medium"
                                                                        >
                                                                            {
                                                                                perm.label
                                                                            }
                                                                        </label>
                                                                        <p className="text-sm text-muted-foreground">
                                                                            {
                                                                                perm.description
                                                                            }
                                                                        </p>
                                                                        <code className="text-xs text-muted-foreground">
                                                                            {
                                                                                perm.name
                                                                            }
                                                                        </code>
                                                                    </div>
                                                                </div>
                                                            ),
                                                        )}
                                                    </div>
                                                )}
                                            </ScrollArea>
                                        );
                                    })}
                            </div>
                        </ScrollArea>
                    )}
                </div>

                <DrawerFooter className="border-t">
                    <div className="flex items-center justify-between">
                        <div className="text-sm text-muted-foreground">
                            {selectedPermissions.size} of{' '}
                            {permissionsData.length} permissions selected
                            {isDirty && (
                                <Badge variant="outline" className="ml-2">
                                    Unsaved changes
                                </Badge>
                            )}
                        </div>
                        <div className="flex gap-2">
                            <DrawerClose asChild>
                                <Button variant="outline" disabled={saving}>
                                    Cancel
                                </Button>
                            </DrawerClose>
                            <Button
                                onClick={handleSave}
                                disabled={saving || !isDirty}
                            >
                                {saving && (
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                )}
                                <Save className="mr-2 h-4 w-4" />
                                Save Changes
                            </Button>
                        </div>
                    </div>
                </DrawerFooter>
            </DrawerContent>
        </Drawer>
    );
}
