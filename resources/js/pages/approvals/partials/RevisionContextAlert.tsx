import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { AlertCircle, ArrowUpRight } from 'lucide-react';

interface Props {
    isVisible: boolean;
    lastComment: string | null;
    requesterName: string | null;
    editUrl: string | null;
}

export function RevisionContextAlert({
    isVisible,
    lastComment,
    requesterName,
    editUrl,
}: Props) {
    if (!isVisible) return null;

    return (
        <Alert variant="destructive" className="mb-4 bg-orange-50 border-orange-200 text-orange-900">
            <AlertCircle className="h-4 w-4 stroke-orange-900" />
            <AlertTitle className="mb-2">Action Required: Changes Requested</AlertTitle>
            <AlertDescription>
                <p className="mb-3">
                    <span className="font-semibold">{requesterName || 'Reviewer'}</span> requested changes:
                </p>
                <div className="bg-white/50 p-3 rounded-md border border-orange-100 italic text-sm mb-4 text-orange-800">
                    "{lastComment || 'No comment provided.'}"
                </div>
                {editUrl && (
                    <Button
                        variant="default" // Using default (primary) to highlight action
                        size="sm"
                        className="bg-orange-600 hover:bg-orange-700 text-white border-none"
                        onClick={() => window.location.href = editUrl}
                    >
                        Edit Item
                        <ArrowUpRight className="ml-2 h-3 w-3" />
                    </Button>
                )}
            </AlertDescription>
        </Alert>
    );
}
