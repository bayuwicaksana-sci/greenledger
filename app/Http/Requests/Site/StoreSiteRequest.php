<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('sites.create');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-uppercase site_code
        if ($this->has('site_code')) {
            $this->merge([
                'site_code' => strtoupper($this->site_code),
            ]);
        }

        // Construct contact_info from flat phone and email inputs
        if ($this->has('phone') || $this->has('email')) {
            $this->merge([
                'contact_info' => [
                    'phone' => $this->phone,
                    'email' => $this->email,
                ],
            ]);
        }

        // Default is_active to true if not provided
        if (! $this->has('is_active')) {
            $this->merge([
                'is_active' => true,
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
            'site_code' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
                Rule::unique('sites', 'site_code'),
            ],
            'site_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'contact_info' => ['required', 'array'],
            'contact_info.phone' => ['required', 'string', 'max:50'],
            'contact_info.email' => ['required', 'email', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'site_code.required' => 'The site code is required.',
            'site_code.size' => 'The site code must be exactly 3 characters.',
            'site_code.regex' => 'The site code must contain only uppercase letters.',
            'site_code.unique' => 'A site with this code already exists.',
            'site_name.required' => 'The site name is required.',
            'site_name.max' => 'The site name cannot exceed 255 characters.',
            'address.required' => 'The address is required.',
            'address.max' => 'The address cannot exceed 500 characters.',
            'contact_info.phone.required' => 'The phone number is required.',
            'contact_info.phone.max' => 'The phone number cannot exceed 50 characters.',
            'contact_info.email.required' => 'The email is required.',
            'contact_info.email.email' => 'The email must be a valid email address.',
            'contact_info.email.max' => 'The email cannot exceed 255 characters.',
        ];
    }
}
