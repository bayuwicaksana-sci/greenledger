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
import type { User } from '@/types/users';

interface DeactivateUserDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    user: User | null;
    onSuccess: () => void;
}

export function DeactivateUserDialog({
    open,
    onOpenChange,
    user,
    onSuccess,
}: DeactivateUserDialogProps) {
    const [loading, setLoading] = useState(false);
    const { toast } = useToast();

    const isDeactivating = user?.is_active === true;

    const handleToggle = async () => {
        if (!user) return;

        setLoading(true);

        try {
            await axios.post(`/api/users/${user.id}/toggle-status`);
            toast({
                title: 'Success',
                description: `User ${isDeactivating ? 'deactivated' : 'activated'} successfully`,
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    `Failed to ${isDeactivating ? 'deactivate' : 'activate'} user`,
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
                        <AlertTriangle
                            className={`h-5 w-5 ${isDeactivating ? 'text-destructive' : 'text-green-500'}`}
                        />
                        {isDeactivating ? 'Deactivate User' : 'Activate User'}
                    </DialogTitle>
                    <DialogDescription>
                        {isDeactivating ? (
                            <>
                                Are you sure you want to deactivate{' '}
                                <strong>{user?.full_name || user?.name}</strong>
                                ?
                            </>
                        ) : (
                            <>
                                Are you sure you want to activate{' '}
                                <strong>{user?.full_name || user?.name}</strong>
                                ?
                            </>
                        )}
                    </DialogDescription>
                </DialogHeader>

                <div className="rounded-md bg-muted p-4">
                    <p className="text-sm text-muted-foreground">
                        {isDeactivating ? (
                            <>
                                This user will be immediately logged out and
                                will not be able to log in again until their
                                account is reactivated. They will lose access to
                                all systems and data.
                            </>
                        ) : (
                            <>
                                This user will regain access to the system and
                                will be able to log in with their existing
                                credentials.
                            </>
                        )}
                    </p>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        disabled={loading}
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        variant={isDeactivating ? 'destructive' : 'default'}
                        onClick={handleToggle}
                        disabled={loading}
                    >
                        {loading && (
                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                        )}
                        {isDeactivating ? 'Deactivate' : 'Activate'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
