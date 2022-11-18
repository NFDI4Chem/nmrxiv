<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title(),
            'orcid_id' => '0000-0001-6966-0814',
            'given_name' => $this->faker->firstName(),
            'family_name' => $this->faker->lastName(),
            'email_id' => $this->faker->unique()->safeEmail(),
            'affiliation'=> $this->faker->text(),
        ];
    }

    

}
