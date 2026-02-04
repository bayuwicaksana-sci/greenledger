import { ProgramsTable } from '@/components/programs/ProgramsTable';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import PageAction from '@/components/page/page-action';
import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import programRoutes from '@/routes/programs';
import { BreadcrumbItem, Program } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

export default function ArchivedPrograms({
    programs,
    site_code,
}: {
    programs: Program[];
    site_code: string;
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Programs',
            href: programRoutes.index.url({ site: site_code }),
        },
        {
            title: 'Archived',
            href: programRoutes.archived.url({ site: site_code }),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Archived Programs" />
            <PageLayout>
                <PageHeader
                    pageTitle="Archived Programs"
                    pageSubtitle="Programs that have been archived."
                >
                    <PageAction>
                        <Link
                            href={programRoutes.index.url({
                                site: site_code,
                            })}
                        >
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" /> Back to
                                Programs
                            </Button>
                        </Link>
                    </PageAction>
                </PageHeader>
                <ProgramsTable programs={programs} site_code={site_code} />
            </PageLayout>
        </AppLayout>
    );
}
