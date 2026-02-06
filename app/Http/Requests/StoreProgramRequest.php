<?php

namespace App\Http\Requests;

use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProgramRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Core fields
            'program_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('programs')
                    ->where(function ($query) {
                        return $query->where('site_id', $this->route('site')->id);
                    })
                    ->ignore($this->route('program')),
            ],
            'program_name' => ['required', 'string', 'max:350'],
            'description' => ['nullable', 'string'],
            'fiscal_year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'total_budget' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => [
                'required',
                'string',
                Rule::in([
                    Program::STATUS_DRAFT,
                    Program::STATUS_ACTIVE,
                    Program::STATUS_COMPLETED,
                    Program::STATUS_ARCHIVED,
                ]),
            ],

            // Classification
            'classification' => ['required', 'string', Rule::in([Program::CLASSIFICATION_PROGRAM, Program::CLASSIFICATION_NON_PROGRAM])],
            'program_type' => ['nullable', 'string', Rule::in(['SINGLE_YEAR', 'MULTI_YEAR'])],

            // Research Program specific
            'program_category' => [
                'nullable',
                'string',
                Rule::in(['RESEARCH', 'TRIAL', 'PRODUCTION']),
                'required_if:classification,'.Program::CLASSIFICATION_PROGRAM,
            ],
            'commodity_id' => [
                'nullable',
                'integer',
                'exists:commodities,id',
                'required_if:classification,'.Program::CLASSIFICATION_PROGRAM,
            ],

            // Non-Program specific
            'non_program_category' => [
                'nullable',
                'string',
                'required_if:classification,'.Program::CLASSIFICATION_NON_PROGRAM,
            ],

            // Identity
            'research_associate_id' => ['nullable', 'integer', 'exists:users,id'],
            'research_officer_id' => ['nullable', 'integer', 'exists:users,id'],
            'support_team_member_ids' => ['nullable', 'array'],
            'support_team_member_ids.*' => ['integer', 'exists:users,id'],

            // Timeline
            'planting_start_date' => ['nullable', 'date'],
            'estimated_duration_days' => ['nullable', 'integer', 'min:1'],

            // Harvest
            'harvest_type' => ['nullable', 'string', Rule::in(['single', 'multiple'])],
            'estimated_harvest_date' => ['nullable', 'date', 'required_if:harvest_type,single'],
            'harvest_frequency_value' => ['nullable', 'integer', 'min:1', 'required_if:harvest_type,multiple'],
            'harvest_frequency_unit' => ['nullable', 'string', Rule::in(['days', 'weeks']), 'required_if:harvest_type,multiple'],
            'harvest_event_count' => ['nullable', 'integer', 'min:1', 'required_if:harvest_type,multiple'],
            'first_harvest_date' => ['nullable', 'date', 'required_if:harvest_type,multiple'],
            'last_harvest_date' => ['nullable', 'date'],

            // Dependencies
            'prerequisite_program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'dependency_note' => ['nullable', 'string'],

            // Scientific Background
            'background_text' => ['nullable', 'string'],
            'problem_statement' => ['nullable', 'string'],
            'hypothesis' => ['nullable', 'string'],
            'objectives' => ['nullable', 'array'],
            'objectives.*' => ['string', 'min:1'],
            'journal_references' => ['nullable', 'string'],

            // Experimental Design
            'trial_design' => ['nullable', 'string', Rule::in(['RAL', 'RCBD', 'Split Plot', 'Factorial', 'Strip Plot', 'Lattice', 'Other'])],
            'trial_design_other' => ['nullable', 'string', 'max:100', 'required_if:trial_design,Other'],
            'num_treatments' => ['nullable', 'integer', 'min:1'],
            'num_replications' => ['nullable', 'integer', 'min:1'],
            'num_samples_per_replication' => ['nullable', 'integer', 'min:1'],
            'plot_width_m' => ['nullable', 'numeric', 'min:0'],
            'plot_length_m' => ['nullable', 'numeric', 'min:0'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],

            // Treatments (nested)
            'treatments' => ['nullable', 'array'],
            'treatments.*.treatment_code' => ['required', 'string', 'max:50'],
            'treatments.*.treatment_description' => ['nullable', 'string'],
            'treatments.*.specification' => ['nullable', 'string'],

            // Activities (nested)
            'activities' => ['nullable', 'array'],
            'activities.*.activity_name' => ['required', 'string', 'max:200'],
            'activities.*.description' => ['nullable', 'string'],
            'activities.*.budget_allocation' => ['required', 'numeric', 'min:0'],
            'activities.*.planned_start_date' => ['nullable', 'date'],
            'activities.*.planned_end_date' => ['nullable', 'date', 'after_or_equal:activities.*.planned_start_date'],

            // Budget items (nested)
            'budget_items' => ['nullable', 'array'],
            'budget_items.*.category_id' => ['required', 'integer', 'exists:program_budget_categories,id'],
            'budget_items.*.phase_id' => ['required', 'integer', 'exists:program_budget_phases,id'],
            'budget_items.*.item_description' => ['required', 'string', 'max:255'],
            'budget_items.*.specification' => ['nullable', 'string'],
            'budget_items.*.unit' => ['required', 'string', 'max:50'],
            'budget_items.*.qty' => ['required', 'numeric', 'min:0'],
            'budget_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'budget_items.*.notes' => ['nullable', 'string'],

            // Files
            'plot_map' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'reference_files' => ['nullable', 'array', 'max:5'],
            'reference_files.*' => ['file', 'mimes:pdf', 'max:10240'],
            'existing_reference_file_ids' => ['nullable', 'array'],
            'existing_reference_file_ids.*' => ['integer', 'exists:media,id'],
            'remove_plot_map' => ['nullable', 'boolean'],

            // Lifecycle reasons
            'completion_reason' => ['nullable', 'string', 'required_if:status,'.Program::STATUS_COMPLETED],
            'archive_reason' => ['nullable', 'string', 'required_if:status,'.Program::STATUS_ARCHIVED],
        ];
    }

    /**
     * Add custom validation for unique treatment codes within the submission.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $treatments = $this->input('treatments', []);
            if (! is_array($treatments)) {
                return;
            }

            $codes = array_column($treatments, 'treatment_code');
            $duplicates = array_keys(array_diff_assoc($codes, array_unique($codes)));

            if (! empty($duplicates)) {
                $validator->errors()->add('treatments', 'Treatment codes must be unique within this program.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('status')) {
            $this->merge(['status' => Program::STATUS_DRAFT]);
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'completion_reason.required_if' => 'Completion reason is required when marking a program as completed.',
            'archive_reason.required_if' => 'Archive reason is required when archiving a program.',
        ];
    }
}
