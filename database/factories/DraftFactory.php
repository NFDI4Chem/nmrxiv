<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draft>
 */
class DraftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $key = Str::uuid();
        $environment = env('APP_ENV', 'local');
        $path = $environment.'/'.$user->id.'/drafts/'.$key;

        return [
            'name'            => Str::random(),
            'slug'            => Str::random(),
            'description'     => $this->faker->text(),
            'relative_url'    => "/".$this->faker->uuid(),
            'path'            => $path,
            'key'             => Str::uuid(),
            'is_deleted'      => "False",
            'owner_id'        => $user->id,
            'team_id'         => Team::factory(),
            'info'            => "{}",
            'settings'        => "{}",
        ];
    }
}
