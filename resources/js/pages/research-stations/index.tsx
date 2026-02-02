import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import { StationTable } from '@/components/research-station/station-table';
import { Button } from '@/components/ui/button';
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import MainLayout from '@/layouts/main-layout';
import { mainDashboard, researchStations } from '@/routes';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Warehouse } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Research Stations',
        href: researchStations().url,
    },
];

export default function ResearchStations() {
    const { sites } = usePage<SharedData>().props;
    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Research Stations" />
            <PageLayout>
                <PageHeader
                    pageTitle="Research Stations"
                    pageSubtitle="Manage your research stations"
                />

                {sites.length === 0 ? (
                    <Empty>
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <Warehouse />
                            </EmptyMedia>
                            <EmptyTitle>No research stations found</EmptyTitle>
                            <EmptyDescription>
                                Create a new research station to get started
                            </EmptyDescription>
                        </EmptyHeader>
                        <EmptyContent>
                            <Button asChild>
                                <Link href={mainDashboard()}>
                                    Add Research Station
                                </Link>
                            </Button>
                        </EmptyContent>
                    </Empty>
                ) : (
                    <StationTable sites={sites} />
                )}
            </PageLayout>
        </MainLayout>
    );
}
