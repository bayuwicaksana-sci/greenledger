import { format } from 'date-fns';
import { id } from 'date-fns/locale';
import { Clock } from 'lucide-react';
import type { RecentActivityData } from '@/types/dashboard';
import { MetricValue } from '../atoms/MetricValue';
import { StatusBadge } from '../atoms/StatusBadge';
import { Widget } from './Widget';

interface RecentActivityWidgetProps {
    activities: RecentActivityData[];
    loading?: boolean;
    onRefresh?: () => void;
}

export function RecentActivityWidget({
    activities,
    loading,
    onRefresh,
}: RecentActivityWidgetProps) {
    return (
        <Widget
            title="Recent Activity"
            icon={<Clock className="size-5" />}
            loading={loading}
            onRefresh={onRefresh}
        >
            <div className="space-y-3">
                {activities.slice(0, 5).map((activity) => (
                    <div
                        key={activity.id}
                        className="flex gap-4 border-b border-border pb-3 last:border-0 last:pb-0"
                    >
                        <div className="min-w-[60px] flex-shrink-0 text-sm text-muted-foreground">
                            {format(new Date(activity.date), 'MMM dd', {
                                locale: id,
                            })}
                        </div>
                        <div className="flex-1 space-y-1">
                            <div className="flex items-center gap-2">
                                <span className="text-sm font-medium">
                                    {activity.type}
                                </span>
                                <span className="text-sm text-muted-foreground">
                                    {activity.reference}
                                </span>
                            </div>
                            <div className="text-sm text-muted-foreground">
                                {activity.description}
                            </div>
                            <div className="flex items-center gap-2">
                                <MetricValue
                                    value={activity.amount}
                                    format="currency"
                                    className="text-sm"
                                />
                                <StatusBadge
                                    status={activity.status
                                        .replace(' ', '_')
                                        .toUpperCase()}
                                />
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </Widget>
    );
}
