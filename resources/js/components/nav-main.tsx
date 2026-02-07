import { Link } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type { NavGroup } from '@/types';

export function NavMain({ items = [] }: { items: NavGroup[] }) {
    const { isCurrentUrl } = useCurrentUrl();

    return items.map((item, index) => (
        <SidebarGroup className="px-2 py-0" key={`sidebar-group-${index}`}>
            {item.title && (
                <SidebarGroupLabel className="mt-4">
                    {item.title}
                </SidebarGroupLabel>
            )}
            <SidebarMenu>
                {item.navItems.map((navItem) => (
                    <SidebarMenuItem key={navItem.title}>
                        <SidebarMenuButton
                            asChild
                            isActive={isCurrentUrl(navItem.href)}
                            tooltip={{ children: navItem.title }}
                        >
                            <Link href={navItem.href} prefetch>
                                {navItem.icon && <navItem.icon />}
                                <span>{navItem.title}</span>
                                {navItem.badge && Number(navItem.badge) > 0 && (
                                    <Badge variant="destructive" className="ml-auto text-xs">
                                        {Number(navItem.badge) > 99 ? '99+' : navItem.badge}
                                    </Badge>
                                )}
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    ));
}
