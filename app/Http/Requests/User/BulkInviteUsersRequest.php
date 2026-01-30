<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class BulkInviteUsersRequest extends FormRequest
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
            'users' => ['required', 'array', 'min:1', 'max:50'],
            'users.*.email' => ['required', 'email', 'distinct', 'unique:users,email', 'unique:user_invitations,email'],
            'users.*.full_name' => ['required', 'string', 'max:100'],
            'users.*.primary_site_id' => ['required', 'integer', 'exists:sites,id'],
            'users.*.additional_site_ids' => ['nullable', 'array'],
            'users.*.additional_site_ids.*' => ['integer', 'exists:sites,id'],
            'users.*.role_ids' => ['required', 'array', 'min:1'],
            'users.*.role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }
}
