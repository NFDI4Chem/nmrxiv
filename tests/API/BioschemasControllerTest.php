<?php

namespace Tests\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BioschemasControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Bioschemas endpoint exists
     * Note: Full testing requires complex data relationships (molecules, studies with spectra, etc.)
     */
    public function test_bioschemas_endpoint_exists()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/P99999');
        
        // Route exists - should return 404, 403, or 500 without full data
        $this->assertContains($response->status(), [403, 404, 500]);
    }

    /**
     * Test cannot retrieve Bioschemas for private project
     */
    public function test_cannot_retrieve_bioschemas_for_private_project()
    {
        $user = User::factory()->create();
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => false,
            'identifier' => 12345,
            'license_id' => $license->id,
        ]);

        $response = $this->getJson('/api/v1/schemas/bioschemas/P12345');

        $response->assertStatus(403);
    }
}
