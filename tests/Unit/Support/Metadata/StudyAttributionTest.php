<?php

namespace Tests\Unit\Support\Metadata;

use App\Models\Author;
use App\Models\Citation;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Support\Bagit\StudyCitationCff;
use App\Support\Metadata\StudyAttribution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StudyAttributionTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudy(array $studyAttributes = [], ?Project $project = null): Study
    {
        $owner = User::factory()->create(['first_name' => 'Olive', 'last_name' => 'Owner', 'orcid_id' => '0000-0001-0000-0001']);
        $license = License::factory()->create(['spdx_id' => 'CC-BY-4.0']);
        $project ??= Project::factory()->create(['owner_id' => $owner->id, 'license_id' => $license->id, 'is_public' => true]);

        return Study::factory()->create(array_merge([
            'project_id' => $project->id,
            'owner_id' => $owner->id,
            'license_id' => $license->id,
            'is_public' => true,
            'identifier' => 7001,
        ], $studyAttributes));
    }

    public function test_project_authors_and_citations_are_used_first(): void
    {
        $study = $this->makeStudy();
        $author = Author::factory()->create(['given_name' => 'Pat', 'family_name' => 'Project', 'orcid_id' => '0000-0002-0000-0002']);
        $study->project->authors()->attach($author->id, ['contributor_type' => 'Researcher', 'sort_order' => 1]);
        $citation = Citation::factory()->create(['doi' => 'https://doi.org/10.1/abc', 'title' => 'A paper', 'authors' => 'Pat Project']);
        $study->project->citations()->attach($citation->id);

        $result = StudyAttribution::resolve($study->fresh());

        $this->assertSame('project', $result['source']);
        $this->assertSame('Project', $result['authors'][0]['family_name']);
        $this->assertSame('Researcher', $result['authors'][0]['role']);
        $this->assertSame('https://orcid.org/0000-0002-0000-0002', $result['authors'][0]['orcid']);
        $this->assertSame('10.1/abc', $result['citations'][0]['doi']);
    }

    public function test_study_authors_are_used_when_project_has_none(): void
    {
        $study = $this->makeStudy();
        $author = Author::factory()->create(['given_name' => 'Sam', 'family_name' => 'Study']);
        $study->studyAuthors()->attach($author->id, ['contributor_type' => 'Researcher', 'sort_order' => 1]);

        $result = StudyAttribution::resolve($study->fresh());

        $this->assertSame('study', $result['source']);
        $this->assertSame('Study', $result['authors'][0]['family_name']);
    }

    public function test_owner_is_used_when_nobody_else_is_listed(): void
    {
        $study = $this->makeStudy();

        $result = StudyAttribution::resolve($study->fresh());

        $this->assertSame('owner', $result['source']);
        $this->assertSame('Owner', $result['authors'][0]['family_name']);
        $this->assertSame('Owner', $result['authors'][0]['role']);
        $this->assertSame([], $result['citations']);
    }

    public function test_private_project_metadata_is_not_used_or_linked(): void
    {
        Http::fake(['pubchem.ncbi.nlm.nih.gov/*' => Http::response([], 404)]);

        $study = $this->makeStudy();
        Sample::factory()->create(['study_id' => $study->id]);
        $study->project->update(['is_public' => false]);
        $author = Author::factory()->create(['given_name' => 'Pat', 'family_name' => 'Private']);
        $study->project->authors()->attach($author->id, ['contributor_type' => 'Researcher', 'sort_order' => 1]);
        $citation = Citation::factory()->create(['doi' => '10.1/private', 'title' => 'Private paper', 'authors' => 'Pat Private']);
        $study->project->citations()->attach($citation->id);

        $result = StudyAttribution::resolve($study->fresh());

        $this->assertSame('owner', $result['source']);
        $this->assertSame([], $result['citations']);

        $response = $this->getJson('/api/v1/schemas/bioschemas/S7001')->assertOk();
        $this->assertArrayNotHasKey('isPartOf', $response->json());
        $this->assertStringNotContainsString('Private', json_encode($response->json()));
    }

    public function test_malformed_orcid_values_are_dropped_and_valid_ones_normalised(): void
    {
        $this->assertSame('https://orcid.org/0000-0002-1825-009X', StudyAttribution::orcidUrl('0000-0002-1825-009x'));
        $this->assertSame('https://orcid.org/0000-0002-1825-0097', StudyAttribution::orcidUrl('http://www.orcid.org/0000-0002-1825-0097/'));
        $this->assertNull(StudyAttribution::orcidUrl('not-an-orcid'));
        $this->assertNull(StudyAttribution::orcidUrl('https://example.com/0000-0002-1825-0097'));
        $this->assertNull(StudyAttribution::orcidUrl('   '));

        $study = $this->makeStudy();
        $study->owner->update(['orcid_id' => 'ORCID: unknown']);

        $this->assertStringNotContainsString('orcid:', StudyCitationCff::build($study->fresh()));
    }

    public function test_cff_has_the_required_keys_and_structure_data(): void
    {
        $study = $this->makeStudy(['name' => 'Caffeine "test"', 'doi' => '10.57992/nmrxiv.p1.s7001']);
        $citation = Citation::factory()->create(['doi' => '10.1/abc', 'title' => 'A paper', 'authors' => 'Pat Project; Quinn Other']);
        $study->project->citations()->attach($citation->id);

        $cff = StudyCitationCff::build($study->fresh());

        $this->assertStringContainsString('cff-version: 1.2.0', $cff);
        $this->assertStringContainsString('type: dataset', $cff);
        $this->assertStringContainsString('title: "Caffeine \"test\""', $cff);
        $this->assertStringContainsString("authors:\n  - family-names: \"Owner\"\n    given-names: \"Olive\"\n    orcid: \"https://orcid.org/0000-0001-0000-0001\"", $cff);
        $this->assertStringContainsString('    value: "10.57992/nmrxiv.p1.s7001"', $cff);
        $this->assertStringContainsString('license: "CC-BY-4.0"', $cff);
        $this->assertStringContainsString('      - name: "Quinn Other"', $cff);
    }

    public function test_bioschema_study_has_author_citation_and_a_single_smiles(): void
    {
        Http::fake(['pubchem.ncbi.nlm.nih.gov/*' => Http::response([], 404)]);

        $study = $this->makeStudy();
        $author = Author::factory()->create(['given_name' => 'Pat', 'family_name' => 'Project']);
        $study->project->authors()->attach($author->id, ['contributor_type' => 'Researcher', 'sort_order' => 1]);
        $citation = Citation::factory()->create(['doi' => '10.1/abc', 'title' => 'A paper', 'authors' => 'Pat Project']);
        $study->project->citations()->attach($citation->id);

        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule = Molecule::factory()->create(['canonical_smiles' => 'CN1C=NC2=C1C(=O)N(C(=O)N2C)C']);
        $sample->molecules()->attach($molecule->id);

        $response = $this->getJson('/api/v1/schemas/bioschemas/S7001');

        $response->assertOk();
        $response->assertJsonPath('author.0.@type', 'Role');
        $response->assertJsonPath('author.0.roleName', 'Researcher');
        $response->assertJsonPath('author.0.author.familyName', 'Project');
        $this->assertArrayNotHasKey('creator', $response->json());
        $response->assertJsonPath('citation.0.@type', 'ScholarlyArticle');
        $response->assertJsonPath('citation.0.sameAs', 'https://doi.org/10.1/abc');
        $response->assertJsonPath('about.hasBioChemEntityPart.0.smiles', 'CN1C=NC2=C1C(=O)N(C(=O)N2C)C');
        $response->assertJsonPath('isPartOf.@type', 'Study');
    }
}
