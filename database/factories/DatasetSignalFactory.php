<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\DatasetSignal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DatasetSignal>
 */
class DatasetSignalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dataset_id' => Dataset::factory(),
            'spectrum_index' => 0,
            'nucleus' => '1H',
            'shift' => $this->faker->randomFloat(4, 0.5, 10),
            'multiplicity' => $this->faker->randomElement(['s', 'd', 't', 'q', 'm']),
            'intensity' => $this->faker->randomFloat(5, 0, 1),
            'is_solvent' => false,
            'source' => 'auto_detected',
        ];
    }

    /**
     * A 13C signal.
     */
    public function carbon(): static
    {
        return $this->state(fn (array $attributes) => [
            'nucleus' => '13C',
            'shift' => $this->faker->randomFloat(4, 10, 210),
            'multiplicity' => null,
        ]);
    }
}
