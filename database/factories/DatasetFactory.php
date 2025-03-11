<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DatasetFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->word();
        $slug = Str::slug($name, '-');

        return [
            'name' => $name,
            'slug' => $slug,
            'color' => $this->faker->rgbCssColor(),
            'starred' => false,
            'is_public' => false,
            'is_deleted' => false,
            'is_archived' => false,
            'status' => null,
            'process_logs' => null,
            'location' => null, // todo: Adjust when location field is provided in nmrXiv
            'obfuscationcode' => Str::random(40),
            'description' => $this->faker->text(),
            'type' => null,  // todo: Adjust when type field is provided in nmrXiv
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
            'dataset_photo_path' => null, // todo: Adjust when datasets images field is provided in nmrXiv
            'release_date' => null,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'has_nmrium' => null,
            'validation_id' => 1,
            'validation_status' => false,
            'internal_status' => null, // todo: provide varying values
        ];
    }
}
