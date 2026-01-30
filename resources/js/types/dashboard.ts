// Dashboard Type Definitions
// Matches database schema for seamless transition to real data

export type TrendDirection = 'up' | 'down';
export type AlertSeverity = 'info' | 'warning' | 'error' | 'success';
export type ProgramStatus = 'DRAFT' | 'ACTIVE' | 'COMPLETED' | 'ARCHIVED';
export type PaymentRequestStatus =
    | 'DRAFT'
    | 'SUBMITTED'
    | 'APPROVED'
    | 'REJECTED'
    | 'PAID'
    | 'SETTLED'
    | 'CANCELLED';
export type SettlementStatus =
    | 'PENDING'
    | 'SUBMITTED'
    | 'REVISION_REQUESTED'
    | 'APPROVED'
    | 'REJECTED';
export type ActivityStatus = 'PLANNED' | 'ACTIVE' | 'COMPLETED' | 'CANCELLED';
export type RevenueStatus = 'POSTED' | 'UNDER_REVIEW' | 'CORRECTED';

// Trend data for KPI cards
export interface TrendData {
    direction: TrendDirection;
    value: string;
    positive: boolean;
}

// KPI Card data structure
export interface KpiCardData {
    title: string;
    value: string | number;
    trend?: TrendData;
    subtitle?: string;
    alert?: AlertSeverity;
    icon?: string;
    onClick?: string; // Route name
    loading?: boolean;
}

// Alert/Notification item
export interface AlertItemData {
    id: string | number;
    severity: AlertSeverity;
    title: string;
    description: string;
    deadline?: string;
    hoursRemaining?: number;
    overdue?: boolean;
    actions?: AlertAction[];
}

export interface AlertAction {
    label: string;
    onClick: () => void;
    variant?: 'default' | 'destructive' | 'outline';
}

// Program data (matches Program model)
export interface ProgramData {
    id: number;
    name: string;
    status: ProgramStatus;
    site_code: string;
    budget: number;
    spent: number;
    revenue: number;
    netIncome: number;
    budgetUtilization: number;
    created_at: string;
    updated_at: string;
}

// Payment Request data (matches PaymentRequest model)
export interface PaymentRequestData {
    id: number;
    request_number: string;
    purpose: string;
    status: PaymentRequestStatus;
    requested_amount: number;
    approved_amount?: number;
    actual_amount?: number;
    surplus_amount?: number;
    settlement_deadline?: string;
    program?: {
        id: number;
        name: string;
    };
    created_by?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
}

// Settlement data (matches Settlement model)
export interface SettlementData {
    id: number;
    payment_request_id: number;
    requestNumber: string;
    purpose: string;
    status: SettlementStatus;
    requested_amount: number;
    actual_amount: number;
    surplus_amount: number;
    deadline: string;
    overdue: boolean;
    hoursRemaining: number;
    receipt_url?: string;
    created_at: string;
    updated_at: string;
}

// Activity data (matches Activity model)
export interface ActivityData {
    id: number;
    program_id: number;
    program_name: string;
    name: string;
    status: ActivityStatus;
    start_date: string;
    end_date: string;
    budget: number;
    spent: number;
    progress: number;
    created_at: string;
    updated_at: string;
}

// Revenue Harvest data (matches RevenueHarvest model)
export interface RevenueHarvestData {
    id: number;
    harvest_number: string;
    program_id: number;
    program_name: string;
    date: string;
    crop_type: string;
    quantity_kg: number;
    price_per_kg: number;
    total_revenue: number;
    buyer_name: string;
    status: RevenueStatus;
    created_at: string;
}

// Revenue Testing Service data (matches RevenueTestingService model)
export interface RevenueTestingServiceData {
    id: number;
    service_number: string;
    program_id: number;
    program_name: string;
    date: string;
    service_type: string;
    total_revenue: number;
    client_name: string;
    status: RevenueStatus;
    created_at: string;
}

// Recent Activity/Transaction
export interface RecentActivityData {
    id: string | number;
    date: string;
    type: 'Payment Request' | 'Settlement' | 'Harvest Revenue' | 'Testing Service';
    reference: string;
    description: string;
    amount: number;
    status: string;
}

// Approval Queue Item
export interface ApprovalItemData {
    id: number;
    type: 'Payment Request' | 'Program' | 'Settlement';
    reference: string;
    amount: number;
    requestor: string;
    age: string; // e.g., "36 hours"
    urgency: 'high' | 'medium' | 'low';
    details?: string;
}

// Team Member Activity
export interface TeamMemberData {
    id: number;
    name: string;
    role: string;
    program?: string;
    pendingSettlements: number;
    overdueSettlements: number;
    lastActivity: string;
    status: 'active' | 'inactive' | 'warning';
}

// Site Performance Metrics
export interface SiteMetrics {
    site_code: string;
    site_name: string;
    activePrograms: number;
    budgetAllocated: number;
    budgetUtilized: number;
    budgetUtilizationPercent: number;
    revenueYTD: number;
    expensesYTD: number;
    netIncome: number;
    avgProgramROI: number;
}

// Budget by Category
export interface BudgetCategoryData {
    category: string;
    allocated: number;
    spent: number;
    percentage: number;
    status: 'good' | 'warning' | 'critical';
}

// Chart Data Point
export interface ChartDataPoint {
    label: string;
    value: number;
    color?: string;
}

// Time Series Data
export interface TimeSeriesData {
    date: string;
    value: number;
    label?: string;
}

// Widget Props Base
export interface BaseWidgetProps {
    loading?: boolean;
    error?: string;
    onRefresh?: () => void;
}

// Dashboard Role Type
export type DashboardRole =
    | 'Research Officer'
    | 'Research Associate'
    | 'Manager'
    | 'AVP'
    | 'Finance Operation'
    | 'Farm Admin';

// Research Officer Dashboard Data
export interface RoDashboardData {
    kpis: KpiCardData[];
    settlementAlerts: SettlementData[];
    programs: ProgramData[];
    recentActivity: RecentActivityData[];
    budgetUtilization: ChartDataPoint[];
    upcomingHarvests: {
        id: number;
        date: string;
        program_name: string;
        crop_type: string;
        expected_quantity: number;
    }[];
}

// Research Associate Dashboard Data
export interface RaDashboardData {
    kpis: KpiCardData[];
    programs: ProgramData[];
    teamMembers: TeamMemberData[];
    activities: ActivityData[];
    revenueBreakdown: {
        harvest: { amount: number; items: RevenueHarvestData[] };
        testing: { amount: number; items: RevenueTestingServiceData[] };
    };
    budgetAlerts: AlertItemData[];
}

// Manager Dashboard Data
export interface ManagerDashboardData {
    kpis: KpiCardData[];
    approvalQueue: ApprovalItemData[];
    siteMetrics: Record<string, SiteMetrics>; // Dynamic site codes
    programsByStatus: {
        draft: number;
        active: number;
        completed: number;
        archived: number;
    };
    budgetByCategory: BudgetCategoryData[];
    revenueTrends: TimeSeriesData[];
    teamPerformance: {
        researchAssociates: TeamMemberData[];
        researchOfficers: {
            settlementCompliance: number;
            avgApprovalTime: number;
            activeToday: number;
            total: number;
        };
    };
}

// AVP Dashboard Data
export interface AvpDashboardData {
    kpis: KpiCardData[];
    executiveSummary: {
        totalRevenue: number;
        totalExpenses: number;
        netProfit: number;
        profitMargin: number;
        activePrograms: number;
        profitablePrograms: number;
        lossMakingPrograms: number;
        avgProgramROI: number;
        settlementCompliance: number;
        avgApprovalTime: number;
        budgetUtilization: number;
    };
    strategicPriorities: {
        budgetApprovals: ApprovalItemData[];
        programCompletions: ProgramData[];
        lossMakingPrograms: ProgramData[];
    };
    sitePerformance: Record<string, SiteMetrics>; // Dynamic site codes
    portfolioAnalysis: {
        stars: any; // Can be array or object
        questions: any;
        dogs: any;
    };
    financialForecast: {
        revenueProjection: number;
        expenseProjection: number;
        netProfitProjection: number;
        confidenceLevel: number;
    };
}

// Finance Operation Dashboard Data
export interface FinanceOpsDashboardData {
    kpis: KpiCardData[];
    paymentQueue: {
        morning: PaymentRequestData[];
        afternoon: PaymentRequestData[];
    };
    settlementReview: SettlementData[];
    revenueVerification: (RevenueHarvestData | RevenueTestingServiceData)[];
    batchSummary: {
        morning: {
            processed: number;
            totalAmount: number;
            bankReference: string;
            status: string;
        };
        afternoon: {
            scheduled: number;
            totalAmount: number;
            status: string;
        };
    };
    coaStats: {
        totalActive: number;
        klatenAccounts: number;
        magelangAccounts: number;
        pendingReview: number;
    };
}

// Farm Admin Dashboard Data
export interface FarmAdminDashboardData {
    kpis: KpiCardData[];
    documentationQueue: Record<string, SettlementData[]>; // Dynamic site codes
    documentChecklist: {
        receiptUploaded: boolean;
        receiptLegible: boolean;
        amountMatches: boolean;
        vendorInvoice: boolean;
        additionalDocs: boolean;
        photoQuality: boolean;
    };
    siteActivity: Record<
        string,
        {
            activeROs: number;
            settlementsDueToday: number;
            documentationComplete: number;
            total: number;
            lastUpload: string;
        }
    >; // Dynamic site codes
    issues: {
        id: number;
        date: string;
        reference: string;
        site: string;
        issue: string;
        action: string;
        status: string;
    }[];
    recentUploads: {
        time: string;
        site: string;
        ro: string;
        reference: string;
        status: string;
    }[];
}

// Union type for all dashboard data
export type DashboardData =
    | RoDashboardData
    | RaDashboardData
    | ManagerDashboardData
    | AvpDashboardData
    | FinanceOpsDashboardData
    | FarmAdminDashboardData;

// Dashboard page props (from Inertia)
export interface DashboardPageProps {
    role: DashboardRole;
    dashboardData: DashboardData;
    canViewAllSites?: boolean;
    currentViewScope?: 'current' | 'all';
    currentSite?: {
        site_code: string;
        site_name: string;
    };
}
