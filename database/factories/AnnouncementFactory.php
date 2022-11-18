<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title'         => 'Scheduled-Maintenace',
            'status'        => 'active',            
            'start_time'    => Carbon::now(),
            'end_time'      => Carbon::now()->addDays(30),
            'message'       => $this->faker->text(),
            'user_id'       => User::factory(),

        ];
    }
}
