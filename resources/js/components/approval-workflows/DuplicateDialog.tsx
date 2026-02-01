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

interface DuplicateDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    defaultName: string;
    onConfirm: (newName: string) => void;
}

export function DuplicateDialog({
    open,
    onOpenChange,
    defaultName,
    onConfirm,
}: DuplicateDialogProps) {
    const [name, setName] = useState(defaultName);

    // Sync name with defaultName when it changes (e.g., when a different workflow is selected)
    useEffect(() => {
        setName(defaultName);
    }, [defaultName]);

    const handleConfirm = () => {
        if (name.trim()) {
            onConfirm(name.trim());
            onOpenChange(false);
        }
    };

    const handleOpenChange = (isOpen: boolean) => {
        if (isOpen) {
            setName(defaultName);
        }
        onOpenChange(isOpen);
    };

    return (
        <Dialog open={open} onOpenChange={handleOpenChange}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Duplicate Workflow</DialogTitle>
                    <DialogDescription>
                        Enter a name for the duplicated workflow. All steps and
                        settings will be copied.
                    </DialogDescription>
                </DialogHeader>
                <div className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Workflow Name</Label>
                        <Input
                            id="name"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder="Enter workflow name"
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    handleConfirm();
                                }
                            }}
                        />
                    </div>
                </div>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        onClick={handleConfirm}
                        disabled={!name.trim()}
                    >
                        Duplicate
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
