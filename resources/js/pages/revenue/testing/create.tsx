import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

export default function RecordTestingService() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Record Testing Service',
            href: '#',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Record Testing Service" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex flex-col items-center justify-center rounded-xl border border-sidebar-border/70 p-12 dark:border-sidebar-border">
                    <h1 className="text-3xl font-bold">
                        Record Testing Service
                    </h1>
                    <p className="mt-4 text-muted-foreground">Coming soon...</p>
                </div>
            </div>
        </AppLayout>
    );
}
