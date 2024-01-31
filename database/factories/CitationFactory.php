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
        $author1 = substr($this->faker->lastName(), 0, 1).' '.$this->faker->firstName();
        $author2 = substr($this->faker->lastName(), 0, 1).' '.$this->faker->firstName();
        $author = $author1.', '.$author2;

        return [
            'doi' => "10.102X/acs.jnatprod.1c01SS",
            'title' => $this->faker->title(),
            'authors' => $author,
            'citation_text' => $this->faker->text(),
        ];
    }
}
