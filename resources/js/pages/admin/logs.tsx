import { Head } from '@inertiajs/react';
import MainLayout from '@/layouts/main-layout';
import type { BreadcrumbItem } from '@/types';

export default function AccessLogs() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Access Logs',
            href: '#',
        },
    ];

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Access Logs" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex flex-col items-center justify-center rounded-xl border border-sidebar-border/70 p-12 dark:border-sidebar-border">
                    <h1 className="text-3xl font-bold">Access Logs</h1>
                    <p className="mt-4 text-muted-foreground">Coming soon...</p>
                </div>
            </div>
        </MainLayout>
    );
}
