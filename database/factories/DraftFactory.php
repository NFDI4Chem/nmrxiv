<?php

namespace Database\Factories;

use App\Models\Draft;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draft>
 */
class DraftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $user = User::factory()->withPersonalTeam()->create();
        $key = Str::uuid();
        $environment = env('APP_ENV', 'local');
        $path = $environment.'/'.$user->id.'/drafts/'.$key;
        $name = $this->faker->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name, '-'),
            'description' => $this->faker->text(),
            'relative_url' => '/'.$this->faker->uuid(),
            'path' => $path,
            'key' => Str::uuid(),
            'is_deleted' => $this->faker->boolean(),
            'owner_id' => $user->id,
            'team_id' => Team::factory(),
            'info' => '{}',
            'settings' => '{}',
        ];
    }
}
