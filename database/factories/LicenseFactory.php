<?php

namespace Database\Factories;

use App\Models\License;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = License::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'       => 'Creative Commons Zero v1.0 Universal',
            'slug'        => $this->faker->slug(),
            'spdx_id'     => Str::random(),
            'url'         => $this->faker->url(),
            'description' => $this->faker->text(),
            'body'        => $this->faker->text(),
            'category'    => 'Creative Commons',
        ];
    }
}