<?php

namespace Database\Factories;

use App\Models\Study;
use App\Models\Validation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Study::class;

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
            'team_id' => 1,
            'owner_id' => 1,
            'project_id' => 1,
            'license_id' => rand(1, 10),
            'draft_id' => 1,
            'fs_id' => 1,
            'release_date' => null,
            'study_photo_path' => null, //todo: Adjust when studies images field is provided in nmrXiv
            'created_at' => $dt->subDays(rand(1, 10)),
            'updated_at' => $dt->subHours(rand(1, 10)),
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'validation_id' => 1,
            'validation_status' => false,
            'internal_status' => null, //todo: provide varying values
        ];
    }
}
