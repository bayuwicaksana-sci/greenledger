import { AlertCircle, FileCheck } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { ApprovalItemData } from '@/types/dashboard';
import { MetricValue } from '../atoms/MetricValue';
import { DataTable } from '../molecules/DataTable';
import { Widget } from './Widget';

interface ApprovalQueueWidgetProps {
    approvals: ApprovalItemData[];
    loading?: boolean;
    onRefresh?: () => void;
}

export function ApprovalQueueWidget({
    approvals,
    loading,
    onRefresh,
}: ApprovalQueueWidgetProps) {
    const columns = [
        {
            key: 'urgency',
            label: '',
            sortable: false,
            render: (value: string) => (
                <AlertCircle
                    className={cn(
                        'size-4',
                        value === 'high' && 'text-red-600 dark:text-red-400',
                        value === 'medium' &&
                            'text-yellow-600 dark:text-yellow-400',
                        value === 'low' && 'text-blue-600 dark:text-blue-400',
                    )}
                />
            ),
            className: 'w-8',
        },
        {
            key: 'type',
            label: 'Type',
            sortable: true,
        },
        {
            key: 'reference',
            label: 'ID',
            sortable: true,
        },
        {
            key: 'amount',
            label: 'Amount',
            sortable: true,
            render: (value: number) => (
                <MetricValue
                    value={value}
                    format="currency"
                    className="text-sm"
                />
            ),
        },
        {
            key: 'requestor',
            label: 'Requestor',
            sortable: true,
        },
        {
            key: 'age',
            label: 'Age',
            sortable: false,
            render: (value: string) => (
                <span className="text-sm text-muted-foreground">{value}</span>
            ),
        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            render: (_: any, row: ApprovalItemData) => (
                <div className="flex gap-1">
                    <Button size="sm" variant="default">
                        Approve
                    </Button>
                    <Button size="sm" variant="destructive">
                        Reject
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <Widget
            title="Pending Approvals"
            subtitle={`${approvals.length} items awaiting your approval`}
            icon={<FileCheck className="size-5" />}
            loading={loading}
            onRefresh={onRefresh}
            fullWidth
        >
            <DataTable columns={columns} data={approvals} sortable />
            {approvals.length > 5 && (
                <div className="flex justify-center pt-4">
                    <Button variant="outline">View All Approvals →</Button>
                </div>
            )}
        </Widget>
    );
}
