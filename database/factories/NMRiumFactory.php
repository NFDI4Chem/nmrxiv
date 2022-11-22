<?php

namespace Database\Factories;

use App\Models\NMRium;
use App\Models\Dataset;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NMRiumFactory extends Factory
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
        $nmriumData = [
            [
                'type' => 'proton',
                'url'  => 'https://nmrxiv.org/datasets/1444/nmriumInfo'
            ],
            [
                'type' => '13c',
                'url'  => 'https://nmrxiv.org/datasets/1447/nmriumInfo'

            ],
            [
                'type' => 'dept',
                'url'  => 'https://nmrxiv.org/datasets/1448/nmriumInfo'
            ],
            [
                'type' => 'hsqc',
                'url'  => 'https://nmrxiv.org/datasets/1446/nmriumInfo'
            ],
            [
                'type' => 'hmbc',
                'url'  => 'https://nmrxiv.org/datasets/1451/nmriumInfo'
            ]

        ];
        foreach ($nmriumData as $item) {
            $response = Http::get($item['url'], [
            ]);
            if($response){
                $nmrium_info = json_encode($response['nmrium_info']);
            }
            //to be uncommented when Dataset Factory is ready
            /*$dataset = Dataset::Factory()->create([
                'type' => $item['type']
            ]);*/
            return [
                // $NMRium = NMRium::create([
                //     'nmrium_info'    => $nmrium_info,
                //     'dataset_id'     => '1',
                // ])
            ];
            return [
                'nmrium_info'    => $nmrium_info,
                'dataset_id'     => '1',
            ];
        }
    }
}
