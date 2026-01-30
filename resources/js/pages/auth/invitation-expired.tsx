import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { Head } from '@inertiajs/react';
import { AlertCircle, Mail } from 'lucide-react';

interface Props {
    email: string;
}

export default function InvitationExpired({ email }: Props) {
    return (
        <AuthLayout
            title="Invitation Expired"
            description="This invitation link is no longer valid"
        >
            <Head title="Invitation Expired" />

            <div className="space-y-6">
                <div className="flex flex-col items-center justify-center space-y-4 rounded-lg border border-destructive/50 bg-destructive/10 p-6 text-center">
                    <div className="rounded-full bg-destructive/20 p-3">
                        <AlertCircle className="h-8 w-8 text-destructive" />
                    </div>
                    <div className="space-y-2">
                        <h3 className="font-semibold">Invitation Expired</h3>
                        <p className="text-sm text-muted-foreground">
                            The invitation link you're trying to use has
                            expired. User invitations are valid for 7 days from
                            the time they are sent.
                        </p>
                    </div>
                </div>

                <div className="space-y-3 rounded-lg border p-4">
                    <div className="flex items-center gap-2 text-sm">
                        <Mail className="h-4 w-4 text-muted-foreground" />
                        <span className="text-muted-foreground">
                            Invitation was sent to:{' '}
                        </span>
                        <span className="font-medium">{email}</span>
                    </div>
                </div>

                <div className="space-y-4 rounded-lg border bg-muted/50 p-4">
                    <h4 className="text-sm font-semibold">What to do next?</h4>
                    <ul className="space-y-2 text-sm text-muted-foreground">
                        <li className="flex gap-2">
                            <span>•</span>
                            <span>
                                Contact your system administrator to request a
                                new invitation
                            </span>
                        </li>
                        <li className="flex gap-2">
                            <span>•</span>
                            <span>
                                Make sure to accept invitations within 7 days to
                                avoid expiration
                            </span>
                        </li>
                        <li className="flex gap-2">
                            <span>•</span>
                            <span>
                                Check your spam folder if you haven't received
                                the new invitation
                            </span>
                        </li>
                    </ul>
                </div>

                <div className="text-center text-sm text-muted-foreground">
                    Need help?{' '}
                    <a
                        href="mailto:support@greenledger.com"
                        className="font-medium text-primary hover:underline"
                    >
                        Contact Support
                    </a>
                </div>
            </div>
        </AuthLayout>
    );
}
