import { Badge } from '@/components/ui/badge';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type { NavGroup } from '@/types';
import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from './ui/collapsible';

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
                {item.navItems.map((navItem) =>
                    navItem.subItems ? (
                        <Collapsible
                            key={navItem.title}
                            className="group/collapsible"
                        >
                            <SidebarMenuItem>
                                <CollapsibleTrigger asChild>
                                    <SidebarMenuButton
                                        tooltip={{ children: navItem.title }}
                                    >
                                        {navItem.icon && <navItem.icon />}
                                        <span>{navItem.title}</span>
                                        <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        {navItem.subItems.map((subItem) => (
                                            <SidebarMenuSubItem
                                                key={subItem.title}
                                            >
                                                <SidebarMenuSubButton
                                                    asChild
                                                    isActive={isCurrentUrl(
                                                        subItem.href,
                                                    )}
                                                >
                                                    <Link
                                                        href={subItem.href}
                                                        prefetch
                                                    >
                                                        <span>
                                                            {subItem.title}
                                                        </span>
                                                        {subItem.badge &&
                                                            Number(
                                                                subItem.badge,
                                                            ) > 0 && (
                                                                <Badge
                                                                    variant="destructive"
                                                                    className="ml-auto text-xs"
                                                                >
                                                                    {Number(
                                                                        subItem.badge,
                                                                    ) > 99
                                                                        ? '99+'
                                                                        : subItem.badge}
                                                                </Badge>
                                                            )}
                                                    </Link>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        ))}
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    ) : (
                        <SidebarMenuItem key={navItem.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={isCurrentUrl(navItem.href)}
                                tooltip={{ children: navItem.title }}
                            >
                                <Link href={navItem.href} prefetch>
                                    {navItem.icon && <navItem.icon />}
                                    <span>{navItem.title}</span>
                                    {navItem.badge &&
                                        Number(navItem.badge) > 0 && (
                                            <Badge
                                                variant="destructive"
                                                className="ml-auto text-xs"
                                            >
                                                {Number(navItem.badge) > 99
                                                    ? '99+'
                                                    : navItem.badge}
                                            </Badge>
                                        )}
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ),
                )}
            </SidebarMenu>
        </SidebarGroup>
    ));
}
