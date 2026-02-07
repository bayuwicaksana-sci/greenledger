import { usePage } from '@inertiajs/react';
import {
    Building2,
    Eye,
    KeyRound,
    MoreHorizontal,
    Pencil,
    Shield,
    UserCheck,
    UserX,
} from 'lucide-react';
import React from 'react';

import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types/users';

interface UserActionsMenuProps {
    user: User;
    onViewDetails: () => void;
    onEdit: () => void;
    onManageRoles: () => void;
    onManageSites: () => void;
    onResetPassword: () => void;
    onToggleStatus: () => void;
}

export function UserActionsMenu({
    user,
    onViewDetails,
    onEdit,
    onManageRoles,
    onManageSites,
    onResetPassword,
    onToggleStatus,
}: UserActionsMenuProps) {
    const { props } = usePage();
    const authUser = props.auth?.user as User | undefined;
    const isSelf = authUser?.id === user.id;

    const handleViewDetails = (e: React.MouseEvent) => {
        e.stopPropagation();
        onViewDetails();
    };

    const handleEdit = (e: React.MouseEvent) => {
        e.stopPropagation();
        onEdit();
    };

    const handleManageRoles = (e: React.MouseEvent) => {
        e.stopPropagation();
        onManageRoles();
    };

    const handleManageSites = (e: React.MouseEvent) => {
        e.stopPropagation();
        onManageSites();
    };

    const handleResetPassword = (e: React.MouseEvent) => {
        e.stopPropagation();
        onResetPassword();
    };

    const handleToggleStatus = (e: React.MouseEvent) => {
        e.stopPropagation();
        onToggleStatus();
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild onClick={(e) => e.stopPropagation()}>
                <Button variant="ghost" className="h-8 w-8 p-0">
                    <span className="sr-only">Open menu</span>
                    <MoreHorizontal className="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={handleViewDetails}>
                    <Eye className="mr-2 h-4 w-4" />
                    View Details
                </DropdownMenuItem>
                <DropdownMenuItem onClick={handleEdit}>
                    <Pencil className="mr-2 h-4 w-4" />
                    Edit User
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={handleManageRoles}>
                    <Shield className="mr-2 h-4 w-4" />
                    Manage Roles
                </DropdownMenuItem>
                <DropdownMenuItem onClick={handleManageSites}>
                    <Building2 className="mr-2 h-4 w-4" />
                    Manage Sites
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem
                    onClick={handleResetPassword}
                    disabled={isSelf}
                    className={isSelf ? 'opacity-50' : ''}
                >
                    <KeyRound className="mr-2 h-4 w-4" />
                    Reset Password
                </DropdownMenuItem>
                <DropdownMenuItem
                    onClick={handleToggleStatus}
                    disabled={isSelf}
                    className={
                        isSelf
                            ? 'opacity-50'
                            : user.is_active
                              ? 'text-destructive focus:text-destructive'
                              : ''
                    }
                >
                    {user.is_active ? (
                        <>
                            <UserX className="mr-2 h-4 w-4" />
                            Deactivate User
                        </>
                    ) : (
                        <>
                            <UserCheck className="mr-2 h-4 w-4" />
                            Activate User
                        </>
                    )}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
