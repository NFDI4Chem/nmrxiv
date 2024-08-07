<?php

namespace Database\Factories;

use App\Models\Sample;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SampleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sample::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->sentence($nbWords = 2);
        $slug = Str::slug($name, '-');
        $x = json_encode('{}');

        return [
            'name' => $name,
            'description' => null,
            'slug' => $slug,
            'sample_type' => null,
            'source' => $x,
            'isa' => $x,
            'study_id' => 1,
            'project_id' => 1,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
        ];
    }
}
