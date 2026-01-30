import { Copy, MoreHorizontal, Pencil, Shield, Trash2 } from 'lucide-react';
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
import type { Role } from '@/types';

interface RoleActionsMenuProps {
    role: Role;
    onViewPermissions: () => void;
    onRename: () => void;
    onDuplicate: () => void;
    onDelete: () => void;
}

export function RoleActionsMenu({
    role,
    onViewPermissions,
    onRename,
    onDuplicate,
    onDelete,
}: RoleActionsMenuProps) {
    const systemRoles = ['Manager', 'AVP', 'Farm Admin'];
    const isSystemRole = systemRoles.includes(role.name);

    const handleViewPermissions = (e: React.MouseEvent) => {
        e.stopPropagation();
        onViewPermissions();
    };

    const handleRename = (e: React.MouseEvent) => {
        e.stopPropagation();
        onRename();
    };

    const handleDuplicate = (e: React.MouseEvent) => {
        e.stopPropagation();
        onDuplicate();
    };

    const handleDelete = (e: React.MouseEvent) => {
        e.stopPropagation();
        onDelete();
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
                <DropdownMenuItem onClick={handleViewPermissions}>
                    <Shield className="mr-2 h-4 w-4" />
                    Edit Permissions
                </DropdownMenuItem>
                <DropdownMenuItem
                    onClick={handleRename}
                    disabled={isSystemRole}
                    className={isSystemRole ? 'opacity-50' : ''}
                >
                    <Pencil className="mr-2 h-4 w-4" />
                    Rename Role
                </DropdownMenuItem>
                <DropdownMenuItem onClick={handleDuplicate}>
                    <Copy className="mr-2 h-4 w-4" />
                    Duplicate Role
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem
                    onClick={handleDelete}
                    disabled={isSystemRole}
                    className={
                        isSystemRole
                            ? 'opacity-50'
                            : 'text-destructive focus:text-destructive'
                    }
                >
                    <Trash2 className="mr-2 h-4 w-4" />
                    Delete Role
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
