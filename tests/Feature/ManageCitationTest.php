<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Citation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageCitationTest extends TestCase
{
    /**
     * Test if a citation can be updated.
     *
     * @return void
     */
    public function test_citation_can_be_added_and_updated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $citation = Citation::factory()->create();

        $project->citations()->sync([$citation->id => ['user' => $user->id]]);

        $body = [
            'citations' => [[
                'doi' => $citation->doi,
                'title' => $citation->title,
                'authors' => $citation->authors,
                'abstract' => $citation->abstract,
                'citation_text' => $citation->citation_text,
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('citations/'.$project->id, $body);

        $response->assertStatus(200);
    }
    /**
     * Test if a citation can be deleted.
     *
     * @return void
     */
    public function test_citation_can_be_deleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $citation = Citation::factory()->create();

        $project->citations()->sync([$citation->id => ['user' => $user->id]]);

        $body = [
            'citations' => [[
                'id'  => $citation->id,
                'doi' => $citation->doi,
                'title' => $citation->title,
                'authors' => $citation->authors,
                'abstract' => $citation->abstract,
                'citation_text' => $citation->citation_text,
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('citations/'.$project->id.'/delete', $body);

        $response->assertStatus(200);
    }
}
