<?php

namespace Tests\Feature;

use App\Models\Citation;
use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class ManageCitationsTest extends TestCase
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

        $body = $this->prepareBody($citation);

        //Update citation
        $response = $this->updateCitation($body, $project->id);

        $response->assertStatus(200);

        //Check if entry got created in DB
        $project = $project->fresh();
        $citations = $project->citations->toArray();
        $this->assertDatabaseHas('citation_project', $citations[0]['pivot']);
        unset($citations[0]['pivot']);
        $this->assertDatabaseHas('citations', $citations[0]);
    }

    /**
     * Test if a citation can be deleted.
     *
     * @return void
     */
    public function test_citation_can_be_detached()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $citation = Citation::factory()->create();

        $project->citations()->sync([$citation->id => ['user' => $user->id]]);
        $project = $project->fresh();
        $citations = $project->citations->toArray();

        $body = $this->prepareBody($citation);

        //Detach citation
        $response = $this->detachCitation($body, $project->id);
        $response->assertStatus(200);

        //Check if entry got deleted from DB
        $this->assertDatabaseMissing('citation_project', $citations[0]['pivot']);
    }

    /**
     * Test if the citation cannot be updated or detached by the reviewer
     *
     * @return void
     */
    public function test_citation_cannot_be_updated_or_deleted_by_reviewer()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create();

        $reviewer = User::find($user->id);
        if (! is_null($reviewer)) {
            $project->users()->attach(
                $reviewer, ['role' => 'reviewer']
            );
        }

        $citation = Citation::factory()->create();

        $body = $this->prepareBody($citation);

        //Update citation
        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(403);

        //Detach citation
        $response = $response = $this->detachCitation($body, $project->id);
        $response->assertStatus(403);
    }

    /**
     * Test if the citation cannot be updated or detached if project is made public
     *
     * @return void
     */
    public function test_citation_cannot_be_updated_or_detached_if_project_is_public()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $citation = Citation::factory()->create();

        $body = $this->prepareBody($citation);

        //Update citation
        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(403);

        //Detach citation
        $response = $response = $this->detachCitation($body, $project->id);
        $response->assertStatus(403);
    }

    /**
     * Prepare request body for citation
     *
     * @param  \App\Models\Citation  $citation
     * @return array $body
     */
    public function prepareBody($citation)
    {
        $body = [];
        if ($citation) {
            $body = [
                'citations' => [[
                    'id' => $citation->id,
                    'doi' => $citation->doi,
                    'title' => $citation->title,
                    'authors' => $citation->authors,
                    'abstract' => $citation->abstract,
                    'citation_text' => $citation->citation_text,
                ]],
            ];
        }

        return $body;
    }

    /**
     * Make Request to update citation
     *
     * @param  \App\Models\Citation  $citation
     * @param  int  $projectId
     * @return \Illuminate\Http\Response
     */
    public function updateCitation($body, $projectId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('citations/'.$projectId, $body);
    }

    /**
     * Make Request to detach citation
     *
     * @param  \App\Models\Citation  $citation
     * @param  int  $projectId
     * @return \Illuminate\Http\Response
     */
    public function detachCitation($body, $projectId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('citations/'.$projectId.'/delete', $body);
    }
}
