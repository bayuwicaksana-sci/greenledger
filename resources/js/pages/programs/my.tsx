import { ProgramsTable } from '@/components/programs/ProgramsTable';
import AppLayout from '@/layouts/app-layout';
import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import programRoutes from '@/routes/programs';
import { BreadcrumbItem, Program } from '@/types';
import { Head } from '@inertiajs/react';

export default function MyPrograms({
    programs,
    site_code,
}: {
    programs: Program[];
    site_code: string;
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'My Programs',
            href: programRoutes.my.url({ site: site_code }),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Programs" />
            <PageLayout>
                <PageHeader
                    pageTitle="My Programs"
                    pageSubtitle="Programs you are assigned to."
                />
                <ProgramsTable programs={programs} site_code={site_code} />
            </PageLayout>
        </AppLayout>
    );
}
