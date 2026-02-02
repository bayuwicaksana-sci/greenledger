import { ApprovalActionDialog } from '@/components/approval-workflows/ApprovalActionDialog';
import { ApprovalHistory } from '@/components/approval-workflows/ApprovalHistory';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react'; // Link missing in imports but likely needed
import { format } from 'date-fns';
import { ArrowLeft, Calendar, FileText, GitCommit, User } from 'lucide-react';

interface ApprovalInstance {
    id: string;
    approvable_type: string;
    approvable_id: string;
    status: string;
    submitted_at: string;
    submitted_by: {
        id: number;
        name: string;
        email: string;
    };
    current_step: {
        id: string;
        name: string;
        description?: string;
    } | null;
    approvable: any; // Dynamic based on model
    actions: any[];
    workflow: {
        name: string;
    };
}

interface PageProps {
    approval: ApprovalInstance;
}

export default function ApprovalShow({ approval }: PageProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pending Approvals',
            href: route('approvals.index'),
        },
        {
            title: 'Review Request',
            href: '#',
        },
    ];

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'approved':
                return 'success';
            case 'rejected':
                return 'destructive';
            case 'pending_approval':
                return 'secondary'; // or warning
            default:
                return 'outline';
        }
    };

    const isPending = approval.status === 'pending_approval';

    // Helper to display key details from the approvable item (generic for now)
    const renderApprovableDetails = () => {
        if (!approval.approvable) return <p>Item details not available.</p>;

        const item = approval.approvable;
        // Try to identify common fields
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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Review Approval #${approval.id}`} />

            <div className="flex w-full flex-col gap-6 p-6">
                {/* Header Section */}
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <Link
                            href={route('approvals.index')}
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
                        {isPending && approval.current_step && (
                            <>
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
                                            getStatusVariant(
                                                approval.status,
                                            ) as any
                                        }
                                    >
                                        {approval.status
                                            .replace('_', ' ')
                                            .toUpperCase()}
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
