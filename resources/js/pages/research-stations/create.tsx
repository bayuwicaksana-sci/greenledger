import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import MainLayout from '@/layouts/main-layout';
import { index, store, suggestCode } from '@/routes/research-stations';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { ArrowLeft, Check, X } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';

export default function CreateSite() {
    const { data, setData, post, processing, errors } = useForm({
        site_code: '',
        site_name: '',
        address: '',
        phone: '',
        email: '',
        is_active: true,
    });

    const [suggestedCode, setSuggestedCode] = useState('');
    const [isCodeAvailable, setIsCodeAvailable] = useState<boolean | null>(
        null,
    );
    const [checkingAvailability, setCheckingAvailability] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Research Stations',
            href: index().url,
        },
        {
            title: 'Create',
            href: '#',
        },
    ];

    // Debounced auto-suggest for site name
    useEffect(() => {
        if (!data.site_name) {
            setSuggestedCode('');
            return;
        }

        const timer = setTimeout(async () => {
            try {
                const response = await fetch(
                    `${suggestCode().url}?site_name=${encodeURIComponent(data.site_name)}`,
                );
                const result = await response.json();
                setSuggestedCode(result.suggested_code);
            } catch (error) {
                console.error('Failed to fetch suggestion:', error);
            }
        }, 500);

        return () => clearTimeout(timer);
    }, [data.site_name]);

    // Debounced availability check for site code
    useEffect(() => {
        if (!data.site_code || data.site_code.length !== 3) {
            setIsCodeAvailable(null);
            return;
        }

        setCheckingAvailability(true);
        const timer = setTimeout(async () => {
            try {
                const response = await fetch(
                    `${suggestCode().url}?site_name=${data.site_code}`,
                );
                const result = await response.json();
                // If the suggested code is the same as input, check its availability
                // Otherwise, just check by making a dummy request with this code
                const checkResponse = await fetch(
                    `${suggestCode().url}?site_name=${data.site_code}`,
                );
                const checkResult = await checkResponse.json();
                setIsCodeAvailable(checkResult.is_available);
            } catch (error) {
                console.error('Failed to check availability:', error);
                setIsCodeAvailable(null);
            } finally {
                setCheckingAvailability(false);
            }
        }, 300);

        return () => {
            clearTimeout(timer);
            setCheckingAvailability(false);
        };
    }, [data.site_code]);

    const handleUseSuggestion = () => {
        setData('site_code', suggestedCode);
        setSuggestedCode('');
    };

    const handleCodeChange = (value: string) => {
        // Auto-uppercase and limit to 3 characters
        const uppercased = value.toUpperCase().slice(0, 3);
        setData('site_code', uppercased);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url);
    };

    const canSubmit =
        !processing &&
        data.site_code.length === 3 &&
        (isCodeAvailable === true || isCodeAvailable === null);

    return (
        <MainLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Site" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => window.history.back()}
                    >
                        <ArrowLeft className="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 className="text-3xl font-bold">Create Site</h1>
                        <p className="text-muted-foreground">
                            Add a new research station location
                        </p>
                    </div>
                </div>

                {/* Form */}
                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Site Details</CardTitle>
                        <CardDescription>
                            Enter the site information. The system will suggest
                            a site code based on the site name.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Site Name */}
                            <div className="space-y-2">
                                <Label htmlFor="site_name">Site Name</Label>
                                <Input
                                    id="site_name"
                                    type="text"
                                    value={data.site_name}
                                    onChange={(e) =>
                                        setData('site_name', e.target.value)
                                    }
                                    placeholder="e.g., Klaten Research Station"
                                    required
                                />
                                {errors.site_name && (
                                    <p className="text-sm text-destructive">
                                        {errors.site_name}
                                    </p>
                                )}
                            </div>

                            {/* Site Code */}
                            <div className="space-y-2">
                                <Label htmlFor="site_code">Site Code</Label>
                                <div className="flex gap-2">
                                    <div className="relative flex-1">
                                        <Input
                                            id="site_code"
                                            type="text"
                                            value={data.site_code}
                                            onChange={(e) =>
                                                handleCodeChange(e.target.value)
                                            }
                                            placeholder="XXX"
                                            maxLength={3}
                                            className="font-mono uppercase"
                                            required
                                        />
                                        {data.site_code.length === 3 &&
                                            !checkingAvailability && (
                                                <div className="absolute top-1/2 right-3 -translate-y-1/2">
                                                    {isCodeAvailable ===
                                                    true ? (
                                                        <div className="flex items-center gap-1 text-sm text-green-600">
                                                            <Check className="h-4 w-4" />
                                                            <span>
                                                                Available
                                                            </span>
                                                        </div>
                                                    ) : isCodeAvailable ===
                                                      false ? (
                                                        <div className="flex items-center gap-1 text-sm text-destructive">
                                                            <X className="h-4 w-4" />
                                                            <span>
                                                                In use
                                                            </span>
                                                        </div>
                                                    ) : null}
                                                </div>
                                            )}
                                    </div>
                                    {suggestedCode &&
                                        !data.site_code &&
                                        suggestedCode !== data.site_code && (
                                            <Button
                                                type="button"
                                                variant="outline"
                                                onClick={handleUseSuggestion}
                                            >
                                                Use &quot;{suggestedCode}&quot;
                                            </Button>
                                        )}
                                </div>
                                {errors.site_code && (
                                    <p className="text-sm text-destructive">
                                        {errors.site_code}
                                    </p>
                                )}
                                <p className="text-sm text-muted-foreground">
                                    3-letter unique code (e.g., KLA, YOG, MGG)
                                </p>
                            </div>

                            {/* Address */}
                            <div className="space-y-2">
                                <Label htmlFor="address">Address</Label>
                                <Textarea
                                    id="address"
                                    value={data.address}
                                    onChange={(e) =>
                                        setData('address', e.target.value)
                                    }
                                    placeholder="Full address of the research station"
                                    rows={3}
                                    required
                                />
                                {errors.address && (
                                    <p className="text-sm text-destructive">
                                        {errors.address}
                                    </p>
                                )}
                            </div>

                            {/* Phone */}
                            <div className="space-y-2">
                                <Label htmlFor="phone">Phone Number</Label>
                                <Input
                                    id="phone"
                                    type="tel"
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData('phone', e.target.value)
                                    }
                                    placeholder="+62 xxx xxxx xxxx"
                                    required
                                />
                                {errors.phone && (
                                    <p className="text-sm text-destructive">
                                        {errors.phone}
                                    </p>
                                )}
                            </div>

                            {/* Email */}
                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) =>
                                        setData('email', e.target.value)
                                    }
                                    placeholder="site@example.com"
                                    required
                                />
                                {errors.email && (
                                    <p className="text-sm text-destructive">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            {/* Is Active */}
                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="is_active"
                                    checked={data.is_active}
                                    onCheckedChange={(checked) =>
                                        setData('is_active', checked === true)
                                    }
                                />
                                <Label
                                    htmlFor="is_active"
                                    className="cursor-pointer font-normal"
                                >
                                    Site is active
                                </Label>
                            </div>

                            {/* Submit Buttons */}
                            <div className="flex gap-4">
                                <Button
                                    type="submit"
                                    disabled={!canSubmit}
                                    className="flex-1"
                                >
                                    {processing
                                        ? 'Creating...'
                                        : 'Create Site'}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => window.history.back()}
                                >
                                    Cancel
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}
