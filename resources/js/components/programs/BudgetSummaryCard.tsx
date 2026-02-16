import { Card, CardContent, CardHeader } from '@/components/ui/card';
import type { Activity, BudgetItem } from '@/pages/programs/utils/budgetCalculations';
import { getBudgetByActivity } from '@/pages/programs/utils/budgetCalculations';

interface BudgetSummaryCardProps {
    budgetItems: BudgetItem[];
    activities: Activity[];
    totalBudget: number;
}

export function BudgetSummaryCard({
    budgetItems,
    activities,
    totalBudget,
}: BudgetSummaryCardProps) {
    if (budgetItems.length === 0) {
        return null;
    }

    const activityBudgets = getBudgetByActivity(budgetItems, activities);

    // Separate assigned and unassigned budgets
    const assignedBudgets = activityBudgets.filter(
        (item) => item.activityIndex !== 'unassigned',
    );
    const unassignedBudget = activityBudgets.find(
        (item) => item.activityIndex === 'unassigned',
    );

    return (
        <Card>
            <CardHeader>
                <h4 className="font-medium">Budget Summary</h4>
            </CardHeader>
            <CardContent className="space-y-3">
                {/* Per-Activity Budgets */}
                {assignedBudgets.map((item) => (
                    <div
                        key={item.activityIndex}
                        className="flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-950/20"
                    >
                        <span className="font-medium">{item.activityName}</span>
                        <span className="font-semibold text-blue-700 dark:text-blue-400">
                            Rp {item.total.toLocaleString('id-ID')}
                        </span>
                    </div>
                ))}

                {/* Unassigned Total */}
                {unassignedBudget && unassignedBudget.total > 0 && (
                    <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                        <span className="font-medium text-muted-foreground">
                            {unassignedBudget.activityName}
                        </span>
                        <span className="font-semibold">
                            Rp {unassignedBudget.total.toLocaleString('id-ID')}
                        </span>
                    </div>
                )}

                {/* Total Program Budget */}
                <div className="flex items-center justify-between rounded-lg bg-primary p-4 text-primary-foreground">
                    <span className="text-lg font-bold">Total Program Budget</span>
                    <span className="text-2xl font-bold">
                        Rp {Number(totalBudget).toLocaleString('id-ID')}
                    </span>
                </div>
            </CardContent>
        </Card>
    );
}
