<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
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
        $study = Study::factory()->create();
        $name = $study->name.'_sample';
        $slug = Str::slug($name, '-');
        $dt = Carbon::now();
        $x = json_encode('{}');

        return[
            'name' => $name,
            'description' => null,
            'slug' => $slug,
            'sample_type' => null,
            'source' => $x,
            'isa' => $x,
            'study_id' => $study->id,
            'project_id' => Project::factory(),
            'created_at' => $dt->subDays(rand(1, 10)),
            'updated_at' => $dt->subHours(rand(1, 10)),
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
        ];
    }
}
