<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Ensure faker is initialized
        if (!$this->faker) {
            $this->faker = \Faker\Factory::create();
        }
        
        $title = $this->faker->sentence(4);
        $slug = Str::slug($title, '-');

        return [
            'title' => $title,
            'slug' => $slug,
            'spdx_id' => Str::random(),
            'url' => $this->faker->url(),
            'description' => $this->faker->text(),
            'body' => $this->faker->text(),
            'category' => Str::random(40),
        ];
    }
}
