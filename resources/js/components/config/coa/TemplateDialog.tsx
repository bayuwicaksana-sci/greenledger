import axios from 'axios';
import { Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';

import { index as templateIndex, apply as templateApply } from '@/actions/App/Http/Controllers/CoaAccountTemplateController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useToast } from '@/hooks/use-toast';
import type { Site } from '@/types';

interface TemplateAccount {
    code: string;
    name: string;
    type: string;
    description: string;
    parent: string | null;
}

interface Template {
    key: string;
    name: string;
    description: string;
    account_count: number;
    accounts: TemplateAccount[];
    conflicts?: Record<string, boolean>;
    has_conflicts?: boolean;
}

interface TemplateDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onSuccess: () => void;
    sites: Site[];
}

export function TemplateDialog({
    open,
    onOpenChange,
    onSuccess,
    sites,
}: TemplateDialogProps) {
    const [templates, setTemplates] = useState<Template[]>([]);
    const [selectedSiteId, setSelectedSiteId] = useState<string>('');
    const [selectedTemplateKey, setSelectedTemplateKey] = useState<string>('');
    const [loadingTemplates, setLoadingTemplates] = useState(false);
    const [loading, setLoading] = useState(false);
    const { toast } = useToast();

    useEffect(() => {
        if (open) {
            setSelectedSiteId('');
            setSelectedTemplateKey('');
            setTemplates([]);
        }
    }, [open]);

    const handleSiteChange = async (siteId: string) => {
        setSelectedSiteId(siteId);
        setSelectedTemplateKey('');
        setLoadingTemplates(true);

        try {
            const response = await axios.get(templateIndex.url(), {
                params: { site_id: siteId },
            });
            setTemplates(response.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load templates.',
                variant: 'destructive',
            });
            setTemplates([]);
        } finally {
            setLoadingTemplates(false);
        }
    };

    const selectedTemplate = templates.find(
        (t) => t.key === selectedTemplateKey,
    );

    const handleApply = async () => {
        if (!selectedSiteId || !selectedTemplateKey) return;

        setLoading(true);

        try {
            const response = await axios.post(templateApply.url(), {
                template_key: selectedTemplateKey,
                site_id: parseInt(selectedSiteId),
                skip_existing: selectedTemplate?.has_conflicts ?? false,
            });

            toast({
                title: 'Success',
                description: response.data.message,
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            toast({
                title: 'Error',
                description:
                    error.response?.data?.message ||
                    'Failed to apply template.',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-[700px]">
                <DialogHeader>
                    <DialogTitle>Apply Account Template</DialogTitle>
                    <DialogDescription>
                        Apply a pre-defined template to quickly set up standard
                        chart of accounts for a site.
                    </DialogDescription>
                </DialogHeader>

                <div className="grid gap-4 py-4">
                    {/* Site selection */}
                    <div className="grid gap-2">
                        <label className="text-sm font-medium">
                            Target Site *
                        </label>
                        <Select
                            value={selectedSiteId}
                            onValueChange={handleSiteChange}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a site" />
                            </SelectTrigger>
                            <SelectContent>
                                {sites.map((site) => (
                                    <SelectItem
                                        key={site.id}
                                        value={site.id.toString()}
                                    >
                                        {site.site_name} ({site.site_code})
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    {/* Template selection */}
                    {selectedSiteId && (
                        <div className="grid gap-2">
                            <label className="text-sm font-medium">
                                Template *
                            </label>
                            {loadingTemplates ? (
                                <div className="flex items-center gap-2 py-2">
                                    <Loader2 className="h-4 w-4 animate-spin text-muted-foreground" />
                                    <span className="text-sm text-muted-foreground">
                                        Loading templates...
                                    </span>
                                </div>
                            ) : (
                                <div className="grid gap-2">
                                    {templates.map((template) => (
                                        <button
                                            key={template.key}
                                            type="button"
                                            className={`rounded-md border p-3 text-left transition-colors ${
                                                selectedTemplateKey ===
                                                template.key
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-muted hover:border-muted-foreground'
                                            }`}
                                            onClick={() =>
                                                setSelectedTemplateKey(
                                                    template.key,
                                                )
                                            }
                                        >
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-medium">
                                                    {template.name}
                                                </span>
                                                <span className="text-xs text-muted-foreground">
                                                    {template.account_count}{' '}
                                                    accounts
                                                </span>
                                            </div>
                                            <p className="mt-0.5 text-xs text-muted-foreground">
                                                {template.description}
                                            </p>
                                            {template.has_conflicts && (
                                                <p className="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                                    Some accounts already exist.
                                                    Existing accounts will be
                                                    skipped.
                                                </p>
                                            )}
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Preview accounts */}
                    {selectedTemplate && (
                        <div className="grid gap-2">
                            <label className="text-sm font-medium">
                                Accounts to Create
                            </label>
                            <div className="max-h-48 overflow-auto rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Code</TableHead>
                                            <TableHead>Name</TableHead>
                                            <TableHead>Type</TableHead>
                                            <TableHead>Parent</TableHead>
                                            <TableHead className="w-20 text-center">
                                                Status
                                            </TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {selectedTemplate.accounts.map(
                                            (account) => {
                                                const exists =
                                                    selectedTemplate.conflicts?.[
                                                        account.code
                                                    ] ?? false;

                                                return (
                                                    <TableRow
                                                        key={account.code}
                                                        className={
                                                            exists
                                                                ? 'opacity-50'
                                                                : ''
                                                        }
                                                    >
                                                        <TableCell className="font-medium">
                                                            {account.code}
                                                        </TableCell>
                                                        <TableCell>
                                                            {account.name}
                                                        </TableCell>
                                                        <TableCell>
                                                            <span
                                                                className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${
                                                                    account.type ===
                                                                    'REVENUE'
                                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                                                }`}
                                                            >
                                                                {account.type}
                                                            </span>
                                                        </TableCell>
                                                        <TableCell className="text-muted-foreground">
                                                            {account.parent ||
                                                                '—'}
                                                        </TableCell>
                                                        <TableCell className="text-center">
                                                            {exists ? (
                                                                <span className="text-xs text-amber-600 dark:text-amber-400">
                                                                    Exists
                                                                </span>
                                                            ) : (
                                                                <span className="text-xs text-green-600">
                                                                    New
                                                                </span>
                                                            )}
                                                        </TableCell>
                                                    </TableRow>
                                                );
                                            },
                                        )}
                                    </TableBody>
                                </Table>
                            </div>
                        </div>
                    )}
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                        disabled={loading}
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        disabled={
                            loading ||
                            !selectedSiteId ||
                            !selectedTemplateKey
                        }
                        onClick={handleApply}
                    >
                        {loading && (
                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                        )}
                        Apply Template
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
