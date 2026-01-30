import { QuickStatsRow } from '../organisms/QuickStatsRow';
import { Widget } from '../organisms/Widget';
import { FileText, CheckSquare, MapPin } from 'lucide-react';
import type { FarmAdminDashboardData } from '@/types/dashboard';

interface FarmAdminDashboardProps {
    data: FarmAdminDashboardData;
}

export function FarmAdminDashboard({ data }: FarmAdminDashboardProps) {
    // Farm Admin sees all sites, but handle missing data gracefully
    const klatenQueue = data.documentationQueue?.klaten || [];
    const magelangQueue = data.documentationQueue?.magelang || [];
    const klatenActivity = data.siteActivity?.klaten || {
        activeROs: 0,
        settlementsDueToday: 0,
        documentationComplete: 0,
        total: 0,
        lastUpload: 'N/A',
    };
    const magelangActivity = data.siteActivity?.magelang || {
        activeROs: 0,
        settlementsDueToday: 0,
        documentationComplete: 0,
        total: 0,
        lastUpload: 'N/A',
    };

    return (
        <div className="flex flex-col gap-6 p-6">
            <QuickStatsRow kpis={data.kpis} />

            <div className="grid gap-6 md:grid-cols-2">
                <Widget title="Settlement Documentation Queue" icon={<FileText className="size-5" />} fullWidth>
                    <div className="space-y-4">
                        <div>
                            <div className="font-medium mb-3">Klaten Site ({klatenQueue.length} pending)</div>
                            <div className="space-y-2">
                                {klatenQueue.slice(0, 3).map((settlement) => (
                                    <div key={settlement.id} className="p-3 rounded-lg border border-border">
                                        <div className="font-medium text-sm">{settlement.requestNumber} | {settlement.purpose}</div>
                                        <div className="text-xs text-muted-foreground mt-1">
                                            Receipt: {settlement.receipt_url ? '✓ Uploaded' : '✗ Pending'}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div>
                            <div className="font-medium mb-3">Magelang Site ({magelangQueue.length} pending)</div>
                            <div className="space-y-2">
                                {magelangQueue.slice(0, 3).map((settlement) => (
                                    <div key={settlement.id} className="p-3 rounded-lg border border-border">
                                        <div className="font-medium text-sm">{settlement.requestNumber} | {settlement.purpose}</div>
                                        <div className="text-xs text-muted-foreground mt-1">
                                            Receipt: {settlement.receipt_url ? '✓ Uploaded' : '✗ Pending'}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </Widget>

                <Widget title="Site Activity Monitor" icon={<MapPin className="size-5" />}>
                    <div className="space-y-4">
                        <div className="p-3 rounded-lg bg-accent">
                            <div className="font-medium">Klaten Station</div>
                            <div className="text-sm space-y-1 mt-2">
                                <div>Active ROs: {klatenActivity.activeROs}</div>
                                <div>Settlements due today: {klatenActivity.settlementsDueToday}</div>
                                <div>Documentation complete: {klatenActivity.documentationComplete}/{klatenActivity.total}</div>
                                <div className="text-muted-foreground text-xs mt-1">
                                    Last upload: {klatenActivity.lastUpload}
                                </div>
                            </div>
                        </div>
                        <div className="p-3 rounded-lg bg-accent">
                            <div className="font-medium">Magelang Station</div>
                            <div className="text-sm space-y-1 mt-2">
                                <div>Active ROs: {magelangActivity.activeROs}</div>
                                <div>Settlements due today: {magelangActivity.settlementsDueToday}</div>
                                <div>Documentation complete: {magelangActivity.documentationComplete}/{magelangActivity.total}</div>
                                <div className="text-muted-foreground text-xs mt-1">
                                    Last upload: {magelangActivity.lastUpload}
                                </div>
                            </div>
                        </div>
                    </div>
                </Widget>

                <Widget title="Document Quality Checklist" icon={<CheckSquare className="size-5" />}>
                    <div className="space-y-2">
                        {Object.entries(data.documentChecklist).map(([key, value]) => (
                            <div key={key} className="flex items-center justify-between">
                                <span className="text-sm capitalize">{key.replace(/([A-Z])/g, ' $1').trim()}</span>
                                <span className={value ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}>
                                    {value ? '✓' : '✗'}
                                </span>
                            </div>
                        ))}
                    </div>
                </Widget>

                <Widget title="Recent Uploads (Last 24 Hours)">
                    <div className="space-y-2">
                        {data.recentUploads.map((upload, idx) => (
                            <div key={idx} className="flex justify-between text-sm p-2 rounded border border-border">
                                <div>
                                    <span className="font-medium">{upload.site}</span> | {upload.ro} | {upload.reference}
                                </div>
                                <span className="text-muted-foreground">{upload.time}</span>
                            </div>
                        ))}
                    </div>
                </Widget>
            </div>
        </div>
    );
}
