<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title(),
            'orcid_id' => null,
            'given_name' => $this->faker->firstName(),
            'family_name' => $this->faker->lastName(),
            'email_id' => $this->faker->unique()->safeEmail(),
            'affiliation' => $this->faker->text(),
        ];
    }
}
