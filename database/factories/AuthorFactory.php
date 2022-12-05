<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Author::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
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
