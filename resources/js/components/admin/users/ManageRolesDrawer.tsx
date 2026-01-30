import axios from 'axios';
import { Loader2, Save, Shield, X } from 'lucide-react';
import { useEffect, useState } from 'react';

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
import { useToast } from '@/hooks/use-toast';
import type { Role, User } from '@/types/users';

interface ManageRolesDrawerProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    user: User | null;
    onSuccess: () => void;
}

export function ManageRolesDrawer({
    open,
    onOpenChange,
    user,
    onSuccess,
}: ManageRolesDrawerProps) {
    const [roles, setRoles] = useState<Role[]>([]);
    const [selectedRoleIds, setSelectedRoleIds] = useState<number[]>([]);
    const [originalRoleIds, setOriginalRoleIds] = useState<number[]>([]);
    const [searchQuery, setSearchQuery] = useState('');
    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);
    const { toast } = useToast();

    useEffect(() => {
        if (open && user) {
            loadRoles();
            const userRoleIds = user.roles.map((r) => r.id);
            setSelectedRoleIds(userRoleIds);
            setOriginalRoleIds(userRoleIds);
        } else if (!open) {
            setSelectedRoleIds([]);
            setOriginalRoleIds([]);
            setSearchQuery('');
        }
    }, [open, user]);

    const loadRoles = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/roles');
            setRoles(response.data.data || response.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load roles',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    const handleRoleToggle = (roleId: number) => {
        setSelectedRoleIds((prev) =>
            prev.includes(roleId)
                ? prev.filter((id) => id !== roleId)
                : [...prev, roleId],
        );
    };

    const handleSave = async () => {
        if (!user) return;

        setSaving(true);
        try {
            await axios.put(`/api/users/${user.id}/roles`, {
                role_ids: selectedRoleIds,
            });
            toast({
                title: 'Success',
                description: 'User roles updated successfully',
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to update user roles',
                variant: 'destructive',
            });
        } finally {
            setSaving(false);
        }
    };

    const filteredRoles = roles.filter((role) =>
        role.name.toLowerCase().includes(searchQuery.toLowerCase()),
    );

    const isDirty =
        JSON.stringify([...selectedRoleIds].sort()) !==
        JSON.stringify([...originalRoleIds].sort());

    return (
        <Drawer open={open} onOpenChange={onOpenChange}>
            <DrawerContent className="max-h-[90vh]!">
                <div className="mx-auto w-full max-w-2xl">
                    <DrawerHeader>
                        <DrawerTitle className="flex items-center gap-2">
                            <Shield className="h-5 w-5" />
                            Manage Roles
                        </DrawerTitle>
                        <DrawerDescription>
                            Assign or remove roles for{' '}
                            {user?.full_name || user?.name}. At least one role
                            must be selected.
                        </DrawerDescription>
                    </DrawerHeader>

                    <div className="space-y-4 p-4">
                        {/* Selected roles */}
                        <div>
                            <p className="mb-2 text-sm font-medium">
                                Selected Roles ({selectedRoleIds.length})
                            </p>
                            <div className="flex min-h-[48px] flex-wrap gap-2 rounded-md border p-3">
                                {selectedRoleIds.length > 0 ? (
                                    roles
                                        .filter((r) =>
                                            selectedRoleIds.includes(r.id),
                                        )
                                        .map((role) => (
                                            <Badge
                                                key={role.id}
                                                variant="secondary"
                                                className="gap-1"
                                            >
                                                {role.name}
                                                <X
                                                    className="h-3 w-3 cursor-pointer"
                                                    onClick={() =>
                                                        handleRoleToggle(
                                                            role.id,
                                                        )
                                                    }
                                                />
                                            </Badge>
                                        ))
                                ) : (
                                    <span className="text-sm text-muted-foreground">
                                        No roles selected
                                    </span>
                                )}
                            </div>
                        </div>

                        {/* Search */}
                        <div className="relative">
                            <Input
                                placeholder="Search roles..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pr-8"
                            />
                        </div>

                        {/* Roles list */}
                        <ScrollArea className="h-[300px] rounded-md border">
                            {loading ? (
                                <div className="flex h-full items-center justify-center">
                                    <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
                                </div>
                            ) : (
                                <div className="space-y-2 p-4">
                                    {filteredRoles.length > 0 ? (
                                        filteredRoles.map((role) => (
                                            <div
                                                key={role.id}
                                                className="flex items-center space-x-3 rounded-md p-2 hover:bg-muted"
                                            >
                                                <Checkbox
                                                    id={`role-${role.id}`}
                                                    checked={selectedRoleIds.includes(
                                                        role.id,
                                                    )}
                                                    onCheckedChange={() =>
                                                        handleRoleToggle(
                                                            role.id,
                                                        )
                                                    }
                                                />
                                                <label
                                                    htmlFor={`role-${role.id}`}
                                                    className="flex flex-1 cursor-pointer flex-col"
                                                >
                                                    <span className="font-medium">
                                                        {role.name}
                                                    </span>
                                                    {role.permissions_count !==
                                                        undefined && (
                                                        <span className="text-xs text-muted-foreground">
                                                            {
                                                                role.permissions_count
                                                            }{' '}
                                                            permissions
                                                        </span>
                                                    )}
                                                </label>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center text-sm text-muted-foreground">
                                            No roles found
                                        </div>
                                    )}
                                </div>
                            )}
                        </ScrollArea>
                    </div>

                    <DrawerFooter>
                        <Button
                            onClick={handleSave}
                            disabled={
                                saving ||
                                !isDirty ||
                                selectedRoleIds.length === 0
                            }
                        >
                            {saving ? (
                                <>
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <Save className="mr-2 h-4 w-4" />
                                    Save Changes
                                </>
                            )}
                        </Button>
                        <DrawerClose asChild>
                            <Button variant="outline" disabled={saving}>
                                Cancel
                            </Button>
                        </DrawerClose>
                    </DrawerFooter>
                </div>
            </DrawerContent>
        </Drawer>
    );
}
