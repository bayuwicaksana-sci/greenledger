import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/react';
import { Check, X } from 'lucide-react';
import { useState } from 'react';

interface ApprovalActionDialogProps {
    instanceId: string;
    action: 'approve' | 'reject';
    trigger?: React.ReactNode;
}

export function ApprovalActionDialog({
    instanceId,
    action,
    trigger,
}: ApprovalActionDialogProps) {
    const [open, setOpen] = useState(false);

    // We can use Inertia's useForm for easy submission
    const { data, setData, post, processing, errors, reset } = useForm({
        action: action,
        comment: '',
    });

    const isApprove = action === 'approve';
    const title = isApprove ? 'Approve Request' : 'Reject Request';
    const description = isApprove
        ? 'Are you sure you want to approve this request? It will move to the next step.'
        : 'Are you sure you want to reject this request? It will be returned to the submitter.';

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('approvals.action', [instanceId]), {
            onSuccess: () => {
                setOpen(false);
                reset();
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                {trigger || (
                    <Button variant={isApprove ? 'default' : 'destructive'}>
                        {isApprove ? 'Approve' : 'Reject'}
                    </Button>
                )}
            </DialogTrigger>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4 py-4">
                    <div className="space-y-2">
                        <Label htmlFor="comment">
                            Reason / Comments{' '}
                            {isApprove ? '(Optional)' : '(Required)'}
                        </Label>
                        <Textarea
                            id="comment"
                            placeholder={
                                isApprove
                                    ? 'Looks good to me...'
                                    : 'Please fix the amount...'
                            }
                            value={data.comment}
                            onChange={(e) => setData('comment', e.target.value)}
                            required={!isApprove}
                        />
                        {errors.comment && (
                            <p className="text-sm text-destructive">
                                {errors.comment}
                            </p>
                        )}
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setOpen(false)}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            variant={isApprove ? 'default' : 'destructive'}
                            disabled={processing}
                        >
                            {isApprove ? (
                                <>
                                    <Check className="mr-2 h-4 w-4" />
                                    Confirm Approval
                                </>
                            ) : (
                                <>
                                    <X className="mr-2 h-4 w-4" />
                                    Reject Request
                                </>
                            )}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
