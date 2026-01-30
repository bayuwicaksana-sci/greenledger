import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type {
    ProgramStatus,
    PaymentRequestStatus,
    SettlementStatus,
    ActivityStatus,
    RevenueStatus,
} from '@/types/dashboard';

type StatusType =
    | ProgramStatus
    | PaymentRequestStatus
    | SettlementStatus
    | ActivityStatus
    | RevenueStatus
    | string;

interface StatusBadgeProps {
    status: StatusType;
    className?: string;
}

const statusConfig: Record<
    string,
    { variant: 'default' | 'secondary' | 'destructive' | 'outline'; className?: string }
> = {
    // Program statuses
    ACTIVE: { variant: 'default', className: 'bg-green-500 hover:bg-green-600' },
    DRAFT: { variant: 'secondary' },
    COMPLETED: { variant: 'default', className: 'bg-blue-500 hover:bg-blue-600' },
    ARCHIVED: { variant: 'outline' },

    // Payment Request statuses
    SUBMITTED: { variant: 'default', className: 'bg-blue-500 hover:bg-blue-600' },
    APPROVED: { variant: 'default', className: 'bg-green-500 hover:bg-green-600' },
    REJECTED: { variant: 'destructive' },
    PAID: { variant: 'default', className: 'bg-purple-500 hover:bg-purple-600' },
    SETTLED: { variant: 'default', className: 'bg-teal-500 hover:bg-teal-600' },
    CANCELLED: { variant: 'outline' },

    // Settlement statuses
    PENDING: { variant: 'default', className: 'bg-yellow-500 hover:bg-yellow-600' },
    REVISION_REQUESTED: { variant: 'default', className: 'bg-orange-500 hover:bg-orange-600' },

    // Activity statuses
    PLANNED: { variant: 'secondary' },

    // Revenue statuses
    POSTED: { variant: 'default', className: 'bg-green-500 hover:bg-green-600' },
    UNDER_REVIEW: { variant: 'default', className: 'bg-yellow-500 hover:bg-yellow-600' },
    CORRECTED: { variant: 'default', className: 'bg-blue-500 hover:bg-blue-600' },
};

export function StatusBadge({ status, className }: StatusBadgeProps) {
    const config = statusConfig[status] || {
        variant: 'outline' as const,
    };

    // Format status for display (convert SNAKE_CASE to Title Case)
    const displayStatus = status
        .split('_')
        .map((word) => word.charAt(0) + word.slice(1).toLowerCase())
        .join(' ');

    return (
        <Badge
            variant={config.variant}
            className={cn(config.className, className)}
        >
            {displayStatus}
        </Badge>
    );
}
