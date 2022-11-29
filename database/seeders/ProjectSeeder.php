<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = Str::random();
        $user = User::factory()->withPersonalTeam()->create(['password' => bcrypt($password)]);
        $team = $user->personalTeam();

        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
        ]);

        $studies = Study::factory(2)->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
            'project_id' => $project->id,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at->subMinutes(rand(1, 30)),
        ]);

        foreach ($studies as $study) {
            $sample = Sample::factory()->create([
                'study_id' => $study->id,
                'project_id' => $project->id,
                'created_at' => $study->created_at->addMinutes(rand(1, 10)),
                'updated_at' => $study->created_at->addMinutes(rand(11, 20)),
            ]);

            //todo: add molecules to the sample

            $datasets = Dataset::factory(5)->create([
                'team_id' => $team->id,
                'owner_id' => $user->id,
                'project_id' => $project->id,
                'study_id' => $study->id,
                'created_at' => $project->created_at,
                'updated_at' => $study->updated_at,
            ]);

            $dsTypes = 
            [
                'proton',
                '13c',
                'dept',
                'hmbc',
                'hsqc', ];

            $index = 0;
            foreach ($datasets as $dataset) {
                $dataset->type = $dsTypes[$index];
                $dataset->has_nmrium = true;
                $dataset->save();

                $nmrium = NMRium::factory()->create([
                    'nmrium_info' => NMRiumMockData($dataset->type),
                    'dataset_id' => $dataset->id,
                ]);
                $index = $index + 1;
            }
        }

        $this->command->alert('nmrXiv: Projects table seed successfully');
        $this->command->line('You may log into the project user account using <info>'.$user->email.'</info> and password: <info>'.$password.'</info>');
        $this->command->line('');
    }
}
