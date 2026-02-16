/**
 * BoQManager - Budget Items (Bill of Quantities) Management
 *
 * This component has been refactored to use TanStack React Table
 * for better data density, column visibility controls, and performance
 * with large datasets.
 */

import type { UseFormReturn } from '@inertiajs/react';
import { BudgetItemsTable } from '@/components/programs/BudgetItemsTable';
import { BudgetSummaryCard } from '@/components/programs/BudgetSummaryCard';
import type { Activity, BudgetItem } from '@/pages/programs/utils/budgetCalculations';

interface CoaAccount {
    id: number;
    fiscal_year_id: number;
    account_code: string;
    account_name: string;
    short_description: string | null;
}

interface Props {
    form: UseFormReturn<any>;
    categories: any[];
    phases: any[];
    coaAccounts: CoaAccount[];
    activities: Activity[];
}

export default function BoQManager({
    form,
    categories,
    phases,
    coaAccounts,
    activities,
}: Props) {
    const { data } = form;
    const items = (data.budget_items as BudgetItem[]) || [];

    return (
        <div className="space-y-6">
            <BudgetItemsTable
                form={form}
                categories={categories}
                phases={phases}
                coaAccounts={coaAccounts}
                activities={activities}
            />

            <BudgetSummaryCard
                budgetItems={items}
                activities={activities}
                totalBudget={data.total_budget || 0}
            />
        </div>
    );
}
