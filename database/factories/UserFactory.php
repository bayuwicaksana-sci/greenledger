<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get or create a default site for testing
        $site = Site::firstOrCreate(
            ['site_code' => 'test'],
            [
                'site_name' => 'Test Site',
                'address' => 'Test Address',
                'contact_info' => ['name' => 'Test Contact'],
                'is_active' => true,
            ],
        );

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'primary_site_id' => $site->id,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($user) {
            // Attach the user's primary site to their sites relationship
            if ($user->primary_site_id) {
                $user->sites()->attach($user->primary_site_id, [
                    'granted_at' => now(),
                    'granted_by' => null,
                ]);
            }

            // Create a test role with dashboard permissions if it doesn't exist
            $role = Role::firstOrCreate(
                ['name' => 'Test User', 'guard_name' => 'web'],
            );

            // Ensure the role has dashboard permissions
            $permission = Permission::firstOrCreate(
                ['name' => 'dashboard.view.own', 'guard_name' => 'web'],
            );

            $role->givePermissionTo($permission);

            // Assign the role to the user
            $user->assignRole($role);
        });
    }
}
