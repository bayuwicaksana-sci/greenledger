import { Upload, X } from 'lucide-react';
import { useRef } from 'react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import type { MediaItem } from '@/types/programs';

interface FileUploadFieldProps {
    files: File[];
    existingFiles: MediaItem[];
    onFilesChange: (files: File[]) => void;
    onRemoveExisting: (id: number) => void;
    accept?: string;
    maxFiles?: number;
    label?: string;
}

export function FileUploadField({
    files,
    existingFiles,
    onFilesChange,
    onRemoveExisting,
    accept = '*',
    maxFiles = 5,
    label,
}: FileUploadFieldProps) {
    const inputRef = useRef<HTMLInputElement>(null);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const selected = Array.from(e.target.files ?? []);
        const remaining = maxFiles - existingFiles.length - files.length;
        onFilesChange([...files, ...selected.slice(0, remaining)]);
        // Reset input so the same file can be re-selected
        if (inputRef.current) {
            inputRef.current.value = '';
        }
    };

    const removeNewFile = (index: number) => {
        onFilesChange(files.filter((_, i) => i !== index));
    };

    const totalCount = existingFiles.length + files.length;
    const canAdd = totalCount < maxFiles;

    return (
        <div className="space-y-2">
            {label && <Label>{label}</Label>}

            {/* Existing files from server */}
            {existingFiles.map((file) => (
                <div key={file.id} className="flex items-center justify-between rounded-md border bg-muted/30 px-3 py-2">
                    <span className="text-sm">{file.original_name}</span>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="h-6 w-6 p-0 text-red-500 hover:text-red-600"
                        onClick={() => onRemoveExisting(file.id)}
                    >
                        <X className="h-4 w-4" />
                    </Button>
                </div>
            ))}

            {/* New files staged for upload */}
            {files.map((file, index) => (
                <div key={index} className="flex items-center justify-between rounded-md border bg-muted/30 px-3 py-2">
                    <span className="text-sm">{file.name}</span>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="h-6 w-6 p-0 text-red-500 hover:text-red-600"
                        onClick={() => removeNewFile(index)}
                    >
                        <X className="h-4 w-4" />
                    </Button>
                </div>
            ))}

            {/* Drop zone / trigger */}
            {canAdd && (
                <div
                    className="flex cursor-pointer items-center justify-center rounded-md border border-dashed border-muted-foreground/40 p-4 hover:bg-muted/30 transition-colors"
                    onClick={() => inputRef.current?.click()}
                >
                    <div className="flex flex-col items-center gap-1 text-muted-foreground">
                        <Upload className="h-5 w-5" />
                        <span className="text-xs">
                            Click to upload ({maxFiles - totalCount} remaining)
                        </span>
                    </div>
                </div>
            )}

            <input
                ref={inputRef}
                type="file"
                accept={accept}
                multiple={maxFiles > 1}
                className="hidden"
                onChange={handleFileChange}
            />
        </div>
    );
}
