import { ApprovalActionDialog } from '@/components/approval-workflows/ApprovalActionDialog';
import { ApprovalHistory } from '@/components/approval-workflows/ApprovalHistory';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { index as approvalsIndex, resubmit } from '@/routes/approvals';
import type { BreadcrumbItem, SharedData } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { format } from 'date-fns';
import {
    ArrowLeft,
    Calendar,
    FileText,
    GitCommit,
    RotateCcw,
    User,
} from 'lucide-react';

interface ApprovalInstance {
    id: number;
    approvable_type: string;
    approvable_id: number;
    status_value: string;
    status_label: string;
    status_color: string;
    submitted_at: string;
    submitted_by: {
        id: number;
        name: string;
        email: string;
    };
    current_step: {
        id: number;
        name: string;
        description?: string;
    } | null;
    approvable: any;
    actions: any[];
    workflow: {
        name: string;
    };
}

interface PageProps {
    approval: ApprovalInstance;
}

const statusVariantMap: Record<
    string,
    'default' | 'secondary' | 'destructive' | 'outline'
> = {
    pending: 'outline',
    pending_approval: 'secondary',
    approved: 'default',
    rejected: 'destructive',
    changes_requested: 'outline',
    cancelled: 'secondary',
};

export default function ApprovalShow({ approval }: PageProps) {
    const { site_code, auth } = usePage<SharedData>().props;
    const isPending = approval.status_value === 'pending_approval';
    const isChangesRequested = approval.status_value === 'changes_requested';
    const isSubmitter = auth.user.id === approval.submitted_by.id;

    const { post, processing } = useForm({});

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pending Approvals',
            href: approvalsIndex.url({ site: site_code ?? '' }),
        },
        {
            title: 'Review Request',
            href: '#',
        },
    ];

    const renderApprovableDetails = () => {
        if (!approval.approvable) return <p>Item details not available.</p>;

        const item = approval.approvable;
        const fields = [
            { label: 'Amount', value: item.amount || item.total_amount },
            { label: 'Title/Name', value: item.title || item.name },
            { label: 'Reference', value: item.reference_number || item.code },
            { label: 'Description', value: item.description },
            {
                label: 'Date',
                value: item.created_at
                    ? format(new Date(item.created_at), 'MMM dd, yyyy')
                    : null,
            },
        ];

        return (
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {fields.map((field) =>
                    field.value ? (
                        <div key={field.label} className="space-y-1">
                            <span className="text-sm font-medium text-muted-foreground">
                                {field.label}
                            </span>
                            <div className="text-sm font-semibold">
                                {field.value}
                            </div>
                        </div>
                    ) : null,
                )}
            </div>
        );
    };

    const handleResubmit = () => {
        post(
            resubmit.url({
                site: site_code ?? '',
                approvalInstance: approval.id,
            }),
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Review Approval #${approval.id}`} />

            <div className="flex w-full flex-col gap-6 p-6">
                {/* Header Section */}
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <Link
                            href={approvalsIndex.url({ site: site_code ?? '' })}
                            className="mb-2 flex items-center text-sm text-muted-foreground hover:text-foreground"
                        >
                            <ArrowLeft className="mr-1 h-3 w-3" /> Back to List
                        </Link>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Approval Request
                        </h1>
                        <p className="text-muted-foreground">
                            Workflow:{' '}
                            <span className="font-medium text-foreground">
                                {approval.workflow?.name}
                            </span>
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        {isPending && approval.current_step && !isSubmitter && (
                            <>
                                <ApprovalActionDialog
                                    instanceId={approval.id}
                                    action="request_changes"
                                />
                                <ApprovalActionDialog
                                    instanceId={approval.id}
                                    action="reject"
                                />
                                <ApprovalActionDialog
                                    instanceId={approval.id}
                                    action="approve"
                                />
                            </>
                        )}
                        {isChangesRequested && isSubmitter && (
                            <Button
                                variant="default"
                                onClick={handleResubmit}
                                disabled={processing}
                            >
                                <RotateCcw className="mr-2 h-4 w-4" />
                                Resubmit
                            </Button>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 md:grid-cols-3">
                    {/* Main Content - Item Details */}
                    <div className="space-y-6 md:col-span-2">
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <CardTitle className="flex items-center gap-2">
                                        <FileText className="h-5 w-5" />
                                        Request Details
                                    </CardTitle>
                                    <Badge variant="outline">
                                        {approval.approvable_type
                                            .split('\\')
                                            .pop()}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {renderApprovableDetails()}
                            </CardContent>
                        </Card>

                        <ApprovalHistory actions={approval.actions} />
                    </div>

                    {/* Sidebar - Status & Metadata */}
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Current Status</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex items-center justify-between rounded-lg border p-3">
                                    <span className="text-sm font-medium">
                                        Status
                                    </span>
                                    <Badge
                                        variant={
                                            statusVariantMap[
                                                approval.status_value
                                            ] || 'outline'
                                        }
                                    >
                                        {approval.status_label}
                                    </Badge>
                                </div>

                                {approval.current_step && (
                                    <div className="space-y-2">
                                        <span className="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                                            <GitCommit className="h-4 w-4" />{' '}
                                            Current Step
                                        </span>
                                        <div className="rounded-lg bg-blue-50 p-3 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400">
                                            <div className="font-semibold">
                                                {approval.current_step.name}
                                            </div>
                                            {approval.current_step
                                                .description && (
                                                <div className="mt-1 text-xs opacity-80">
                                                    {
                                                        approval.current_step
                                                            .description
                                                    }
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}

                                <div className="space-y-2 border-t pt-4">
                                    <div className="flex items-center gap-2 text-sm">
                                        <User className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-muted-foreground">
                                            Submitted by:
                                        </span>
                                        <span className="ml-auto font-medium">
                                            {approval.submitted_by.name}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm">
                                        <Calendar className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-muted-foreground">
                                            Date:
                                        </span>
                                        <span className="ml-auto font-medium">
                                            {format(
                                                new Date(approval.submitted_at),
                                                'MMM dd, yyyy HH:mm',
                                            )}
                                        </span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
