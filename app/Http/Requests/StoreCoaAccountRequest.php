<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoaAccountRequest extends FormRequest
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
        return [
            'site_id' => ['required', 'exists:sites,id'],
            'account_code' => [
                'required',
                'string',
                'max:255',
                // Unique per site
                'unique:coa_accounts,account_code,NULL,id,site_id,' .
                $this->input('site_id'),
            ],
            'account_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string', 'in:REVENUE,EXPENSE'],
            'short_description' => ['nullable', 'string'],
            'parent_account_id' => ['nullable', 'exists:coa_accounts,id'],
            'is_active' => ['boolean'],
        ];
    }
}
