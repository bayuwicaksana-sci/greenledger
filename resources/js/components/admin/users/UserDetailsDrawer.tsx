import { formatDistanceToNow } from 'date-fns';
import {
    Building2,
    Calendar,
    Mail,
    Shield,
    User as UserIcon,
} from 'lucide-react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import type { User } from '@/types/users';

interface UserDetailsDrawerProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    user: User | null;
    onEdit: (user: User) => void;
    onManageRoles: (user: User) => void;
    onManageSites: (user: User) => void;
}

export function UserDetailsDrawer({
    open,
    onOpenChange,
    user,
    onEdit,
    onManageRoles,
    onManageSites,
}: UserDetailsDrawerProps) {
    if (!user) return null;

    return (
        <Drawer open={open} onOpenChange={onOpenChange}>
            <DrawerContent className="max-h-[90vh]!">
                <div className="mx-auto w-full max-w-2xl">
                    <DrawerHeader>
                        <DrawerTitle>User Details</DrawerTitle>
                        <DrawerDescription>
                            View complete information about this user
                        </DrawerDescription>
                    </DrawerHeader>

                    <ScrollArea className="h-[60vh] max-h-[60vh] overflow-hidden">
                        <div className="space-y-6 p-4">
                            {/* Basic Information */}
                            <div className="space-y-4">
                                <h3 className="flex items-center gap-2 text-sm font-semibold">
                                    <UserIcon className="h-4 w-4" />
                                    Basic Information
                                </h3>
                                <div className="grid gap-3 rounded-lg border p-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Full Name
                                            </p>
                                            <p className="font-medium">
                                                {user.full_name || user.name}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Username
                                            </p>
                                            <p className="font-medium">
                                                {user.name}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Email
                                            </p>
                                            <p className="flex items-center gap-2 font-medium">
                                                <Mail className="h-3 w-3" />
                                                {user.email}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Status
                                            </p>
                                            <Badge
                                                variant={
                                                    user.is_active
                                                        ? 'default'
                                                        : 'secondary'
                                                }
                                                className={
                                                    user.is_active
                                                        ? 'bg-green-500 hover:bg-green-600'
                                                        : 'bg-gray-500 hover:bg-gray-600'
                                                }
                                            >
                                                {user.is_active
                                                    ? 'Active'
                                                    : 'Inactive'}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            {/* Site Access */}
                            <div className="space-y-4">
                                <h3 className="flex items-center gap-2 text-sm font-semibold">
                                    <Building2 className="h-4 w-4" />
                                    Site Access
                                </h3>
                                <div className="space-y-3 rounded-lg border p-4">
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Primary Site
                                        </p>
                                        <p className="mt-1">
                                            {user.primary_site_name ? (
                                                <Badge variant="outline">
                                                    {user.primary_site_name}
                                                </Badge>
                                            ) : (
                                                <span className="text-sm text-muted-foreground">
                                                    No primary site
                                                </span>
                                            )}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            All Sites ({user.sites.length})
                                        </p>
                                        <div className="mt-2 flex flex-wrap gap-2">
                                            {user.sites.length > 0 ? (
                                                user.sites.map((site) => (
                                                    <Badge
                                                        key={site.id}
                                                        variant="secondary"
                                                    >
                                                        {site.name}
                                                    </Badge>
                                                ))
                                            ) : (
                                                <span className="text-sm text-muted-foreground">
                                                    No sites assigned
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => {
                                            onOpenChange(false);
                                            onManageSites(user);
                                        }}
                                    >
                                        Manage Sites
                                    </Button>
                                </div>
                            </div>

                            <Separator />

                            {/* Roles & Permissions */}
                            <div className="space-y-4">
                                <h3 className="flex items-center gap-2 text-sm font-semibold">
                                    <Shield className="h-4 w-4" />
                                    Roles & Permissions
                                </h3>
                                <div className="space-y-3 rounded-lg border p-4">
                                    <div>
                                        <p className="text-sm text-muted-foreground">
                                            Assigned Roles ({user.roles.length})
                                        </p>
                                        <div className="mt-2 flex flex-wrap gap-2">
                                            {user.roles.length > 0 ? (
                                                user.roles.map((role) => (
                                                    <Badge
                                                        key={role.id}
                                                        variant="secondary"
                                                    >
                                                        {role.name}
                                                    </Badge>
                                                ))
                                            ) : (
                                                <span className="text-sm text-muted-foreground">
                                                    No roles assigned
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => {
                                            onOpenChange(false);
                                            onManageRoles(user);
                                        }}
                                    >
                                        Manage Roles
                                    </Button>
                                </div>
                            </div>

                            <Separator />

                            {/* Account Activity */}
                            <div className="space-y-4">
                                <h3 className="flex items-center gap-2 text-sm font-semibold">
                                    <Calendar className="h-4 w-4" />
                                    Account Activity
                                </h3>
                                <div className="grid gap-3 rounded-lg border p-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Last Login
                                            </p>
                                            <p className="font-medium">
                                                {user.last_login_at
                                                    ? formatDistanceToNow(
                                                          new Date(
                                                              user.last_login_at,
                                                          ),
                                                          { addSuffix: true },
                                                      )
                                                    : 'Never'}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Last Login IP
                                            </p>
                                            <p className="font-mono text-sm font-medium">
                                                {user.last_login_ip || 'N/A'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Email Verified
                                            </p>
                                            <p className="font-medium">
                                                {user.email_verified_at
                                                    ? 'Yes'
                                                    : 'No'}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Account Created
                                            </p>
                                            <p className="font-medium">
                                                {new Date(
                                                    user.created_at,
                                                ).toLocaleDateString()}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ScrollArea>

                    <DrawerFooter>
                        <Button
                            onClick={() => {
                                onOpenChange(false);
                                onEdit(user);
                            }}
                        >
                            Edit User
                        </Button>
                        <DrawerClose asChild>
                            <Button variant="outline">Close</Button>
                        </DrawerClose>
                    </DrawerFooter>
                </div>
            </DrawerContent>
        </Drawer>
    );
}
