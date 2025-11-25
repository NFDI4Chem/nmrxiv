<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ValidationFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'report' => [
                'project' => [
                    'status' => true,
                    'title' => true,
                    'description' => true,
                    'authors' => true,
                    'affiliation' => true,
                    'license' => true,
                    'keywords' => true,
                    'studies' => [],
                ],
                'missing' => [],
                'errors' => [],
                'version' => 1,
            ],
        ];
    }
}
