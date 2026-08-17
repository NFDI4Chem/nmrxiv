<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerNmriumAccessTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private Study $study;

    private Dataset $dataset;

    private array $nmriumInfo;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->withPersonalTeam()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->personalTeam()->id,
            'is_public' => false,
            'obfuscationcode' => 'reviewer-access-token-abcdefghijklmnopqrstuvwxyz',
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $user->id,
            'team_id' => $user->personalTeam()->id,
            'is_public' => false,
        ]);

        $this->dataset = Dataset::factory()->create([
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'owner_id' => $user->id,
            'team_id' => $user->personalTeam()->id,
            'is_public' => false,
        ]);

        $this->nmriumInfo = [
            'version' => '4',
            'data' => [
                'spectra' => [
                    ['id' => 'spectrum-1'],
                ],
                'molecules' => [],
            ],
        ];

        NMRium::factory()->forStudy($this->study)->create([
            'nmrium_info' => $this->nmriumInfo,
        ]);

        NMRium::factory()->forDataset($this->dataset)->create([
            'nmrium_info' => $this->nmriumInfo,
        ]);
    }

    public function test_guest_cannot_fetch_private_study_nmrium_info(): void
    {
        $this->getJson('/studies/'.$this->study->id.'/nmriumInfo')
            ->assertForbidden();
    }

    public function test_guest_cannot_fetch_private_dataset_nmrium_info(): void
    {
        $this->getJson('/datasets/'.$this->dataset->id.'/nmriumInfo')
            ->assertForbidden();
    }

    public function test_reviewer_obfuscation_code_allows_private_study_nmrium_info(): void
    {
        $this->getJson('/studies/'.$this->study->id.'/nmriumInfo?obfuscationcode='.$this->project->obfuscationcode)
            ->assertOk()
            ->assertJsonPath('version', '4')
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_reviewer_obfuscation_code_allows_private_dataset_nmrium_info(): void
    {
        $this->getJson('/datasets/'.$this->dataset->id.'/nmriumInfo?obfuscationcode='.$this->project->obfuscationcode)
            ->assertOk()
            ->assertJsonPath('version', '4')
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_wrong_obfuscation_code_is_rejected_for_private_study_nmrium_info(): void
    {
        $this->getJson('/studies/'.$this->study->id.'/nmriumInfo?obfuscationcode=not-the-project-code')
            ->assertForbidden();
    }

    public function test_other_project_obfuscation_code_is_rejected_for_private_study_nmrium_info(): void
    {
        $otherProject = Project::factory()->create([
            'owner_id' => $this->project->owner_id,
            'is_public' => false,
            'obfuscationcode' => 'other-project-reviewer-token-abcdefghijklmnopqrstu',
        ]);

        $this->getJson('/studies/'.$this->study->id.'/nmriumInfo?obfuscationcode='.$otherProject->obfuscationcode)
            ->assertForbidden();
    }

    public function test_visiting_reviewer_link_allows_subsequent_nmrium_info_request(): void
    {
        $this->get('/project/'.$this->project->obfuscationcode.'?study='.$this->study->id.'&tab=study')
            ->assertOk();

        $this->getJson('/studies/'.$this->study->id.'/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_visiting_reviewer_link_allows_subsequent_dataset_nmrium_info_request(): void
    {
        $this->get('/project/'.$this->project->obfuscationcode.'?study='.$this->study->id.'&tab=dataset&dataset='.$this->dataset->id)
            ->assertOk();

        $this->getJson('/datasets/'.$this->dataset->id.'/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }
}
