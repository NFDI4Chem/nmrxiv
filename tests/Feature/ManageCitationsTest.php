<?php

namespace Tests\Feature;

use App\Models\Citation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageCitationsTest extends TestCase
{
    use RefreshDatabase;

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

        // Update citation
        $response = $this->updateCitation($body, $project->id);

        $response->assertStatus(200);

        // Check if entry got created in DB
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

        // Detach citation
        $response = $this->detachCitation($body, $project->id);
        $response->assertStatus(200);

        // Check if entry got deleted from DB
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

        // Update citation
        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(403);

        // Detach citation
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

        // Update citation
        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(403);

        // Detach citation
        $response = $response = $this->detachCitation($body, $project->id);
        $response->assertStatus(403);
    }

    /**
     * Test validation rules for required fields
     *
     * @return void
     */
    public function test_citation_validation_requires_title_doi_and_authors()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        // Test missing title
        $body = [
            'citations' => [[
                'doi' => '10.1000/test',
                'authors' => 'Test Author',
                // 'title' missing
            ]],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Test missing DOI
        $body = [
            'citations' => [[
                'title' => 'Test Title',
                'authors' => 'Test Author',
                // 'doi' missing
            ]],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['doi']);

        // Test missing authors
        $body = [
            'citations' => [[
                'title' => 'Test Title',
                'doi' => '10.1000/test',
                // 'authors' missing
            ]],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['authors']);
    }

    /**
     * Test maximum citations limit
     *
     * @return void
     */
    public function test_citation_maximum_limit_validation()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        // Create citations array exceeding limit
        $citations = [];
        for ($i = 0; $i < 101; $i++) { // Exceeds max of 100
            $citations[] = [
                'title' => "Test Title {$i}",
                'doi' => "10.1000/test{$i}",
                'authors' => 'Test Author',
                'citation_text' => 'Test citation text',
            ];
        }

        $body = ['citations' => $citations];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['citations']);
    }

    /**
     * Test duplicate citation prevention by DOI
     *
     * @return void
     */
    public function test_citation_duplicate_prevention_by_doi()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $doi = '10.1000/duplicate-test';

        // First citation
        $body = [
            'citations' => [[
                'title' => 'First Title',
                'doi' => $doi,
                'authors' => 'First Author',
                'citation_text' => 'First citation text',
            ]],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(200);

        // Second citation with same DOI should update, not create duplicate
        $body = [
            'citations' => [[
                'title' => 'Updated Title',
                'doi' => $doi,
                'authors' => 'Updated Author',
                'citation_text' => 'Updated citation text',
            ]],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(200);

        // Should have only one citation in project
        $project = $project->refresh();
        $this->assertEquals(1, $project->citations()->count());

        // Should have updated title
        $citation = $project->citations()->first();
        $this->assertEquals('Updated Title', $citation->title);
        $this->assertEquals('Updated Author', $citation->authors);
    }

    /**
     * Test empty citations array handling
     *
     * @return void
     */
    public function test_citation_empty_array_handling()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = ['citations' => []];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(422); // Empty array fails validation
        $response->assertJsonValidationErrors(['citations']);
    }

    /**
     * Test JSON response structure for successful operations
     *
     * @return void
     */
    public function test_citation_json_response_structure()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $citation = Citation::factory()->create();
        $body = $this->prepareBody($citation);

        $response = $this->updateCitation($body, $project->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'success',
            'data' => [
                'citations' => [
                    '*' => [
                        'id',
                        'doi',
                        'title',
                        'authors',
                        'citation_text',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test citation_text optional field
     *
     * @return void
     */
    public function test_citation_text_is_optional()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'citations' => [[
                'title' => 'Test Title',
                'doi' => '10.1000/test',
                'authors' => 'Test Author',
                // citation_text is optional
            ]],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(200);

        // Should create citation without citation_text
        $project = $project->refresh();
        $citation = $project->citations()->first();
        $this->assertNull($citation->citation_text);
    }

    /**
     * Test citation with null DOI is skipped
     *
     * @return void
     */
    public function test_citation_with_null_doi_is_skipped()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'citations' => [
                [
                    'title' => 'Valid Citation',
                    'doi' => '10.1000/valid',
                    'authors' => 'Valid Author',
                ],
                [
                    'title' => 'Invalid Citation',
                    'doi' => null,
                    'authors' => 'Invalid Author',
                ],
            ],
        ];

        // This will fail validation because DOI is required
        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['doi']);
    }

    /**
     * Test multiple citations can be processed
     *
     * @return void
     */
    public function test_multiple_citations_can_be_processed()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'citations' => [
                [
                    'title' => 'First Citation',
                    'doi' => '10.1000/first',
                    'authors' => 'First Author',
                    'citation_text' => 'First text',
                ],
                [
                    'title' => 'Second Citation',
                    'doi' => '10.1000/second',
                    'authors' => 'Second Author',
                    'citation_text' => 'Second text',
                ],
                [
                    'title' => 'Third Citation',
                    'doi' => '10.1000/third',
                    'authors' => 'Third Author',
                    'citation_text' => 'Third text',
                ],
            ],
        ];

        $response = $this->updateCitation($body, $project->id);
        $response->assertStatus(200);

        // Should create all citations
        $project = $project->refresh();
        $this->assertEquals(3, $project->citations()->count());

        // Verify all citations exist
        $titles = $project->citations()->pluck('title')->toArray();
        $this->assertContains('First Citation', $titles);
        $this->assertContains('Second Citation', $titles);
        $this->assertContains('Third Citation', $titles);
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
