import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import MainLayout from '@/layouts/main-layout';
import { close, edit, index, reopen } from '@/routes/admin/fiscal-years';
import type { BreadcrumbItem, FiscalYear } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import { AlertTriangle, ArrowLeft, Lock, LockOpen, Pencil } from 'lucide-react';
import { useState } from 'react';

export default function ShowFiscalYear({
    fiscalYear,
    programCount,
}: {
    fiscalYear: FiscalYear;
    programCount: number;
}) {
    const [closeDialogOpen, setCloseDialogOpen] = useState(false);
    const [reopenDialogOpen, setReopenDialogOpen] = useState(false);

    const closeForm = useForm({
        notes: '',
        options: {
            archive_completed_programs: false,
            block_new_programs: false,
            block_new_transactions: false,
            generate_report: false,
            send_notifications: false,
        },
    });

    const reopenForm = useForm({
        reason: '',
    });

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Administration',
            href: '#',
        },
        {
            title: 'Fiscal Years',
            href: index().url,
        },
        {
            title: `FY${fiscalYear.year}`,
            href: '#',
        },
    ];

    const handleClose = (e: React.FormEvent) => {
        e.preventDefault();
        closeForm.post(close({ fiscalYear: fiscalYear.id }).url, {
            onSuccess: () => setCloseDialogOpen(false),
        });
    };

    const handleReopen = (e: React.FormEvent) => {
        e.preventDefault();
        reopenForm.post(reopen({ fiscalYear: fiscalYear.id }).url, {
            onSuccess: () => setReopenDialogOpen(false),
        });
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title={`Fiscal Year ${fiscalYear.year}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => router.visit(index().url)}
                        >
                            <ArrowLeft className="h-4 w-4" />
                        </Button>
                        <div>
                            <div className="flex items-center gap-3">
                                <h1 className="text-3xl font-bold">
                                    Fiscal Year {fiscalYear.year}
                                </h1>
                                {fiscalYear.is_closed ? (
                                    <Badge variant="secondary">Closed</Badge>
                                ) : (
                                    <Badge variant="default">Open</Badge>
                                )}
                            </div>
                            <p className="text-muted-foreground">
                                {format(
                                    new Date(fiscalYear.start_date),
                                    'MMMM d, yyyy',
                                )}{' '}
                                -{' '}
                                {format(
                                    new Date(fiscalYear.end_date),
                                    'MMMM d, yyyy',
                                )}
                            </p>
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Link href={edit({ fiscal_year: fiscalYear.id }).url}>
                            <Button variant="outline">
                                <Pencil className="mr-2 h-4 w-4" />
                                Edit
                            </Button>
                        </Link>
                        {fiscalYear.is_closed ? (
                            <Dialog
                                open={reopenDialogOpen}
                                onOpenChange={setReopenDialogOpen}
                            >
                                <DialogTrigger asChild>
                                    <Button variant="outline">
                                        <LockOpen className="mr-2 h-4 w-4" />
                                        Reopen
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <form onSubmit={handleReopen}>
                                        <DialogHeader>
                                            <DialogTitle>
                                                Reopen Fiscal Year{' '}
                                                {fiscalYear.year}
                                            </DialogTitle>
                                            <DialogDescription>
                                                Provide a reason for reopening
                                                this fiscal year. This action
                                                will be logged for audit
                                                purposes.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <div className="py-4">
                                            <Label htmlFor="reason">
                                                Reason (Required)
                                            </Label>
                                            <Textarea
                                                id="reason"
                                                value={reopenForm.data.reason}
                                                onChange={(e) =>
                                                    reopenForm.setData(
                                                        'reason',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Explain why this fiscal year needs to be reopened..."
                                                required
                                                className="mt-2"
                                            />
                                            {reopenForm.errors.reason && (
                                                <p className="mt-2 text-sm text-destructive">
                                                    {reopenForm.errors.reason}
                                                </p>
                                            )}
                                        </div>
                                        <DialogFooter>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                onClick={() =>
                                                    setReopenDialogOpen(false)
                                                }
                                            >
                                                Cancel
                                            </Button>
                                            <Button
                                                type="submit"
                                                disabled={reopenForm.processing}
                                            >
                                                {reopenForm.processing
                                                    ? 'Reopening...'
                                                    : 'Reopen Fiscal Year'}
                                            </Button>
                                        </DialogFooter>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        ) : (
                            <Dialog
                                open={closeDialogOpen}
                                onOpenChange={setCloseDialogOpen}
                            >
                                <DialogTrigger asChild>
                                    <Button>
                                        <Lock className="mr-2 h-4 w-4" />
                                        Close Fiscal Year
                                    </Button>
                                </DialogTrigger>
                                <DialogContent className="max-w-2xl">
                                    <form onSubmit={handleClose}>
                                        <DialogHeader>
                                            <DialogTitle>
                                                Close Fiscal Year{' '}
                                                {fiscalYear.year}
                                            </DialogTitle>
                                            <DialogDescription>
                                                Select the actions to perform
                                                when closing this fiscal year.
                                                This action will be logged for
                                                audit purposes.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <div className="space-y-4 py-4">
                                            {/* Pre-close validation warning */}
                                            {programCount > 0 && (
                                                <Alert variant="default">
                                                    <AlertTriangle className="h-4 w-4" />
                                                    <AlertDescription>
                                                        This fiscal year has{' '}
                                                        {programCount} active
                                                        program
                                                        {programCount !== 1
                                                            ? 's'
                                                            : ''}
                                                        . Consider completing or
                                                        archiving programs
                                                        before closing.
                                                    </AlertDescription>
                                                </Alert>
                                            )}

                                            <div className="space-y-3">
                                                <Label>Closing Actions</Label>
                                                <div className="space-y-2">
                                                    <div className="flex items-center space-x-2">
                                                        <Checkbox
                                                            id="archive"
                                                            checked={
                                                                closeForm.data
                                                                    .options
                                                                    .archive_completed_programs
                                                            }
                                                            onCheckedChange={(
                                                                checked,
                                                            ) =>
                                                                closeForm.setData(
                                                                    'options',
                                                                    {
                                                                        ...closeForm
                                                                            .data
                                                                            .options,
                                                                        archive_completed_programs:
                                                                            checked as boolean,
                                                                    },
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor="archive"
                                                            className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            Archive completed
                                                            programs
                                                        </label>
                                                    </div>
                                                    <div className="flex items-center space-x-2">
                                                        <Checkbox
                                                            id="block_programs"
                                                            checked={
                                                                closeForm.data
                                                                    .options
                                                                    .block_new_programs
                                                            }
                                                            onCheckedChange={(
                                                                checked,
                                                            ) =>
                                                                closeForm.setData(
                                                                    'options',
                                                                    {
                                                                        ...closeForm
                                                                            .data
                                                                            .options,
                                                                        block_new_programs:
                                                                            checked as boolean,
                                                                    },
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor="block_programs"
                                                            className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            Block new programs
                                                        </label>
                                                    </div>
                                                    <div className="flex items-center space-x-2">
                                                        <Checkbox
                                                            id="block_transactions"
                                                            checked={
                                                                closeForm.data
                                                                    .options
                                                                    .block_new_transactions
                                                            }
                                                            onCheckedChange={(
                                                                checked,
                                                            ) =>
                                                                closeForm.setData(
                                                                    'options',
                                                                    {
                                                                        ...closeForm
                                                                            .data
                                                                            .options,
                                                                        block_new_transactions:
                                                                            checked as boolean,
                                                                    },
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor="block_transactions"
                                                            className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            Block new
                                                            transactions
                                                        </label>
                                                    </div>
                                                    <div className="flex items-center space-x-2">
                                                        <Checkbox
                                                            id="generate_report"
                                                            checked={
                                                                closeForm.data
                                                                    .options
                                                                    .generate_report
                                                            }
                                                            onCheckedChange={(
                                                                checked,
                                                            ) =>
                                                                closeForm.setData(
                                                                    'options',
                                                                    {
                                                                        ...closeForm
                                                                            .data
                                                                            .options,
                                                                        generate_report:
                                                                            checked as boolean,
                                                                    },
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor="generate_report"
                                                            className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            Generate year-end
                                                            report
                                                        </label>
                                                    </div>
                                                    <div className="flex items-center space-x-2">
                                                        <Checkbox
                                                            id="send_notifications"
                                                            checked={
                                                                closeForm.data
                                                                    .options
                                                                    .send_notifications
                                                            }
                                                            onCheckedChange={(
                                                                checked,
                                                            ) =>
                                                                closeForm.setData(
                                                                    'options',
                                                                    {
                                                                        ...closeForm
                                                                            .data
                                                                            .options,
                                                                        send_notifications:
                                                                            checked as boolean,
                                                                    },
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor="send_notifications"
                                                            className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            Send notifications
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="space-y-2">
                                                <Label htmlFor="notes">
                                                    Notes (Optional)
                                                </Label>
                                                <Textarea
                                                    id="notes"
                                                    value={closeForm.data.notes}
                                                    onChange={(e) =>
                                                        closeForm.setData(
                                                            'notes',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Add any notes about this closure..."
                                                />
                                            </div>
                                        </div>
                                        <DialogFooter>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                onClick={() =>
                                                    setCloseDialogOpen(false)
                                                }
                                            >
                                                Cancel
                                            </Button>
                                            <Button
                                                type="submit"
                                                disabled={closeForm.processing}
                                            >
                                                {closeForm.processing
                                                    ? 'Closing...'
                                                    : 'Close Fiscal Year'}
                                            </Button>
                                        </DialogFooter>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        )}
                    </div>
                </div>

                {/* Details Card */}
                <Card>
                    <CardHeader>
                        <CardTitle>Fiscal Year Information</CardTitle>
                        <CardDescription>
                            Overview of this fiscal year period
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">
                                    Year Code
                                </p>
                                <p className="text-2xl font-bold">
                                    {fiscalYear.year}
                                </p>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">
                                    Status
                                </p>
                                <div className="mt-1">
                                    {fiscalYear.is_closed ? (
                                        <Badge variant="secondary">
                                            Closed
                                        </Badge>
                                    ) : (
                                        <Badge variant="default">Open</Badge>
                                    )}
                                </div>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">
                                    Start Date
                                </p>
                                <p className="text-lg">
                                    {format(
                                        new Date(fiscalYear.start_date),
                                        'MMMM d, yyyy',
                                    )}
                                </p>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">
                                    End Date
                                </p>
                                <p className="text-lg">
                                    {format(
                                        new Date(fiscalYear.end_date),
                                        'MMMM d, yyyy',
                                    )}
                                </p>
                            </div>
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">
                                    Associated Programs
                                </p>
                                <p className="text-lg">
                                    {programCount} program
                                    {programCount !== 1 ? 's' : ''}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}
