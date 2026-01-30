import { useEffect, useState } from 'react';
import axios from 'axios';
import { Plus } from 'lucide-react';

import { RoleDeleteDialog } from '@/components/admin/RoleDeleteDialog';
import { RoleFormDialog } from '@/components/admin/RoleFormDialog';
import { RolePermissionsDrawer } from '@/components/admin/RolePermissionsDrawer';
import { RolesTable } from '@/components/admin/RolesTable';
import { Button } from '@/components/ui/button';
import { useToast } from '@/hooks/use-toast';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem, Role } from '@/types';
import { Head } from '@inertiajs/react';

export default function RoleConfiguration() {
    const [roles, setRoles] = useState<Role[]>([]);
    const [loading, setLoading] = useState(true);
    const [selectedRole, setSelectedRole] = useState<Role | null>(null);
    const [drawerOpen, setDrawerOpen] = useState(false);
    const [roleFormOpen, setRoleFormOpen] = useState(false);
    const [editingRole, setEditingRole] = useState<Role | null>(null);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [roleToDelete, setRoleToDelete] = useState<Role | null>(null);

    const { toast } = useToast();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Administration',
            href: '#',
        },
        {
            title: 'Role Configuration',
            href: '#',
        },
    ];

    // Load roles on mount
    useEffect(() => {
        loadRoles();
    }, []);

    const loadRoles = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/roles');
            setRoles(response.data.data);
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

    const handleRoleSelect = async (role: Role) => {
        try {
            const response = await axios.get(`/api/roles/${role.id}`);
            setSelectedRole(response.data.data);
            setDrawerOpen(true);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load role details',
                variant: 'destructive',
            });
        }
    };

    const handleCreateRole = () => {
        setEditingRole(null);
        setRoleFormOpen(true);
    };

    const handleRenameRole = (role: Role) => {
        setEditingRole(role);
        setRoleFormOpen(true);
    };

    const handleDeleteRole = (role: Role) => {
        setRoleToDelete(role);
        setDeleteDialogOpen(true);
    };

    const handleDuplicateRole = async (role: Role) => {
        try {
            // Load the role with permissions
            const response = await axios.get(`/api/roles/${role.id}`);
            const roleData = response.data.data;

            // Create new role with same permissions
            const newName = `${role.name} (Copy)`;
            const permissionNames = roleData.permissions.map(
                (p: any) => p.name,
            );

            await axios.post('/api/roles', {
                name: newName,
                permissions: permissionNames,
            });

            toast({
                title: 'Success',
                description: `Role duplicated as "${newName}"`,
            });

            loadRoles();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to duplicate role',
                variant: 'destructive',
            });
        }
    };

    const handleSuccess = () => {
        loadRoles();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Role Configuration" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">
                            Role Configuration
                        </h1>
                        <p className="text-muted-foreground">
                            Manage roles and their permissions
                        </p>
                    </div>
                    <Button onClick={handleCreateRole}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Role
                    </Button>
                </div>

                {/* Roles Table */}
                {loading ? (
                    <div className="flex h-[400px] items-center justify-center rounded-md border">
                        <div className="text-center">
                            <div className="mb-2 text-sm text-muted-foreground">
                                Loading roles...
                            </div>
                        </div>
                    </div>
                ) : (
                    <RolesTable
                        roles={roles}
                        onRoleSelect={handleRoleSelect}
                        onRename={handleRenameRole}
                        onDelete={handleDeleteRole}
                        onDuplicate={handleDuplicateRole}
                    />
                )}

                {/* Permissions Drawer */}
                <RolePermissionsDrawer
                    open={drawerOpen}
                    onOpenChange={setDrawerOpen}
                    role={selectedRole}
                    onSuccess={handleSuccess}
                />

                {/* Role Form Dialog (Create/Edit) */}
                <RoleFormDialog
                    open={roleFormOpen}
                    onOpenChange={setRoleFormOpen}
                    role={editingRole}
                    onSuccess={handleSuccess}
                />

                {/* Delete Confirmation Dialog */}
                <RoleDeleteDialog
                    open={deleteDialogOpen}
                    onOpenChange={setDeleteDialogOpen}
                    role={roleToDelete}
                    onSuccess={handleSuccess}
                />
            </div>
        </AppLayout>
    );
}
