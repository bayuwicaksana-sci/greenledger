<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCoaAccountRequest extends FormRequest
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
        $siteId = $this->input('site_id');

        return [
            'site_id' => ['required', 'exists:sites,id'],
            'account_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('coa_accounts')->where(function ($query) use (
                    $siteId,
                ) {
                    return $query->where('site_id', $siteId);
                }),
            ],
            'account_name' => ['required', 'string', 'max:255'],
            'account_type' => [
                'required',
                'string',
                'in:ASSET,LIABILITY,EQUITY,REVENUE,EXPENSE',
            ],
            'short_description' => ['nullable', 'string'],
            'parent_account_id' => ['nullable', 'exists:coa_accounts,id'],
            'is_active' => ['boolean'],
            'budget_control' => ['boolean'],
            'initial_budget' => ['nullable', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'in:PROGRAM,NON_PROGRAM'],
            'sub_category' => ['nullable', 'string', 'max:50'],
            'typical_usage' => ['nullable', 'string'],
            'tax_applicable' => ['boolean'],
        ];
    }
}
