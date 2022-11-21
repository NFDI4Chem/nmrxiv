<?php

namespace Database\Factories;

use App\Models\Validation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValidationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Validation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dt = Carbon::now();

        return
        [
            'report' => $this->faker->sentence(), //todo: adjust the structure of the outout to real report values
            'score' => 0, //todo: provide varying scores
            'created_at' => $dt->subDays(rand(1, 10)),
            'updated_at' => $dt->subHours(rand(1, 10)),
        ];
    }
}
