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
import { mainDashboard } from '@/routes';
import admin from '@/routes/admin';
import fiscalYears from '@/routes/admin/fiscal-years';
import config from '@/routes/config';
import { index as researchStations } from '@/routes/research-stations';
import type { NavGroup } from '@/types';
import { Link } from '@inertiajs/react';
import {
    BookOpen,
    BookUser,
    Calendar,
    CircleHelp,
    Compass,
    FileText,
    GitBranch,
    HandCoins,
    Handshake,
    LayoutDashboard,
    ListChecks,
    MonitorPlay,
    Shield,
    Users,
    Warehouse,
    Wrench,
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavGroup[] = [
    {
        navItems: [
            {
                title: 'Dashboard',
                href: mainDashboard(),
                icon: LayoutDashboard,
            },
            {
                title: 'Research Station',
                href: researchStations(),
                icon: Warehouse,
            },
            {
                title: 'Fiscal Year',
                href: fiscalYears.index(),
                icon: Calendar,
            },
        ],
    },
    {
        title: 'Finance',
        navItems: [
            {
                title: 'Chart of Accounts',
                href: config.coa.index(),
                icon: BookOpen,
            },
            {
                title: 'Claim Benefits',
                href: '#',
                icon: HandCoins,
            },
        ],
    },
    {
        title: 'Human Capital',
        navItems: [
            {
                title: 'User Management',
                href: admin.users(),
                icon: Users,
            },
            {
                title: 'Buyers',
                href: '#',
                icon: BookUser,
            },
            {
                title: 'Clients',
                href: '#',
                icon: Handshake,
            },
        ],
    },
    {
        title: 'Authorization',
        navItems: [
            {
                title: 'Role Configuration',
                href: admin.roles(),
                icon: Shield,
            },
            {
                title: 'Access Log',
                href: admin.logs(),
                icon: FileText,
            },
        ],
    },
    {
        title: 'System Configurations',
        navItems: [
            {
                title: 'Approval Workflow',
                href: admin.approvalWorkflows.index(),
                icon: GitBranch,
            },
        ],
    },
    {
        title: 'Help Center',
        navItems: [
            {
                title: 'Getting Started Guide',
                icon: Compass,
                subItems: [
                    {
                        title: 'System Overview',
                        href: '#',
                    },
                    {
                        title: 'Quick Start Tutorial',
                        href: '#',
                    },
                ],
            },
            {
                title: 'How-To Guides',
                icon: ListChecks,
                subItems: [
                    {
                        title: 'Create RAB',
                        href: '#',
                    },
                    {
                        title: 'Submit Payment Request',
                        href: '#',
                    },
                    {
                        title: 'Submit Settlement',
                        href: '#',
                    },
                    {
                        title: 'Approve Requests',
                        href: '#',
                    },
                    {
                        title: 'Generate Reports',
                        href: '#',
                    },
                ],
            },
            {
                title: 'Video Tutorials',
                icon: MonitorPlay,
                subItems: [
                    {
                        title: 'RAB Creation Walkthrough',
                        href: '#',
                    },
                    {
                        title: 'Payment Request Process',
                        href: '#',
                    },
                    {
                        title: 'Settlement Submission',
                        href: '#',
                    },
                ],
            },
            {
                title: 'FAQ',
                icon: CircleHelp,
                subItems: [
                    {
                        title: 'General',
                        href: '#',
                    },
                    {
                        title: 'Budget Management',
                        href: '#',
                    },
                    {
                        title: 'Payment Requests',
                        href: '#',
                    },
                    {
                        title: 'Settlements',
                        href: '#',
                    },
                ],
            },
            {
                title: 'Troubleshooting',
                icon: Wrench,
                subItems: [
                    {
                        title: 'Login Issues',
                        href: '#',
                    },
                    {
                        title: 'Upload Problems',
                        href: '#',
                    },
                    {
                        title: 'Approval Workflow Issues',
                        href: '#',
                    },
                ],
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
