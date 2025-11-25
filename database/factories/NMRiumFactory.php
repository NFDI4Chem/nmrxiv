<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NMRiumFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nmrium_info' => '{}',
        ];
    }

    /**
     * Make this NMRium belong to a Dataset
     */
    public function forDataset($dataset = null): static
    {
        return $this->state(fn (array $attributes) => [
            'nmriumable_id' => $dataset?->id ?? \App\Models\Dataset::factory(),
            'nmriumable_type' => \App\Models\Dataset::class,
        ]);
    }

    /**
     * Make this NMRium belong to a Study
     */
    public function forStudy($study = null): static
    {
        return $this->state(fn (array $attributes) => [
            'nmriumable_id' => $study?->id ?? \App\Models\Study::factory(),
            'nmriumable_type' => \App\Models\Study::class,
        ]);
    }
}
