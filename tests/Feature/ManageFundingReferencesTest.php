<?php

namespace Tests\Feature;

use App\Actions\FundingReference\PushProjectDoiMetadata;
use App\Models\FundingReference;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ManageFundingReferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_funding_reference_can_be_added_and_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $fundingReference = FundingReference::factory()->make();

        $body = $this->prepareBody($fundingReference);

        $response = $this->updateFundingReference($body, $project->id);

        $response->assertStatus(200);

        $project = $project->fresh();
        $fundingReferences = $project->fundingReferences->toArray();
        $this->assertDatabaseHas('funding_reference_project', $fundingReferences[0]['pivot']);
        unset($fundingReferences[0]['pivot']);
        $this->assertDatabaseHas('funding_references', $fundingReferences[0]);
    }

    public function test_funding_reference_can_be_detached(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $fundingReference = FundingReference::factory()->create();

        $project->fundingReferences()->sync([$fundingReference->id => ['user' => $user->id]]);
        $project = $project->fresh();
        $fundingReferences = $project->fundingReferences->toArray();

        $body = $this->prepareBody($fundingReference);

        $response = $this->detachFundingReference($body, $project->id);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('funding_reference_project', $fundingReferences[0]['pivot']);
        $this->assertDatabaseMissing('funding_references', ['id' => $fundingReference->id]);
    }

    public function test_funding_reference_cannot_be_updated_or_deleted_by_reviewer(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create();

        $reviewer = User::find($user->id);
        if (! is_null($reviewer)) {
            $project->users()->attach(
                $reviewer, ['role' => 'reviewer']
            );
        }

        $fundingReference = FundingReference::factory()->create();

        $body = $this->prepareBody($fundingReference);

        $response = $this->updateFundingReference($body, $project->id);
        $response->assertStatus(403);

        $response = $this->detachFundingReference($body, $project->id);
        $response->assertStatus(403);
    }

    public function test_funding_reference_validation_requires_funder_name(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'funding_references' => [[
                'award_number' => '441958208',
            ]],
        ];

        $response = $this->updateFundingReference($body, $project->id);
        $response->assertStatus(422);
    }

    public function test_funding_reference_requires_identifier_type_when_identifier_present(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = [
            'funding_references' => [[
                'funder_name' => 'Deutsche Forschungsgemeinschaft',
                'funder_identifier' => 'https://ror.org/018mejw64',
            ]],
        ];

        $response = $this->updateFundingReference($body, $project->id);
        $response->assertStatus(422);
    }

    public function test_saving_funding_reference_syncs_doi_metadata_when_project_has_doi(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'doi' => '10.1234/nmrxiv.test-project',
        ]);

        $this->mock(PushProjectDoiMetadata::class, function ($mock): void {
            $mock->shouldReceive('push')
                ->once()
                ->withArgs(fn (?Project $project): bool => $project?->doi === '10.1234/nmrxiv.test-project');
        });

        $fundingReference = FundingReference::factory()->make();
        $body = $this->prepareBody($fundingReference);

        $response = $this->updateFundingReference($body, $project->id);
        $response->assertStatus(200);
    }

    public function test_funding_reference_cannot_be_updated_with_foreign_id(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $otherProject = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $foreignFundingReference = FundingReference::factory()->create();
        $otherProject->fundingReferences()->sync([
            $foreignFundingReference->id => ['user' => (string) $user->id],
        ]);

        $body = [
            'funding_references' => [[
                'id' => $foreignFundingReference->id,
                'funder_name' => 'Deutsche Forschungsgemeinschaft',
                'funder_identifier' => 'https://ror.org/018mejw64',
                'funder_identifier_type' => 'ROR',
            ]],
        ];

        $response = $this->updateFundingReference($body, $project->id);
        $response->assertStatus(422);

        $this->assertDatabaseHas('funding_references', [
            'id' => $foreignFundingReference->id,
            'funder_name' => $foreignFundingReference->funder_name,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function prepareBody(FundingReference $fundingReference): array
    {
        return [
            'funding_references' => [[
                'id' => $fundingReference->id,
                'funder_name' => $fundingReference->funder_name,
                'funder_identifier' => $fundingReference->funder_identifier,
                'funder_identifier_type' => $fundingReference->funder_identifier_type,
                'award_number' => $fundingReference->award_number,
                'award_title' => $fundingReference->award_title,
                'award_uri' => $fundingReference->award_uri,
            ]],
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public function updateFundingReference(array $body, int $projectId): TestResponse
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('funding-references/'.$projectId, $body);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public function detachFundingReference(array $body, int $projectId): TestResponse
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('funding-references/'.$projectId.'/delete', $body);
    }
}
