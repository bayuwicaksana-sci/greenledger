<?php

namespace Database\Factories;

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
            'program_code' => $this->faker->unique()->bothify('PROG-####'),
            'program_name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'fiscal_year' => now()->year,
            'total_budget' => $this->faker->randomFloat(2, 10000, 1000000),
            'status' => Program::STATUS_ACTIVE,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
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
