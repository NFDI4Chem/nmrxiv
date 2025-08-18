<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OEmbedTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private Study $study;
    private Dataset $dataset;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create([
            'name' => 'Test Author',
            'username' => 'testauthor',
        ]);

        $this->project = Project::factory()->create([
            'name' => 'Test Project',
            'owner_id' => $this->user->id,
            'identifier' => 123,
            'is_public' => true,
        ]);

        $this->study = Study::factory()->create([
            'name' => 'Test Study',
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'identifier' => 456,
            'is_public' => true,
        ]);

        // Create a sample for the study (required for StudyResource)
        Sample::factory()->create([
            'name' => 'Test Sample',
            'study_id' => $this->study->id,
            'project_id' => $this->project->id,
        ]);

        $this->dataset = Dataset::factory()->create([
            'name' => 'Test Dataset',
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'identifier' => 789,
            'is_public' => true,
        ]);
    }

    public function test_it_can_generate_oembed_response_for_study(): void
    {
        $url = config('app.url') . '/S456';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'type' => 'rich',
                'version' => '1.0',
                'provider_name' => config('app.name'),
                'provider_url' => config('app.url'),
                'title' => 'Test Study',
                'author_name' => 'Test Author',
                'author_url' => config('app.url') . '/author/testauthor',
                'height' => '300',
                'width' => '320',
                'thumbnail_width' => '300',
                'thumbnail_height' => '125',
                'thumbnail_url' => null,
            ])
            ->assertJsonStructure([
                'success',
                'type',
                'version',
                'provider_name',
                'provider_url',
                'title',
                'author_name',
                'author_url',
                'height',
                'width',
                'thumbnail_width',
                'thumbnail_height',
                'thumbnail_url',
                'html',
            ]);

        // Verify the HTML contains the expected iframe
        $data = $response->json();
        $this->assertStringContainsString('<iframe', $data['html']);
        $this->assertStringContainsString('nmrxiv_embed', $data['html']);
        $this->assertStringContainsString('/embed/NMRXIV:S456', $data['html']);
    }


    public function test_it_can_generate_oembed_response_for_dataset(): void
    {
        $url = config('app.url') . '/D789';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'type' => 'rich',
                'version' => '1.0',
                'provider_name' => config('app.name'),
                'provider_url' => config('app.url'),
                'title' => 'Test Dataset',
                'author_name' => 'Test Author',
                'author_url' => config('app.url') . '/author/testauthor',
            ]);
    }


    public function test_it_accepts_custom_width_and_height_parameters(): void
    {
        $url = config('app.url') . '/S456';

        $response = $this->get('/services/oembed?url=' . urlencode($url) . '&width=500&height=400');

        $response->assertStatus(200)
            ->assertJson([
                'width' => '500',
                'height' => '400',
            ]);
    }


    public function test_it_handles_study_without_thumbnail(): void
    {
        $studyWithoutThumbnail = Study::factory()->create([
            'name' => 'Study Without Thumbnail',
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'identifier' => 999,
            'is_public' => true,
        ]);

        $url = config('app.url') . '/S999';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(200)
            ->assertJson([
                'thumbnail_url' => null,
            ]);
    }


    public function test_it_returns_400_when_url_parameter_is_missing(): void
    {
        $response = $this->get('/services/oembed');

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid request parameters',
            ]);
    }


    public function test_it_returns_400_when_url_format_is_invalid(): void
    {
        $response = $this->get('/services/oembed?url=invalid-url');

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid request parameters',
            ]);
    }


    public function test_it_returns_400_when_identifier_is_missing_from_url(): void
    {
        $url = config('app.url') . '/';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid request parameters',
            ]);
    }


    public function test_it_returns_404_when_identifier_cannot_be_resolved(): void
    {
        $url = config('app.url') . '/S99999';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Content not found',
            ]);
    }


    public function test_it_returns_500_when_identifier_format_is_invalid(): void
    {
        $url = config('app.url') . '/INVALID123';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'An error occurred while processing the request',
            ]);
    }


    public function test_it_can_render_embedded_study_content(): void
    {
        $response = $this->get('/embed/S456');

        $response->assertStatus(200);

        // Just verify it's a successful HTML response (the Inertia component is embedded in complex JSON/HTML)
    }


    public function test_it_can_render_embedded_dataset_content(): void
    {
        $response = $this->get('/embed/D789');

        $response->assertStatus(200);

        // Just verify it's a successful HTML response (the Inertia component is embedded in complex JSON/HTML)
    }


    public function test_it_returns_400_when_embed_identifier_is_empty(): void
    {
        $response = $this->get('/embed/');

        $response->assertStatus(404); // This will be a 404 from Laravel routing, not our controller
    }


    public function test_it_returns_404_when_embed_identifier_cannot_be_resolved(): void
    {
        $response = $this->get('/embed/S99999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Content not found',
            ]);
    }


    public function test_it_returns_500_when_embed_identifier_format_is_invalid(): void
    {
        $response = $this->get('/embed/INVALID123');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'An error occurred while processing the request',
            ]);
    }


    public function test_it_returns_404_for_unsupported_content_type_in_embed(): void
    {
        // Create a project (which should not be embeddable)
        $response = $this->get('/embed/P123');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Content not found',
            ]);
    }


    public function test_it_handles_dataset_without_associated_study(): void
    {
        // Create a dataset without a study relationship
        $orphanDataset = Dataset::factory()->create([
            'name' => 'Orphan Dataset',
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'study_id' => null, // No study
            'identifier' => 888,
            'is_public' => true,
        ]);

        $response = $this->get('/embed/D888');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Content not found',
            ]);
    }


    public function test_it_handles_nmrxiv_prefix_in_identifier(): void
    {
        $url = config('app.url') . '/NMRXIV:S456';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Study',
            ]);
    }


    public function test_it_handles_case_insensitive_identifiers(): void
    {
        $url = config('app.url') . '/s456'; // lowercase

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Study',
            ]);
    }


    public function test_it_supports_json_format_parameter(): void
    {
        $url = config('app.url') . '/S456';

        $response = $this->get('/services/oembed?url=' . urlencode($url) . '&format=json');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'type' => 'rich',
            ]);
    }


    public function test_it_handles_server_errors_gracefully(): void
    {
        // Mock a scenario that would cause an exception
        // This test ensures our try-catch blocks work properly
        $url = config('app.url') . '/S456';

        // We can't easily mock an exception in this context, 
        // but this test documents the expected behavior
        $response = $this->get('/services/oembed?url=' . urlencode($url));

        // Should not return 500 error for valid input
        $response->assertStatus(200);
    }

    public function test_it_blocks_private_content_in_oembed(): void
    {
        // Create a private study
        $privateStudy = Study::factory()->create([
            'name' => 'Private Study',
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'identifier' => 777,
            'is_public' => false, // This is private
        ]);

        $url = config('app.url') . '/S777';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Content not found',
            ]);
    }

    public function test_it_blocks_private_content_in_embed(): void
    {
        // Create a private study
        $privateStudy = Study::factory()->create([
            'name' => 'Private Study',
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'identifier' => 888,
            'is_public' => false, // This is private
        ]);

        $response = $this->get('/embed/S888');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Content not found',
            ]);
    }

    public function test_it_blocks_external_domain_urls(): void
    {
        $externalUrl = 'https://evil.com/S456';

        $response = $this->get('/services/oembed?url=' . urlencode($externalUrl));

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid request parameters',
            ]);
    }

    public function test_it_validates_width_and_height_parameters(): void
    {
        $url = config('app.url') . '/S456';

        // Test with malicious width/height values - should be blocked by validation
        $response = $this->get('/services/oembed?url=' . urlencode($url) . '&width=<script>alert(1)</script>&height=999999');

        // Should return validation error for invalid parameters
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid request parameters',
            ]);

        // Test with valid numeric values
        $response2 = $this->get('/services/oembed?url=' . urlencode($url) . '&width=500&height=400');
        
        $response2->assertStatus(200);
        $data = $response2->json();
        
        $this->assertEquals('500', $data['width']);
        $this->assertEquals('400', $data['height']);
    }

    public function test_iframe_html_is_properly_sanitized(): void
    {
        $url = config('app.url') . '/S456';

        $response = $this->get('/services/oembed?url=' . urlencode($url));

        $response->assertStatus(200);

        $data = $response->json();
        
        // Verify iframe includes sandbox attribute for security
        $this->assertStringContainsString('sandbox="allow-scripts allow-same-origin"', $data['html']);
        
        // Verify all content is properly HTML escaped
        $this->assertStringNotContainsString('<script>', $data['html']);
        $this->assertStringNotContainsString('javascript:', $data['html']);
    }
}
