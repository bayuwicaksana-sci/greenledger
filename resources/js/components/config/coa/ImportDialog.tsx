import axios from 'axios';
import { Loader2, Upload } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useToast } from '@/hooks/use-toast';
import { validate, importMethod } from '@/actions/App/Http/Controllers/CoaAccountImportController';

interface ImportRow {
    site_code: string;
    account_code: string;
    account_name: string;
    account_type: string;
    short_description: string;
    parent_account_code: string;
    is_active: boolean;
}

interface RowErrors {
    [key: string]: string;
}

interface ImportDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onSuccess: () => void;
}

const REQUIRED_HEADERS = [
    'site_code',
    'account_code',
    'account_name',
    'account_type',
];

export function ImportDialog({
    open,
    onOpenChange,
    onSuccess,
}: ImportDialogProps) {
    const [rows, setRows] = useState<ImportRow[]>([]);
    const [errors, setErrors] = useState<Record<number, RowErrors>>({});
    const [parseError, setParseError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);
    const [validating, setValidating] = useState(false);
    const [isValid, setIsValid] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);
    const { toast } = useToast();

    useEffect(() => {
        if (!open) {
            resetState();
        }
    }, [open]);

    const resetState = () => {
        setRows([]);
        setErrors({});
        setParseError(null);
        setIsValid(false);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const parseCSV = (text: string): string[][] => {
        const result: string[][] = [];
        let current = '';
        let inQuotes = false;
        let row: string[] = [];

        // Strip BOM if present
        const cleanText = text.replace(/^\uFEFF/, '');

        for (let i = 0; i < cleanText.length; i++) {
            const char = cleanText[i];

            if (char === '"') {
                if (inQuotes && cleanText[i + 1] === '"') {
                    current += '"';
                    i++;
                } else {
                    inQuotes = !inQuotes;
                }
            } else if (char === ',' && !inQuotes) {
                row.push(current.trim());
                current = '';
            } else if ((char === '\n' || char === '\r') && !inQuotes) {
                if (char === '\r' && cleanText[i + 1] === '\n') {
                    i++;
                }
                row.push(current.trim());
                if (row.some((cell) => cell !== '')) {
                    result.push(row);
                }
                row = [];
                current = '';
            } else {
                current += char;
            }
        }

        // Push last row
        row.push(current.trim());
        if (row.some((cell) => cell !== '')) {
            result.push(row);
        }

        return result;
    };

    const handleFileChange = async (
        e: React.ChangeEvent<HTMLInputElement>,
    ) => {
        const file = e.target.files?.[0];
        if (!file) return;

        setParseError(null);
        setErrors({});
        setIsValid(false);

        const text = await file.text();
        const parsed = parseCSV(text);

        if (parsed.length < 2) {
            setParseError('CSV must contain a header row and at least one data row.');
            return;
        }

        const headers = parsed[0].map((h) => h.toLowerCase().replace(/\s+/g, '_'));

        // Validate required headers
        const missingHeaders = REQUIRED_HEADERS.filter(
            (h) => !headers.includes(h),
        );
        if (missingHeaders.length > 0) {
            setParseError(
                `Missing required columns: ${missingHeaders.join(', ')}`,
            );
            return;
        }

        // Map header indices
        const headerMap: Record<string, number> = {};
        headers.forEach((h, i) => {
            headerMap[h] = i;
        });

        // Parse rows
        const importRows: ImportRow[] = parsed.slice(1).map((row) => {
            const isActiveRaw = (row[headerMap.is_active] ?? 'true')
                .toLowerCase()
                .trim();

            return {
                site_code: row[headerMap.site_code] ?? '',
                account_code: row[headerMap.account_code] ?? '',
                account_name: row[headerMap.account_name] ?? '',
                account_type: (row[headerMap.account_type] ?? '')
                    .toUpperCase()
                    .trim(),
                short_description: row[headerMap.short_description] ?? '',
                parent_account_code: row[headerMap.parent_account_code] ?? '',
                is_active: isActiveRaw === 'true' || isActiveRaw === '1',
            };
        });

        setRows(importRows);
        runValidation(importRows);
    };

    const runValidation = async (rowsToValidate: ImportRow[]) => {
        setValidating(true);

        try {
            const response = await axios.post(validate.url(), {
                rows: rowsToValidate,
            });

            setErrors(response.data.errors || {});
            setIsValid(response.data.valid);
        } catch (error: any) {
            if (error.response?.status === 422) {
                // Laravel validation errors on the request itself
                toast({
                    title: 'Validation Error',
                    description: 'Some rows have invalid data. Please check and fix.',
                    variant: 'destructive',
                });
                setIsValid(false);
            } else {
                toast({
                    title: 'Error',
                    description: 'Failed to validate import data.',
                    variant: 'destructive',
                });
            }
        } finally {
            setValidating(false);
        }
    };

    const handleImport = async () => {
        setLoading(true);

        try {
            const response = await axios.post(importMethod.url(), {
                rows,
            });

            toast({
                title: 'Success',
                description: response.data.message,
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            if (error.response?.status === 422) {
                // Re-validate and show errors
                const serverErrors = error.response.data.errors || {};
                setErrors(serverErrors);
                setIsValid(false);
                toast({
                    title: 'Import Failed',
                    description: 'Some rows have errors. Please review and fix.',
                    variant: 'destructive',
                });
            } else {
                toast({
                    title: 'Error',
                    description:
                        error.response?.data?.message ||
                        'Failed to import accounts.',
                    variant: 'destructive',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-[800px]">
                <DialogHeader>
                    <DialogTitle>Import COA Accounts</DialogTitle>
                    <DialogDescription>
                        Upload a CSV file to bulk import chart of accounts.
                    </DialogDescription>
                </DialogHeader>

                <div className="grid gap-4 py-4">
                    {/* File upload */}
                    <div className="grid gap-2">
                        <label className="text-sm font-medium">CSV File</label>
                        <div className="rounded-lg border-2 border-dashed border-muted p-6 text-center">
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept=".csv"
                                className="hidden"
                                onChange={handleFileChange}
                            />
                            <Upload className="mx-auto mb-2 h-8 w-8 text-muted-foreground" />
                            <p className="text-sm text-muted-foreground">
                                {rows.length > 0
                                    ? `${rows.length} row(s) loaded`
                                    : 'Select a CSV file'}
                            </p>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                className="mt-2"
                                onClick={() => fileInputRef.current?.click()}
                            >
                                {rows.length > 0
                                    ? 'Change File'
                                    : 'Browse'}
                            </Button>
                        </div>
                        {parseError && (
                            <p className="text-sm text-destructive">
                                {parseError}
                            </p>
                        )}
                    </div>

                    {/* CSV format reference */}
                    <div className="rounded-md bg-muted p-3">
                        <p className="text-xs font-medium text-muted-foreground">
                            Expected CSV format:
                        </p>
                        <code className="text-xs text-muted-foreground">
                            site_code, account_code, account_name,
                            account_type, short_description,
                            parent_account_code, is_active
                        </code>
                    </div>

                    {/* Preview table */}
                    {rows.length > 0 && (
                        <div className="grid gap-2">
                            <div className="flex items-center justify-between">
                                <label className="text-sm font-medium">
                                    Preview ({rows.length} rows)
                                </label>
                                {validating && (
                                    <span className="flex items-center gap-1 text-xs text-muted-foreground">
                                        <Loader2 className="h-3 w-3 animate-spin" />
                                        Validating...
                                    </span>
                                )}
                                {!validating && Object.keys(errors).length === 0 && (
                                    <span className="text-xs text-green-600">
                                        All rows valid
                                    </span>
                                )}
                                {!validating && Object.keys(errors).length > 0 && (
                                    <span className="text-xs text-destructive">
                                        {Object.keys(errors).length} row(s)
                                        with errors
                                    </span>
                                )}
                            </div>
                            <div className="max-h-64 overflow-auto rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead className="w-10">#</TableHead>
                                            <TableHead>Site</TableHead>
                                            <TableHead>Code</TableHead>
                                            <TableHead>Name</TableHead>
                                            <TableHead>Type</TableHead>
                                            <TableHead>Parent</TableHead>
                                            <TableHead>Active</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {rows.map((row, index) => {
                                            const rowErrors =
                                                errors[index] || {};
                                            const hasError =
                                                Object.keys(rowErrors).length >
                                                0;

                                            return (
                                                <TableRow
                                                    key={index}
                                                    className={
                                                        hasError
                                                            ? 'bg-red-50 dark:bg-red-950'
                                                            : ''
                                                    }
                                                >
                                                    <TableCell className="text-muted-foreground">
                                                        {index + 1}
                                                    </TableCell>
                                                    <TableCell>
                                                        <div>
                                                            {row.site_code}
                                                            {rowErrors.site_code && (
                                                                <p className="text-xs text-destructive">
                                                                    {rowErrors.site_code}
                                                                </p>
                                                            )}
                                                        </div>
                                                    </TableCell>
                                                    <TableCell>
                                                        <div>
                                                            {row.account_code}
                                                            {rowErrors.account_code && (
                                                                <p className="text-xs text-destructive">
                                                                    {rowErrors.account_code}
                                                                </p>
                                                            )}
                                                        </div>
                                                    </TableCell>
                                                    <TableCell>
                                                        {row.account_name}
                                                    </TableCell>
                                                    <TableCell>
                                                        <span
                                                            className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${
                                                                row.account_type ===
                                                                'REVENUE'
                                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                                    : row.account_type ===
                                                                      'EXPENSE'
                                                                      ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                                                      : 'bg-gray-100 text-gray-800'
                                                            }`}
                                                        >
                                                            {row.account_type ||
                                                                '—'}
                                                        </span>
                                                    </TableCell>
                                                    <TableCell>
                                                        <div>
                                                            {row.parent_account_code ||
                                                                '—'}
                                                            {rowErrors.parent_account_code && (
                                                                <p className="text-xs text-destructive">
                                                                    {rowErrors.parent_account_code}
                                                                </p>
                                                            )}
                                                        </div>
                                                    </TableCell>
                                                    <TableCell>
                                                        {row.is_active ? (
                                                            <span className="text-green-600">
                                                                Yes
                                                            </span>
                                                        ) : (
                                                            <span className="text-gray-500">
                                                                No
                                                            </span>
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                            );
                                        })}
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
                            validating ||
                            !isValid ||
                            rows.length === 0
                        }
                        onClick={handleImport}
                    >
                        {loading && (
                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                        )}
                        Import {rows.length > 0 ? `${rows.length} Accounts` : 'Accounts'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
