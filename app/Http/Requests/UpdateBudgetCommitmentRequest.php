<?php

namespace App\Http\Requests;

use App\Models\BudgetCommitment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBudgetCommitmentRequest extends FormRequest
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
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'description' => ['sometimes', 'string', 'max:500'],
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
            'commitment_date' => ['sometimes', 'date'],
        ];
    }

    /**
     * Configure validator to validate status transitions.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->has('status')) {
                $commitment = $this->route('commitment');
                $newStatus = $this->input('status');

                // Valid transitions: PENDING -> COMMITTED, COMMITTED -> RELEASED
                // Cannot go backwards: COMMITTED -> PENDING, RELEASED -> (any)
                $validTransitions = [
                    BudgetCommitment::STATUS_PENDING => [
                        BudgetCommitment::STATUS_COMMITTED,
                    ],
                    BudgetCommitment::STATUS_COMMITTED => [
                        BudgetCommitment::STATUS_RELEASED,
                    ],
                    BudgetCommitment::STATUS_RELEASED => [], // Cannot change from released
                ];

                $currentStatus = $commitment->status;

                // If staying in same status, that's OK
                if ($currentStatus === $newStatus) {
                    return;
                }

                // Check if transition is valid
                if (
                    ! in_array(
                        $newStatus,
                        $validTransitions[$currentStatus] ?? [],
                    )
                ) {
                    $validator
                        ->errors()
                        ->add(
                            'status',
                            "Invalid status transition from {$currentStatus} to {$newStatus}. ".
                                'Valid transitions: PENDING → COMMITTED → RELEASED.',
                        );
                }
            }
        });
    }
}
