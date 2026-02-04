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
            'sequence_number' => [
                'required',
                'string',
                'max:5',
                'regex:/^[0-9]+$/',
            ],
            // Check uniqueness of the combination
            // Ideally we want unique(site_id, account_code, category, subcategory, sequence_number)
            // But simple unique rule on account_code alone won't work anymore if 5211 is reused for different categories
            // We'll rely on app logic or manual check for compound unique.
            // Let's keep unique check on account_code for now to prevent duplicates of the BASE code if they represent unique concepts.
            // For now, we rely on the primary key enforcement or custom logic elsewhere.
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
        ];
    }
}
