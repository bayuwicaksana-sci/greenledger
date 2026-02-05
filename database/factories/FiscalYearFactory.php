<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FiscalYear>
 */
class FiscalYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->unique()->numberBetween(2020, 2030);

        return [
            'year' => $year,
            'start_date' => "{$year}-01-01",
            'end_date' => "{$year}-12-31",
            'is_closed' => false,
        ];
    }

    /**
     * Pin to a specific year and derive dates.
     */
    public function forYear(int $year): static
    {
        return $this->state([
            'year' => $year,
            'start_date' => "{$year}-01-01",
            'end_date' => "{$year}-12-31",
        ]);
    }

    /**
     * Mark the fiscal year as closed.
     */
    public function closed(): static
    {
        return $this->state(['is_closed' => true]);
    }
}
