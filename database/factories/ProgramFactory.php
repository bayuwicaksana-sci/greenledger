<?php

namespace Database\Factories;

use App\Models\FiscalYear;
use App\Models\Program;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
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
            'program_code' => $this->faker->unique()->bothify('PROG-####'),
            'program_name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'total_budget' => $this->faker->randomFloat(2, 10000, 1000000),
            'status' => Program::STATUS_ACTIVE,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    /**
     * Scope program to a specific fiscal year.
     */
    public function forFiscalYear(int|FiscalYear $fiscalYear): static
    {
        $fiscalYearId =
            $fiscalYear instanceof FiscalYear ? $fiscalYear->id : $fiscalYear;

        return $this->state(fn () => ['fiscal_year_id' => $fiscalYearId]);
    }

    public function draft(): static
    {
        return $this->state(['status' => Program::STATUS_DRAFT]);
    }

    public function active(): static
    {
        return $this->state(['status' => Program::STATUS_ACTIVE]);
    }

    public function completed(): static
    {
        return $this->state(['status' => Program::STATUS_COMPLETED]);
    }

    public function archived(): static
    {
        return $this->state(['status' => Program::STATUS_ARCHIVED]);
    }
}
