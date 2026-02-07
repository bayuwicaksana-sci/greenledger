import { ChevronDown, ChevronUp, RefreshCw } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { CardDescription, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';

interface WidgetHeaderProps {
    title: string;
    subtitle?: string;
    icon?: React.ReactNode;
    onRefresh?: () => void;
    collapsible?: boolean;
    expanded?: boolean;
    onToggle?: () => void;
    className?: string;
}

export function WidgetHeader({
    title,
    subtitle,
    icon,
    onRefresh,
    collapsible = false,
    expanded = true,
    onToggle,
    className,
}: WidgetHeaderProps) {
    return (
        <div
            className={cn('flex items-start justify-between gap-2', className)}
        >
            <div className="flex flex-1 items-start gap-2">
                {icon && (
                    <div className="mt-0.5 text-muted-foreground">{icon}</div>
                )}
                <div className="flex-1 space-y-1">
                    <CardTitle className="text-base font-semibold">
                        {title}
                    </CardTitle>
                    {subtitle && (
                        <CardDescription className="text-sm">
                            {subtitle}
                        </CardDescription>
                    )}
                </div>
            </div>
            <div className="flex items-center gap-1">
                {onRefresh && (
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={(e) => {
                            e.stopPropagation();
                            onRefresh();
                        }}
                        className="size-8"
                    >
                        <RefreshCw className="size-4" />
                        <span className="sr-only">Refresh</span>
                    </Button>
                )}
                {collapsible && (
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={(e) => {
                            e.stopPropagation();
                            onToggle?.();
                        }}
                        className="size-8"
                    >
                        {expanded ? (
                            <ChevronUp className="size-4" />
                        ) : (
                            <ChevronDown className="size-4" />
                        )}
                        <span className="sr-only">
                            {expanded ? 'Collapse' : 'Expand'}
                        </span>
                    </Button>
                )}
            </div>
        </div>
    );
}
