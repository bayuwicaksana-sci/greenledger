interface ValidationResult {
    isValid: boolean;
    errors: string[];
}

export const validateStep1 = (data: any): ValidationResult => {
    const errors: string[] = [];

    if (!data.program_code) {
        errors.push('Program code is required');
    }
    if (!data.program_name) {
        errors.push('Program name is required');
    }
    if (!data.fiscal_year_id) {
        errors.push('Fiscal year is required');
    }

    if (data.classification === 'PROGRAM') {
        if (!data.program_category) {
            errors.push('Category is required for programs');
        }
        if (!data.commodity_id) {
            errors.push('Commodity is required for programs');
        }
    }

    if (data.classification === 'NON_PROGRAM') {
        if (!data.non_program_category) {
            errors.push('Non-program category is required');
        }
    }

    return { isValid: errors.length === 0, errors };
};

export const validateStep2 = (data: any): ValidationResult => {
    const errors: string[] = [];

    // Team fields are optional, so this step is always valid
    // But you can add custom logic here if needed

    return { isValid: errors.length === 0, errors };
};

export const validateStep3 = (data: any): ValidationResult => {
    const errors: string[] = [];

    // Scientific background fields are optional
    // Add validation if any become required

    return { isValid: errors.length === 0, errors };
};

export const validateStep4 = (data: any): ValidationResult => {
    const errors: string[] = [];

    // Experimental design fields are optional
    // Add validation if any become required

    return { isValid: errors.length === 0, errors };
};

export const validateStep5 = (data: any): ValidationResult => {
    const errors: string[] = [];

    // Budget and activities validation
    // Most fields are optional, but you can add custom logic

    if (data.budget_items && data.budget_items.length > 0) {
        data.budget_items.forEach((item: any, index: number) => {
            if (!item.category_id) {
                errors.push(`Budget item ${index + 1}: Category is required`);
            }
            if (!item.phase_id) {
                errors.push(`Budget item ${index + 1}: Phase is required`);
            }
            if (!item.item_description) {
                errors.push(
                    `Budget item ${index + 1}: Description is required`,
                );
            }
            if (!item.unit) {
                errors.push(`Budget item ${index + 1}: Unit is required`);
            }
            if (!item.qty || Number(item.qty) <= 0) {
                errors.push(
                    `Budget item ${index + 1}: Quantity must be greater than 0`,
                );
            }
            if (!item.unit_price || Number(item.unit_price) <= 0) {
                errors.push(
                    `Budget item ${index + 1}: Unit price must be greater than 0`,
                );
            }
        });
    }

    if (data.activities && data.activities.length > 0) {
        data.activities.forEach((activity: any, index: number) => {
            if (!activity.activity_name) {
                errors.push(`Activity ${index + 1}: Name is required`);
            }
        });
    }

    return { isValid: errors.length === 0, errors };
};

export const validateStep6 = (data: any): ValidationResult => {
    const errors: string[] = [];

    // File uploads are optional
    // Add validation if files become required

    return { isValid: errors.length === 0, errors };
};

export const validateAllSteps = (
    data: any,
    classification: 'PROGRAM' | 'NON_PROGRAM',
): ValidationResult => {
    const allErrors: string[] = [];

    const step1 = validateStep1(data);
    allErrors.push(...step1.errors);

    if (classification === 'PROGRAM') {
        const step2 = validateStep2(data);
        allErrors.push(...step2.errors);

        const step3 = validateStep3(data);
        allErrors.push(...step3.errors);

        const step4 = validateStep4(data);
        allErrors.push(...step4.errors);
    }

    const step5 = validateStep5(data);
    allErrors.push(...step5.errors);

    const step6 = validateStep6(data);
    allErrors.push(...step6.errors);

    return { isValid: allErrors.length === 0, errors: allErrors };
};
