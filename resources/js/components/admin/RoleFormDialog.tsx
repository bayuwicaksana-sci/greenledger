import axios from 'axios';
import { Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';

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
import { useToast } from '@/hooks/use-toast';
import type { Role } from '@/types';

interface RoleFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    role: Role | null;
    onSuccess: () => void;
}

export function RoleFormDialog({
    open,
    onOpenChange,
    role,
    onSuccess,
}: RoleFormDialogProps) {
    const [name, setName] = useState('');
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState<{ name?: string }>({});
    const { toast } = useToast();

    useEffect(() => {
        if (open && role) {
            setName(role.name);
        } else if (!open) {
            setName('');
            setErrors({});
        }
    }, [open, role]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        setLoading(true);

        try {
            if (role) {
                // Update existing role
                await axios.put(`/api/roles/${role.id}`, { name });
                toast({
                    title: 'Success',
                    description: 'Role renamed successfully',
                });
            } else {
                // Create new role
                await axios.post('/api/roles', { name });
                toast({
                    title: 'Success',
                    description: 'Role created successfully',
                });
            }
            onOpenChange(false);
            onSuccess();
        } catch (error: any) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.errors || {});
            } else {
                toast({
                    title: 'Error',
                    description:
                        error.response?.data?.message || 'Failed to save role',
                    variant: 'destructive',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <form onSubmit={handleSubmit}>
                    <DialogHeader>
                        <DialogTitle>
                            {role ? 'Rename Role' : 'Create New Role'}
                        </DialogTitle>
                        <DialogDescription>
                            {role
                                ? 'Update the role name. Note: System roles cannot be renamed.'
                                : 'Enter a name for the new role. You can assign permissions after creating it.'}
                        </DialogDescription>
                    </DialogHeader>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Role Name</Label>
                            <Input
                                id="name"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="e.g., Project Manager"
                                disabled={loading}
                                className={
                                    errors.name ? 'border-destructive' : ''
                                }
                            />
                            {errors.name && (
                                <p className="text-sm text-destructive">
                                    {errors.name}
                                </p>
                            )}
                        </div>
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
                            type="submit"
                            disabled={loading || !name.trim()}
                        >
                            {loading && (
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                            )}
                            {role ? 'Save Changes' : 'Create Role'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
