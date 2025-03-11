<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageAuthorsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test if a author can be created and updated
     *
     * @return void
     */
    public function test_author_can_be_created_and_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = $this->prepareBody(null);

        // Add author to project
        $response = $this->addAuthor($body, $project->id);

        $response->assertStatus(200);

        // Check if entry got created in DB
        $project = $project->fresh();
        $authors = $project->authors->toArray();
        $this->assertDatabaseHas('author_project', $authors[0]['pivot']);
    }

    /**
     * Test if a author can be updated
     *
     * @return void
     */
    public function test_author_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = $this->prepareBody(null);

        // Add author to project
        $response = $this->addAuthor($body, $project->id);

        $response->assertStatus(200);

        // Fetch authors details
        $project = $project->fresh();
        $authors = $project->authors->toArray();

        // Update existing author
        $response = $this->updateAuthor($authors[0], $project->id);
        $response->assertStatus(200);

        // Check if entry got updated in DB
        $project = $project->fresh();
        $authors = $project->authors->toArray();
        $this->assertDatabaseHas('author_project', $authors[0]['pivot']);
    }

    /**
     * Test if a author can be deleted
     *
     * @return void
     */
    public function test_author_can_be_detached(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);
        $project = $project->fresh();
        $authors = $project->authors->toArray();

        $body = $this->prepareBody($author);

        // Detach author
        $response = $this->detachAuthor($body, $project->id);
        $response->assertStatus(200);

        // Check if entry got deleted from DB
        $this->assertDatabaseMissing('author_project', $authors[0]['pivot']);
    }

    /**
     * Test if the author cannot be updated or detached by the reviewer
     *
     * @return void
     */
    public function test_author_cannot_be_updated_or_deleted_by_reviewer(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create();

        $reviewer = User::find($user->id);
        if (! is_null($reviewer)) {
            $project->users()->attach(
                $reviewer, ['role' => 'reviewer']
            );
        }

        $author = Author::factory()->create();

        $body = $this->prepareBody($author);

        // Update author
        $response = $this->addAuthor($body, $project->id);
        $response->assertStatus(403);

        // Detach author
        $response = $response = $this->detachAuthor($body, $project->id);
        $response->assertStatus(403);
    }

    /**
     * Test if the author cannot be updated or detached if project is made public
     *
     * @return void
     */
    public function test_author_cannot_be_updated_or_detached_if_project_is_public(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $author = Author::factory()->create();

        $body = $this->prepareBody($author);

        // Update author
        $response = $this->addAuthor($body, $project->id);
        $response->assertStatus(403);

        // Detach author
        $response = $response = $this->detachAuthor($body, $project->id);
        $response->assertStatus(403);
    }

    /**
     * Test if the role of an author can be updated
     *
     * @return void
     */
    public function test_role_of_an_author_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'author_id' => $author->id,
            'role' => 'DataCurator',
        ];

        // Update author's role
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(200);

        $project = $project->refresh();
        $authors = $project->authors->toArray();

        // Check if entry got updated in DB
        $this->assertDatabaseHas('author_project', $authors[0]['pivot']);
    }

    /**
     * Test if the role of an author cannot be updated by reviewer
     *
     * @return void
     */
    public function test_role_of_an_author_cannot_be_updated_by_reviewer(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create();

        $reviewer = User::find($user->id);
        if (! is_null($reviewer)) {
            $project->users()->attach(
                $reviewer, ['role' => 'reviewer']
            );
        }
        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'author_id' => $author->id,
            'role' => 'DataCurator',
        ];

        // Update author's role
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(403);
    }

    /**
     * Test if the role of an author can be updated if the roles are other than configured type.
     *
     * @return void
     */
    public function test_role_of_an_author_cannot_be_updated_for_random_contributor_types(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'author_id' => $author->id,
            'role' => $this->faker->text(),
        ];

        // Update author's role
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(500);
    }

    /**
     * Prepare request body for author
     *
     * @param  \App\Models\Author  $author
     * @return array $body
     */
    public function prepareBody($author)
    {
        $body = [];
        if ($author) {
            $body = [
                'authors' => [[
                    'id' => $author->id,
                    'title' => $author->title,
                    'given_name' => $author->given_name,
                    'family_name' => $author->family_name,
                    'orcid_id' => null,
                    'email_id' => $author->email_id,
                    'affiliation' => $author->affiliation,
                ]],
            ];
        } else {
            $body = [
                'authors' => [[
                    'title' => $this->faker->title(),
                    'given_name' => $this->faker->firstName(),
                    'family_name' => $this->faker->lastName(),
                    'orcid_id' => null,
                    'email_id' => $this->faker->unique()->safeEmail(),
                    'affiliation' => $this->faker->text(),
                ]],
            ];
        }

        return $body;
    }

    /**
     * Make Request to add author
     *
     * @param  \App\Models\Author  $body
     * @return \Illuminate\Http\Response
     */
    public function addAuthor($body, $projectId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$projectId, $body);
    }

    /**
     * Make Request to update author
     *
     * @param  \App\Models\Author  $body
     * @return \Illuminate\Http\Response
     */
    public function updateAuthor($author, $projectId)
    {
        $body = [];
        if ($author) {
            $body = [
                'authors' => [[
                    'id' => $author['id'],
                    'title' => $author['title'],
                    'given_name' => $author['given_name'].'_updated',
                    'family_name' => $author['family_name'].'_updated',
                    'orcid_id' => $author['orcid_id'],
                    'email_id' => $author['email_id'],
                    'affiliation' => $author['affiliation'].'_ updated',
                ]],
            ];
        }

        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$projectId, $body);
    }

    /**
     * Make Request to detach author
     *
     * @param  \App\Models\Author  $body
     * @return \Illuminate\Http\Response
     */
    public function detachAuthor($body, $projectId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('authors/'.$projectId.'/delete', $body);
    }
}
