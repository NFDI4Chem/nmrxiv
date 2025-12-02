<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Validation>
 */
class ValidationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report' => [
                'project' => [
                    'status' => fake()->boolean(),
                    'title' => fake()->boolean(),
                    'description' => fake()->boolean(),
                    'authors' => fake()->boolean(),
                    'affiliation' => fake()->boolean(),
                    'license' => fake()->boolean(),
                    'keywords' => fake()->boolean(),
                    'studies' => [
                        ['status' => fake()->boolean()],
                        ['status' => fake()->boolean()],
                    ],
                ],
                'missing' => [],
                'errors' => [],
                'version' => 1,
            ],
        ];
    }

    /**
     * Indicate that the validation has passed.
     */
    public function passed(): static
    {
        return $this->state(fn (array $attributes) => [
            'report' => [
                'project' => [
                    'status' => true,
                    'title' => true,
                    'description' => true,
                    'authors' => true,
                    'affiliation' => true,
                    'license' => true,
                    'keywords' => true,
                    'studies' => [
                        ['status' => true],
                        ['status' => true],
                    ],
                ],
                'missing' => [],
                'errors' => [],
                'version' => 1,
            ],
        ]);
    }

    /**
     * Indicate that the validation has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'report' => [
                'project' => [
                    'status' => false,
                    'title' => false,
                    'description' => false,
                    'authors' => false,
                    'affiliation' => false,
                    'license' => false,
                    'keywords' => false,
                    'studies' => [
                        ['status' => false],
                        ['status' => false],
                    ],
                ],
                'missing' => ['title', 'description', 'authors'],
                'errors' => ['Project validation failed'],
                'version' => 1,
            ],
        ]);
    }
}
