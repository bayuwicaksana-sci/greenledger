import axios from 'axios';
import { AlertTriangle, Loader2 } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useToast } from '@/hooks/use-toast';
import type { Role } from '@/types';

interface RoleDeleteDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    role: Role | null;
    onSuccess: () => void;
}

export function RoleDeleteDialog({
    open,
    onOpenChange,
    role,
    onSuccess,
}: RoleDeleteDialogProps) {
    const [loading, setLoading] = useState(false);
    const { toast } = useToast();

    const systemRoles = ['Manager', 'AVP', 'Farm Admin'];
    const isSystemRole = role ? systemRoles.includes(role.name) : false;
    const hasUsers = role && role.users_count && role.users_count > 0;

    const handleDelete = async () => {
        if (!role) return;

        setLoading(true);

        try {
            await axios.delete(`/api/roles/${role.id}`);
            toast({
                title: 'Success',
                description: `Role "${role.name}" deleted successfully`,
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message || 'Failed to delete role',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <AlertTriangle className="h-5 w-5 text-destructive" />
                        Delete Role
                    </DialogTitle>
                    <DialogDescription>
                        {isSystemRole ? (
                            <span className="text-destructive">
                                Cannot delete system-critical role.
                            </span>
                        ) : hasUsers ? (
                            <span className="text-destructive">
                                Cannot delete this role. {role.users_count}{' '}
                                user(s) are assigned to it.
                            </span>
                        ) : (
                            <span>
                                Are you sure you want to delete the role "
                                <strong>{role?.name}</strong>"? This action
                                cannot be undone.
                            </span>
                        )}
                    </DialogDescription>
                </DialogHeader>

                {!isSystemRole && !hasUsers && (
                    <div className="rounded-md bg-muted p-4">
                        <p className="text-sm text-muted-foreground">
                            This will permanently delete the role and all its
                            permission assignments. Users previously assigned to
                            this role will lose these permissions.
                        </p>
                    </div>
                )}

                {hasUsers ? (
                    <div className="rounded-md bg-muted p-4">
                        <p className="text-sm text-muted-foreground">
                            Please reassign the {role.users_count} user(s) to
                            another role before deleting this one.
                        </p>
                    </div>
                ) : null}

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        disabled={loading}
                    >
                        Cancel
                    </Button>
                    {!isSystemRole && !hasUsers && (
                        <Button
                            type="button"
                            variant="destructive"
                            onClick={handleDelete}
                            disabled={loading}
                        >
                            {loading && (
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                            )}
                            Delete Role
                        </Button>
                    )}
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
