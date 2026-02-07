import { format } from 'date-fns';
import type { DetailComponentProps } from '../ContentResolver';

export function GenericDetails({ approvable }: DetailComponentProps) {
    if (!approvable) {
        return <p className="text-muted-foreground">Item details not available.</p>;
    }

    const fields = [
        { label: 'Amount', value: approvable.amount || approvable.total_amount },
        {
            label: 'Title/Name',
            value: approvable.title || approvable.name || approvable.account_name,
        },
        { label: 'Reference', value: approvable.reference_number || approvable.code },
        { label: 'Description', value: approvable.description },
        {
            label: 'Date',
            value: approvable.created_at
                ? format(new Date(approvable.created_at), 'MMM dd, yyyy')
                : null,
        },
    ];

    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            {fields.map((field) =>
                field.value ? (
                    <div key={field.label} className="space-y-1">
                        <span className="text-sm font-medium text-muted-foreground">
                            {field.label}
                        </span>
                        <div className="text-sm font-semibold">
                            {field.value}
                        </div>
                    </div>
                ) : null,
            )}
        </div>
    );
}
