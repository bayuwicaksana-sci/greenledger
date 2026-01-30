import { toast as sonnerToast } from 'sonner';

export function useToast() {
    return {
        toast: ({ title, description, variant }: { title: string; description?: string; variant?: 'default' | 'destructive' }) => {
            if (variant === 'destructive') {
                sonnerToast.error(title, { description });
            } else {
                sonnerToast.success(title, { description });
            }
        },
    };
}

// Also export the toast function directly for non-hook usage
export const toast = sonnerToast;
