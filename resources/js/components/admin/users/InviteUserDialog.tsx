import axios from 'axios';
import { Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { useToast } from '@/hooks/use-toast';
import type { Role, Site } from '@/types/users';

interface InviteUserDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onSuccess: () => void;
}

export function InviteUserDialog({
    open,
    onOpenChange,
    onSuccess,
}: InviteUserDialogProps) {
    const [email, setEmail] = useState('');
    const [fullName, setFullName] = useState('');
    const [primarySiteId, setPrimarySiteId] = useState<number | null>(null);
    const [additionalSiteIds, setAdditionalSiteIds] = useState<number[]>([]);
    const [roleIds, setRoleIds] = useState<number[]>([]);
    const [sites, setSites] = useState<Site[]>([]);
    const [roles, setRoles] = useState<Role[]>([]);
    const [loading, setLoading] = useState(false);
    const [loadingData, setLoadingData] = useState(false);
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const { toast } = useToast();

    useEffect(() => {
        if (open) {
            loadFormData();
        } else {
            resetForm();
        }
    }, [open]);

    const loadFormData = async () => {
        setLoadingData(true);
        try {
            const [sitesRes, rolesRes] = await Promise.all([
                axios.get('/api/sites'),
                axios.get('/api/roles'),
            ]);
            setSites(sitesRes.data.data || sitesRes.data);
            setRoles(rolesRes.data.data || rolesRes.data);
        } catch (error: any) {
            toast({
                title: 'Error',
                description: 'Failed to load form data',
                variant: 'destructive',
            });
        } finally {
            setLoadingData(false);
        }
    };

    const resetForm = () => {
        setEmail('');
        setFullName('');
        setPrimarySiteId(null);
        setAdditionalSiteIds([]);
        setRoleIds([]);
        setErrors({});
    };

    const handleRoleToggle = (roleId: number) => {
        setRoleIds((prev) =>
            prev.includes(roleId)
                ? prev.filter((id) => id !== roleId)
                : [...prev, roleId],
        );
    };

    const handleAdditionalSiteToggle = (siteId: number) => {
        setAdditionalSiteIds((prev) =>
            prev.includes(siteId)
                ? prev.filter((id) => id !== siteId)
                : [...prev, siteId],
        );
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        setLoading(true);

        try {
            await axios.post('/api/users/invite', {
                email,
                full_name: fullName,
                primary_site_id: primarySiteId,
                additional_site_ids: additionalSiteIds,
                role_ids: roleIds,
            });

            toast({
                title: 'Success',
                description: 'User invitation sent successfully',
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
                        'Failed to send invitation',
                    variant: 'destructive',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-[600px]">
                <form onSubmit={handleSubmit}>
                    <DialogHeader>
                        <DialogTitle>Invite New User</DialogTitle>
                        <DialogDescription>
                            Send an invitation email to a new user. They will
                            receive instructions to set up their account.
                        </DialogDescription>
                    </DialogHeader>

                    {loadingData ? (
                        <div className="flex items-center justify-center py-8">
                            <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
                        </div>
                    ) : (
                        <div className="grid gap-4 py-4">
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
                                <Label htmlFor="full_name">Full Name *</Label>
                                <Input
                                    id="full_name"
                                    value={fullName}
                                    onChange={(e) =>
                                        setFullName(e.target.value)
                                    }
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
                                    Primary Site *
                                </Label>
                                <Select
                                    value={primarySiteId?.toString() || ''}
                                    onValueChange={(value) =>
                                        setPrimarySiteId(parseInt(value))
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

                            <div className="grid gap-2">
                                <Label>Additional Sites</Label>
                                <div className="max-h-32 space-y-2 overflow-y-auto rounded-md border p-3">
                                    {sites
                                        .filter(
                                            (site) => site.id !== primarySiteId,
                                        )
                                        .map((site) => (
                                            <div
                                                key={site.id}
                                                className="flex items-center space-x-2"
                                            >
                                                <Checkbox
                                                    id={`site-${site.id}`}
                                                    checked={additionalSiteIds.includes(
                                                        site.id,
                                                    )}
                                                    onCheckedChange={() =>
                                                        handleAdditionalSiteToggle(
                                                            site.id,
                                                        )
                                                    }
                                                    disabled={loading}
                                                />
                                                <label
                                                    htmlFor={`site-${site.id}`}
                                                    className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                >
                                                    {site.site_name}
                                                </label>
                                            </div>
                                        ))}
                                </div>
                                {errors.additional_site_ids && (
                                    <p className="text-sm text-destructive">
                                        {errors.additional_site_ids[0]}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-2">
                                <Label>Roles * (select at least one)</Label>
                                <div className="max-h-32 space-y-2 overflow-y-auto rounded-md border p-3">
                                    {roles.map((role) => (
                                        <div
                                            key={role.id}
                                            className="flex items-center space-x-2"
                                        >
                                            <Checkbox
                                                id={`role-${role.id}`}
                                                checked={roleIds.includes(
                                                    role.id,
                                                )}
                                                onCheckedChange={() =>
                                                    handleRoleToggle(role.id)
                                                }
                                                disabled={loading}
                                            />
                                            <label
                                                htmlFor={`role-${role.id}`}
                                                className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            >
                                                {role.name}
                                            </label>
                                        </div>
                                    ))}
                                </div>
                                {errors.role_ids && (
                                    <p className="text-sm text-destructive">
                                        {errors.role_ids[0]}
                                    </p>
                                )}
                            </div>
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
                                !email.trim() ||
                                !fullName.trim() ||
                                !primarySiteId ||
                                roleIds.length === 0
                            }
                        >
                            {loading && (
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                            )}
                            Send Invitation
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
