import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { mainDashboard, researchStations } from '@/routes';
import admin from '@/routes/admin';
import config from '@/routes/config';
import { NavGroup } from '@/types';
import { Link } from '@inertiajs/react';
import {
    BookOpen,
    Calendar,
    FileText,
    GitBranch,
    LayoutDashboard,
    Shield,
    Users,
    Warehouse,
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavGroup[] = [
    {
        navItems: [
            {
                title: 'Dashboard',
                href: mainDashboard().url,
                icon: LayoutDashboard,
            },
            {
                title: 'Research Station',
                href: researchStations().url,
                icon: Warehouse,
            },
            {
                title: 'Fiscal Year',
                href: mainDashboard().url,
                icon: Calendar,
            },
        ],
    },
    {
        title: 'Administration',
        navItems: [
            {
                title: 'Chart of Accounts',
                href: config.coa.index(),
                icon: BookOpen,
            },
            {
                title: 'User Management',
                href: admin.users().url,
                icon: Users,
            },
            {
                title: 'Role Configuration',
                href: admin.roles().url,
                icon: Shield,
            },
            {
                title: 'Approval Workflow',
                href: admin.approvalWorkflows.index(),
                icon: GitBranch,
            },
            {
                title: 'Access Log',
                href: admin.logs().url,
                icon: FileText,
            },
        ],
    },
];

export function MainSidebar() {
    // const { navigation, site_code } = usePage<SharedData>().props;
    // const mainNavItems = mapNavigationIcons(navigation, site_code ?? '');

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                {/* <SiteSwitcher /> */}
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={mainDashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                {/* <NavFooter items={footerNavItems} className="mt-auto" /> */}
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
