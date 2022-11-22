<?php

namespace Database\Factories;

use App\Models\Citation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Citation>
 */
class CitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Citation::class;

    /**
     * Define the model's default state.
     *
     * @return array
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
