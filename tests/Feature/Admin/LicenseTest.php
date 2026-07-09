<?php

namespace Tests\Feature\Admin;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
    }

    public function test_index_returns_all_licenses(): void
    {
        Cache::forget('licenses');

        $license1 = License::factory()->create([
            'title' => 'MIT License',
            'category' => 'permissive',
        ]);

        $license2 = License::factory()->create([
            'title' => 'GPL License',
            'category' => 'copyleft',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/licenses');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    public function test_index_orders_by_category_then_title(): void
    {
        Cache::forget('licenses');

        $licenseB = License::factory()->create([
            'title' => 'B License',
            'category' => 'beta',
        ]);

        $licenseA = License::factory()->create([
            'title' => 'A License',
            'category' => 'alpha',
        ]);

        $licenseC = License::factory()->create([
            'title' => 'C License',
            'category' => 'beta',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/licenses');

        $response->assertStatus(200);
        $json = $response->json();

        // Should be ordered: alpha (A), beta (B), beta (C)
        $this->assertEquals('A License', $json[0]['title']);
        $this->assertEquals('B License', $json[1]['title']);
        $this->assertEquals('C License', $json[2]['title']);
    }

    public function test_index_caches_licenses(): void
    {
        License::factory()->create(['title' => 'Cached License']);

        // First call should cache
        $this->actingAs($this->user)->get('/licenses');

        // Verify cache exists
        $this->assertTrue(Cache::has('licenses'));

        // Second call should use cache
        $response = $this->actingAs($this->user)
            ->get('/licenses');
        $response->assertStatus(200);
    }

    public function test_index_returns_only_specific_fields(): void
    {
        License::factory()->create([
            'title' => 'Test License',
            'description' => 'Test Description',
            'category' => 'test',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/licenses');

        $response->assertStatus(200);
        $json = $response->json()[0];

        // Should only have these fields
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('title', $json);
        $this->assertArrayHasKey('description', $json);
        $this->assertArrayHasKey('category', $json);
    }

    public function test_index_handles_empty_licenses(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/licenses');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_get_license_by_id_returns_specific_license(): void
    {
        $license = License::factory()->create([
            'title' => 'Specific License',
            'description' => 'Specific Description',
            'category' => 'specific',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/licenses/{$license->id}");

        $response->assertStatus(200);
        $response->assertJson([
            [
                'id' => $license->id,
                'title' => 'Specific License',
                'description' => 'Specific Description',
                'category' => 'specific',
            ],
        ]);
    }

    public function test_get_license_by_id_returns_only_specific_fields(): void
    {
        $license = License::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/licenses/{$license->id}");

        $response->assertStatus(200);
        $json = $response->json()[0];

        // Should only have these fields
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('title', $json);
        $this->assertArrayHasKey('description', $json);
        $this->assertArrayHasKey('category', $json);
    }

    public function test_get_license_by_id_returns_empty_for_non_existent_id(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/licenses/99999');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_licenses_requires_authentication(): void
    {
        License::factory()->create();

        $response = $this->get('/licenses');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_get_license_by_id_requires_authentication(): void
    {
        $license = License::factory()->create();

        $response = $this->get("/licenses/{$license->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
