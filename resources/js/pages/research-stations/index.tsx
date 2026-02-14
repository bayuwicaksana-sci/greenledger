import { Head, Link, router, usePage } from '@inertiajs/react';
import { ArrowRight, Building2, MapPin, Plus, Warehouse } from 'lucide-react';
import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import MainLayout from '@/layouts/main-layout';
import { dashboard, mainDashboard } from '@/routes';
import { create, index } from '@/routes/research-stations';
import type { BreadcrumbItem, Site } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Research Stations',
        href: index().url,
    },
];

export default function ResearchStations({ sites }: { sites: Site[] }) {
    const { auth } = usePage<{
        auth: { allPermissions: string[] };
    }>().props;
    const canCreate = auth.allPermissions.includes('sites.create');
    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Research Stations" />
            <PageLayout>
                <div className="flex items-center justify-between">
                    <PageHeader
                        pageTitle="Research Stations"
                        pageSubtitle="Manage your research stations"
                    />
                    {canCreate && (
                        <Link href={create().url}>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Create Site
                            </Button>
                        </Link>
                    )}
                </div>

                {sites.length === 0 ? (
                    <Empty>
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <Warehouse />
                            </EmptyMedia>
                            <EmptyTitle>No research stations found</EmptyTitle>
                            <EmptyDescription>
                                {canCreate
                                    ? 'Create a new research station to get started'
                                    : 'No research stations are available yet'}
                            </EmptyDescription>
                        </EmptyHeader>
                        {canCreate && (
                            <EmptyContent>
                                <Button asChild>
                                    <Link href={create().url}>
                                        <Plus className="mr-2 h-4 w-4" />
                                        Add Research Station
                                    </Link>
                                </Button>
                            </EmptyContent>
                        )}
                    </Empty>
                ) : (
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {sites.map((site) => (
                            <Card
                                key={site.id}
                                className="h-full cursor-pointer transition-all hover:bg-accent hover:shadow-md"
                                onClick={() =>
                                    router.get(
                                        dashboard({ site: site.site_code }),
                                    )
                                }
                            >
                                <CardHeader>
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="flex items-center gap-3">
                                            <Building2 className="h-5 w-5 shrink-0 text-muted-foreground" />
                                            <CardTitle className="text-lg">
                                                {site.site_name}
                                            </CardTitle>
                                        </div>
                                        <Badge
                                            variant="outline"
                                            className="shrink-0"
                                        >
                                            {site.site_code}
                                        </Badge>
                                    </div>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex items-start gap-2 text-sm text-muted-foreground">
                                        <MapPin className="mt-0.5 h-4 w-4 shrink-0" />
                                        <span className="line-clamp-2">
                                            {site.address}
                                        </span>
                                    </div>
                                </CardContent>
                                <CardFooter className="pt-0">
                                    <Button variant="ghost" size="sm" className="ml-auto">
                                        View Dashboard
                                        <ArrowRight className="ml-2 h-4 w-4" />
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}
            </PageLayout>
        </MainLayout>
    );
}
