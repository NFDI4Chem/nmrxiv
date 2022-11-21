<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array
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
