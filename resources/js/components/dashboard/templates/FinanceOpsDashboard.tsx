import { Banknote, FileCheck } from 'lucide-react';
import type { FinanceOpsDashboardData } from '@/types/dashboard';
import { MetricValue } from '../atoms/MetricValue';
import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { SettlementAlertsWidget } from '../organisms/SettlementAlertsWidget';
import { Widget } from '../organisms/Widget';

interface FinanceOpsDashboardProps {
    data: FinanceOpsDashboardData;
}

export function FinanceOpsDashboard({ data }: FinanceOpsDashboardProps) {
    return (
        <div className="flex flex-col gap-6 p-6">
            <QuickStatsRow kpis={data.kpis} />

            <div className="grid gap-6 md:grid-cols-2">
                <Widget
                    title="Payment Processing Queue"
                    icon={<Banknote className="size-5" />}
                    fullWidth
                >
                    <div className="space-y-4">
                        <div>
                            <div className="mb-3 font-medium">
                                Morning Batch (10:00 AM) -{' '}
                                {data.paymentQueue.morning.length} requests
                            </div>
                            <div className="space-y-2">
                                {data.paymentQueue.morning
                                    .slice(0, 3)
                                    .map((payment) => (
                                        <div
                                            key={payment.id}
                                            className="flex justify-between rounded border border-border p-2"
                                        >
                                            <span className="text-sm">
                                                {payment.request_number} |{' '}
                                                {payment.purpose}
                                            </span>
                                            <MetricValue
                                                value={payment.requested_amount}
                                                format="currency"
                                                className="text-sm"
                                            />
                                        </div>
                                    ))}
                            </div>
                        </div>
                        <div>
                            <div className="mb-3 font-medium">
                                Afternoon Batch (2:00 PM) -{' '}
                                {data.paymentQueue.afternoon.length} requests
                            </div>
                            <div className="space-y-2">
                                {data.paymentQueue.afternoon
                                    .slice(0, 3)
                                    .map((payment) => (
                                        <div
                                            key={payment.id}
                                            className="flex justify-between rounded border border-border p-2"
                                        >
                                            <span className="text-sm">
                                                {payment.request_number} |{' '}
                                                {payment.purpose}
                                            </span>
                                            <MetricValue
                                                value={payment.requested_amount}
                                                format="currency"
                                                className="text-sm"
                                            />
                                        </div>
                                    ))}
                            </div>
                        </div>
                    </div>
                </Widget>

                <SettlementAlertsWidget settlements={data.settlementReview} />

                <Widget
                    title="Batch Processing Summary (Today)"
                    icon={<FileCheck className="size-5" />}
                >
                    <div className="space-y-4">
                        <div className="rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-900 dark:bg-green-950/20">
                            <div className="font-medium">Morning Batch</div>
                            <div className="mt-1 text-sm text-muted-foreground">
                                {data.batchSummary.morning.processed} requests
                                processed
                            </div>
                            <MetricValue
                                value={data.batchSummary.morning.totalAmount}
                                format="currency"
                                className="text-sm"
                            />
                            <div className="mt-1 text-xs text-muted-foreground">
                                Ref: {data.batchSummary.morning.bankReference}
                            </div>
                        </div>
                        <div className="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-900 dark:bg-blue-950/20">
                            <div className="font-medium">Afternoon Batch</div>
                            <div className="mt-1 text-sm text-muted-foreground">
                                {data.batchSummary.afternoon.scheduled} requests
                                scheduled
                            </div>
                            <MetricValue
                                value={data.batchSummary.afternoon.totalAmount}
                                format="currency"
                                className="text-sm"
                            />
                        </div>
                    </div>
                </Widget>

                <Widget title="COA Management">
                    <div className="space-y-3">
                        <div className="flex justify-between">
                            <span className="text-sm">
                                Total Active Accounts
                            </span>
                            <span className="font-medium">
                                {data.coaStats.totalActive}
                            </span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-sm">Klaten Accounts</span>
                            <span className="font-medium">
                                {data.coaStats.klatenAccounts}
                            </span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-sm">Magelang Accounts</span>
                            <span className="font-medium">
                                {data.coaStats.magelangAccounts}
                            </span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-sm">Pending Review</span>
                            <span className="font-medium text-yellow-600 dark:text-yellow-400">
                                {data.coaStats.pendingReview}
                            </span>
                        </div>
                    </div>
                </Widget>
            </div>
        </div>
    );
}
