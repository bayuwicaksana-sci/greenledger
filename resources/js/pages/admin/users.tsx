import { Head } from '@inertiajs/react';
import axios from 'axios';
import { UserPlus, Users } from 'lucide-react';
import { useEffect, useState } from 'react';

import { DeactivateUserDialog } from '@/components/admin/users/DeactivateUserDialog';
import { EditUserDialog } from '@/components/admin/users/EditUserDialog';
import { InviteUserDialog } from '@/components/admin/users/InviteUserDialog';
import { ManageRolesDrawer } from '@/components/admin/users/ManageRolesDrawer';
import { ManageSitesDrawer } from '@/components/admin/users/ManageSitesDrawer';
import { PendingInvitationsTable } from '@/components/admin/users/PendingInvitationsTable';
import { ResetPasswordDialog } from '@/components/admin/users/ResetPasswordDialog';
import { UserDetailsDrawer } from '@/components/admin/users/UserDetailsDrawer';
import { UsersTable } from '@/components/admin/users/UsersTable';
import { Button } from '@/components/ui/button';
import { useToast } from '@/hooks/use-toast';
import MainLayout from '@/layouts/main-layout';
import type { BreadcrumbItem } from '@/types';
import type { User } from '@/types/users';

export default function UserManagement() {
    const [users, setUsers] = useState<User[]>([]);
    const [loading, setLoading] = useState(true);
    const [selectedUser, setSelectedUser] = useState<User | null>(null);
    const [showInvitations, setShowInvitations] = useState(false);

    // Dialog states
    const [inviteDialogOpen, setInviteDialogOpen] = useState(false);
    const [detailsDrawerOpen, setDetailsDrawerOpen] = useState(false);
    const [editDialogOpen, setEditDialogOpen] = useState(false);
    const [rolesDrawerOpen, setRolesDrawerOpen] = useState(false);
    const [sitesDrawerOpen, setSitesDrawerOpen] = useState(false);
    const [resetPasswordDialogOpen, setResetPasswordDialogOpen] =
        useState(false);
    const [deactivateDialogOpen, setDeactivateDialogOpen] = useState(false);

    const { toast } = useToast();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'User Management',
            href: '#',
        },
    ];

    // Load users on mount
    useEffect(() => {
        loadUsers();
    }, []);

    const loadUsers = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/users');
            setUsers(response.data.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load users',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    const handleUserSelect = (user: User) => {
        setSelectedUser(user);
        setDetailsDrawerOpen(true);
    };

    const handleEditUser = (user: User) => {
        setSelectedUser(user);
        setEditDialogOpen(true);
    };

    const handleManageRoles = (user: User) => {
        setSelectedUser(user);
        setRolesDrawerOpen(true);
    };

    const handleManageSites = (user: User) => {
        setSelectedUser(user);
        setSitesDrawerOpen(true);
    };

    const handleResetPassword = (user: User) => {
        setSelectedUser(user);
        setResetPasswordDialogOpen(true);
    };

    const handleToggleStatus = (user: User) => {
        setSelectedUser(user);
        setDeactivateDialogOpen(true);
    };

    const handleSuccess = () => {
        loadUsers();
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="User Management" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">User Management</h1>
                        <p className="text-muted-foreground">
                            Manage users, roles, and site access
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button
                            variant="outline"
                            onClick={() => setShowInvitations(!showInvitations)}
                        >
                            <Users className="mr-2 h-4 w-4" />
                            {showInvitations
                                ? 'Hide Invitations'
                                : 'Pending Invitations'}
                        </Button>
                        <Button onClick={() => setInviteDialogOpen(true)}>
                            <UserPlus className="mr-2 h-4 w-4" />
                            Invite User
                        </Button>
                    </div>
                </div>

                {/* Pending Invitations */}
                {showInvitations && (
                    <PendingInvitationsTable onSuccess={handleSuccess} />
                )}

                {/* Users Table */}
                {loading ? (
                    <div className="flex h-[400px] items-center justify-center rounded-md border">
                        <div className="text-center">
                            <div className="mb-2 text-sm text-muted-foreground">
                                Loading users...
                            </div>
                        </div>
                    </div>
                ) : (
                    <UsersTable
                        users={users}
                        onUserSelect={handleUserSelect}
                        onEdit={handleEditUser}
                        onManageRoles={handleManageRoles}
                        onManageSites={handleManageSites}
                        onResetPassword={handleResetPassword}
                        onToggleStatus={handleToggleStatus}
                    />
                )}

                {/* User Details Drawer */}
                <UserDetailsDrawer
                    open={detailsDrawerOpen}
                    onOpenChange={setDetailsDrawerOpen}
                    user={selectedUser}
                    onEdit={handleEditUser}
                    onManageRoles={handleManageRoles}
                    onManageSites={handleManageSites}
                />

                {/* Invite User Dialog */}
                <InviteUserDialog
                    open={inviteDialogOpen}
                    onOpenChange={setInviteDialogOpen}
                    onSuccess={handleSuccess}
                />

                {/* Edit User Dialog */}
                <EditUserDialog
                    open={editDialogOpen}
                    onOpenChange={setEditDialogOpen}
                    user={selectedUser}
                    onSuccess={handleSuccess}
                />

                {/* Manage Roles Drawer */}
                <ManageRolesDrawer
                    open={rolesDrawerOpen}
                    onOpenChange={setRolesDrawerOpen}
                    user={selectedUser}
                    onSuccess={handleSuccess}
                />

                {/* Manage Sites Drawer */}
                <ManageSitesDrawer
                    open={sitesDrawerOpen}
                    onOpenChange={setSitesDrawerOpen}
                    user={selectedUser}
                    onSuccess={handleSuccess}
                />

                {/* Reset Password Dialog */}
                <ResetPasswordDialog
                    open={resetPasswordDialogOpen}
                    onOpenChange={setResetPasswordDialogOpen}
                    user={selectedUser}
                    onSuccess={handleSuccess}
                />

                {/* Deactivate User Dialog */}
                <DeactivateUserDialog
                    open={deactivateDialogOpen}
                    onOpenChange={setDeactivateDialogOpen}
                    user={selectedUser}
                    onSuccess={handleSuccess}
                />
            </div>
        </MainLayout>
    );
}
