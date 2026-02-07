import { AlertCircle, AlertTriangle, CheckCircle, Info } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { AlertAction, AlertItemData } from '@/types/dashboard';

interface AlertItemProps {
    alert: AlertItemData;
    actions?: AlertAction[];
    className?: string;
}

const severityConfig = {
    error: {
        icon: AlertCircle,
        className: 'text-red-600 dark:text-red-400',
        bgClassName:
            'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-900',
    },
    warning: {
        icon: AlertTriangle,
        className: 'text-yellow-600 dark:text-yellow-400',
        bgClassName:
            'bg-yellow-50 dark:bg-yellow-950/20 border-yellow-200 dark:border-yellow-900',
    },
    info: {
        icon: Info,
        className: 'text-blue-600 dark:text-blue-400',
        bgClassName:
            'bg-blue-50 dark:bg-blue-950/20 border-blue-200 dark:border-blue-900',
    },
    success: {
        icon: CheckCircle,
        className: 'text-green-600 dark:text-green-400',
        bgClassName:
            'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-900',
    },
};

export function AlertItem({ alert, actions, className }: AlertItemProps) {
    const config = severityConfig[alert.severity];
    const Icon = config.icon;

    const getDeadlineText = () => {
        if (!alert.deadline) return null;

        const deadline = new Date(alert.deadline);
        const now = new Date();

        if (alert.overdue) {
            return (
                <span className="font-medium text-red-600 dark:text-red-400">
                    OVERDUE by {Math.abs(alert.hoursRemaining || 0)} hours
                </span>
            );
        }

        if (alert.hoursRemaining && alert.hoursRemaining < 12) {
            return (
                <span className="font-medium text-orange-600 dark:text-orange-400">
                    Due in {alert.hoursRemaining} hours
                </span>
            );
        }

        return (
            <span className="text-muted-foreground">
                Due:{' '}
                {deadline.toLocaleString('id-ID', {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                })}
            </span>
        );
    };

    return (
        <div
            className={cn(
                'rounded-lg border p-4 transition-colors',
                config.bgClassName,
                className,
            )}
        >
            <div className="flex gap-3">
                <Icon
                    className={cn(
                        'mt-0.5 size-5 flex-shrink-0',
                        config.className,
                    )}
                />
                <div className="flex-1 space-y-2">
                    <div>
                        <h4 className="font-medium text-foreground">
                            {alert.title}
                        </h4>
                        <p className="mt-1 text-sm text-muted-foreground">
                            {alert.description}
                        </p>
                    </div>
                    {alert.deadline && (
                        <div className="text-sm">{getDeadlineText()}</div>
                    )}
                    {actions && actions.length > 0 && (
                        <div className="flex flex-wrap gap-2 pt-2">
                            {actions.map((action, idx) => (
                                <Button
                                    key={idx}
                                    size="sm"
                                    variant={action.variant || 'default'}
                                    onClick={action.onClick}
                                >
                                    {action.label}
                                </Button>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
