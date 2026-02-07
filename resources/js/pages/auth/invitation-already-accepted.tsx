import { Head } from '@inertiajs/react';
import { CheckCircle2, LogIn } from 'lucide-react';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';

export default function InvitationAlreadyAccepted() {
    return (
        <AuthLayout
            title="Invitation Already Accepted"
            description="This invitation has been used"
        >
            <Head title="Invitation Already Accepted" />

            <div className="space-y-6">
                <div className="flex flex-col items-center justify-center space-y-4 rounded-lg border border-green-500/50 bg-green-500/10 p-6 text-center">
                    <div className="rounded-full bg-green-500/20 p-3">
                        <CheckCircle2 className="h-8 w-8 text-green-600" />
                    </div>
                    <div className="space-y-2">
                        <h3 className="font-semibold">
                            Account Already Created
                        </h3>
                        <p className="text-sm text-muted-foreground">
                            This invitation has already been accepted and an
                            account has been created.
                        </p>
                    </div>
                </div>

                <div className="space-y-4 rounded-lg border bg-muted/50 p-4">
                    <h4 className="text-sm font-semibold">What to do next?</h4>
                    <ul className="space-y-2 text-sm text-muted-foreground">
                        <li className="flex gap-2">
                            <span>•</span>
                            <span>
                                If you already have login credentials, use the
                                login page to access your account
                            </span>
                        </li>
                        <li className="flex gap-2">
                            <span>•</span>
                            <span>
                                If you forgot your password, you can reset it
                                from the login page
                            </span>
                        </li>
                        <li className="flex gap-2">
                            <span>•</span>
                            <span>
                                If you didn't create this account, contact your
                                system administrator immediately
                            </span>
                        </li>
                    </ul>
                </div>

                <Button asChild className="w-full">
                    <TextLink href={login()}>
                        <LogIn className="mr-2 h-4 w-4" />
                        Go to Login
                    </TextLink>
                </Button>

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
