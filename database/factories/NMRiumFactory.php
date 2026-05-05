<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\Model;
use App\Models\Study;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
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
            'nmriumable_id' => 1,
            'nmriumable_type' => Dataset::class,
        ];
    }

    /**
     * Make this NMRium belong to a Dataset
     */
    public function forDataset($dataset = null): static
    {
        return $this->state(fn (array $attributes) => [
            'nmriumable_id' => $dataset?->id ?? Dataset::factory(),
            'nmriumable_type' => Dataset::class,
        ]);
    }

    /**
     * Make this NMRium belong to a Study
     */
    public function forStudy($study = null): static
    {
        return $this->state(fn (array $attributes) => [
            'nmriumable_id' => $study?->id ?? Study::factory(),
            'nmriumable_type' => Study::class,
        ]);
    }
}
