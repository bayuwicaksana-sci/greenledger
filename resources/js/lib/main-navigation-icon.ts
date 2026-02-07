import {
    Activity,
    Archive,
    Banknote,
    Bell,
    BookOpen,
    Calculator,
    Calendar,
    DollarSign,
    FileCheck,
    FilePlus,
    FileText,
    FlaskConical,
    FolderTree,
    GitBranch,
    History,
    LayoutGrid,
    MapPin,
    PieChart,
    Plus,
    Receipt,
    Settings,
    Shield,
    Sprout,
    TrendingUp,
    Users,
    Wallet,
    Wheat,
} from 'lucide-react';
import type { NavGroup } from '@/types';

const iconMap: Record<string, any> = {
    Activity,
    Archive,
    Banknote,
    Bell,
    BookOpen,
    Calculator,
    Calendar,
    DollarSign,
    FileCheck,
    FilePlus,
    FileText,
    FlaskConical,
    FolderTree,
    History,
    LayoutGrid,
    MapPin,
    PieChart,
    Plus,
    Receipt,
    Settings,
    Shield,
    Sprout,
    TrendingUp,
    Users,
    Wallet,
    Wheat,
    GitBranch,
};

/**
 * Generate route URL from route name
 */
function generateRouteUrl(routeName: string, siteCode: string): string {
    // Map route names to actual URLs
    // Since we're using site-prefixed routes, we need to construct the URL
    const baseUrl = `/site/${siteCode}`;

    const routeMap: Record<string, string> = {
        // Dashboard
        dashboard: `${baseUrl}/dashboard`,

        // Programs
        'programs.my': `${baseUrl}/programs/my`,
        'programs.index': `${baseUrl}/programs`,
        'programs.create': `${baseUrl}/programs/create`,
        'programs.archived': `${baseUrl}/programs/archived`,

        // Payment Requests
        'payment-requests.index': `${baseUrl}/payment-requests`,
        'payment-requests.my': `${baseUrl}/payment-requests/my`,
        'payment-requests.create': `${baseUrl}/payment-requests/create`,
        'payment-requests.approvals': `${baseUrl}/payment-requests/approvals`,
        'payment-requests.queue': `${baseUrl}/payment-requests/queue`,

        // Settlements
        'settlements.index': `${baseUrl}/settlements`,

        // Activities
        'activities.index': `${baseUrl}/activities`,
        'activities.tracking': `${baseUrl}/activities/tracking`,

        // Revenue - Harvest
        'revenue.harvest.create': `${baseUrl}/revenue/harvest/create`,
        'revenue.harvest.index': `${baseUrl}/revenue/harvest`,

        // Revenue - Testing
        'revenue.testing.create': `${baseUrl}/revenue/testing/create`,
        'revenue.testing.index': `${baseUrl}/revenue/testing`,

        // Subsidi
        'subsidi.my': `${baseUrl}/subsidi/my`,
        'subsidi.claim': `${baseUrl}/subsidi/claim`,
        'subsidi.history': `${baseUrl}/subsidi/history`,
        'subsidi.admin': `${baseUrl}/subsidi/admin`,

        // Reports
        'reports.program-pnl': `${baseUrl}/reports/program-pnl`,
        'reports.budget': `${baseUrl}/reports/budget`,
        'reports.revenue': `${baseUrl}/reports/revenue`,
        'reports.expense': `${baseUrl}/reports/expense`,
        'reports.transactions': `${baseUrl}/reports/transactions`,

        // Configuration
        'config.coa': `${baseUrl}/config/coa`,
        'config.sites': `${baseUrl}/config/sites`,
        'config.buyers-clients': `${baseUrl}/config/buyers-clients`,
        'config.settings': `${baseUrl}/config/settings`,

        // Administration
        'admin.users': `${baseUrl}/admin/users`,
        'admin.roles': `${baseUrl}/admin/roles`,
        'admin.logs': `${baseUrl}/admin/logs`,
        'admin.health': `${baseUrl}/admin/health`,
        'admin.approval-workflows': `${baseUrl}/admin/approval-workflows`,

        // Notifications
        'notifications.index': `${baseUrl}/notifications`,
    };

    return routeMap[routeName] || '#';
}

export function mapMainNavigationIcons(
    navigation: any[],
    siteCode: string,
): NavGroup[] {
    return navigation.map((group) => ({
        title: group.title,
        navItems: group.items.map((item: any) => ({
            title: item.title,
            href: generateRouteUrl(item.route, siteCode),
            icon: iconMap[item.icon] || null,
            badge: item.badgeCount,
        })),
    }));
}
