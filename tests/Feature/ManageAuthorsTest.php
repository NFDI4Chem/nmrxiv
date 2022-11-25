<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageAuthorsTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * Test if a author can be created and updated
     *
     * @return void
     */
    public function test_author_can_be_created_and_updated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'authors' => [[
                'title' => $author->title,
                'given_name' => $author->given_name,
                'family_name' => $author->family_name,
                'orcid_id' => $author->orcid_id,
                'email_id' => $author->email_id,
                'affiliation' => $author->affiliation.'_ updated',
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $body);

        $response->assertStatus(200);
    }

    /**
     * Test if a author can be deleted
     *
     * @return void
     */
    public function test_author_can_be_deleted()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'authors' => [[
                'id' => $author->id,
                'title' => $author->title,
                'given_name' => $author->given_name,
                'family_name' => $author->family_name,
                'orcid_id' => $author->orcid_id,
                'email_id' => $author->email_id,
                'affiliation' => $author->affiliation.'_ updated',
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('authors/'.$project->id.'/delete', $body);

        $response->assertStatus(200);
    }

    /**
     * Test if the role of an author can be updated
     *
     * @return void
     */
    public function test_role_of_an_author_can_be_updated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'author_id' => $author->id,
            'role' => 'Researcher',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(200);
    }
}
