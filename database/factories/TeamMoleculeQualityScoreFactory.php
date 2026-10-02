<?php

namespace Database\Factories;

use App\Models\Molecule;
use App\Models\Team;
use App\Models\TeamMoleculeQualityScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMoleculeQualityScore>
 */
class TeamMoleculeQualityScoreFactory extends Factory
{
    protected $model = TeamMoleculeQualityScore::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'molecule_id' => Molecule::factory(),
            'tier' => 0,
            'breakdown' => null,
            'rubric_version' => 1,
            'scored_at' => now(),
        ];
    }
}
