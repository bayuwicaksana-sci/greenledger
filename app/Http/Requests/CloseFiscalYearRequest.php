<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseFiscalYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('fiscal-year.close');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'options' => ['nullable', 'array'],
            'options.archive_completed_programs' => ['boolean'],
            'options.block_new_programs' => ['boolean'],
            'options.block_new_transactions' => ['boolean'],
            'options.generate_year_end_report' => ['boolean'],
            'options.send_notifications' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'options.array' => 'The closure options must be an array.',
            'options.*.boolean' => 'Each option must be true or false.',
            'notes.max' => 'The notes field cannot exceed 1000 characters.',
        ];
    }
}
