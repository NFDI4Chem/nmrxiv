<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\Project;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftProvisionalDoiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Draft $draft;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'doi.datacite.prefix' => '10.99999',
            'doi.host' => 'https://doi.org',
        ]);

        $this->user = User::factory()->withPersonalTeam()->create();
        $team = $this->user->currentTeam;

        $this->draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $team->id,
            'key' => 'a1b2c3d4',
        ]);

        $validation = Validation::factory()->create();

        $this->project = Project::factory()->create([
            'draft_id' => $this->draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $team->id,
            'validation_id' => $validation->id,
            'provisional_doi' => null,
        ]);
    }

    public function test_store_provisional_doi_creates_and_is_idempotent(): void
    {
        $url = '/dashboard/drafts/'.$this->draft->id.'/provisional-doi';

        $first = $this->actingAs($this->user)->postJson($url);
        $first->assertStatus(200)
            ->assertJsonPath('provisional_doi', '10.99999/nmrxiv.a1b2c3d4')
            ->assertJsonPath('url', 'https://doi.org/10.99999/nmrxiv.a1b2c3d4');

        $this->project->refresh();
        $this->assertSame('10.99999/nmrxiv.a1b2c3d4', $this->project->provisional_doi);

        $second = $this->actingAs($this->user)->postJson($url);
        $second->assertStatus(200)
            ->assertJsonPath('provisional_doi', '10.99999/nmrxiv.a1b2c3d4');
    }

    public function test_destroy_provisional_doi_clears_value(): void
    {
        $this->project->update(['provisional_doi' => '10.99999/nmrxiv.a1b2c3d4']);

        $url = '/dashboard/drafts/'.$this->draft->id.'/provisional-doi';

        $this->actingAs($this->user)->deleteJson($url)->assertNoContent();

        $this->project->refresh();
        $this->assertNull($this->project->provisional_doi);
    }

    public function test_store_creates_project_when_none_exists_for_draft(): void
    {
        $this->project->delete();

        $this->assertNull(Project::query()->where('draft_id', $this->draft->id)->first());

        $url = '/dashboard/drafts/'.$this->draft->id.'/provisional-doi';

        $this->actingAs($this->user)
            ->postJson($url)
            ->assertOk()
            ->assertJsonPath('provisional_doi', '10.99999/nmrxiv.a1b2c3d4')
            ->assertJsonPath('url', 'https://doi.org/10.99999/nmrxiv.a1b2c3d4');

        $created = Project::query()->where('draft_id', $this->draft->id)->first();
        $this->assertNotNull($created);
        $this->assertSame('10.99999/nmrxiv.a1b2c3d4', $created->provisional_doi);
    }

    public function test_store_forbidden_for_non_owner(): void
    {
        $other = User::factory()->withPersonalTeam()->create();

        $url = '/dashboard/drafts/'.$this->draft->id.'/provisional-doi';

        $this->actingAs($other)
            ->postJson($url)
            ->assertForbidden();
    }

    public function test_info_returns_null_project_when_missing(): void
    {
        $this->project->delete();

        $this->actingAs($this->user)
            ->getJson('/dashboard/drafts/'.$this->draft->id.'/info')
            ->assertOk()
            ->assertJsonPath('project', null)
            ->assertJsonPath('studies', []);
    }

    public function test_project_json_includes_provisional_doi_url_when_set(): void
    {
        $this->project->update(['provisional_doi' => '10.99999/nmrxiv.a1b2c3d4']);

        $this->project->refresh();

        $this->assertSame(
            'https://doi.org/10.99999/nmrxiv.a1b2c3d4',
            $this->project->provisional_doi_url
        );

        $array = $this->project->toArray();
        $this->assertArrayHasKey('provisional_doi_url', $array);
        $this->assertSame('https://doi.org/10.99999/nmrxiv.a1b2c3d4', $array['provisional_doi_url']);
    }
}
