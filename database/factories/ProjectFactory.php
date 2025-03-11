<?php

namespace Database\Factories;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $name = $this->faker->sentence($nbWords = 4);
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
            'sort_order' => 0,
            'type' => null,  // todo: Adjust when type field is provided in nmrXiv
            'uuid' => Str::uuid(),
            'access' => 'restricted',
            'access_type' => 'viewer',
            'team_id' => 1,
            'owner_id' => 1,
            'license_id' => rand(1, 10),
            'draft_id' => 1,
            'fs_id' => null,
            'release_date' => null,
            'project_photo_path' => null,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'validation_id' => 1,
            'validation_status' => false,
            'schema_version' => 'beta', // todo: provide varying values
            'internal_status' => null, // todo: provide varying values
        ];
    }
}
