import { Head, router } from '@inertiajs/react';
import { Building2, Check, Mail, Shield, X } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';

interface InvitationData {
    id: number;
    email: string;
    full_name: string;
    primary_site_name: string;
    additional_sites: string[];
    roles: string[];
    invited_by_name: string;
    expires_at: string;
}

interface Props {
    invitation: InvitationData;
    token: string;
}

export default function AcceptInvitation({ invitation, token }: Props) {
    console.log(invitation);
    console.log(token);
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    // Password strength indicator
    const getPasswordStrength = (pwd: string) => {
        if (!pwd) return { strength: 0, label: '', color: '' };
        let strength = 0;
        if (pwd.length >= 8) strength++;
        if (pwd.length >= 12) strength++;
        if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
        if (/\d/.test(pwd)) strength++;
        if (/[^a-zA-Z\d]/.test(pwd)) strength++;

        if (strength <= 2)
            return { strength, label: 'Weak', color: 'text-red-500' };
        if (strength <= 3)
            return { strength, label: 'Fair', color: 'text-amber-500' };
        if (strength === 4)
            return { strength, label: 'Good', color: 'text-blue-500' };
        return { strength, label: 'Strong', color: 'text-green-500' };
    };

    const passwordStrength = getPasswordStrength(password);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        router.post(
            `/invitation/${token}`,
            {
                password,
                password_confirmation: passwordConfirmation,
            },
            {
                onError: (errors) => {
                    setErrors(errors);
                    setProcessing(false);
                },
                onSuccess: () => {
                    // Success handled by backend redirect
                },
            },
        );
    };

    return (
        <AuthLayout
            title="Accept Your Invitation"
            description="Complete your account setup to get started"
        >
            <Head title="Accept Invitation" />

            <div className="space-y-6">
                {/* Invitation Details */}
                <div className="space-y-3 rounded-lg border p-4">
                    <div className="flex items-center gap-2">
                        <Mail className="h-4 w-4 text-muted-foreground" />
                        <span className="text-sm">
                            <span className="text-muted-foreground">
                                Email:{' '}
                            </span>
                            <span className="font-medium">
                                {invitation.email}
                            </span>
                        </span>
                    </div>
                    <div className="flex items-center gap-2">
                        <Building2 className="h-4 w-4 text-muted-foreground" />
                        <span className="text-sm">
                            <span className="text-muted-foreground">
                                Site:{' '}
                            </span>
                            <Badge variant="outline">
                                {invitation.primary_site_name}
                            </Badge>
                        </span>
                    </div>
                    <div className="flex items-start gap-2">
                        <Shield className="mt-0.5 h-4 w-4 text-muted-foreground" />
                        <div className="flex-1">
                            <span className="text-sm text-muted-foreground">
                                Roles:{' '}
                            </span>
                            <div className="mt-1 flex flex-wrap gap-1">
                                {invitation.roles.map((role, idx) => (
                                    <Badge key={idx} variant="secondary">
                                        {role}
                                    </Badge>
                                ))}
                            </div>
                        </div>
                    </div>
                    <div className="border-t pt-3 text-xs text-muted-foreground">
                        Invited by {invitation.invited_by_name}
                    </div>
                </div>

                {/* Password Form */}
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid gap-2">
                        <Label htmlFor="password">Create Password *</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            autoFocus
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            autoComplete="new-password"
                            placeholder="Enter a strong password"
                            disabled={processing}
                        />
                        {password && (
                            <div className="space-y-2">
                                <div className="flex items-center gap-2">
                                    <div className="h-1.5 flex-1 overflow-hidden rounded-full bg-muted">
                                        <div
                                            className={`h-full transition-all ${
                                                passwordStrength.strength <= 2
                                                    ? 'bg-red-500'
                                                    : passwordStrength.strength ===
                                                        3
                                                      ? 'bg-amber-500'
                                                      : passwordStrength.strength ===
                                                          4
                                                        ? 'bg-blue-500'
                                                        : 'bg-green-500'
                                            }`}
                                            style={{
                                                width: `${(passwordStrength.strength / 5) * 100}%`,
                                            }}
                                        />
                                    </div>
                                    <span
                                        className={`text-xs font-medium ${passwordStrength.color}`}
                                    >
                                        {passwordStrength.label}
                                    </span>
                                </div>
                                <ul className="space-y-1 text-xs">
                                    <li
                                        className={`flex items-center gap-1 ${password.length >= 8 ? 'text-green-600' : 'text-muted-foreground'}`}
                                    >
                                        {password.length >= 8 ? (
                                            <Check className="h-3 w-3" />
                                        ) : (
                                            <X className="h-3 w-3" />
                                        )}
                                        At least 8 characters
                                    </li>
                                    <li
                                        className={`flex items-center gap-1 ${/[a-z]/.test(password) && /[A-Z]/.test(password) ? 'text-green-600' : 'text-muted-foreground'}`}
                                    >
                                        {/[a-z]/.test(password) &&
                                        /[A-Z]/.test(password) ? (
                                            <Check className="h-3 w-3" />
                                        ) : (
                                            <X className="h-3 w-3" />
                                        )}
                                        Upper and lowercase letters
                                    </li>
                                    <li
                                        className={`flex items-center gap-1 ${/\d/.test(password) ? 'text-green-600' : 'text-muted-foreground'}`}
                                    >
                                        {/\d/.test(password) ? (
                                            <Check className="h-3 w-3" />
                                        ) : (
                                            <X className="h-3 w-3" />
                                        )}
                                        At least one number
                                    </li>
                                </ul>
                            </div>
                        )}
                        <InputError message={errors.password} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password_confirmation">
                            Confirm Password *
                        </Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            value={passwordConfirmation}
                            onChange={(e) =>
                                setPasswordConfirmation(e.target.value)
                            }
                            autoComplete="new-password"
                            placeholder="Re-enter your password"
                            disabled={processing}
                        />
                        {passwordConfirmation &&
                            password !== passwordConfirmation && (
                                <p className="text-xs text-red-500">
                                    Passwords do not match
                                </p>
                            )}
                        <InputError message={errors.password_confirmation} />
                    </div>

                    <Button
                        type="submit"
                        className="w-full"
                        disabled={
                            processing ||
                            !password ||
                            !passwordConfirmation ||
                            password !== passwordConfirmation
                        }
                    >
                        {processing && <Spinner />}
                        Complete Setup
                    </Button>
                </form>
            </div>
        </AuthLayout>
    );
}
