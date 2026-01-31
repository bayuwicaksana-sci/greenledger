import { Button } from '@/components/ui/button';
import { Empty } from '@/components/ui/empty';
import type { SettlementData } from '@/types/dashboard';
import { AlertTriangle, CheckCircle } from 'lucide-react';
import { AlertItem } from '../molecules/AlertItem';
import { Widget } from './Widget';

interface SettlementAlertsWidgetProps {
    settlements: SettlementData[];
    loading?: boolean;
    onRefresh?: () => void;
}

export function SettlementAlertsWidget({
    settlements,
    loading,
    onRefresh,
}: SettlementAlertsWidgetProps) {
    const sortedSettlements = [...settlements].sort((a, b) => {
        // Overdue first
        if (a.overdue && !b.overdue) return -1;
        if (!a.overdue && b.overdue) return 1;
        // Then by hours remaining
        return (a.hoursRemaining || 0) - (b.hoursRemaining || 0);
    });

    const displayedSettlements = sortedSettlements.slice(0, 5);
    const hasMore = settlements.length > 5;

    return (
        <Widget
            title="Settlement Deadlines"
            icon={<AlertTriangle className="size-5" />}
            loading={loading}
            onRefresh={onRefresh}
            fullWidth
        >
            {settlements.length === 0 ? (
                <Empty
                    icon={CheckCircle}
                    title="All settlements up to date"
                    description="No pending settlements require your attention"
                    variant="icon"
                />
            ) : (
                <div className="space-y-3">
                    {displayedSettlements.map((settlement) => (
                        <AlertItem
                            key={settlement.id}
                            alert={{
                                id: settlement.id,
                                severity: settlement.overdue
                                    ? 'error'
                                    : settlement.hoursRemaining &&
                                        settlement.hoursRemaining < 12
                                      ? 'warning'
                                      : 'info',
                                title: `${settlement.requestNumber} | ${settlement.purpose}`,
                                description: `Amount: ${new Intl.NumberFormat(
                                    'id-ID',
                                    {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0,
                                    },
                                ).format(settlement.requested_amount)}`,
                                deadline: settlement.deadline,
                                hoursRemaining: settlement.hoursRemaining,
                                overdue: settlement.overdue,
                            }}
                            actions={[
                                {
                                    label: settlement.overdue
                                        ? 'Upload Receipt - URGENT'
                                        : 'Upload Receipt',
                                    onClick: () => {
                                        // Navigate to settlement upload
                                        console.log(
                                            'Upload for',
                                            settlement.id,
                                        );
                                    },
                                    variant: settlement.overdue
                                        ? 'destructive'
                                        : 'default',
                                },
                            ]}
                        />
                    ))}
                    {hasMore && (
                        <div className="flex justify-center pt-2">
                            <Button variant="outline" size="sm">
                                View All {settlements.length} Settlements
                            </Button>
                        </div>
                    )}
                </div>
            )}
        </Widget>
    );
}
