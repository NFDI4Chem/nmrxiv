<?php

namespace Tests\Feature\ExternalServices;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChemistryStandardizeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();

        Config::set(
            'services.chemistry_standardize.url',
            'https://api.example.test/latest/chem/standardize'
        );
    }

    public function test_standardize_requires_authentication(): void
    {
        $response = $this->post(route('chemistry.standardize'), [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode('mol block'));

        $response->assertRedirect();
    }

    public function test_standardize_rejects_empty_body(): void
    {
        $response = $this->actingAs($this->user)
            ->call('POST', route('chemistry.standardize'), [], [], [], [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ], '');

        $response->assertStatus(422);
        $response->assertJson([
            'error' => 'Molecule structure is required.',
        ]);
    }

    public function test_standardize_proxies_molfile_to_upstream_service(): void
    {
        $molfile = "test molfile\n";

        Http::fake([
            'api.example.test/*' => Http::response([
                'inchi' => 'InChI=1S/C/H',
                'inchikey' => 'TESTKEY',
                'standardized_mol' => 'standardized',
                'canonical_smiles' => 'C',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->call('POST', route('chemistry.standardize'), [], [], [], [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ], json_encode($molfile));

        $response->assertOk();
        $response->assertJson([
            'inchi' => 'InChI=1S/C/H',
            'canonical_smiles' => 'C',
        ]);

        Http::assertSent(function ($request) use ($molfile) {
            return $request->url() === 'https://api.example.test/latest/chem/standardize'
                && $request->body() === json_encode($molfile);
        });
    }

    public function test_inertia_shares_app_proxy_url_not_upstream_api(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $page = $this->assertInertiaPageComponent($response, 'Dashboard');
        $this->assertSame(route('chemistry.standardize'), $page['props']['chemistryStandardizeUrl']);
    }
}
