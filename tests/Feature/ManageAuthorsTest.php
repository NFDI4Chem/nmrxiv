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
    public function test_author_can_be_created_and_updated()
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
    public function test_author_can_be_updated()
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
    public function test_author_can_be_detached()
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
    public function test_author_cannot_be_updated_or_deleted_by_reviewer()
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
    public function test_author_cannot_be_updated_or_detached_if_project_is_public()
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
    public function test_role_of_an_author_cannot_be_updated_by_reviewer()
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
    public function test_role_of_an_author_cannot_be_updated_for_random_contributor_types()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0]]);

        $body = [
            'author_id' => $author->id,
            'role' => 'InvalidRole', // Valid format but not in configured contributor types
        ];

        // Update author's role
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(400);
    }

    /**
     * Test validation errors for invalid author data
     *
     * @return void
     */
    public function test_author_creation_with_invalid_data_returns_validation_errors()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        // Test missing required fields
        $invalidBody = [
            'authors' => [[
                'title' => 'Dr.',
                // Missing given_name and family_name
                'email_id' => 'invalid-email', // Invalid email format
                'orcid_id' => str_repeat('a', 20), // Too long ORCID
                'affiliation' => str_repeat('a', 501), // Too long affiliation
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $invalidBody);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['authors.0.given_name', 'authors.0.family_name']);
    }

    /**
     * Test author creation with empty authors array
     *
     * @return void
     */
    public function test_author_creation_with_empty_authors_array()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = ['authors' => []];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $body);

        // Empty authors array should fail validation as 'authors' is required and must have content
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['authors']);
    }

    /**
     * Test author creation with too many authors (over limit)
     *
     * @return void
     */
    public function test_author_creation_with_too_many_authors()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        // Create 51 authors (over the limit of 50)
        $authors = [];
        for ($i = 0; $i < 51; $i++) {
            $authors[] = [
                'given_name' => "Author{$i}",
                'family_name' => "Surname{$i}",
                'email_id' => "author{$i}@example.com",
            ];
        }

        $body = ['authors' => $authors];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $body);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['authors']);
    }

    /**
     * Test multiple authors can be added at once
     *
     * @return void
     */
    public function test_multiple_authors_can_be_added_at_once()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'authors' => [
                [
                    'given_name' => 'John',
                    'family_name' => 'Doe',
                    'email_id' => 'john@example.com',
                    'contributor_type' => 'Researcher',
                ],
                [
                    'given_name' => 'Jane',
                    'family_name' => 'Smith',
                    'email_id' => 'jane@example.com',
                    'contributor_type' => 'DataCurator',
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $body);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'authors' => [
                    '*' => [
                        'id',
                        'given_name',
                        'family_name',
                        'email_id',
                        'full_name',
                        'title',
                        'orcid_id',
                        'affiliation',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);

        // Verify both authors were created
        $project->refresh();
        $this->assertCount(2, $project->authors);
    }

    /**
     * Test existing author is updated instead of creating duplicate
     *
     * @return void
     */
    public function test_existing_author_is_updated_instead_of_duplicated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $existingAuthor = Author::factory()->create([
            'given_name' => 'John',
            'family_name' => 'Doe',
            'email_id' => 'old@example.com',
        ]);

        // Attach author to project first
        $project->authors()->attach($existingAuthor->id, [
            'contributor_type' => 'Researcher',
            'sort_order' => 0,
        ]);

        // Now try to "add" the same author with updated info
        $body = [
            'authors' => [[
                'given_name' => 'John',
                'family_name' => 'Doe',
                'email_id' => 'updated@example.com',
                'affiliation' => 'New University',
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $body);

        $response->assertStatus(200);

        // Verify author was updated, not duplicated
        $project->refresh();
        $this->assertCount(1, $project->authors);

        $updatedAuthor = $project->authors->first();
        $this->assertEquals('updated@example.com', $updatedAuthor->email_id);
        $this->assertEquals('New University', $updatedAuthor->affiliation);
    }

    /**
     * Test author deletion with invalid author ID
     *
     * @return void
     */
    public function test_author_deletion_with_invalid_author_id()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'authors' => [[
                'id' => 999999, // Non-existent author ID
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('authors/'.$project->id.'/delete', $body);

        $response->assertStatus(200); // Should still succeed (no-op)
    }

    /**
     * Test author deletion with invalid request structure
     *
     * @return void
     */
    public function test_author_deletion_with_invalid_request_structure()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        // Test with missing authors array
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('authors/'.$project->id.'/delete', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['authors']);

        // Test with empty authors array
        $body = ['authors' => []];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('authors/'.$project->id.'/delete', $body);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['authors']);
    }

    /**
     * Test role update with invalid author ID succeeds but doesn't update anything
     *
     * @return void
     */
    public function test_role_update_with_invalid_author_id()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'author_id' => 999999, // Non-existent author ID
            'role' => 'DataCurator',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        // The current implementation will succeed even with invalid author IDs
        // because updateExistingPivot doesn't fail, it just doesn't update anything
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Author role updated successfully',
            'success' => true,
        ]);
    }

    /**
     * Test role update with validation errors
     *
     * @return void
     */
    public function test_role_update_with_validation_errors()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();
        $project->authors()->attach($author->id, [
            'contributor_type' => 'Researcher',
            'sort_order' => 0,
        ]);

        // Test with invalid role format (contains numbers and special chars)
        $body = [
            'author_id' => $author->id,
            'role' => 'Invalid123@Role',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);

        // Test with too long role
        $body = [
            'author_id' => $author->id,
            'role' => str_repeat('a', 51),
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);

        // Test with missing author_id
        $body = ['role' => 'DataCurator'];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['author_id']);
    }

    /**
     * Test role update for public project
     *
     * @return void
     */
    public function test_role_update_cannot_be_done_for_public_project()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $author = Author::factory()->create();
        $project->authors()->attach($author->id, [
            'contributor_type' => 'Researcher',
            'sort_order' => 0,
        ]);

        $body = [
            'author_id' => $author->id,
            'role' => 'DataCurator',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id.'/updateRole', $body);

        $response->assertStatus(403);
    }

    /**
     * Test successful role update with valid contributor type
     *
     * @return void
     */
    public function test_role_update_with_all_valid_contributor_types()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $author = Author::factory()->create();
        $project->authors()->attach($author->id, [
            'contributor_type' => 'Researcher',
            'sort_order' => 0,
        ]);

        $validTypes = [
            'ContactPerson', 'DataCollector', 'DataCurator', 'DataManager',
            'Distributor', 'Editor', 'HostingInstitution', 'Producer',
            'ProjectLeader', 'ProjectManager', 'ProjectMember', 'RegistrationAgency',
            'RegistrationAuthority', 'RelatedPerson', 'Researcher', 'ResearchGroup',
            'RightsHolder', 'Sponsor', 'Supervisor', 'WorkPackageLeader', 'Other',
        ];

        foreach ($validTypes as $type) {
            $body = [
                'author_id' => $author->id,
                'role' => $type,
            ];

            $response = $this->withHeaders([
                'Accept' => 'application/json',
            ])->post('authors/'.$project->id.'/updateRole', $body);

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'Author role updated successfully',
                'success' => true,
            ]);

            // Verify the role was actually updated
            $project->refresh();
            $authorWithPivot = $project->authors()->where('authors.id', $author->id)->first();
            $this->assertEquals($type, $authorWithPivot->pivot->contributor_type);
        }
    }

    /**
     * Test author operations return proper JSON responses
     *
     * @return void
     */
    public function test_author_operations_return_proper_json_responses()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        // Test successful author creation response structure
        $body = $this->prepareBody(null);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('authors/'.$project->id, $body);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'success',
            'data' => [
                'authors' => [
                    '*' => [
                        'id',
                        'title',
                        'given_name',
                        'family_name',
                        'full_name',
                        'email_id',
                        'orcid_id',
                        'affiliation',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);

        // Test successful deletion response
        $project->refresh();
        $author = $project->authors->first();

        $deleteBody = [
            'authors' => [[
                'id' => $author->id,
            ]],
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('authors/'.$project->id.'/delete', $deleteBody);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Author deleted successfully',
            'success' => true,
        ]);
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
