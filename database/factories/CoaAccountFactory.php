<?php

namespace Database\Factories;

use App\Models\FiscalYear;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaAccount>
 */
class CoaAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'fiscal_year_id' => FiscalYear::factory(),
            'account_code' => $this->faker->numerify('####'),
            'account_name' => $this->faker->words(3, true),
            'account_type' => $this->faker->randomElement([
                'REVENUE',
                'EXPENSE',
            ]),
            'hierarchy_level' => 1,
            'short_description' => $this->faker->sentence(),
            'abbreviation' => $this->faker->lexify('???'),
            'is_active' => true,
            'initial_budget' => $this->faker->randomFloat(2, 0, 500000),
            'category' => $this->faker->randomElement([
                'PROGRAM',
                'NON_PROGRAM',
            ]),
            'sub_category' => $this->faker->randomElement([
                'Research',
                'Administrative',
                'Financial',
                'Operational',
            ]),
            'tax_applicable' => false,
        ];
    }

    /**
     * Scope COA account to a specific fiscal year.
     */
    public function forFiscalYear(int|FiscalYear $fiscalYear): static
    {
        $fiscalYearId =
            $fiscalYear instanceof FiscalYear ? $fiscalYear->id : $fiscalYear;

        return $this->state(fn () => ['fiscal_year_id' => $fiscalYearId]);
    }
}
