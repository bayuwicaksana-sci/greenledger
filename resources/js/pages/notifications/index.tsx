import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { markAllRead, markRead } from '@/routes/notifications';
import type { BreadcrumbItem, PaginatedResponse, SharedData } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { formatDistance } from 'date-fns';
import { Bell, CheckCircle2, ExternalLink } from 'lucide-react';

interface Notification {
    id: string;
    type: string;
    data: {
        message: string;
        url?: string;
        type?: string;
    };
    read_at: string | null;
    created_at: string;
}

interface PageProps {
    notifications: PaginatedResponse<Notification>;
    unreadCount: number;
}

export default function Notifications({
    notifications,
    unreadCount,
}: PageProps) {
    const { site_code } = usePage<SharedData>().props;
    const { post, processing } = useForm({});

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Notifications',
            href: '#',
        },
    ];

    const handleMarkAllRead = () => {
        post(markAllRead.url({ site: site_code ?? '' }));
    };

    const handleMarkRead = (notificationId: string) => {
        post(markRead.url({ site: site_code ?? '', notificationId }));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Notifications" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Notifications</h1>
                        {unreadCount > 0 && (
                            <p className="text-muted-foreground">
                                {unreadCount} unread notification
                                {unreadCount !== 1 ? 's' : ''}
                            </p>
                        )}
                    </div>
                    {unreadCount > 0 && (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={handleMarkAllRead}
                            disabled={processing}
                        >
                            <CheckCircle2 className="mr-2 h-4 w-4" />
                            Mark All as Read
                        </Button>
                    )}
                </div>

                <Card>
                    <CardContent className="p-0">
                        {notifications.data.length === 0 ? (
                            <div className="flex h-64 flex-col items-center justify-center gap-3 text-center">
                                <Bell className="h-12 w-12 text-muted-foreground opacity-50" />
                                <div>
                                    <p className="font-medium text-muted-foreground">
                                        No notifications yet
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        You'll see approval updates and other
                                        activity here.
                                    </p>
                                </div>
                            </div>
                        ) : (
                            <div className="divide-y">
                                {notifications.data.map((notification) => (
                                    <div
                                        key={notification.id}
                                        className={`flex items-start gap-3 p-4 transition-colors hover:bg-muted/50 ${
                                            !notification.read_at
                                                ? 'bg-blue-50/50 dark:bg-blue-950/20'
                                                : ''
                                        }`}
                                    >
                                        {/* Read/Unread indicator */}
                                        <div className="mt-1.5 shrink-0">
                                            {!notification.read_at ? (
                                                <div className="h-2.5 w-2.5 rounded-full bg-blue-500" />
                                            ) : (
                                                <div className="h-2.5 w-2.5 rounded-full bg-transparent" />
                                            )}
                                        </div>

                                        {/* Content */}
                                        <div className="min-w-0 flex-1">
                                            <p
                                                className={`text-sm ${!notification.read_at ? 'font-semibold' : 'font-medium'}`}
                                            >
                                                {notification.data.message}
                                            </p>
                                            <p className="mt-0.5 text-xs text-muted-foreground">
                                                {formatDistance(
                                                    new Date(
                                                        notification.created_at,
                                                    ),
                                                    new Date(),
                                                    { addSuffix: true },
                                                )}
                                            </p>
                                        </div>

                                        {/* Actions */}
                                        <div className="flex shrink-0 items-center gap-2">
                                            {notification.data.url && (
                                                <Link
                                                    href={notification.data.url}
                                                    className="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    <ExternalLink className="h-3.5 w-3.5" />
                                                </Link>
                                            )}
                                            {!notification.read_at && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        handleMarkRead(
                                                            notification.id,
                                                        )
                                                    }
                                                    disabled={processing}
                                                    className="text-xs text-muted-foreground transition-colors hover:text-foreground disabled:opacity-50"
                                                >
                                                    <CheckCircle2 className="h-4 w-4" />
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
