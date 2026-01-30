import { useEffect, useState } from 'react';
import axios from 'axios';
import { formatDistanceToNow } from 'date-fns';
import { Mail, Trash2, AlertCircle, Clock } from 'lucide-react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useToast } from '@/hooks/use-toast';
import type { UserInvitation } from '@/types/users';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';

interface PendingInvitationsTableProps {
    onSuccess: () => void;
}

export function PendingInvitationsTable({
    onSuccess,
}: PendingInvitationsTableProps) {
    const [invitations, setInvitations] = useState<UserInvitation[]>([]);
    const [loading, setLoading] = useState(true);
    const [resendingId, setResendingId] = useState<number | null>(null);
    const [cancellingId, setCancellingId] = useState<number | null>(null);
    const [confirmCancel, setConfirmCancel] = useState<UserInvitation | null>(
        null,
    );
    const { toast } = useToast();

    useEffect(() => {
        loadInvitations();
        // Auto-refresh every 30 seconds
        const interval = setInterval(loadInvitations, 30000);
        return () => clearInterval(interval);
    }, []);

    const loadInvitations = async () => {
        try {
            const response = await axios.get('/api/users/invitations');
            setInvitations(response.data.data || response.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load invitations',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    const handleResend = async (invitation: UserInvitation) => {
        setResendingId(invitation.id);
        try {
            await axios.post(
                `/api/users/invitations/${invitation.id}/resend`,
            );
            toast({
                title: 'Success',
                description: `Invitation resent to ${invitation.email}`,
            });
            loadInvitations();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to resend invitation',
                variant: 'destructive',
            });
        } finally {
            setResendingId(null);
        }
    };

    const handleCancel = async (invitation: UserInvitation) => {
        setCancellingId(invitation.id);
        try {
            await axios.delete(`/api/users/invitations/${invitation.id}`);
            toast({
                title: 'Success',
                description: 'Invitation cancelled',
            });
            loadInvitations();
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to cancel invitation',
                variant: 'destructive',
            });
        } finally {
            setCancellingId(null);
            setConfirmCancel(null);
        }
    };

    if (loading) {
        return (
            <div className="flex h-24 items-center justify-center rounded-md border">
                <div className="text-sm text-muted-foreground">
                    Loading invitations...
                </div>
            </div>
        );
    }

    if (invitations.length === 0) {
        return (
            <div className="flex h-24 items-center justify-center rounded-md border">
                <div className="text-center">
                    <Mail className="mx-auto h-8 w-8 text-muted-foreground" />
                    <p className="mt-2 text-sm text-muted-foreground">
                        No pending invitations
                    </p>
                </div>
            </div>
        );
    }

    return (
        <>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Email</TableHead>
                            <TableHead>Full Name</TableHead>
                            <TableHead>Primary Site</TableHead>
                            <TableHead>Roles</TableHead>
                            <TableHead>Invited By</TableHead>
                            <TableHead>Expires</TableHead>
                            <TableHead className="text-right">
                                Actions
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {invitations.map((invitation) => (
                            <TableRow key={invitation.id}>
                                <TableCell>
                                    <div className="flex items-center gap-2">
                                        <Mail className="h-4 w-4 text-muted-foreground" />
                                        <span className="font-medium">
                                            {invitation.email}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>{invitation.full_name}</TableCell>
                                <TableCell>
                                    <Badge variant="outline">
                                        {invitation.primary_site_name}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div className="flex flex-wrap gap-1">
                                        {invitation.roles.length > 0 ? (
                                            invitation.roles
                                                .slice(0, 2)
                                                .map((role) => (
                                                    <Badge
                                                        key={role}
                                                        variant="secondary"
                                                        className="text-xs"
                                                    >
                                                        {role}
                                                    </Badge>
                                                ))
                                        ) : (
                                            <span className="text-sm text-muted-foreground">
                                                No roles
                                            </span>
                                        )}
                                        {invitation.roles.length > 2 && (
                                            <Badge
                                                variant="outline"
                                                className="text-xs"
                                            >
                                                +
                                                {invitation.roles.length - 2}
                                            </Badge>
                                        )}
                                    </div>
                                </TableCell>
                                <TableCell className="text-sm text-muted-foreground">
                                    {invitation.invited_by_name}
                                </TableCell>
                                <TableCell>
                                    {invitation.is_expired ? (
                                        <Badge
                                            variant="destructive"
                                            className="gap-1"
                                        >
                                            <AlertCircle className="h-3 w-3" />
                                            Expired
                                        </Badge>
                                    ) : (
                                        <div className="flex items-center gap-1 text-sm text-muted-foreground">
                                            <Clock className="h-3 w-3" />
                                            {formatDistanceToNow(
                                                new Date(
                                                    invitation.expires_at,
                                                ),
                                                { addSuffix: true },
                                            )}
                                        </div>
                                    )}
                                </TableCell>
                                <TableCell className="text-right">
                                    <div className="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                handleResend(invitation)
                                            }
                                            disabled={
                                                resendingId === invitation.id
                                            }
                                        >
                                            {resendingId === invitation.id
                                                ? 'Sending...'
                                                : 'Resend'}
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                setConfirmCancel(invitation)
                                            }
                                            disabled={
                                                cancellingId === invitation.id
                                            }
                                        >
                                            <Trash2 className="h-4 w-4 text-destructive" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            {/* Cancel Confirmation Dialog */}
            <AlertDialog
                open={!!confirmCancel}
                onOpenChange={(open) => !open && setConfirmCancel(null)}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Cancel Invitation</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to cancel the invitation for{' '}
                            <strong>{confirmCancel?.email}</strong>? This action
                            cannot be undone.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction
                            onClick={() =>
                                confirmCancel && handleCancel(confirmCancel)
                            }
                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        >
                            Delete Invitation
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
