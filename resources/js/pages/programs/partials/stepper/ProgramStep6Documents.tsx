import type { UseFormReturn } from '@inertiajs/react';
import { Upload, X, FileText } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';

interface Props {
    form: UseFormReturn<any>;
}

export default function ProgramStep6Documents({ form }: Props) {
    const { data, setData, errors } = form;

    const handlePlotMapChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setData('plot_map', file);
        }
    };

    const removePlotMap = () => {
        setData('plot_map', null);
        const input = document.getElementById(
            'plot_map_input',
        ) as HTMLInputElement;
        if (input) {
            input.value = '';
        }
    };

    const handleReferenceFilesChange = (
        e: React.ChangeEvent<HTMLInputElement>,
    ) => {
        const files = Array.from(e.target.files || []);
        const currentFiles = Array.isArray(data.reference_files)
            ? data.reference_files
            : [];
        const newFiles = [...currentFiles, ...files].slice(0, 5); // Max 5 files
        setData('reference_files', newFiles);
    };

    const removeReferenceFile = (index: number) => {
        const currentFiles = Array.isArray(data.reference_files)
            ? data.reference_files
            : [];
        const newFiles = currentFiles.filter(
            (_: any, i: number) => i !== index,
        );
        setData('reference_files', newFiles);
    };

    return (
        <div className="space-y-6">
            <div className="rounded-lg border border-muted bg-muted/50 p-4">
                <h4 className="mb-2 font-medium">Supporting Documents</h4>
                <p className="text-sm text-muted-foreground">
                    Upload plot map and reference documents (optional)
                </p>
            </div>

            {/* Plot Map Upload */}
            <div className="space-y-3">
                <Label>Plot Map</Label>
                <div className="rounded-lg border-2 border-dashed p-6">
                    {!data.plot_map ? (
                        <div className="flex flex-col items-center justify-center space-y-3">
                            <Upload className="h-10 w-10 text-muted-foreground" />
                            <div className="text-center">
                                <Label
                                    htmlFor="plot_map_input"
                                    className="cursor-pointer text-sm font-medium text-primary hover:underline"
                                >
                                    Click to upload plot map
                                </Label>
                                <p className="text-xs text-muted-foreground">
                                    JPG, PNG, or GIF (max 5MB)
                                </p>
                            </div>
                            <Input
                                id="plot_map_input"
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                onChange={handlePlotMapChange}
                                className="hidden"
                            />
                        </div>
                    ) : (
                        <div className="flex items-center justify-between rounded-lg bg-muted p-3">
                            <div className="flex items-center gap-3">
                                <FileText className="h-8 w-8 text-muted-foreground" />
                                <div>
                                    <p className="text-sm font-medium">
                                        {data.plot_map.name}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {(
                                            data.plot_map.size /
                                            1024 /
                                            1024
                                        ).toFixed(2)}{' '}
                                        MB
                                    </p>
                                </div>
                            </div>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                onClick={removePlotMap}
                            >
                                <X className="h-4 w-4 text-red-500" />
                            </Button>
                        </div>
                    )}
                </div>
                {errors.plot_map && (
                    <p className="text-sm text-red-500">{errors.plot_map}</p>
                )}
            </div>

            {/* Reference Files Upload */}
            <div className="space-y-3">
                <div className="flex items-center justify-between">
                    <Label>Reference Files</Label>
                    <Badge variant="secondary" className="text-xs">
                        {Array.isArray(data.reference_files)
                            ? data.reference_files.length
                            : 0}{' '}
                        / 5 files
                    </Badge>
                </div>

                <div className="rounded-lg border-2 border-dashed p-6">
                    <div className="flex flex-col items-center justify-center space-y-3">
                        <Upload className="h-10 w-10 text-muted-foreground" />
                        <div className="text-center">
                            <Label
                                htmlFor="reference_files_input"
                                className="cursor-pointer text-sm font-medium text-primary hover:underline"
                            >
                                Click to upload reference files
                            </Label>
                            <p className="text-xs text-muted-foreground">
                                PDF only (max 10MB each, up to 5 files)
                            </p>
                        </div>
                        <Input
                            id="reference_files_input"
                            type="file"
                            accept="application/pdf"
                            multiple
                            onChange={handleReferenceFilesChange}
                            className="hidden"
                            disabled={
                                Array.isArray(data.reference_files) &&
                                data.reference_files.length >= 5
                            }
                        />
                    </div>
                </div>

                {/* Reference Files List */}
                {Array.isArray(data.reference_files) &&
                    data.reference_files.length > 0 && (
                        <div className="space-y-2">
                            {data.reference_files.map((file: any, index: number) => (
                                <div
                                    key={index}
                                    className="flex items-center justify-between rounded-lg bg-muted p-3"
                                >
                                    <div className="flex items-center gap-3">
                                        <FileText className="h-6 w-6 text-muted-foreground" />
                                        <div>
                                            <p className="text-sm font-medium">
                                                {file.name}
                                            </p>
                                            <p className="text-xs text-muted-foreground">
                                                {(
                                                    file.size /
                                                    1024 /
                                                    1024
                                                ).toFixed(2)}{' '}
                                                MB
                                            </p>
                                        </div>
                                    </div>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        onClick={() => removeReferenceFile(index)}
                                    >
                                        <X className="h-4 w-4 text-red-500" />
                                    </Button>
                                </div>
                            ))}
                        </div>
                    )}

                {errors.reference_files && (
                    <p className="text-sm text-red-500">
                        {errors.reference_files}
                    </p>
                )}
            </div>
        </div>
    );
}
