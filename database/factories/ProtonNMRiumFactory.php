<?php

namespace Database\Factories;

use App\Models\Dataset;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProtonNMRiumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NMRium::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $url = 'https://nmrxiv.org/datasets/1444/nmriumInfo';

        $response = Http::get($item['url'], [
        ]);
        if($response){
            $nmrium_info = json_encode($response['nmrium_info']);
        }

        return [
            'nmrium_info'    => $nmrium_info,
            'dataset_id'     => Dataset::factory(),
        ];
    }
}
