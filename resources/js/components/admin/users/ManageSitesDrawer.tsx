import axios from 'axios';
import { Building2, Loader2, Save, Star, X } from 'lucide-react';
import { useEffect, useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useToast } from '@/hooks/use-toast';
import type { Site, User } from '@/types/users';

interface ManageSitesDrawerProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    user: User | null;
    onSuccess: () => void;
}

export function ManageSitesDrawer({
    open,
    onOpenChange,
    user,
    onSuccess,
}: ManageSitesDrawerProps) {
    const [sites, setSites] = useState<Site[]>([]);
    const [selectedSiteIds, setSelectedSiteIds] = useState<number[]>([]);
    const [primarySiteId, setPrimarySiteId] = useState<number | null>(null);
    const [originalSiteIds, setOriginalSiteIds] = useState<number[]>([]);
    const [originalPrimarySiteId, setOriginalPrimarySiteId] = useState<
        number | null
    >(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const { toast } = useToast();

    useEffect(() => {
        if (open && user) {
            loadSites();
            const userSiteIds = user.sites.map((s) => s.id);
            setSelectedSiteIds(userSiteIds);
            setOriginalSiteIds(userSiteIds);
            setPrimarySiteId(user.primary_site_id);
            setOriginalPrimarySiteId(user.primary_site_id);
        } else if (!open) {
            setSelectedSiteIds([]);
            setOriginalSiteIds([]);
            setPrimarySiteId(null);
            setOriginalPrimarySiteId(null);
            setSearchQuery('');
            setError(null);
        }
    }, [open, user]);

    const loadSites = async () => {
        setLoading(true);
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
            setLoading(false);
        }
    };

    const handleSiteToggle = (siteId: number) => {
        setSelectedSiteIds((prev) => {
            const newSiteIds = prev.includes(siteId)
                ? prev.filter((id) => id !== siteId)
                : [...prev, siteId];

            // If primary site is deselected, clear it
            if (
                !newSiteIds.includes(primarySiteId as number) &&
                primarySiteId
            ) {
                setPrimarySiteId(null);
            }

            return newSiteIds;
        });
        setError(null);
    };

    const handlePrimarySiteChange = (siteId: string) => {
        const id = parseInt(siteId);
        if (!selectedSiteIds.includes(id)) {
            setError('Primary site must be one of the selected sites');
            return;
        }
        setPrimarySiteId(id);
        setError(null);
    };

    const handleSave = async () => {
        if (!user) return;

        // Validation
        if (selectedSiteIds.length === 0) {
            setError('Please select at least one site');
            return;
        }

        if (!primarySiteId) {
            setError('Please select a primary site');
            return;
        }

        if (!selectedSiteIds.includes(primarySiteId)) {
            setError('Primary site must be one of the selected sites');
            return;
        }

        setSaving(true);
        setError(null);

        try {
            await axios.put(`/api/users/${user.id}/sites`, {
                site_ids: selectedSiteIds,
                primary_site_id: primarySiteId,
            });
            toast({
                title: 'Success',
                description: 'User site access updated successfully',
            });
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            if (error.response?.status === 422) {
                setError(
                    error.response.data.errors?.primary_site_id?.[0] ||
                        'Validation failed',
                );
            } else {
                toast({
                    title: 'Error',
                    description:
                        error.response?.data?.message ||
                        'Failed to update site access',
                    variant: 'destructive',
                });
            }
        } finally {
            setSaving(false);
        }
    };

    const filteredSites = sites.filter((site) =>
        site.site_name.toLowerCase().includes(searchQuery.toLowerCase()),
    );

    const isDirty =
        JSON.stringify([...selectedSiteIds].sort()) !==
            JSON.stringify([...originalSiteIds].sort()) ||
        primarySiteId !== originalPrimarySiteId;

    return (
        <Drawer open={open} onOpenChange={onOpenChange}>
            <DrawerContent className="max-h-[90vh]!">
                <div className="mx-auto w-full max-w-2xl">
                    <DrawerHeader>
                        <DrawerTitle className="flex items-center gap-2">
                            <Building2 className="h-5 w-5" />
                            Manage Site Access
                        </DrawerTitle>
                        <DrawerDescription>
                            Assign site access for{' '}
                            {user?.full_name || user?.name}. Select at least one
                            site and designate a primary site.
                        </DrawerDescription>
                    </DrawerHeader>

                    <div className="space-y-4 p-4">
                        {/* Selected sites */}
                        <div>
                            <p className="mb-2 text-sm font-medium">
                                Selected Sites ({selectedSiteIds.length})
                            </p>
                            <div className="flex min-h-[48px] flex-wrap gap-2 rounded-md border p-3">
                                {selectedSiteIds.length > 0 ? (
                                    sites
                                        .filter((s) =>
                                            selectedSiteIds.includes(s.id),
                                        )
                                        .map((site) => (
                                            <Badge
                                                key={site.id}
                                                variant={
                                                    site.id === primarySiteId
                                                        ? 'default'
                                                        : 'secondary'
                                                }
                                                className="gap-1"
                                            >
                                                {site.id === primarySiteId && (
                                                    <Star className="h-3 w-3 fill-current" />
                                                )}
                                                {site.site_name}
                                                <X
                                                    className="h-3 w-3 cursor-pointer"
                                                    onClick={() =>
                                                        handleSiteToggle(
                                                            site.id,
                                                        )
                                                    }
                                                />
                                            </Badge>
                                        ))
                                ) : (
                                    <span className="text-sm text-muted-foreground">
                                        No sites selected
                                    </span>
                                )}
                            </div>
                        </div>

                        {/* Primary site selection */}
                        {selectedSiteIds.length > 0 && (
                            <div>
                                <Label className="mb-2 text-sm font-medium">
                                    Primary Site *
                                </Label>
                                <RadioGroup
                                    value={primarySiteId?.toString() || ''}
                                    onValueChange={handlePrimarySiteChange}
                                    className="grid grid-cols-2 gap-2"
                                >
                                    {sites
                                        .filter((s) =>
                                            selectedSiteIds.includes(s.id),
                                        )
                                        .map((site) => (
                                            <div
                                                key={site.id}
                                                className="flex items-center space-x-2 rounded-md border p-3"
                                            >
                                                <RadioGroupItem
                                                    value={site.id.toString()}
                                                    id={`primary-${site.id}`}
                                                />
                                                <Label
                                                    htmlFor={`primary-${site.id}`}
                                                    className="flex-1 cursor-pointer text-sm"
                                                >
                                                    {site.site_name}
                                                </Label>
                                            </div>
                                        ))}
                                </RadioGroup>
                            </div>
                        )}

                        {/* Error message */}
                        {error && (
                            <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
                                {error}
                            </div>
                        )}

                        {/* Search */}
                        <div className="relative">
                            <Input
                                placeholder="Search sites..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pr-8"
                            />
                        </div>

                        {/* Sites list */}
                        <ScrollArea className="max-h-[300px] rounded-md border">
                            {loading ? (
                                <div className="flex h-full items-center justify-center">
                                    <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
                                </div>
                            ) : (
                                <div className="space-y-2 p-4">
                                    {filteredSites.length > 0 ? (
                                        filteredSites.map((site) => (
                                            <div
                                                key={site.id}
                                                className="flex items-center space-x-3 rounded-md p-2 hover:bg-muted"
                                            >
                                                <Checkbox
                                                    id={`site-${site.id}`}
                                                    checked={selectedSiteIds.includes(
                                                        site.id,
                                                    )}
                                                    onCheckedChange={() =>
                                                        handleSiteToggle(
                                                            site.id,
                                                        )
                                                    }
                                                />
                                                <label
                                                    htmlFor={`site-${site.id}`}
                                                    className="flex flex-1 cursor-pointer items-center gap-2"
                                                >
                                                    <span className="font-medium">
                                                        {site.site_name}
                                                    </span>
                                                    {site.id ===
                                                        primarySiteId && (
                                                        <Badge variant="default">
                                                            <Star className="mr-1 h-3 w-3 fill-current" />
                                                            Primary
                                                        </Badge>
                                                    )}
                                                </label>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-center text-sm text-muted-foreground">
                                            No sites found
                                        </div>
                                    )}
                                </div>
                            )}
                        </ScrollArea>
                    </div>

                    <DrawerFooter>
                        <Button
                            onClick={handleSave}
                            disabled={
                                saving ||
                                !isDirty ||
                                selectedSiteIds.length === 0 ||
                                !primarySiteId
                            }
                        >
                            {saving ? (
                                <>
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <Save className="mr-2 h-4 w-4" />
                                    Save Changes
                                </>
                            )}
                        </Button>
                        <DrawerClose asChild>
                            <Button variant="outline" disabled={saving}>
                                Cancel
                            </Button>
                        </DrawerClose>
                    </DrawerFooter>
                </div>
            </DrawerContent>
        </Drawer>
    );
}
