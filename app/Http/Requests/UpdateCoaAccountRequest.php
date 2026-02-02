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

        return [
            'account_name' => ['sometimes', 'required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            // Code and Type are usually locked after creation or have stricter checks,
            // but for simple update, we might allow them if they don't conflict.
            // For now, let's assume Code is immutable or checked similarly if passed.
            // If the user tries to update site_id or account_code, ensure uniqueness ignoring this ID.
            'account_code' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'unique:coa_accounts,account_code,' .
                $coaId .
                ',id,site_id,' .
                $siteId,
            ],
        ];
    }
}
