<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\Validation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DatasetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dataset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word().'dx';
        $slug = Str::slug($name, '-');

        $dt = Carbon::now();

        return[
            'name' => $name,
            'slug' => $slug,
            'color' => $this->faker->rgbCssColor(),
            'starred' => false,
            'is_public' => false,
            'is_deleted' => false,
            'is_archived' => false,
            'status' => null,
            'process_logs' => null,
            'location' => null, //todo: Adjust when location field is provided in nmrXiv
            'url' => Str::random(40),
            'description' => $name,
            'type' => null,  //todo: Adjust when type field is provided in nmrXiv
            'uuid' => Str::uuid(),
            'access' => 'restricted',
            'access_type' => 'viewer',
            'team_id' => 1,
            'owner_id' => 1,
            'project_id' => 1,
            'study_id' => 1,
            'license_id' => rand(1, 10),
            'draft_id' => 1,
            'fs_id' => 1,
            'dataset_photo_path' => null, //todo: Adjust when datasets images field is provided in nmrXiv
            'release_date' => null,
            'created_at' => $dt->subDays(rand(1, 10)),
            'updated_at' => $dt->subHours(rand(1, 10)),
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'has_nmrium' => null,
            'validation_id' => 1,
            'validation_status' => false,
            'internal_status' => null, //todo: provide varying values
        ];
    }
}
