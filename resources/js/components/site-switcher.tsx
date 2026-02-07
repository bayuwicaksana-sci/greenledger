import { Link, usePage } from '@inertiajs/react';
import { ChevronsUpDown, Plus, Warehouse } from 'lucide-react';

import { useMemo } from 'react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { SharedData } from '@/types';

export function SiteSwitcher() {
    const { sites: teams, site_code } = usePage<SharedData>().props;
    const { isMobile } = useSidebar();

    const activeTeam = useMemo(() => {
        const currentTeam = teams.find((team) => team.site_code === site_code);
        return currentTeam || teams[0]; // Fallback to first team
    }, [site_code]);

    if (!activeTeam) {
        return null;
    }

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton
                            size="lg"
                            className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <div className="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
                                <Warehouse className="size-4" />
                            </div>
                            <div className="grid flex-1 text-left text-sm leading-tight">
                                <span className="truncate font-medium">
                                    {activeTeam.site_name}
                                </span>
                                <span className="truncate text-xs">
                                    Research Station
                                </span>
                            </div>
                            <ChevronsUpDown className="ml-auto" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                        align="start"
                        side={isMobile ? 'bottom' : 'right'}
                        sideOffset={4}
                    >
                        <DropdownMenuLabel className="text-xs text-muted-foreground">
                            Research Station
                        </DropdownMenuLabel>
                        {teams.map((team, index) => (
                            <DropdownMenuItem
                                asChild
                                key={team.site_name}
                                className="gap-2 p-2"
                            >
                                <Link
                                    href={dashboard({
                                        site_code: team.site_code,
                                    })}
                                >
                                    {/* <div
                                        className={cn(
                                            'flex size-6 items-center justify-center rounded-md border',
                                            team.bgColor,
                                        )}
                                    >
                                        <team.logo className="size-3.5 shrink-0 text-neutral-900" />
                                    </div> */}
                                    {team.site_name}
                                </Link>
                            </DropdownMenuItem>
                        ))}
                        <DropdownMenuSeparator />
                        <DropdownMenuItem className="gap-2 p-2">
                            <div className="flex size-6 items-center justify-center rounded-md border bg-transparent">
                                <Plus className="size-4" />
                            </div>
                            <div className="font-medium text-muted-foreground">
                                Add Research Station
                            </div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
