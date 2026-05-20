<?php

namespace Tests\Unit\Models;

use App\Models\Citation;
use App\Models\License;
use App\Models\Project;
use App\Models\User;
use App\Services\DOI\DOIService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RDM-best-practice + provisional DOI link tests.
 *
 * Kept in their own file so the original `HasDOIModelTest` (964 lines and
 * thoroughly hand-crafted) stays focused on the historical behavior; the
 * new tests below pin the DataCite 4.4 / FAIR / MIChI changes.
 */
class HasDOIRdmFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_metadata_uses_schema_version_4_4(): void
    {
        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $this->assertSame('http://datacite.org/schema/kernel-4.4', $metadata['schemaVersion']);
    }

    public function test_metadata_description_is_abstract_type(): void
    {
        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $this->assertSame('Abstract', $metadata['descriptions'][0]['descriptionType']);
    }

    public function test_metadata_dates_include_issued(): void
    {
        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $dateTypes = array_column($metadata['dates'], 'dateType');
        $this->assertContains('Issued', $dateTypes);
        $this->assertContains('Available', $dateTypes);
        $this->assertContains('Submitted', $dateTypes);
        $this->assertContains('Updated', $dateTypes);
    }

    public function test_metadata_publisher_object_when_ror_configured(): void
    {
        config()->set('doi.publisher_name', 'nmrXiv');
        config()->set('doi.publisher_ror', 'https://ror.org/04k8k6n84');

        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['publisher']);
        $this->assertSame('nmrXiv', $metadata['publisher']['name']);
        $this->assertSame('https://ror.org/04k8k6n84', $metadata['publisher']['publisherIdentifier']);
        $this->assertSame('ROR', $metadata['publisher']['publisherIdentifierScheme']);
    }

    public function test_metadata_publisher_string_when_ror_missing(): void
    {
        config()->set('doi.publisher_name', 'nmrXiv');
        config()->set('doi.publisher_ror', null);

        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $this->assertSame('nmrXiv', $metadata['publisher']);
    }

    public function test_owner_is_contact_person_other_users_are_researchers(): void
    {
        $owner = User::factory()->create();
        $teamMember = User::factory()->create();

        $project = $this->makeProject(['owner_id' => $owner->id]);
        $project->users()->attach($owner->id, ['role' => 'owner']);
        $project->users()->attach($teamMember->id, ['role' => 'editor']);

        $metadata = $project->fresh()->getMetadata();

        $contactPersons = array_filter(
            $metadata['contributors'],
            fn ($c) => ($c['contributorType'] ?? null) === 'ContactPerson'
        );
        $researchers = array_filter(
            $metadata['contributors'],
            fn ($c) => ($c['contributorType'] ?? null) === 'Researcher'
        );

        $this->assertCount(1, $contactPersons);
        $this->assertCount(1, $researchers);
    }

    public function test_hosting_institution_contributor_is_emitted_when_publisher_configured(): void
    {
        config()->set('doi.publisher_name', 'nmrXiv');
        config()->set('doi.publisher_ror', 'https://ror.org/04k8k6n84');

        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $hosts = array_filter(
            $metadata['contributors'],
            fn ($c) => ($c['contributorType'] ?? null) === 'HostingInstitution'
        );
        $this->assertCount(1, $hosts);
        $this->assertSame('Organizational', array_values($hosts)[0]['nameType']);
    }

    public function test_citations_use_is_referenced_by_relation(): void
    {
        $project = $this->makeProject();
        $citation = Citation::factory()->create(['doi' => '10.1234/cited.work']);
        $project->citations()->attach($citation->id);

        $metadata = $project->fresh()->getMetadata();

        $citationEntries = array_filter(
            $metadata['relatedIdentifiers'],
            fn ($r) => ($r['relatedIdentifier'] ?? null) === '10.1234/cited.work'
        );
        $entry = array_values($citationEntries)[0] ?? null;
        $this->assertNotNull($entry);
        $this->assertSame('IsReferencedBy', $entry['relationType']);
    }

    public function test_metadata_includes_has_metadata_links(): void
    {
        config()->set('app.url', 'https://nmrxiv.test');

        $project = $this->makeProject(['identifier' => 7]);

        $metadata = $project->getMetadata();

        $hasMetadata = array_filter(
            $metadata['relatedIdentifiers'],
            fn ($r) => ($r['relationType'] ?? null) === 'HasMetadata'
        );
        $this->assertGreaterThanOrEqual(1, count($hasMetadata));

        $first = array_values($hasMetadata)[0];
        $this->assertStringContainsString('/api/v1/schemas/datacite/P7', $first['relatedIdentifier']);
    }

    public function test_metadata_includes_is_compiled_by_for_nmrium(): void
    {
        config()->set('doi.related_software.nmrium', '10.5281/zenodo.10209593');

        $project = $this->makeProject();

        $metadata = $project->getMetadata();

        $compiled = array_filter(
            $metadata['relatedIdentifiers'],
            fn ($r) => ($r['relationType'] ?? null) === 'IsCompiledBy'
                && ($r['relatedIdentifier'] ?? null) === '10.5281/zenodo.10209593'
        );
        $this->assertCount(1, $compiled);
    }

    public function test_provisional_doi_is_appended_as_is_identical_to_for_project(): void
    {
        $project = $this->makeProject([
            'doi' => '10.99999/nmrxiv.P1',
            'provisional_doi' => '10.99999/nmrxiv.deadbeef',
        ]);

        $metadata = $project->getMetadata();

        $matches = array_filter(
            $metadata['relatedIdentifiers'],
            fn ($r) => ($r['relationType'] ?? null) === 'IsIdenticalTo'
                && ($r['relatedIdentifier'] ?? null) === '10.99999/nmrxiv.deadbeef'
        );
        $this->assertCount(1, $matches);
    }

    public function test_no_is_identical_to_when_no_provisional_doi(): void
    {
        $project = $this->makeProject([
            'doi' => '10.99999/nmrxiv.P1',
            'provisional_doi' => null,
        ]);

        $metadata = $project->getMetadata();

        $matches = array_filter(
            $metadata['relatedIdentifiers'],
            fn ($r) => ($r['relationType'] ?? null) === 'IsIdenticalTo'
        );
        $this->assertCount(0, $matches);
    }

    public function test_link_provisional_doi_no_op_when_provisional_missing(): void
    {
        $project = $this->makeProject(['doi' => '10.x/foo', 'provisional_doi' => null]);

        $service = $this->createMock(DOIService::class);
        $service->expects($this->never())->method('createCustomDOI');
        $service->expects($this->never())->method('putRelatedIdentifiers');

        $project->linkProvisionalDoi($service);

        $this->assertNull($project->fresh()->provisional_doi_registered_at);
    }

    public function test_link_provisional_doi_no_op_when_canonical_missing(): void
    {
        $project = $this->makeProject(['doi' => null, 'provisional_doi' => '10.x/prov']);

        $service = $this->createMock(DOIService::class);
        $service->expects($this->never())->method('createCustomDOI');
        $service->expects($this->never())->method('putRelatedIdentifiers');

        $project->linkProvisionalDoi($service);
    }

    public function test_link_provisional_doi_no_op_when_already_registered(): void
    {
        $project = $this->makeProject([
            'doi' => '10.x/canon',
            'provisional_doi' => '10.x/prov',
            'provisional_doi_registered_at' => Carbon::now()->subDay(),
        ]);

        $service = $this->createMock(DOIService::class);
        $service->expects($this->never())->method('createCustomDOI');
        $service->expects($this->never())->method('putRelatedIdentifiers');

        $project->linkProvisionalDoi($service);
    }

    public function test_link_provisional_doi_creates_registers_links_and_marks(): void
    {
        $project = $this->makeProject([
            'identifier' => 1,
            'doi' => '10.99999/nmrxiv.P1',
            'provisional_doi' => '10.99999/nmrxiv.deadbeef',
        ]);

        $service = $this->createMock(DOIService::class);

        $service->expects($this->once())
            ->method('createCustomDOI')
            ->with(
                $this->equalTo('10.99999/nmrxiv.deadbeef'),
                $this->callback(function ($attrs) {
                    $hasIsIdenticalToCanonical = false;
                    $hasIsIdenticalToProvisional = false;
                    foreach ($attrs['relatedIdentifiers'] ?? [] as $entry) {
                        if (($entry['relationType'] ?? null) !== 'IsIdenticalTo') {
                            continue;
                        }
                        if (($entry['relatedIdentifier'] ?? null) === '10.99999/nmrxiv.P1') {
                            $hasIsIdenticalToCanonical = true;
                        }
                        if (($entry['relatedIdentifier'] ?? null) === '10.99999/nmrxiv.deadbeef') {
                            $hasIsIdenticalToProvisional = true;
                        }
                    }

                    // provisional record points at canonical, never at itself
                    return $hasIsIdenticalToCanonical && ! $hasIsIdenticalToProvisional;
                })
            )
            ->willReturn(['data' => ['id' => '10.99999/nmrxiv.deadbeef']]);

        $service->expects($this->once())
            ->method('getRelatedIdentifiers')
            ->with('10.99999/nmrxiv.P1')
            ->willReturn([]);

        $service->expects($this->once())
            ->method('putRelatedIdentifiers')
            ->with(
                $this->equalTo('10.99999/nmrxiv.P1'),
                $this->callback(function ($related) {
                    foreach ($related as $entry) {
                        if (($entry['relationType'] ?? null) === 'IsIdenticalTo'
                            && ($entry['relatedIdentifier'] ?? null) === '10.99999/nmrxiv.deadbeef'
                        ) {
                            return true;
                        }
                    }

                    return false;
                })
            )
            ->willReturn(['data' => ['id' => '10.99999/nmrxiv.P1']]);

        $project->linkProvisionalDoi($service);

        $this->assertNotNull($project->fresh()->provisional_doi_registered_at);
    }

    public function test_link_provisional_doi_dedupes_existing_relation(): void
    {
        $project = $this->makeProject([
            'identifier' => 1,
            'doi' => '10.99999/nmrxiv.P1',
            'provisional_doi' => '10.99999/nmrxiv.deadbeef',
        ]);

        $existing = [[
            'relatedIdentifier' => '10.99999/nmrxiv.deadbeef',
            'relatedIdentifierType' => 'DOI',
            'relationType' => 'IsIdenticalTo',
        ]];

        $service = $this->createMock(DOIService::class);
        $service->method('createCustomDOI')
            ->willReturn(['data' => ['id' => '10.99999/nmrxiv.deadbeef']]);
        $service->method('getRelatedIdentifiers')->willReturn($existing);

        $service->expects($this->once())
            ->method('putRelatedIdentifiers')
            ->with(
                '10.99999/nmrxiv.P1',
                $this->callback(function ($payload) {
                    $hits = array_filter(
                        $payload,
                        fn ($r) => ($r['relationType'] ?? null) === 'IsIdenticalTo'
                            && ($r['relatedIdentifier'] ?? null) === '10.99999/nmrxiv.deadbeef'
                    );

                    return count($hits) === 1;
                })
            )
            ->willReturn([]);

        $project->linkProvisionalDoi($service);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeProject(array $overrides = []): Project
    {
        $license = License::factory()->create();

        return Project::factory()->create(array_merge([
            'license_id' => $license->id,
        ], $overrides));
    }
}
