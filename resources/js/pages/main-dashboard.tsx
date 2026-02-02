import PageHeader from '@/components/page/page-header';
import PageLayout from '@/components/page/page-layout';
import MainLayout from '@/layouts/main-layout';
import { mainDashboard } from '@/routes';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: mainDashboard().url,
    },
];

export default function MainDashboard() {
    const { auth } = usePage<SharedData>().props;
    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <PageLayout>
                <PageHeader
                    pageTitle={`Halo 👋, ${auth.user.name}`}
                    pageSubtitle="Welcome to GreenLedger"
                />
            </PageLayout>
        </MainLayout>
    );
}
