import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import MainLayout from '@/layouts/main-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { format } from 'date-fns';

interface ApprovalInstance {
    id: number;
    approvable_type: string;
    approvable_id: string;
    status: string;
    submitted_at: string;
    submitted_by: {
        id: number;
        name: string;
    };
    current_step: {
        id: string;
        name: string;
    };
    approvable: {
        id: string;
        // Add common fields that might exist on approvables
        name?: string;
        title?: string;
        reference_number?: string;
        amount?: number;
    } | null;
}

interface PageProps {
    approvals: {
        data: ApprovalInstance[];
        links: any[];
    };
}

export default function PendingApprovals({ approvals }: PageProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pending Approvals',
            href: '/site/klaten/approvals', // TODO: dynamic site code
        },
    ];

    const getApprovableName = (instance: ApprovalInstance) => {
        if (!instance.approvable) return 'Unknown Item';
        return (
            instance.approvable.title ||
            instance.approvable.name ||
            instance.approvable.reference_number ||
            `ID: ${instance.approvable.id}`
        );
    };

    const getApprovableTypeLabel = (type: string) => {
        const parts = type.split('\\');
        return parts[parts.length - 1]; // Simple class name
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Pending Approvals" />

            <div className="flex w-full flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">
                        Pending Approvals
                    </h1>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Items Requiring Your Action</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {approvals.data.length === 0 ? (
                            <div className="flex h-64 flex-col items-center justify-center text-center text-muted-foreground">
                                <p>
                                    You have no pending approvals at this time.
                                </p>
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Type</TableHead>
                                        <TableHead>Item</TableHead>
                                        <TableHead>Submitted By</TableHead>
                                        <TableHead>Step</TableHead>
                                        <TableHead>Submitted At</TableHead>
                                        <TableHead className="text-right">
                                            Action
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {approvals.data.map((approval) => (
                                        <TableRow key={approval.id}>
                                            <TableCell>
                                                <Badge variant="outline">
                                                    {getApprovableTypeLabel(
                                                        approval.approvable_type,
                                                    )}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="font-medium">
                                                {getApprovableName(approval)}
                                            </TableCell>
                                            <TableCell>
                                                {approval.submitted_by?.name ||
                                                    'Unknown'}
                                            </TableCell>
                                            <TableCell>
                                                {approval.current_step?.name ||
                                                    'Processing'}
                                            </TableCell>
                                            <TableCell>
                                                {format(
                                                    new Date(
                                                        approval.submitted_at,
                                                    ),
                                                    'MMM dd, yyyy',
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button asChild size="sm">
                                                    Review
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}
