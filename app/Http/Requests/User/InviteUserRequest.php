<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('users.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email', 'unique:user_invitations,email'],
            'full_name' => ['required', 'string', 'max:100'],
            'primary_site_id' => ['required', 'integer', 'exists:sites,id'],
            'additional_site_ids' => ['nullable', 'array'],
            'additional_site_ids.*' => ['integer', 'exists:sites,id'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.unique' => 'A user with this email already exists or has a pending invitation.',
            'full_name.required' => 'Full name is required.',
            'primary_site_id.required' => 'Primary site is required.',
            'role_ids.required' => 'At least one role must be assigned.',
            'role_ids.min' => 'At least one role must be assigned.',
        ];
    }
}
