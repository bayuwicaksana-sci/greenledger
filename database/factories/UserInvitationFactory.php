<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserInvitation>
 */
class UserInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'full_name' => fake()->name(),
            'token' => Str::random(64),
            'invited_by' => User::factory(),
            'primary_site_id' => Site::factory(),
            'additional_site_ids' => null,
            'role_ids' => function () {
                $role = Role::firstOrCreate(['name' => 'RO', 'guard_name' => 'web']);

                return [$role->id];
            },
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }
}
