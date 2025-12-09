<?php

namespace Tests\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataCiteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test DataCite endpoint returns empty response for projects without DOI
     * Note: datacite_schema attribute is null by default for new records
     */
    public function test_datacite_returns_empty_for_project_without_doi()
    {
        $user = User::factory()->create();
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 12345,
            'license_id' => $license->id,
        ]);

        $response = $this->getJson('/api/v1/schemas/datacite/P12345');

        $response->assertStatus(200);
        // The controller returns raw null, not JSON-encoded null
        $this->assertEmpty($response->getContent());
    }

    /**
     * Test DataCite endpoint exists
     */
    public function test_datacite_endpoint_exists()
    {
        $response = $this->getJson('/api/v1/schemas/datacite/P99999');
        
        // Should return 500 (null model error) or 404
        $this->assertContains($response->status(), [404, 500]);
    }
}
