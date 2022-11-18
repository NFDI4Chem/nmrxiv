<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Citation>
 */
class CitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'doi' => '10.1002/mrc.4737',
            'title' => $this->faker->title(),
            'authors' => $this->faker->firstName().$this->faker->lastName(),
            'abstract' => $this->faker->text(),
            'citation_text' => $this->faker->text(),
        ];
    }
}
