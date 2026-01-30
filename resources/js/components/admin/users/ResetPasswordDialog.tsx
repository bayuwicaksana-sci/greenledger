import axios from 'axios';
import { AlertTriangle, Loader2 } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { useToast } from '@/hooks/use-toast';
import type { User } from '@/types/users';

interface ResetPasswordDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    user: User | null;
    onSuccess: () => void;
}

export function ResetPasswordDialog({
    open,
    onOpenChange,
    user,
    onSuccess,
}: ResetPasswordDialogProps) {
    const [loading, setLoading] = useState(false);
    const [mustChangePassword, setMustChangePassword] = useState(true);
    const { toast } = useToast();

    const handleReset = async () => {
        if (!user) return;

        setLoading(true);

        try {
            await axios.post(`/api/users/${user.id}/reset-password`, {
                must_change_password: mustChangePassword,
            });
            toast({
                title: 'Success',
                description: `Password reset email sent to ${user.email}`,
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to send password reset',
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
                        <AlertTriangle className="h-5 w-5 text-amber-500" />
                        Reset Password
                    </DialogTitle>
                    <DialogDescription>
                        Send a password reset email to{' '}
                        <strong>{user?.email}</strong>
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4 py-4">
                    <div className="rounded-md bg-muted p-4">
                        <p className="text-sm text-muted-foreground">
                            The user will receive an email with instructions to
                            reset their password. The reset link will be valid
                            for 60 minutes.
                        </p>
                    </div>

                    <div className="flex items-center space-x-2">
                        <Checkbox
                            id="must_change"
                            checked={mustChangePassword}
                            onCheckedChange={(checked) =>
                                setMustChangePassword(checked as boolean)
                            }
                        />
                        <Label
                            htmlFor="must_change"
                            className="text-sm font-normal leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            Require password change on next login
                        </Label>
                    </div>
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
                        onClick={handleReset}
                        disabled={loading}
                    >
                        {loading && (
                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                        )}
                        Send Reset Email
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
