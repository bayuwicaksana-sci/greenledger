<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoaAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('coa.view.all') ||
            $this->user()->can('coa.view.site');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $coaId = $this->route('coa')->id;
        $siteId = $this->input('site_id', $this->route('coa')->site_id);
        $coa = $this->route('coa');

        $rules = [
            'account_name' => ['sometimes', 'required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'abbreviation' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'parent_account_id' => ['nullable', 'exists:coa_accounts,id'],
            'initial_budget' => ['nullable', 'numeric', 'min:0'],
        ];

        // Lock account_code and account_type if account has transactions
        if (! $coa->isLocked()) {
            $rules['account_code'] = [
                'sometimes',
                'required',
                'string',
                'max:20',
                'unique:coa_accounts,account_code,'.
                $coaId.
                ',id,site_id,'.
                $siteId.
                ',fiscal_year_id,'.
                $coa->fiscal_year_id,
            ];

            $rules['account_type'] = [
                'sometimes',
                'required',
                'string',
                'in:ASSET,LIABILITY,EQUITY,REVENUE,EXPENSE',
            ];
        } else {
            $rules['account_code'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                'in:'.$coa->account_code,
            ];

            $rules['account_type'] = [
                'sometimes',
                'required',
                'string',
                'in:'.$coa->account_type,
            ];
        }

        // CRITICAL: fiscal_year_id cannot be changed after creation
        $rules['fiscal_year_id'] = [
            'sometimes',
            'required',
            'integer',
            'in:'.$coa->fiscal_year_id,
        ];

        $rules['budget_control'] = ['boolean'];
        $rules['category'] = [
            'sometimes',
            'required',
            'string',
            'in:PROGRAM,NON_PROGRAM',
        ];
        $rules['sub_category'] = ['sometimes', 'nullable', 'string', 'max:50'];
        $rules['typical_usage'] = ['sometimes', 'nullable', 'string'];
        $rules['tax_applicable'] = ['sometimes', 'boolean'];

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        $coa = $this->route('coa');

        return [
            'account_code.unique' => 'An account with this code already exists for the selected site and fiscal year.',
            'account_code.in' => 'Account code cannot be changed after transactions have been recorded.',
            'account_type.in' => $coa->isLocked()
                ? 'Account type cannot be changed after transactions have been recorded.'
                : 'Account type must be one of ASSET, LIABILITY, EQUITY, REVENUE, or EXPENSE.',
            'fiscal_year_id.in' => 'Fiscal year cannot be changed after account creation.',
            'parent_account_id.exists' => 'The selected parent account does not exist.',
        ];
    }
}
