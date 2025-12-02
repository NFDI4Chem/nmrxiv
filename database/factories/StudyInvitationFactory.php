<?php

namespace Database\Factories;

use App\Models\Study;
use App\Models\StudyInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyInvitationFactory extends Factory
{
    protected $model = StudyInvitation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'study_id' => Study::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'role' => $this->faker->randomElement(['viewer', 'collaborator', 'reviewer']),
            'message' => $this->faker->optional()->sentence(),
            'invited_by' => $this->faker->optional()->safeEmail(),
        ];
    }

    /**
     * Factory state for viewer role
     */
    public function viewer(): self
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'viewer',
        ]);
    }

    /**
     * Factory state for collaborator role
     */
    public function collaborator(): self
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'collaborator',
        ]);
    }

    /**
     * Factory state for reviewer role
     */
    public function reviewer(): self
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'reviewer',
        ]);
    }

    /**
     * Factory state with a specific inviter
     */
    public function invitedBy(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'invited_by' => $user->email,
        ]);
    }

    /**
     * Factory state for a specific study
     */
    public function forStudy(Study $study): self
    {
        return $this->state(fn (array $attributes) => [
            'study_id' => $study->id,
        ]);
    }
}
