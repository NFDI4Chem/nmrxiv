<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Author;
use App\Models\Citation;
use App\Models\Draft;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;

class TestFactory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $author = Author::factory()->create();
        // $citation = Citation::factory()->create();

        // $license = License::factory()->create();
        // $announcement = Announcement::factory()->create();
        // $team = Team::factory()->create();
        // $draft = Draft::factory()->create();

        /*$nmrium = NMRium::factory()->count(3)
        ->sequence(fn ($sequence) => ['dataset_id' => $sequence->index])
        ->create()*/

        // $nmrium = NMRium::factory()->create([
        //     'nmrium_info' => NMRiumMockData('13c'),
        //     'dataset_id' => 1
        // ]);

        // $project =  Project::factory()->create();
        // $this->command->line('Author created succesfully..'.$author);

        // $this->command->line('Citation created succesfully..'.$citation);
    }
}
