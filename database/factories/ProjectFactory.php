<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Validation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->sentence($nbWords = 4);
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
            'description' => $this->faker->text(),
            'sort_order' => rand(1, 100),
            'type' => null,  //todo: Adjust when type field is provided in nmrXiv
            'uuid' => Str::uuid(),
            'access' => 'restricted',
            'access_type' => 'viewer',
            'team_id' => rand(1, 100),
            'owner_id' => rand(1, 100),
            'license_id' => rand(1, 10),
            'draft_id' => rand(1, 100),
            'fs_id' => null,
            'release_date' => null,
            'project_photo_path' => null,
            'created_at' => $dt->subDays(rand(1, 10)),
            'updated_at' => $dt->subHours(rand(1, 10)),
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'validation_id' => Validation::factory(),
            'validation_status' => false,
            'schema_version' => 'beta', //todo: provide varying values
            'internal_status' => null, //todo: provide varying values
        ];
    }
}
