<?php

namespace App\Data;

/**
 * Dashboard Dummy Data Generator
 *
 * Provides realistic dummy data matching database schema for all dashboard roles.
 * Data structure aligns with TypeScript interfaces for seamless frontend integration.
 */
class DashboardDummyData
{
    /**
     * Get Research Officer Dashboard Data
     *
     * @param  string  $scope  'current' or 'all'
     * @param  \App\Models\Site  $site  Current site context
     */
    public static function getRoData(string $scope = 'current', $site = null): array
    {
        // Research Officers always see current site only (no 'all' scope)
        // Even if scope is 'all', they only see their assigned programs
        return [
            'kpis' => [
                [
                    'title' => 'Active Programs',
                    'value' => 3,
                    'trend' => [
                        'direction' => 'up',
                        'value' => '1 from last month',
                        'positive' => true,
                    ],
                    'onClick' => 'programs.my',
                ],
                [
                    'title' => 'Pending Settlements',
                    'value' => 5,
                    'alert' => 'warning',
                    'subtitle' => '2 overdue',
                    'onClick' => 'settlements.index',
                ],
                [
                    'title' => 'Available Budget',
                    'value' => 45000000,
                    'subtitle' => '60% remaining',
                    'onClick' => null,
                ],
                [
                    'title' => 'This Month',
                    'value' => 12500000,
                    'subtitle' => '8 Requests | 3 Harvests',
                    'onClick' => null,
                ],
            ],
            'settlementAlerts' => [
                [
                    'id' => 1,
                    'payment_request_id' => 1,
                    'requestNumber' => 'PR-2026-001',
                    'purpose' => 'Fertilizer Purchase',
                    'status' => 'PENDING',
                    'requested_amount' => 8500000,
                    'actual_amount' => null,
                    'surplus_amount' => 0,
                    'deadline' => '2026-01-28 15:00:00',
                    'overdue' => false,
                    'hoursRemaining' => 18,
                    'created_at' => '2026-01-25 09:00:00',
                    'updated_at' => '2026-01-25 09:00:00',
                ],
                [
                    'id' => 2,
                    'payment_request_id' => 5,
                    'requestNumber' => 'PR-2026-005',
                    'purpose' => 'Pesticide',
                    'status' => 'PENDING',
                    'requested_amount' => 3200000,
                    'actual_amount' => null,
                    'surplus_amount' => 0,
                    'deadline' => '2026-01-27 10:00:00',
                    'overdue' => true,
                    'hoursRemaining' => -6,
                    'created_at' => '2026-01-24 10:00:00',
                    'updated_at' => '2026-01-24 10:00:00',
                ],
            ],
            'programs' => [
                [
                    'id' => 1,
                    'name' => 'Rice Trial 2026',
                    'status' => 'ACTIVE',
                    'site_code' => 'klaten',
                    'budget' => 50000000,
                    'spent' => 30000000,
                    'revenue' => 15000000,
                    'netIncome' => -15000000,
                    'budgetUtilization' => 60,
                    'created_at' => '2026-01-01 00:00:00',
                    'updated_at' => '2026-01-27 00:00:00',
                ],
                [
                    'id' => 2,
                    'name' => 'Chili Harvest Cycle',
                    'status' => 'ACTIVE',
                    'site_code' => 'klaten',
                    'budget' => 40000000,
                    'spent' => 38000000,
                    'revenue' => 42000000,
                    'netIncome' => 4000000,
                    'budgetUtilization' => 95,
                    'created_at' => '2025-12-15 00:00:00',
                    'updated_at' => '2026-01-27 00:00:00',
                ],
                [
                    'id' => 3,
                    'name' => 'Corn Research',
                    'status' => 'ACTIVE',
                    'site_code' => 'klaten',
                    'budget' => 35000000,
                    'spent' => 12000000,
                    'revenue' => 5000000,
                    'netIncome' => -7000000,
                    'budgetUtilization' => 34,
                    'created_at' => '2026-01-10 00:00:00',
                    'updated_at' => '2026-01-27 00:00:00',
                ],
            ],
            'recentActivity' => [
                [
                    'id' => 'PR-2026-012',
                    'date' => '2026-01-27',
                    'type' => 'Payment Request',
                    'reference' => 'PR-2026-012',
                    'description' => 'Fertilizer',
                    'amount' => 5500000,
                    'status' => 'Pending Approval',
                ],
                [
                    'id' => 'HRV-2026-045',
                    'date' => '2026-01-26',
                    'type' => 'Harvest Revenue',
                    'reference' => 'HRV-2026-045',
                    'description' => 'Chili 250kg',
                    'amount' => 5000000,
                    'status' => 'Posted',
                ],
                [
                    'id' => 'PR-2026-008',
                    'date' => '2026-01-25',
                    'type' => 'Settlement',
                    'reference' => 'PR-2026-008',
                    'description' => 'Pesticide',
                    'amount' => 3200000,
                    'status' => 'Approved',
                ],
            ],
            'budgetUtilization' => [
                ['label' => 'Rice Trial 2026', 'value' => 60],
                ['label' => 'Chili Harvest', 'value' => 95],
                ['label' => 'Corn Research', 'value' => 34],
            ],
            'upcomingHarvests' => [
                [
                    'id' => 1,
                    'date' => '2026-01-30',
                    'program_name' => 'Chili Harvest Cycle',
                    'crop_type' => 'Chili',
                    'expected_quantity' => 300,
                ],
                [
                    'id' => 2,
                    'date' => '2026-02-05',
                    'program_name' => 'Rice Trial 2026',
                    'crop_type' => 'Rice',
                    'expected_quantity' => 800,
                ],
                [
                    'id' => 3,
                    'date' => '2026-02-12',
                    'program_name' => 'Chili Harvest Cycle',
                    'crop_type' => 'Chili',
                    'expected_quantity' => 280,
                ],
            ],
        ];
    }

    /**
     * Get Research Associate Dashboard Data
     *
     * @param  string  $scope  'current' or 'all'
     * @param  \App\Models\Site  $site  Current site context
     */
    public static function getRaData(string $scope = 'current', $site = null): array
    {
        // Research Associates always see current site only
        return [
            'kpis' => [
                [
                    'title' => 'My Programs',
                    'value' => '4 Active | 1 Draft',
                    'subtitle' => 'Budget: Rp 180M',
                ],
                [
                    'title' => 'Net Program Income',
                    'value' => 12500000,
                    'trend' => [
                        'direction' => 'up',
                        'value' => '+2.5M from last month',
                        'positive' => true,
                    ],
                ],
                [
                    'title' => 'Team Pending',
                    'value' => '8 Settlements',
                    'subtitle' => '3 Payment Requests',
                ],
                [
                    'title' => 'Revenue (MTD)',
                    'value' => 45000000,
                    'subtitle' => '15 Harvests | 2 Services',
                ],
                [
                    'title' => 'Budget Utilization',
                    'value' => '68%',
                    'subtitle' => 'Rp 122M / 180M',
                ],
                [
                    'title' => 'Activities',
                    'value' => '12 Active | 3 Completed',
                    'subtitle' => '2 Behind Schedule',
                    'alert' => 'warning',
                ],
            ],
            'programs' => self::getRoData()['programs'],
            'teamMembers' => [
                [
                    'id' => 1,
                    'name' => 'Budi',
                    'role' => 'RO',
                    'program' => 'Rice Trial',
                    'pendingSettlements' => 2,
                    'overdueSettlements' => 1,
                    'lastActivity' => '2 hours ago',
                    'status' => 'warning',
                ],
                [
                    'id' => 2,
                    'name' => 'Siti',
                    'role' => 'RO',
                    'program' => 'Chili Cycle',
                    'pendingSettlements' => 0,
                    'overdueSettlements' => 0,
                    'lastActivity' => '30 minutes ago',
                    'status' => 'active',
                ],
                [
                    'id' => 3,
                    'name' => 'Andi',
                    'role' => 'RO',
                    'program' => 'Corn Study',
                    'pendingSettlements' => 1,
                    'overdueSettlements' => 0,
                    'lastActivity' => '1 day ago',
                    'status' => 'inactive',
                ],
            ],
            'activities' => [
                [
                    'id' => 1,
                    'program_id' => 1,
                    'program_name' => 'Rice Trial 2026',
                    'name' => 'Land Preparation',
                    'status' => 'COMPLETED',
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-01-15',
                    'budget' => 12000000,
                    'spent' => 12000000,
                    'progress' => 100,
                    'created_at' => '2026-01-01 00:00:00',
                    'updated_at' => '2026-01-15 00:00:00',
                ],
                [
                    'id' => 2,
                    'program_id' => 1,
                    'program_name' => 'Rice Trial 2026',
                    'name' => 'Planting',
                    'status' => 'ACTIVE',
                    'start_date' => '2026-01-16',
                    'end_date' => '2026-02-01',
                    'budget' => 15000000,
                    'spent' => 9000000,
                    'progress' => 60,
                    'created_at' => '2026-01-16 00:00:00',
                    'updated_at' => '2026-01-27 00:00:00',
                ],
            ],
            'revenueBreakdown' => [
                'harvest' => [
                    'amount' => 35000000,
                    'items' => [
                        [
                            'id' => 1,
                            'harvest_number' => 'HRV-2026-001',
                            'program_id' => 2,
                            'program_name' => 'Chili Harvest Cycle',
                            'date' => '2026-01-20',
                            'crop_type' => 'Chili',
                            'quantity_kg' => 200,
                            'price_per_kg' => 125000,
                            'total_revenue' => 25000000,
                            'buyer_name' => 'PT Sayur Segar',
                            'status' => 'POSTED',
                            'created_at' => '2026-01-20 00:00:00',
                        ],
                        [
                            'id' => 2,
                            'harvest_number' => 'HRV-2026-002',
                            'program_id' => 1,
                            'program_name' => 'Rice Trial 2026',
                            'date' => '2026-01-22',
                            'crop_type' => 'Rice',
                            'quantity_kg' => 500,
                            'price_per_kg' => 20000,
                            'total_revenue' => 10000000,
                            'buyer_name' => 'CV Beras Sejahtera',
                            'status' => 'POSTED',
                            'created_at' => '2026-01-22 00:00:00',
                        ],
                    ],
                ],
                'testing' => [
                    'amount' => 10000000,
                    'items' => [
                        [
                            'id' => 1,
                            'service_number' => 'TS-2026-001',
                            'program_id' => 3,
                            'program_name' => 'Testing Service Program',
                            'date' => '2026-01-18',
                            'service_type' => 'Soil Analysis',
                            'total_revenue' => 6000000,
                            'client_name' => 'PT Agri Indonesia',
                            'status' => 'POSTED',
                            'created_at' => '2026-01-18 00:00:00',
                        ],
                        [
                            'id' => 2,
                            'service_number' => 'TS-2026-002',
                            'program_id' => 3,
                            'program_name' => 'Testing Service Program',
                            'date' => '2026-01-25',
                            'service_type' => 'Product Testing',
                            'total_revenue' => 4000000,
                            'client_name' => 'CV Pertanian Maju',
                            'status' => 'POSTED',
                            'created_at' => '2026-01-25 00:00:00',
                        ],
                    ],
                ],
            ],
            'budgetAlerts' => [
                [
                    'id' => 1,
                    'severity' => 'error',
                    'title' => 'Chili Cycle: Budget 95% utilized',
                    'description' => 'Consider requesting budget increase',
                    'deadline' => null,
                ],
                [
                    'id' => 2,
                    'severity' => 'warning',
                    'title' => 'Corn Study: Low utilization (34%)',
                    'description' => 'Review activity schedule and accelerate',
                    'deadline' => null,
                ],
            ],
        ];
    }

    /**
     * Get Manager Dashboard Data
     *
     * @param  string  $scope  'current' or 'all'
     * @param  \App\Models\Site  $site  Current site context
     */
    public static function getManagerData(string $scope = 'current', $site = null): array
    {
        // Managers can view 'current' site or 'all' sites
        if ($scope === 'current' && $site) {
            return self::getManagerDataForSite($site);
        }

        // Default: consolidated view across all sites
        return [
            'kpis' => [
                [
                    'title' => 'Active Programs',
                    'value' => '15 Total',
                    'subtitle' => 'klaten: 8 | magelang: 7',
                ],
                [
                    'title' => 'Pending Approvals',
                    'value' => '12 Total',
                    'subtitle' => '8 Payments | 4 Programs',
                    'alert' => 'warning',
                ],
                [
                    'title' => 'Budget Utilization',
                    'value' => '1.8B / 3B (60%)',
                    'subtitle' => 'Available: 1.2B',
                ],
                [
                    'title' => 'Net Income (YTD)',
                    'value' => 150000000,
                    'subtitle' => 'klaten: +80M | magelang: +70M',
                    'trend' => [
                        'direction' => 'up',
                        'value' => '+18% YoY',
                        'positive' => true,
                    ],
                ],
                [
                    'title' => 'Settlement Compliance',
                    'value' => '87%',
                    'subtitle' => '3 Overdue | 5 Pending',
                    'alert' => 'warning',
                ],
                [
                    'title' => 'System Status',
                    'value' => 'All Systems Normal',
                    'subtitle' => '18 Active Users',
                ],
            ],
            'approvalQueue' => [
                [
                    'id' => 1,
                    'type' => 'Payment Request',
                    'reference' => 'PR-001',
                    'amount' => 8500000,
                    'requestor' => 'Budi (RO)',
                    'age' => '36 hours',
                    'urgency' => 'high',
                    'details' => 'Fertilizer - 3 Programs',
                ],
                [
                    'id' => 2,
                    'type' => 'Payment Request',
                    'reference' => 'PR-008',
                    'amount' => 3200000,
                    'requestor' => 'Siti (RO)',
                    'age' => '12 hours',
                    'urgency' => 'medium',
                    'details' => 'Pesticide - Rice Trial',
                ],
                [
                    'id' => 3,
                    'type' => 'Program',
                    'reference' => 'Rice V2',
                    'amount' => 50000000,
                    'requestor' => 'Ahmad (RA)',
                    'age' => '2 hours',
                    'urgency' => 'low',
                    'details' => 'Budget Approval',
                ],
            ],
            'siteMetrics' => [
                'klaten' => [
                    'site_code' => 'klaten',
                    'site_name' => 'Klaten',
                    'activePrograms' => 8,
                    'budgetAllocated' => 1500000000,
                    'budgetUtilized' => 900000000,
                    'budgetUtilizationPercent' => 60,
                    'revenueYTD' => 450000000,
                    'expensesYTD' => 370000000,
                    'netIncome' => 80000000,
                    'avgProgramROI' => 21,
                ],
                'magelang' => [
                    'site_code' => 'magelang',
                    'site_name' => 'Magelang',
                    'activePrograms' => 7,
                    'budgetAllocated' => 1500000000,
                    'budgetUtilized' => 950000000,
                    'budgetUtilizationPercent' => 63,
                    'revenueYTD' => 380000000,
                    'expensesYTD' => 310000000,
                    'netIncome' => 70000000,
                    'avgProgramROI' => 23,
                ],
            ],
            'programsByStatus' => [
                'draft' => 3,
                'active' => 15,
                'completed' => 2,
                'archived' => 0,
            ],
            'budgetByCategory' => [
                [
                    'category' => 'Fertilizer',
                    'allocated' => 450000000,
                    'spent' => 380000000,
                    'percentage' => 84,
                    'status' => 'warning',
                ],
                [
                    'category' => 'Seeds',
                    'allocated' => 280000000,
                    'spent' => 150000000,
                    'percentage' => 54,
                    'status' => 'good',
                ],
                [
                    'category' => 'Labor',
                    'allocated' => 520000000,
                    'spent' => 390000000,
                    'percentage' => 75,
                    'status' => 'good',
                ],
                [
                    'category' => 'Equipment',
                    'allocated' => 180000000,
                    'spent' => 165000000,
                    'percentage' => 92,
                    'status' => 'critical',
                ],
                [
                    'category' => 'Testing Services',
                    'allocated' => 200000000,
                    'spent' => 85000000,
                    'percentage' => 43,
                    'status' => 'good',
                ],
            ],
            'revenueTrends' => [
                ['date' => '2025-08', 'value' => 60000000],
                ['date' => '2025-09', 'value' => 75000000],
                ['date' => '2025-10', 'value' => 90000000],
                ['date' => '2025-11', 'value' => 70000000],
                ['date' => '2025-12', 'value' => 85000000],
                ['date' => '2026-01', 'value' => 95000000],
            ],
            'teamPerformance' => [
                'researchAssociates' => [
                    [
                        'id' => 1,
                        'name' => 'Ahmad',
                        'role' => 'RA',
                        'program' => '3 programs (klaten)',
                        'pendingSettlements' => 0,
                        'overdueSettlements' => 0,
                        'lastActivity' => 'Budget 95% util',
                        'status' => 'warning',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Dewi',
                        'role' => 'RA',
                        'program' => '2 programs (magelang)',
                        'pendingSettlements' => 0,
                        'overdueSettlements' => 0,
                        'lastActivity' => 'Budget 68% util',
                        'status' => 'active',
                    ],
                ],
                'researchOfficers' => [
                    'settlementCompliance' => 87,
                    'avgApprovalTime' => 4.2,
                    'activeToday' => 8,
                    'total' => 10,
                ],
            ],
        ];
    }

    /**
     * Get AVP Dashboard Data
     *
     * @param  string  $scope  'current' or 'all'
     * @param  \App\Models\Site  $site  Current site context
     */
    public static function getAvpData(string $scope = 'current', $site = null): array
    {
        // AVP can view 'current' site or 'all' sites
        if ($scope === 'current' && $site) {
            return self::getAvpDataForSite($site);
        }

        // Default: consolidated view across all sites
        return [
            'kpis' => [
                [
                    'title' => 'Budget Position',
                    'value' => '1.85B / 3B (62%)',
                    'trend' => [
                        'direction' => 'down',
                        'value' => '-2% vs target',
                        'positive' => false,
                    ],
                ],
                [
                    'title' => 'Net Profit (YTD)',
                    'value' => 150000000,
                    'trend' => [
                        'direction' => 'up',
                        'value' => '+18% YoY',
                        'positive' => true,
                    ],
                ],
                [
                    'title' => 'Program Portfolio',
                    'value' => '12 Profitable | 3 Loss',
                    'subtitle' => 'ROI: +12%',
                ],
                [
                    'title' => 'Awaiting AVP Approval',
                    'value' => '2 Budget | 1 Completion',
                    'subtitle' => 'Total Value: Rp 120M',
                ],
                [
                    'title' => 'Compliance Score',
                    'value' => '91%',
                    'subtitle' => 'Target: 95%',
                    'alert' => 'warning',
                ],
                [
                    'title' => 'Revenue Growth',
                    'value' => '+24% YoY',
                    'subtitle' => 'Harvest: +18% | Test: +35%',
                    'trend' => [
                        'direction' => 'up',
                        'value' => '+24%',
                        'positive' => true,
                    ],
                ],
            ],
            'executiveSummary' => [
                'totalRevenue' => 830000000,
                'totalExpenses' => 680000000,
                'netProfit' => 150000000,
                'profitMargin' => 22,
                'activePrograms' => 15,
                'profitablePrograms' => 12,
                'lossMakingPrograms' => 3,
                'avgProgramROI' => 12,
                'settlementCompliance' => 87,
                'avgApprovalTime' => 4.2,
                'budgetUtilization' => 62,
            ],
            'strategicPriorities' => [
                'budgetApprovals' => [
                    [
                        'id' => 1,
                        'type' => 'Program',
                        'reference' => 'Rice Variety 2.0',
                        'amount' => 50000000,
                        'requestor' => 'Ahmad (RA)',
                        'age' => '3 days',
                        'urgency' => 'medium',
                    ],
                    [
                        'id' => 2,
                        'type' => 'Program',
                        'reference' => 'Testing Lab Expansion',
                        'amount' => 70000000,
                        'requestor' => 'Dewi (RA)',
                        'age' => '1 day',
                        'urgency' => 'high',
                    ],
                ],
                'programCompletions' => [
                    [
                        'id' => 1,
                        'name' => 'Corn Trial 2025',
                        'status' => 'COMPLETED',
                        'site_code' => 'klaten',
                        'budget' => 40000000,
                        'spent' => 38000000,
                        'revenue' => 42000000,
                        'netIncome' => 4000000,
                        'budgetUtilization' => 95,
                        'created_at' => '2025-06-01 00:00:00',
                        'updated_at' => '2026-01-15 00:00:00',
                    ],
                ],
                'lossMakingPrograms' => [
                    [
                        'id' => 1,
                        'name' => 'Experimental Crop A',
                        'status' => 'ACTIVE',
                        'site_code' => 'magelang',
                        'budget' => 30000000,
                        'spent' => 28000000,
                        'revenue' => 15000000,
                        'netIncome' => -13000000,
                        'budgetUtilization' => 93,
                        'created_at' => '2025-10-01 00:00:00',
                        'updated_at' => '2026-01-27 00:00:00',
                    ],
                ],
            ],
            'sitePerformance' => self::getManagerData()['siteMetrics'],
            'portfolioAnalysis' => [
                'stars' => array_slice(
                    self::getManagerData()['siteMetrics']['klaten'],
                    0,
                    5,
                ),
                'questions' => [],
                'dogs' => [
                    'id' => 1,
                    'name' => 'Experimental Crop A',
                    'status' => 'ACTIVE',
                    'site_code' => 'magelang',
                    'budget' => 30000000,
                    'spent' => 28000000,
                    'revenue' => 15000000,
                    'netIncome' => -13000000,
                    'budgetUtilization' => 93,
                    'created_at' => '2025-10-01 00:00:00',
                    'updated_at' => '2026-01-27 00:00:00',
                ],
            ],
            'financialForecast' => [
                'revenueProjection' => 1200000000,
                'expenseProjection' => 980000000,
                'netProfitProjection' => 220000000,
                'confidenceLevel' => 85,
            ],
        ];
    }

    /**
     * Get Finance Operation Dashboard Data
     *
     * @param  string  $scope  'current' or 'all'
     * @param  \App\Models\Site  $site  Current site context
     */
    public static function getFinanceOpsData(string $scope = 'current', $site = null): array
    {
        // Finance Operation always sees all sites (cross-site role)
        return [
            'kpis' => [
                [
                    'title' => 'Payments to Process',
                    'value' => 'Morning: 8 | Afternoon: 5',
                    'subtitle' => 'Total: Rp 45M',
                ],
                [
                    'title' => 'Settlements to Review',
                    'value' => '12 Pending',
                    'subtitle' => '3 Revisions Requested',
                ],
                [
                    'title' => 'Processed Today',
                    'value' => '15 Payments',
                    'subtitle' => 'Rp 67,500,000',
                ],
                [
                    'title' => 'Settlement Compliance',
                    'value' => '87%',
                    'subtitle' => '3 Overdue | 8 Pending',
                    'alert' => 'warning',
                ],
                [
                    'title' => 'Revenue Verification',
                    'value' => '5 Pending',
                    'subtitle' => '2 Corrections Needed',
                ],
                [
                    'title' => 'COA Usage',
                    'value' => '45 Active',
                    'subtitle' => '3 Pending Review',
                ],
            ],
            'paymentQueue' => [
                'morning' => [
                    [
                        'id' => 1,
                        'request_number' => 'PR-2026-001',
                        'purpose' => 'Fertilizer',
                        'status' => 'APPROVED',
                        'requested_amount' => 8500000,
                        'approved_amount' => 8500000,
                        'created_by' => ['id' => 1, 'name' => 'Budi'],
                        'created_at' => '2026-01-25 09:00:00',
                        'updated_at' => '2026-01-26 10:00:00',
                    ],
                    [
                        'id' => 2,
                        'request_number' => 'PR-2026-003',
                        'purpose' => 'Seeds',
                        'status' => 'APPROVED',
                        'requested_amount' => 3200000,
                        'approved_amount' => 3200000,
                        'created_by' => ['id' => 2, 'name' => 'Siti'],
                        'created_at' => '2026-01-25 10:00:00',
                        'updated_at' => '2026-01-26 11:00:00',
                    ],
                ],
                'afternoon' => [
                    [
                        'id' => 3,
                        'request_number' => 'PR-2026-007',
                        'purpose' => 'Pesticide',
                        'status' => 'APPROVED',
                        'requested_amount' => 4200000,
                        'approved_amount' => 4200000,
                        'created_by' => ['id' => 3, 'name' => 'Andi'],
                        'created_at' => '2026-01-26 09:00:00',
                        'updated_at' => '2026-01-27 09:00:00',
                    ],
                ],
            ],
            'settlementReview' => self::getRoData()['settlementAlerts'],
            'revenueVerification' => [
                [
                    'id' => 1,
                    'harvest_number' => 'HRV-2026-045',
                    'program_id' => 2,
                    'program_name' => 'Chili Harvest Cycle',
                    'date' => '2026-01-25',
                    'crop_type' => 'Chili',
                    'quantity_kg' => 250,
                    'price_per_kg' => 20000,
                    'total_revenue' => 5000000,
                    'buyer_name' => 'PT Sayur Segar',
                    'status' => 'POSTED',
                    'created_at' => '2026-01-25 00:00:00',
                ],
            ],
            'batchSummary' => [
                'morning' => [
                    'processed' => 12,
                    'totalAmount' => 48500000,
                    'bankReference' => 'TRF-20260127-001',
                    'status' => 'Complete',
                ],
                'afternoon' => [
                    'scheduled' => 8,
                    'totalAmount' => 32000000,
                    'status' => 'Awaiting batch time',
                ],
            ],
            'coaStats' => [
                'totalActive' => 45,
                'klatenAccounts' => 23,
                'magelangAccounts' => 22,
                'pendingReview' => 3,
            ],
        ];
    }

    /**
     * Get Farm Admin Dashboard Data
     *
     * @param  string  $scope  'current' or 'all'
     * @param  \App\Models\Site  $site  Current site context
     */
    public static function getFarmAdminData(string $scope = 'current', $site = null): array
    {
        // Farm Admin sees all sites (cross-site role)
        return [
            'kpis' => [
                [
                    'title' => 'Pending Review',
                    'value' => '8 Settlements',
                    'subtitle' => 'klaten: 5 | magelang: 3',
                ],
                [
                    'title' => 'Uploaded Today',
                    'value' => '12 Documents',
                    'subtitle' => 'All sites',
                ],
                [
                    'title' => 'Issues Flagged',
                    'value' => '3 Total',
                    'subtitle' => '2 klaten | 1 magelang',
                ],
                [
                    'title' => 'Compliance Today',
                    'value' => '92%',
                    'subtitle' => 'On Track',
                ],
            ],
            'documentationQueue' => [
                'klaten' => array_slice(
                    self::getRoData()['settlementAlerts'],
                    0,
                    5,
                ),
                'magelang' => array_slice(
                    self::getRoData()['settlementAlerts'],
                    0,
                    3,
                ),
            ],
            'documentChecklist' => [
                'receiptUploaded' => true,
                'receiptLegible' => true,
                'amountMatches' => true,
                'vendorInvoice' => true,
                'additionalDocs' => false,
                'photoQuality' => true,
            ],
            'siteActivity' => [
                'klaten' => [
                    'activeROs' => 5,
                    'settlementsDueToday' => 3,
                    'documentationComplete' => 4,
                    'total' => 5,
                    'lastUpload' => '15 minutes ago',
                ],
                'magelang' => [
                    'activeROs' => 5,
                    'settlementsDueToday' => 2,
                    'documentationComplete' => 2,
                    'total' => 2,
                    'lastUpload' => '2 hours ago',
                ],
            ],
            'issues' => [
                [
                    'id' => 1,
                    'date' => '2026-01-27 11:30',
                    'reference' => 'PR-2026-003',
                    'site' => 'Klaten',
                    'issue' => 'Receipt unclear',
                    'action' => 'Sent message to Budi (RO)',
                    'status' => 'Awaiting response',
                ],
                [
                    'id' => 2,
                    'date' => '2026-01-27 10:15',
                    'reference' => 'PR-2026-007',
                    'site' => 'Magelang',
                    'issue' => 'Missing vendor invoice',
                    'action' => 'Called Siti (RO)',
                    'status' => 'Resolved',
                ],
            ],
            'recentUploads' => [
                [
                    'time' => '13:45',
                    'site' => 'klaten',
                    'ro' => 'Budi',
                    'reference' => 'PR-001',
                    'status' => 'Complete',
                ],
                [
                    'time' => '13:20',
                    'site' => 'magelang',
                    'ro' => 'Siti',
                    'reference' => 'PR-005',
                    'status' => 'Complete',
                ],
                [
                    'time' => '12:50',
                    'site' => 'klaten',
                    'ro' => 'Andi',
                    'reference' => 'PR-008',
                    'status' => 'Review',
                ],
            ],
        ];
    }

    /**
     * Get Manager Dashboard Data for specific site
     */
    private static function getManagerDataForSite($site): array
    {
        $siteCode = $site->site_code;
        $siteName = $site->site_name;

        // Site-specific metrics (example: Klaten)
        $isKlaten = strtolower($siteCode) === 'klt' || strtolower($siteCode) === 'klaten';

        return [
            'kpis' => [
                [
                    'title' => 'Active Programs',
                    'value' => $isKlaten ? '8 Total' : '7 Total',
                    'subtitle' => "Site: {$siteName}",
                ],
                [
                    'title' => 'Pending Approvals',
                    'value' => $isKlaten ? '7 Total' : '5 Total',
                    'subtitle' => '5 Payments | 2 Programs',
                    'alert' => 'warning',
                ],
                [
                    'title' => 'Budget Utilization',
                    'value' => $isKlaten ? '900M / 1.5B (60%)' : '950M / 1.5B (63%)',
                    'subtitle' => $isKlaten ? 'Available: 600M' : 'Available: 550M',
                ],
                [
                    'title' => 'Net Income (YTD)',
                    'value' => $isKlaten ? 80000000 : 70000000,
                    'subtitle' => "Site: {$siteName}",
                    'trend' => [
                        'direction' => 'up',
                        'value' => $isKlaten ? '+20% YoY' : '+16% YoY',
                        'positive' => true,
                    ],
                ],
                [
                    'title' => 'Settlement Compliance',
                    'value' => $isKlaten ? '90%' : '84%',
                    'subtitle' => $isKlaten ? '1 Overdue | 3 Pending' : '2 Overdue | 2 Pending',
                    'alert' => $isKlaten ? null : 'warning',
                ],
                [
                    'title' => 'System Status',
                    'value' => 'Site Operational',
                    'subtitle' => $isKlaten ? '10 Active Users' : '8 Active Users',
                ],
            ],
            'approvalQueue' => [
                [
                    'id' => 1,
                    'type' => 'Payment Request',
                    'reference' => 'PR-001',
                    'amount' => 8500000,
                    'requestor' => 'Budi (RO)',
                    'age' => '36 hours',
                    'urgency' => 'high',
                    'details' => 'Fertilizer - 2 Programs',
                ],
                [
                    'id' => 2,
                    'type' => 'Payment Request',
                    'reference' => 'PR-008',
                    'amount' => 3200000,
                    'requestor' => 'Siti (RO)',
                    'age' => '12 hours',
                    'urgency' => 'medium',
                    'details' => 'Pesticide - Rice Trial',
                ],
            ],
            'siteMetrics' => [
                $siteCode => [
                    'site_code' => $siteCode,
                    'site_name' => $siteName,
                    'activePrograms' => $isKlaten ? 8 : 7,
                    'budgetAllocated' => 1500000000,
                    'budgetUtilized' => $isKlaten ? 900000000 : 950000000,
                    'budgetUtilizationPercent' => $isKlaten ? 60 : 63,
                    'revenueYTD' => $isKlaten ? 450000000 : 380000000,
                    'expensesYTD' => $isKlaten ? 370000000 : 310000000,
                    'netIncome' => $isKlaten ? 80000000 : 70000000,
                    'avgProgramROI' => $isKlaten ? 21 : 23,
                ],
            ],
            'programsByStatus' => [
                'draft' => $isKlaten ? 2 : 1,
                'active' => $isKlaten ? 8 : 7,
                'completed' => $isKlaten ? 1 : 1,
                'archived' => 0,
            ],
            'budgetByCategory' => [
                [
                    'category' => 'Fertilizer',
                    'allocated' => $isKlaten ? 250000000 : 200000000,
                    'spent' => $isKlaten ? 210000000 : 170000000,
                    'percentage' => 84,
                    'status' => 'warning',
                ],
                [
                    'category' => 'Seeds',
                    'allocated' => $isKlaten ? 150000000 : 130000000,
                    'spent' => $isKlaten ? 80000000 : 70000000,
                    'percentage' => 53,
                    'status' => 'good',
                ],
                [
                    'category' => 'Labor',
                    'allocated' => $isKlaten ? 280000000 : 240000000,
                    'spent' => $isKlaten ? 210000000 : 180000000,
                    'percentage' => 75,
                    'status' => 'good',
                ],
            ],
            'revenueTrends' => [
                ['date' => '2025-08', 'value' => $isKlaten ? 35000000 : 25000000],
                ['date' => '2025-09', 'value' => $isKlaten ? 42000000 : 33000000],
                ['date' => '2025-10', 'value' => $isKlaten ? 50000000 : 40000000],
                ['date' => '2025-11', 'value' => $isKlaten ? 38000000 : 32000000],
                ['date' => '2025-12', 'value' => $isKlaten ? 45000000 : 40000000],
                ['date' => '2026-01', 'value' => $isKlaten ? 52000000 : 43000000],
            ],
            'teamPerformance' => [
                'researchAssociates' => [
                    [
                        'id' => 1,
                        'name' => $isKlaten ? 'Ahmad' : 'Dewi',
                        'role' => 'RA',
                        'program' => $isKlaten ? '3 programs' : '2 programs',
                        'pendingSettlements' => 0,
                        'overdueSettlements' => 0,
                        'lastActivity' => 'Budget 75% util',
                        'status' => 'active',
                    ],
                ],
                'researchOfficers' => [
                    'settlementCompliance' => $isKlaten ? 90 : 84,
                    'avgApprovalTime' => 4.2,
                    'activeToday' => $isKlaten ? 5 : 4,
                    'total' => $isKlaten ? 5 : 5,
                ],
            ],
        ];
    }

    /**
     * Get AVP Dashboard Data for specific site
     */
    private static function getAvpDataForSite($site): array
    {
        $siteCode = $site->site_code;
        $siteName = $site->site_name;

        $isKlaten = strtolower($siteCode) === 'klt' || strtolower($siteCode) === 'klaten';

        return [
            'kpis' => [
                [
                    'title' => 'Budget Position',
                    'value' => $isKlaten ? '900M / 1.5B (60%)' : '950M / 1.5B (63%)',
                    'trend' => [
                        'direction' => $isKlaten ? 'up' : 'down',
                        'value' => $isKlaten ? '+3% vs target' : '-1% vs target',
                        'positive' => $isKlaten,
                    ],
                ],
                [
                    'title' => 'Net Profit (YTD)',
                    'value' => $isKlaten ? 80000000 : 70000000,
                    'trend' => [
                        'direction' => 'up',
                        'value' => $isKlaten ? '+20% YoY' : '+16% YoY',
                        'positive' => true,
                    ],
                ],
                [
                    'title' => 'Program Portfolio',
                    'value' => $isKlaten ? '6 Profitable | 2 Loss' : '6 Profitable | 1 Loss',
                    'subtitle' => $isKlaten ? 'ROI: +10%' : 'ROI: +14%',
                ],
                [
                    'title' => 'Awaiting AVP Approval',
                    'value' => $isKlaten ? '1 Budget | 1 Completion' : '1 Budget | 0 Completion',
                    'subtitle' => $isKlaten ? 'Total: Rp 70M' : 'Total: Rp 50M',
                ],
                [
                    'title' => 'Compliance Score',
                    'value' => $isKlaten ? '92%' : '90%',
                    'subtitle' => 'Target: 95%',
                    'alert' => 'warning',
                ],
                [
                    'title' => 'Revenue Growth',
                    'value' => $isKlaten ? '+22% YoY' : '+26% YoY',
                    'subtitle' => 'Strong performance',
                    'trend' => [
                        'direction' => 'up',
                        'value' => $isKlaten ? '+22%' : '+26%',
                        'positive' => true,
                    ],
                ],
            ],
            'executiveSummary' => [
                'totalRevenue' => $isKlaten ? 450000000 : 380000000,
                'totalExpenses' => $isKlaten ? 370000000 : 310000000,
                'netProfit' => $isKlaten ? 80000000 : 70000000,
                'profitMargin' => $isKlaten ? 18 : 18,
                'activePrograms' => $isKlaten ? 8 : 7,
                'profitablePrograms' => $isKlaten ? 6 : 6,
                'lossMakingPrograms' => $isKlaten ? 2 : 1,
                'avgProgramROI' => $isKlaten ? 10 : 14,
                'settlementCompliance' => $isKlaten ? 90 : 84,
                'avgApprovalTime' => 4.2,
                'budgetUtilization' => $isKlaten ? 60 : 63,
            ],
            'strategicPriorities' => [
                'budgetApprovals' => [
                    [
                        'id' => 1,
                        'type' => 'Program',
                        'reference' => $isKlaten ? 'Rice Variety 2.0' : 'Corn Expansion',
                        'amount' => $isKlaten ? 50000000 : 45000000,
                        'requestor' => $isKlaten ? 'Ahmad (RA)' : 'Dewi (RA)',
                        'age' => '3 days',
                        'urgency' => 'medium',
                    ],
                ],
                'programCompletions' => [
                    [
                        'id' => 1,
                        'name' => $isKlaten ? 'Rice Trial 2025' : 'Corn Trial 2025',
                        'status' => 'COMPLETED',
                        'site_code' => $siteCode,
                        'budget' => 40000000,
                        'spent' => 38000000,
                        'revenue' => 42000000,
                        'netIncome' => 4000000,
                        'budgetUtilization' => 95,
                        'created_at' => '2025-06-01 00:00:00',
                        'updated_at' => '2026-01-15 00:00:00',
                    ],
                ],
                'lossMakingPrograms' => $isKlaten ? [
                    [
                        'id' => 1,
                        'name' => 'Experimental Crop A',
                        'status' => 'ACTIVE',
                        'site_code' => $siteCode,
                        'budget' => 25000000,
                        'spent' => 23000000,
                        'revenue' => 12000000,
                        'netIncome' => -11000000,
                        'budgetUtilization' => 92,
                        'created_at' => '2025-10-01 00:00:00',
                        'updated_at' => '2026-01-27 00:00:00',
                    ],
                ] : [],
            ],
            'sitePerformance' => [
                $siteCode => [
                    'site_code' => $siteCode,
                    'site_name' => $siteName,
                    'activePrograms' => $isKlaten ? 8 : 7,
                    'budgetAllocated' => 1500000000,
                    'budgetUtilized' => $isKlaten ? 900000000 : 950000000,
                    'budgetUtilizationPercent' => $isKlaten ? 60 : 63,
                    'revenueYTD' => $isKlaten ? 450000000 : 380000000,
                    'expensesYTD' => $isKlaten ? 370000000 : 310000000,
                    'netIncome' => $isKlaten ? 80000000 : 70000000,
                    'avgProgramROI' => $isKlaten ? 21 : 23,
                ],
            ],
            'portfolioAnalysis' => [
                'stars' => [],
                'questions' => [],
                'dogs' => $isKlaten ? [
                    'id' => 1,
                    'name' => 'Experimental Crop A',
                    'status' => 'ACTIVE',
                    'site_code' => $siteCode,
                    'budget' => 25000000,
                    'spent' => 23000000,
                    'revenue' => 12000000,
                    'netIncome' => -11000000,
                    'budgetUtilization' => 92,
                    'created_at' => '2025-10-01 00:00:00',
                    'updated_at' => '2026-01-27 00:00:00',
                ] : [],
            ],
            'financialForecast' => [
                'revenueProjection' => $isKlaten ? 650000000 : 550000000,
                'expenseProjection' => $isKlaten ? 530000000 : 460000000,
                'netProfitProjection' => $isKlaten ? 120000000 : 90000000,
                'confidenceLevel' => 85,
            ],
        ];
    }
}
