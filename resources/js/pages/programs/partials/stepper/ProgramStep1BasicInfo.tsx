import type { UseFormReturn } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { FiscalYear } from '@/types';

interface Props {
    form: UseFormReturn<any>;
    fiscalYears: FiscalYear[];
    commodities: any[];
}

export default function ProgramStep1BasicInfo({
    form,
    fiscalYears,
    commodities,
}: Props) {
    const { data, setData, errors } = form;

    return (
        <div className="space-y-6">
            {/* Classification Selector */}
            <div className="space-y-2">
                <Label>
                    Classification <span className="text-red-500">*</span>
                </Label>
                <Select
                    value={data.classification}
                    onValueChange={(val) => setData('classification', val)}
                >
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="PROGRAM">
                            Research Program
                        </SelectItem>
                        <SelectItem value="NON_PROGRAM">
                            Non-Program Activity
                        </SelectItem>
                    </SelectContent>
                </Select>
                {errors.classification && (
                    <p className="text-sm text-red-500">
                        {errors.classification}
                    </p>
                )}
            </div>

            {/* Basic Info Grid */}
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {/* Program Code */}
                <div className="space-y-2">
                    <Label>
                        Program Code <span className="text-red-500">*</span>
                    </Label>
                    <Input
                        value={data.program_code}
                        onChange={(e) =>
                            setData('program_code', e.target.value)
                        }
                        placeholder="e.g., PRG-2024-001"
                    />
                    {errors.program_code && (
                        <p className="text-sm text-red-500">
                            {errors.program_code}
                        </p>
                    )}
                </div>

                {/* Fiscal Year */}
                <div className="space-y-2">
                    <Label>
                        Fiscal Year <span className="text-red-500">*</span>
                    </Label>
                    <Select
                        value={data.fiscal_year_id}
                        onValueChange={(val) => setData('fiscal_year_id', val)}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select fiscal year" />
                        </SelectTrigger>
                        <SelectContent>
                            {fiscalYears.map((fy) => (
                                <SelectItem
                                    key={fy.id}
                                    value={fy.id.toString()}
                                >
                                    {fy.year}{' '}
                                    {fy.is_closed && '(Closed)'}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    {errors.fiscal_year_id && (
                        <p className="text-sm text-red-500">
                            {errors.fiscal_year_id}
                        </p>
                    )}
                </div>
            </div>

            {/* Program Name */}
            <div className="space-y-2">
                <Label>
                    Program Name <span className="text-red-500">*</span>
                </Label>
                <Input
                    value={data.program_name}
                    onChange={(e) => setData('program_name', e.target.value)}
                    placeholder="Enter descriptive program name"
                />
                {errors.program_name && (
                    <p className="text-sm text-red-500">{errors.program_name}</p>
                )}
            </div>

            {/* Program Type */}
            <div className="space-y-2">
                <Label>
                    Program Type <span className="text-red-500">*</span>
                </Label>
                <Select
                    value={data.program_type}
                    onValueChange={(val) => setData('program_type', val)}
                >
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="SINGLE_YEAR">Single Year</SelectItem>
                        <SelectItem value="MULTI_YEAR">Multi Year</SelectItem>
                    </SelectContent>
                </Select>
                {errors.program_type && (
                    <p className="text-sm text-red-500">{errors.program_type}</p>
                )}
            </div>

            {/* Timeline */}
            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div className="space-y-2">
                    <Label>Start Date</Label>
                    <Input
                        type="date"
                        value={data.start_date}
                        onChange={(e) => setData('start_date', e.target.value)}
                    />
                    {errors.start_date && (
                        <p className="text-sm text-red-500">
                            {errors.start_date}
                        </p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label>End Date</Label>
                    <Input
                        type="date"
                        value={data.end_date}
                        onChange={(e) => setData('end_date', e.target.value)}
                    />
                    {errors.end_date && (
                        <p className="text-sm text-red-500">{errors.end_date}</p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label>Estimated Duration (days)</Label>
                    <Input
                        type="number"
                        value={data.estimated_duration_days}
                        onChange={(e) =>
                            setData('estimated_duration_days', e.target.value)
                        }
                        placeholder="e.g., 120"
                        min="0"
                    />
                    {errors.estimated_duration_days && (
                        <p className="text-sm text-red-500">
                            {errors.estimated_duration_days}
                        </p>
                    )}
                </div>
            </div>

            {/* Conditional: Research Program Fields */}
            {data.classification === 'PROGRAM' && (
                <>
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {/* Program Category */}
                        <div className="space-y-2">
                            <Label>
                                Category <span className="text-red-500">*</span>
                            </Label>
                            <Select
                                value={data.program_category}
                                onValueChange={(val) =>
                                    setData('program_category', val)
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="RESEARCH">
                                        Research
                                    </SelectItem>
                                    <SelectItem value="TRIAL">Trial</SelectItem>
                                    <SelectItem value="PRODUCTION">
                                        Production
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.program_category && (
                                <p className="text-sm text-red-500">
                                    {errors.program_category}
                                </p>
                            )}
                        </div>

                        {/* Commodity */}
                        <div className="space-y-2">
                            <Label>
                                Commodity <span className="text-red-500">*</span>
                            </Label>
                            <Select
                                value={data.commodity_id}
                                onValueChange={(val) =>
                                    setData('commodity_id', val)
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select commodity" />
                                </SelectTrigger>
                                <SelectContent>
                                    {commodities.map((commodity) => (
                                        <SelectItem
                                            key={commodity.id}
                                            value={commodity.id.toString()}
                                        >
                                            {commodity.commodity_name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.commodity_id && (
                                <p className="text-sm text-red-500">
                                    {errors.commodity_id}
                                </p>
                            )}
                        </div>
                    </div>
                </>
            )}

            {/* Conditional: Non-Program Fields */}
            {data.classification === 'NON_PROGRAM' && (
                <>
                    <div className="space-y-2">
                        <Label>
                            Non-Program Category{' '}
                            <span className="text-red-500">*</span>
                        </Label>
                        <Input
                            value={data.non_program_category}
                            onChange={(e) =>
                                setData('non_program_category', e.target.value)
                            }
                            placeholder="e.g., Administrative, Maintenance"
                        />
                        {errors.non_program_category && (
                            <p className="text-sm text-red-500">
                                {errors.non_program_category}
                            </p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label>Description</Label>
                        <Textarea
                            value={data.description}
                            onChange={(e) =>
                                setData('description', e.target.value)
                            }
                            placeholder="Describe the non-program activity..."
                            rows={4}
                        />
                        {errors.description && (
                            <p className="text-sm text-red-500">
                                {errors.description}
                            </p>
                        )}
                    </div>
                </>
            )}
        </div>
    );
}
