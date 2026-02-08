<?php

namespace App\Http\Requests;

use App\Models\BudgetCommitment;
use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetCommitmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fiscal_year_id' => [
                'required',
                'integer',
                'exists:fiscal_years,id',
            ],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'coa_account_id' => [
                'required',
                'integer',
                'exists:coa_accounts,id',
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:500'],
            'status' => [
                'sometimes',
                'string',
                'in:'.
                BudgetCommitment::STATUS_PENDING.
                ','.
                BudgetCommitment::STATUS_COMMITTED.
                ','.
                BudgetCommitment::STATUS_RELEASED,
            ],
            'commitment_date' => ['required', 'date'],
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Default status to PENDING
        if (! $this->has('status')) {
            $this->merge(['status' => BudgetCommitment::STATUS_PENDING]);
        }

        // Default commitment date to today
        if (! $this->has('commitment_date')) {
            $this->merge(['commitment_date' => now()->format('Y-m-d')]);
        }
    }
}
