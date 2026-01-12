<?php

namespace Database\Factories;

use App\Models\LinkedSocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkedSocialAccountFactory extends Factory
{
    protected $model = LinkedSocialAccount::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider_id' => $this->faker->unique()->numerify('########'),
            'provider_name' => $this->faker->randomElement(['github', 'google', 'orcid']),
        ];
    }

    /**
     * Indicate that the linked account is for GitHub.
     */
    public function github(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider_name' => 'github',
        ]);
    }

    /**
     * Indicate that the linked account is for Google.
     */
    public function google(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider_name' => 'google',
        ]);
    }

    /**
     * Indicate that the linked account is for ORCID.
     */
    public function orcid(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider_name' => 'orcid',
        ]);
    }
}
