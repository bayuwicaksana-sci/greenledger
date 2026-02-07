import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
import MainLayout from '@/layouts/main-layout';
import {
    create,
    destroy,
    edit,
    index,
    show,
} from '@/routes/admin/fiscal-years';
import type { BreadcrumbItem, FiscalYear } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { Eye, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useState } from 'react';

export default function FiscalYearsIndex({
    fiscalYears,
    filters,
}: {
    fiscalYears: FiscalYear[];
    filters: { search?: string; status?: string };
}) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Administration',
            href: '#',
        },
        {
            title: 'Fiscal Years',
            href: '#',
        },
    ];

    const handleSearch = () => {
        router.get(
            index().url,
            {
                search: search || undefined,
                status: status !== 'all' ? status : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    const handleDelete = (fiscalYear: FiscalYear) => {
        if (
            confirm(
                `Are you sure you want to delete fiscal year ${fiscalYear.year}?`,
            )
        ) {
            router.delete(destroy(fiscalYear.id).url);
        }
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Fiscal Years" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Fiscal Years</h1>
                        <p className="text-muted-foreground">
                            Manage fiscal year periods and year-end closing
                        </p>
                    </div>
                    <Link href={create().url}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Fiscal Year
                        </Button>
                    </Link>
                </div>

                {/* Filters */}
                <div className="flex gap-4">
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            placeholder="Search by year..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    handleSearch();
                                }
                            }}
                            className="pl-9"
                        />
                    </div>
                    <Select value={status} onValueChange={setStatus}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            <SelectItem value="open">Open</SelectItem>
                            <SelectItem value="closed">Closed</SelectItem>
                        </SelectContent>
                    </Select>
                    <Button onClick={handleSearch}>Apply Filters</Button>
                </div>

                {/* Table */}
                <div className="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Year</TableHead>
                                <TableHead>Date Range</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Programs</TableHead>
                                <TableHead className="text-right">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {fiscalYears.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={5}
                                        className="text-center text-muted-foreground"
                                    >
                                        No fiscal years found
                                    </TableCell>
                                </TableRow>
                            ) : (
                                fiscalYears.map((fy) => (
                                    <TableRow key={fy.id}>
                                        <TableCell className="font-medium">
                                            {fy.year}
                                        </TableCell>
                                        <TableCell>
                                            {format(
                                                new Date(fy.start_date),
                                                'MMM d, yyyy',
                                            )}{' '}
                                            -{' '}
                                            {format(
                                                new Date(fy.end_date),
                                                'MMM d, yyyy',
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {fy.is_closed ? (
                                                <Badge variant="secondary">
                                                    Closed
                                                </Badge>
                                            ) : (
                                                <Badge variant="default">
                                                    Open
                                                </Badge>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {fy.programs_count || 0} programs
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <div className="flex justify-end gap-2">
                                                <Link href={show(fy.id).url}>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                    >
                                                        <Eye className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Link href={edit(fy.id).url}>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                    >
                                                        <Pencil className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        handleDelete(fy)
                                                    }
                                                    disabled={
                                                        (fy.programs_count ||
                                                            0) > 0
                                                    }
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </MainLayout>
    );
}
