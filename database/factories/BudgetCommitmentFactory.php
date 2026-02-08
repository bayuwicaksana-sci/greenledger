<?php

namespace Database\Factories;

use App\Models\BudgetCommitment;
use App\Models\CoaAccount;
use App\Models\FiscalYear;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetCommitment>
 */
class BudgetCommitmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fiscal_year_id' => FiscalYear::factory(),
            'program_id' => Program::factory(),
            'coa_account_id' => CoaAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'description' => $this->faker->sentence(),
            'status' => BudgetCommitment::STATUS_PENDING,
            'commitment_date' => $this->faker->date(),
            'committed_by' => User::factory(),
        ];
    }

    /**
     * Set commitment status to pending.
     */
    public function pending(): static
    {
        return $this->state(['status' => BudgetCommitment::STATUS_PENDING]);
    }

    /**
     * Set commitment status to committed.
     */
    public function committed(): static
    {
        return $this->state(['status' => BudgetCommitment::STATUS_COMMITTED]);
    }

    /**
     * Set commitment status to released.
     */
    public function released(): static
    {
        return $this->state(['status' => BudgetCommitment::STATUS_RELEASED]);
    }

    /**
     * Scope commitment to a specific fiscal year.
     */
    public function forFiscalYear(int|FiscalYear $fiscalYear): static
    {
        $fiscalYearId =
            $fiscalYear instanceof FiscalYear ? $fiscalYear->id : $fiscalYear;

        return $this->state(fn () => ['fiscal_year_id' => $fiscalYearId]);
    }
}
