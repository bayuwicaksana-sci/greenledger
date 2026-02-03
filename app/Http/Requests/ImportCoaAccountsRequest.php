<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCoaAccountsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('coa.view.all') || $this->user()->can('coa.view.site');
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'rows' => ['required', 'array', 'min:1', 'max:200'],
            'rows.*.site_code' => ['required', 'string', 'max:50'],
            'rows.*.account_code' => ['required', 'string', 'max:50'],
            'rows.*.account_name' => ['required', 'string', 'max:255'],
            'rows.*.account_type' => ['required', 'string', 'in:REVENUE,EXPENSE'],
            'rows.*.short_description' => ['nullable', 'string', 'max:500'],
            'rows.*.parent_account_code' => ['nullable', 'string', 'max:50'],
            'rows.*.is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rows.required' => 'No data rows provided.',
            'rows.min' => 'At least one row is required.',
            'rows.max' => 'Maximum 200 rows allowed per import.',
            'rows.*.account_type.in' => 'Account type must be REVENUE or EXPENSE.',
        ];
    }
}
