<?php

namespace Database\Factories;

use App\Models\NMRium;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NMRiumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nmrium_info' => '{}',
            'dataset_id' => 1,
        ];
    }
}
