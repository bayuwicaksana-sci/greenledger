import { ProgramsTable } from '@/components/programs/ProgramsTable';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import PageAction from '@/components/page/page-action';
import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import programRoutes from '@/routes/programs';
import { BreadcrumbItem, Program } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

export default function ProgramsIndex({
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
    ];
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Programs" />
            <PageLayout>
                <PageHeader
                    pageTitle="Programs"
                    pageSubtitle="Manage research programs for this site."
                >
                    <PageAction>
                        <Link
                            href={programRoutes.create.url({
                                site: site_code,
                            })}
                        >
                            <Button>
                                <Plus className="mr-2 h-4 w-4" /> New Program
                            </Button>
                        </Link>
                    </PageAction>
                </PageHeader>
                <ProgramsTable programs={programs} site_code={site_code} />
            </PageLayout>
        </AppLayout>
    );
}
