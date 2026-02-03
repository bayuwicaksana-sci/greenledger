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
        return true;
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
                'max:255',
                'unique:coa_accounts,account_code,'.
                $coaId.
                ',id,site_id,'.
                $siteId,
            ];

            $rules['account_type'] = [
                'sometimes',
                'required',
                'string',
                'in:REVENUE,EXPENSE',
            ];
        } else {
            // Add prohibited rules for locked fields
            $rules['account_code'] = [
                'sometimes',
                'required',
                'string',
                'max:255',
                'in:'.$coa->account_code, // Must match current value
            ];

            $rules['account_type'] = [
                'sometimes',
                'required',
                'string',
                'in:'.$coa->account_type, // Must match current value
            ];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        $coa = $this->route('coa');

        return [
            'account_code.unique' => 'An account with this code already exists for the selected site.',
            'account_code.in' => 'Account code cannot be changed after transactions have been recorded.',
            'account_type.in' => $coa->isLocked()
                ? 'Account type cannot be changed after transactions have been recorded.'
                : 'Account type must be either REVENUE or EXPENSE.',
            'parent_account_id.exists' => 'The selected parent account does not exist.',
        ];
    }
}
