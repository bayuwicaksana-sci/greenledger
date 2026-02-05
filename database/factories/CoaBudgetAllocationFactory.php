<?php

namespace Database\Factories;

use App\Models\CoaAccount;
use App\Models\FiscalYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoaBudgetAllocation>
 */
class CoaBudgetAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coa_account_id' => CoaAccount::factory(),
            'fiscal_year_id' => FiscalYear::factory(),
            'budget_amount' => $this->faker->randomFloat(2, 1000, 500000),
        ];
    }
}
