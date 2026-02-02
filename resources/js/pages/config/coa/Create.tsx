import {
    index,
    store,
} from '@/actions/App/Http/Controllers/CoaAccountController'; // Wayfinder action import
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import MainLayout from '@/layouts/main-layout';
import { BreadcrumbItem, Site } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react'; // Standard Inertia useForm

interface ParentAccount {
    id: number;
    account_code: string;
    account_name: string;
    hierarchy_level: number;
}

interface CreateProps {
    sites: Site[];
    parents: ParentAccount[];
}

export default function Create({ sites, parents }: CreateProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Chart of Accounts',
            href: index.url(),
        },
        {
            title: 'Create Account',
            href: '#',
        },
    ];

    const { data, setData, post, processing, errors } = useForm({
        site_id: '',
        account_code: '',
        account_name: '',
        account_type: 'EXPENSE',
        short_description: '',
        parent_account_id: '',
        is_active: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        // Using Wayfinder store() for the route, or standard route helper if preferred.
        // Wayfinder: store() returns a Route object. store.url() returns the URL.
        // We can use the helper `route('config.coa.store')` or `store.url()`.
        // Since we are using standard inertia useForm, `post` expects a URL.
        post(store.url());
    };

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Account" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <form
                    onSubmit={handleSubmit}
                    className="grid grid-cols-1 gap-4 md:grid-cols-3"
                >
                    <div className="flex flex-col gap-4 md:col-span-2">
                        <Card>
                            <CardHeader className="pb-3">
                                <CardTitle>Account Details</CardTitle>
                                <CardDescription>
                                    Basic information about the account.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="account_code">
                                        Account Code
                                    </Label>
                                    <Input
                                        id="account_code"
                                        value={data.account_code}
                                        onChange={(e) =>
                                            setData(
                                                'account_code',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g. KLT-EXP-001"
                                    />
                                    {errors.account_code && (
                                        <p className="text-sm text-destructive">
                                            {errors.account_code}
                                        </p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="account_name">
                                        Account Name
                                    </Label>
                                    <Input
                                        id="account_name"
                                        value={data.account_name}
                                        onChange={(e) =>
                                            setData(
                                                'account_name',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g. Fertilizer Expenses"
                                    />
                                    {errors.account_name && (
                                        <p className="text-sm text-destructive">
                                            {errors.account_name}
                                        </p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="short_description">
                                        Description
                                    </Label>
                                    <Textarea
                                        id="short_description"
                                        value={data.short_description}
                                        onChange={(e) =>
                                            setData(
                                                'short_description',
                                                e.target.value,
                                            )
                                        }
                                        rows={4}
                                    />
                                    {errors.short_description && (
                                        <p className="text-sm text-destructive">
                                            {errors.short_description}
                                        </p>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        <div className="flex justify-end gap-2">
                            <Link href={index.url()}>
                                <Button type="button" variant="secondary">
                                    Cancel
                                </Button>
                            </Link>
                            <Button type="submit" disabled={processing}>
                                Create Account
                            </Button>
                        </div>
                    </div>

                    <div className="flex flex-col gap-4">
                        <Card>
                            <CardHeader className="pb-3">
                                <CardTitle>Classification</CardTitle>
                                <CardDescription>
                                    Organize this account.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="site_id">Site</Label>
                                    <Select
                                        value={data.site_id}
                                        onValueChange={(val) =>
                                            setData('site_id', val)
                                        }
                                    >
                                        <SelectTrigger id="site_id">
                                            <SelectValue placeholder="Select site" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {sites.map((site) => (
                                                <SelectItem
                                                    key={site.id}
                                                    value={site.id.toString()}
                                                >
                                                    {site.site_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.site_id && (
                                        <p className="text-sm text-destructive">
                                            {errors.site_id}
                                        </p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="parent_account_id">
                                        Parent Account
                                    </Label>
                                    <Select
                                        value={data.parent_account_id}
                                        onValueChange={(val) =>
                                            setData(
                                                'parent_account_id',
                                                val === 'none' ? '' : val,
                                            )
                                        }
                                    >
                                        <SelectTrigger id="parent_account_id">
                                            <SelectValue placeholder="None" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="none">
                                                None
                                            </SelectItem>
                                            {parents.map((parent) => (
                                                <SelectItem
                                                    key={parent.id}
                                                    value={parent.id.toString()}
                                                >
                                                    {parent.account_code} -{' '}
                                                    {parent.account_name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.parent_account_id && (
                                        <p className="text-sm text-destructive">
                                            {errors.parent_account_id}
                                        </p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="account_type">Type</Label>
                                    <Select
                                        value={data.account_type}
                                        onValueChange={(val) =>
                                            setData('account_type', val)
                                        }
                                    >
                                        <SelectTrigger id="account_type">
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="REVENUE">
                                                Revenue
                                            </SelectItem>
                                            <SelectItem value="EXPENSE">
                                                Expense
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.account_type && (
                                        <p className="text-sm text-destructive">
                                            {errors.account_type}
                                        </p>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </form>
            </div>
        </MainLayout>
    );
}
