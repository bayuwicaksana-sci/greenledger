<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFiscalYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('fiscal-year.manage');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-calculate year from start_date
        if ($this->has('start_date')) {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $this->merge([
                'year' => $startDate->year,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'year' => [
                'required',
                'integer',
                Rule::unique('fiscal_years', 'year'),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'The fiscal year start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The fiscal year end date is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',
            'year.unique' => 'A fiscal year with this year code already exists.',
        ];
    }
}
