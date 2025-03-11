<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => Str::random(20),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addDays(30),
            'message' => $this->faker->text(),
            'user_id' => User::factory(),

        ];
    }
}
