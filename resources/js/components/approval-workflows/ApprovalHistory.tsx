import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { format } from 'date-fns';
import { AlertCircle, CheckCircle2, Clock, XCircle } from 'lucide-react';

interface ApprovalAction {
    id: string;
    action_type: string;
    actor: {
        name: string;
    };
    step: {
        name: string;
    };
    comments: string | null;
    created_at: string;
}

interface ApprovalHistoryProps {
    actions: ApprovalAction[];
}

export function ApprovalHistory({ actions }: ApprovalHistoryProps) {
    if (actions.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>History</CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-sm text-muted-foreground">
                        No actions taken yet.
                    </p>
                </CardContent>
            </Card>
        );
    }

    const getIcon = (type: string) => {
        switch (type) {
            case 'approve':
                return <CheckCircle2 className="h-5 w-5 text-green-500" />;
            case 'reject':
                return <XCircle className="h-5 w-5 text-red-500" />;
            case 'request_changes':
                return <AlertCircle className="h-5 w-5 text-orange-500" />;
            default:
                return <Clock className="h-5 w-5 text-gray-500" />;
        }
    };

    const getBadgeVariant = (type: string) => {
        switch (type) {
            case 'approve':
                return 'success'; // Assuming you have these variants or use default/custom classes
            case 'reject':
                return 'destructive';
            case 'request_changes':
                return 'outline'; // or warning if available
            default:
                return 'secondary';
        }
    };

    // Reverse actions to show newest first? Or oldest first? Usually timeline is newest top.
    // For a step diagram, oldest first makes sense. For audit log, newest first.
    // Let's stick to the order they came in (likely newest first from controller load).

    return (
        <Card>
            <CardHeader>
                <CardTitle>Approval History</CardTitle>
            </CardHeader>
            <CardContent>
                <div className="relative space-y-6 border-l-2 border-muted pt-2 pl-6">
                    {actions.map((action) => (
                        <div key={action.id} className="relative">
                            <span className="absolute top-0 -left-[35px] flex h-8 w-8 items-center justify-center rounded-full bg-background ring-2 ring-muted">
                                {getIcon(action.action_type)}
                            </span>
                            <div className="flex flex-col gap-1">
                                <div className="flex items-center gap-2">
                                    <span className="font-semibold">
                                        {action.actor?.name || 'System'}
                                    </span>
                                    <span className="text-sm text-muted-foreground">
                                        {action.action_type === 'approve'
                                            ? 'approved'
                                            : action.action_type === 'reject'
                                              ? 'rejected'
                                              : 'acted on'}{' '}
                                        step{' '}
                                        <strong>{action.step?.name}</strong>
                                    </span>
                                    <span className="ml-auto text-xs text-muted-foreground">
                                        {format(
                                            new Date(action.created_at),
                                            'MMM dd, yyyy HH:mm',
                                        )}
                                    </span>
                                </div>
                                {action.comments && (
                                    <div className="mt-2 rounded-md bg-muted/50 p-3 text-sm italic">
                                        "{action.comments}"
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
