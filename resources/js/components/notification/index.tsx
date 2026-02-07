import { router, useForm, usePage } from '@inertiajs/react';
import { formatDistance } from 'date-fns';
import { Bell } from 'lucide-react';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { markAllRead, markRead } from '@/routes/notifications';
import type { SharedData } from '@/types';
import { Button } from '../ui/button';

export function Notification() {
    const {
        auth: { notifications, unreadCount },
    } = usePage<SharedData>().props;
    const { post, processing } = useForm({});

    const handleMarkAllRead = () => {
        post(markAllRead.url());
    };

    const handleMarkRead = (notificationId: string, url?: string) => {
        post(markRead.url({ notificationId }));
        router.visit(url);
    };
    return (
        <Sheet>
            <SheetTrigger asChild>
                <Button variant="outline" size="icon" className="relative">
                    <Bell />
                    {unreadCount > 0 && (
                        <span className="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500" />
                    )}
                </Button>
            </SheetTrigger>
            <SheetContent className="w-full sm:max-w-xl">
                <SheetHeader>
                    <SheetTitle>Notifications</SheetTitle>
                    <SheetDescription className="sr-only">
                        List of Notifications.
                    </SheetDescription>
                </SheetHeader>
                <div>
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
                                            <Button
                                                onClick={() =>
                                                    handleMarkRead(
                                                        notification.id,
                                                        notification.data.url,
                                                    )
                                                }
                                                variant={'outline'}
                                                size={'sm'}
                                                className="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                View
                                            </Button>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </SheetContent>
        </Sheet>
    );
}
