<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectInvitation>
 */
class ProjectInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['viewer', 'collaborator']),
            'message' => fake()->optional()->sentence(),
            'invited_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the invitation is for a collaborator role.
     */
    public function collaborator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'collaborator',
        ]);
    }

    /**
     * Indicate that the invitation is for a viewer role.
     */
    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'viewer',
        ]);
    }

    /**
     * Indicate that the invitation has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => now()->subDays(8), // Assuming 7-day expiry
        ]);
    }
}
