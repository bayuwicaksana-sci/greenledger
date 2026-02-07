import { useForm, usePage } from '@inertiajs/react';
import { AlertCircle, Check, X } from 'lucide-react';
import { useState } from 'react';
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
import { action } from '@/routes/approvals';
import type { SharedData } from '@/types';

type ActionType = 'approve' | 'reject' | 'request_changes';

interface ActionConfig {
    title: string;
    description: string;
    icon: React.ComponentType<{ className?: string }>;
    variant: 'default' | 'destructive' | 'outline';
    buttonLabel: string;
    commentRequired: boolean;
    placeholder: string;
}

const actionConfigs: Record<ActionType, ActionConfig> = {
    approve: {
        title: 'Approve Request',
        description: 'Are you sure you want to approve this request? It will move to the next step or be fully approved.',
        icon: Check,
        variant: 'default',
        buttonLabel: 'Confirm Approval',
        commentRequired: false,
        placeholder: 'Looks good to me...',
    },
    reject: {
        title: 'Reject Request',
        description: 'Are you sure you want to reject this request? It will be returned to the submitter.',
        icon: X,
        variant: 'destructive',
        buttonLabel: 'Reject Request',
        commentRequired: true,
        placeholder: 'Please fix the amount...',
    },
    request_changes: {
        title: 'Request Changes',
        description: 'Are you sure you want to request changes? The submitter will be notified to make revisions.',
        icon: AlertCircle,
        variant: 'outline',
        buttonLabel: 'Request Changes',
        commentRequired: true,
        placeholder: 'Please update the following...',
    },
};

interface ApprovalActionDialogProps {
    instanceId: string | number;
    action: ActionType;
    trigger?: React.ReactNode;
}

export function ApprovalActionDialog({
    instanceId,
    action: actionType,
    trigger,
}: ApprovalActionDialogProps) {
    const [open, setOpen] = useState(false);
    const { site_code } = usePage<SharedData>().props;
    const config = actionConfigs[actionType];
    const Icon = config.icon;

    const { data, setData, post, processing, errors, reset } = useForm({
        action: actionType,
        comment: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(action.url({ site: site_code ?? '', approvalInstance: instanceId }), {
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
                    <Button variant={config.variant}>
                        <Icon className="mr-2 h-4 w-4" />
                        {actionType === 'approve' ? 'Approve' : actionType === 'reject' ? 'Reject' : 'Request Changes'}
                    </Button>
                )}
            </DialogTrigger>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{config.title}</DialogTitle>
                    <DialogDescription>{config.description}</DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4 py-4">
                    <div className="space-y-2">
                        <Label htmlFor="comment">
                            Reason / Comments{' '}
                            {config.commentRequired ? '(Required)' : '(Optional)'}
                        </Label>
                        <Textarea
                            id="comment"
                            placeholder={config.placeholder}
                            value={data.comment}
                            onChange={(e) => setData('comment', e.target.value)}
                            required={config.commentRequired}
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
                            variant={config.variant}
                            disabled={processing}
                        >
                            <Icon className="mr-2 h-4 w-4" />
                            {config.buttonLabel}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
