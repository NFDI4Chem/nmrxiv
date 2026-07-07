<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => Str::random(20),
            'type' => 'announcement',
            'release_version' => null,
            'release_notes' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addDays(30),
            'message' => $this->faker->text(),
            'user_id' => User::factory(),

        ];
    }
}
