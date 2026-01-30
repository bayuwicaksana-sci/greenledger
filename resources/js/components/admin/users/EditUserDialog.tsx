import { useEffect, useState } from 'react';
import axios from 'axios';
import { Loader2 } from 'lucide-react';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { useToast } from '@/hooks/use-toast';
import type { User, Site } from '@/types/users';

interface EditUserDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    user: User | null;
    onSuccess: () => void;
}

export function EditUserDialog({
    open,
    onOpenChange,
    user,
    onSuccess,
}: EditUserDialogProps) {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [fullName, setFullName] = useState('');
    const [primarySiteId, setPrimarySiteId] = useState<number | null>(null);
    const [isActive, setIsActive] = useState(true);
    const [sites, setSites] = useState<Site[]>([]);
    const [loading, setLoading] = useState(false);
    const [loadingData, setLoadingData] = useState(false);
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const { toast } = useToast();

    useEffect(() => {
        if (open && user) {
            setName(user.name);
            setEmail(user.email);
            setFullName(user.full_name || '');
            setPrimarySiteId(user.primary_site_id);
            setIsActive(user.is_active);
            loadSites();
        } else if (!open) {
            resetForm();
        }
    }, [open, user]);

    const loadSites = async () => {
        setLoadingData(true);
        try {
            const response = await axios.get('/api/sites');
            setSites(response.data.data || response.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load sites',
                variant: 'destructive',
            });
        } finally {
            setLoadingData(false);
        }
    };

    const resetForm = () => {
        setName('');
        setEmail('');
        setFullName('');
        setPrimarySiteId(null);
        setIsActive(true);
        setErrors({});
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!user) return;

        setErrors({});
        setLoading(true);

        try {
            await axios.put(`/api/users/${user.id}`, {
                name,
                email,
                full_name: fullName,
                primary_site_id: primarySiteId,
                is_active: isActive,
            });

            toast({
                title: 'Success',
                description: 'User updated successfully',
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.errors || {});
            } else {
                toast({
                    title: 'Error',
                    description:
                        error.response?.data?.message ||
                        'Failed to update user',
                    variant: 'destructive',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[500px]">
                <form onSubmit={handleSubmit}>
                    <DialogHeader>
                        <DialogTitle>Edit User</DialogTitle>
                        <DialogDescription>
                            Update basic user information
                        </DialogDescription>
                    </DialogHeader>

                    {loadingData ? (
                        <div className="flex items-center justify-center py-8">
                            <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
                        </div>
                    ) : (
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Username *</Label>
                                <Input
                                    id="name"
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                    placeholder="username"
                                    disabled={loading}
                                    className={
                                        errors.name ? 'border-destructive' : ''
                                    }
                                />
                                {errors.name && (
                                    <p className="text-sm text-destructive">
                                        {errors.name[0]}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email *</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    placeholder="user@example.com"
                                    disabled={loading}
                                    className={
                                        errors.email ? 'border-destructive' : ''
                                    }
                                />
                                {errors.email && (
                                    <p className="text-sm text-destructive">
                                        {errors.email[0]}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="full_name">Full Name</Label>
                                <Input
                                    id="full_name"
                                    value={fullName}
                                    onChange={(e) => setFullName(e.target.value)}
                                    placeholder="John Doe"
                                    disabled={loading}
                                    className={
                                        errors.full_name
                                            ? 'border-destructive'
                                            : ''
                                    }
                                />
                                {errors.full_name && (
                                    <p className="text-sm text-destructive">
                                        {errors.full_name[0]}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="primary_site">
                                    Primary Site
                                </Label>
                                <Select
                                    value={primarySiteId?.toString() || ''}
                                    onValueChange={(value) =>
                                        setPrimarySiteId(
                                            value ? parseInt(value) : null,
                                        )
                                    }
                                    disabled={loading}
                                >
                                    <SelectTrigger
                                        id="primary_site"
                                        className={
                                            errors.primary_site_id
                                                ? 'border-destructive'
                                                : ''
                                        }
                                    >
                                        <SelectValue placeholder="Select primary site" />
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
                                {errors.primary_site_id && (
                                    <p className="text-sm text-destructive">
                                        {errors.primary_site_id[0]}
                                    </p>
                                )}
                            </div>

                            <div className="flex items-center justify-between space-x-2">
                                <Label htmlFor="is_active">Active Status</Label>
                                <Switch
                                    id="is_active"
                                    checked={isActive}
                                    onCheckedChange={setIsActive}
                                    disabled={loading}
                                />
                            </div>
                            {errors.is_active && (
                                <p className="text-sm text-destructive">
                                    {errors.is_active[0]}
                                </p>
                            )}
                        </div>
                    )}

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
                            type="submit"
                            disabled={
                                loading ||
                                loadingData ||
                                !name.trim() ||
                                !email.trim()
                            }
                        >
                            {loading && (
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                            )}
                            Save Changes
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
