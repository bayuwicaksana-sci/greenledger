import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

export default function ServiceHistory() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Service History',
            href: '#',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Service History" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex flex-col items-center justify-center rounded-xl border border-sidebar-border/70 p-12 dark:border-sidebar-border">
                    <h1 className="text-3xl font-bold">Service History</h1>
                    <p className="mt-4 text-muted-foreground">Coming soon...</p>
                </div>
            </div>
        </AppLayout>
    );
}
