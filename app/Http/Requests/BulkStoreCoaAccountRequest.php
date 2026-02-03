<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BulkStoreCoaAccountRequest extends FormRequest
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
            'accounts' => ['required', 'array', 'min:1', 'max:50'],
            'accounts.*.site_id' => ['required', 'exists:sites,id'],
            'accounts.*.account_code' => ['required', 'string', 'max:255'],
            'accounts.*.account_name' => ['required', 'string', 'max:255'],
            'accounts.*.account_type' => [
                'required',
                'string',
                'in:REVENUE,EXPENSE',
            ],
            'accounts.*.short_description' => ['nullable', 'string'],
            'accounts.*.parent_account_id' => [
                'nullable',
                'exists:coa_accounts,id',
            ],
            'accounts.*.parent_temp_id' => ['nullable', 'string', 'max:255'],
            'accounts.*._temp_id' => ['nullable', 'string', 'max:255'],
            'accounts.*.is_active' => ['boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $accounts = $this->input('accounts', []);

            // Validate unique account codes within the batch
            $codesBySite = [];
            foreach ($accounts as $index => $account) {
                $siteId = $account['site_id'] ?? null;
                $code = $account['account_code'] ?? null;

                if ($siteId && $code) {
                    $key = "{$siteId}:{$code}";
                    if (isset($codesBySite[$key])) {
                        $validator
                            ->errors()
                            ->add(
                                "accounts.{$index}.account_code",
                                'Duplicate account code found in batch for this site.',
                            );
                    }
                    $codesBySite[$key] = true;

                    // Check against existing accounts in database
                    $exists = \App\Models\CoaAccount::where('site_id', $siteId)
                        ->where('account_code', $code)
                        ->exists();

                    if ($exists) {
                        $validator
                            ->errors()
                            ->add(
                                "accounts.{$index}.account_code",
                                'This account code already exists for the selected site.',
                            );
                    }
                }
            }

            // Validate parent_temp_id references
            $tempIds = collect($accounts)
                ->pluck('_temp_id')
                ->filter()
                ->all();

            foreach ($accounts as $index => $account) {
                $parentTempId = $account['parent_temp_id'] ?? null;
                if ($parentTempId && ! in_array($parentTempId, $tempIds)) {
                    $validator
                        ->errors()
                        ->add(
                            "accounts.{$index}.parent_temp_id",
                            'Referenced parent account not found in batch.',
                        );
                }
            }
        });
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'accounts.required' => 'At least one account is required.',
            'accounts.array' => 'Accounts must be an array.',
            'accounts.min' => 'At least one account is required.',
            'accounts.max' => 'Cannot create more than 50 accounts at once.',
            'accounts.*.site_id.required' => 'Site is required for all accounts.',
            'accounts.*.site_id.exists' => 'Selected site does not exist.',
            'accounts.*.account_code.required' => 'Account code is required for all accounts.',
            'accounts.*.account_name.required' => 'Account name is required for all accounts.',
            'accounts.*.account_type.required' => 'Account type is required for all accounts.',
            'accounts.*.account_type.in' => 'Account type must be REVENUE or EXPENSE.',
        ];
    }
}
