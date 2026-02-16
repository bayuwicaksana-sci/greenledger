/**
 * Budget calculation utilities for Program forms
 */

export interface BudgetItem {
    activity_id?: string;
    category_id: string;
    phase_id: string;
    coa_account_id?: string;
    item_description: string;
    specification?: string;
    unit: string;
    qty: string | number;
    unit_price: string | number;
    days?: string | number;
    estimated_realization_date?: string | null;
    subtotal: number;
    notes?: string;
}

export interface Activity {
    activity_name: string;
    description?: string;
    planned_start_date?: string;
    planned_end_date?: string;
}

/**
 * Calculate subtotal for a budget item (qty × unit_price)
 */
export function calculateSubtotal(item: BudgetItem): number {
    const qty = Number(item.qty) || 0;
    const unitPrice = Number(item.unit_price) || 0;
    return qty * unitPrice;
}

/**
 * Calculate total budget from all budget items
 */
export function calculateTotalBudget(items: BudgetItem[]): number {
    return items.reduce((sum, item) => sum + (item.subtotal || 0), 0);
}

/**
 * Calculate estimated realization date from start date + days
 */
export function calculateEstimatedDate(
    startDate: string | null,
    days: number | string | null,
): string | null {
    if (!startDate || !days || Number(days) <= 0) {
        return null;
    }

    const date = new Date(startDate);
    date.setDate(date.getDate() + Number(days));
    return date.toISOString().split('T')[0];
}

/**
 * Get budget breakdown by activity
 */
export function getBudgetByActivity(
    items: BudgetItem[],
    activities: Activity[],
): { activityName: string; activityIndex: string; total: number }[] {
    const byActivity: Record<string, number> = {};

    items.forEach((item) => {
        const activityId = item.activity_id || 'unassigned';
        byActivity[activityId] = (byActivity[activityId] || 0) + (item.subtotal || 0);
    });

    return Object.entries(byActivity).map(([id, total]) => {
        if (id === 'unassigned') {
            return {
                activityName: 'Unassigned (General/Overhead)',
                activityIndex: id,
                total,
            };
        }

        const activityIndex = Number(id);
        return {
            activityName: activities[activityIndex]?.activity_name || 'Unknown',
            activityIndex: id,
            total,
        };
    });
}
