import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { mapNavigationIcons } from '@/lib/navigation-icons';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { SiteSwitcher } from './site-switcher';

export function AppSidebar() {
    const { navigation, site_code } = usePage<SharedData>().props;
    const mainNavItems = mapNavigationIcons(navigation, site_code ?? '');

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SiteSwitcher />
                {/* <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link
                                href={dashboard({ site_code: site_code })}
                                prefetch
                            >
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu> */}
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
