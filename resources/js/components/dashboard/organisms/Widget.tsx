import { useState } from 'react';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { AlertCircle } from 'lucide-react';
import { WidgetHeader } from '../molecules/WidgetHeader';
import { cn } from '@/lib/utils';

interface WidgetProps {
    title: string;
    subtitle?: string;
    icon?: React.ReactNode;
    loading?: boolean;
    error?: string;
    onRefresh?: () => void;
    collapsible?: boolean;
    defaultExpanded?: boolean;
    children: React.ReactNode;
    className?: string;
    fullWidth?: boolean;
}

export function Widget({
    title,
    subtitle,
    icon,
    loading = false,
    error,
    onRefresh,
    collapsible = false,
    defaultExpanded = true,
    children,
    className,
    fullWidth = false,
}: WidgetProps) {
    const [expanded, setExpanded] = useState(defaultExpanded);

    return (
        <Card
            className={cn(
                'h-fit',
                fullWidth && 'md:col-span-2',
                className,
            )}
        >
            <CardHeader>
                <WidgetHeader
                    title={title}
                    subtitle={subtitle}
                    icon={icon}
                    onRefresh={onRefresh}
                    collapsible={collapsible}
                    expanded={expanded}
                    onToggle={() => setExpanded(!expanded)}
                />
            </CardHeader>
            {expanded && (
                <CardContent>
                    {loading && (
                        <div className="space-y-3">
                            <Skeleton className="h-4 w-full" />
                            <Skeleton className="h-4 w-3/4" />
                            <Skeleton className="h-4 w-1/2" />
                        </div>
                    )}
                    {error && !loading && (
                        <Alert variant="destructive">
                            <AlertCircle className="size-4" />
                            <AlertDescription>{error}</AlertDescription>
                        </Alert>
                    )}
                    {!loading && !error && children}
                </CardContent>
            )}
        </Card>
    );
}
