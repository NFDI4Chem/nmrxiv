<?php

namespace Database\Factories;

use App\Models\Ticker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticker>
 */
class TickerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['sample', 'molecule', 'dataset']),
            'index' => $this->faker->numberBetween(1, 1000),
            'meta' => null,
        ];
    }

    /**
     * Create a ticker for samples
     */
    public function sample(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'sample',
            ];
        });
    }

    /**
     * Create a ticker for molecules
     */
    public function molecule(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'molecule',
            ];
        });
    }

    /**
     * Create a ticker for datasets
     */
    public function dataset(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'dataset',
            ];
        });
    }

    /**
     * Set a specific index value
     */
    public function withIndex(int $index): Factory
    {
        return $this->state(function (array $attributes) use ($index) {
            return [
                'index' => $index,
            ];
        });
    }
}