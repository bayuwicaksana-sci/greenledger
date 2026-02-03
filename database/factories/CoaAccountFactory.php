<?php

namespace Database\Factories;

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
            'account_code' => $this->faker->unique()->bothify('###-???-###'),
            'account_name' => $this->faker->words(3, true),
            'account_type' => $this->faker->randomElement([
                'REVENUE',
                'EXPENSE',
            ]),
            'hierarchy_level' => 1,
            'short_description' => $this->faker->sentence(),
            'is_active' => true,
            'initial_budget' => $this->faker->randomFloat(2, 0, 500000),
        ];
    }
}
