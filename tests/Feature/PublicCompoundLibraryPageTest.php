<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCompoundLibraryPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
    }

    /**
     * @param  array<string, mixed>  $studyAttributes
     * @param  array<string, mixed>  $moleculeAttributes
     */
    private function createCompound(
        array $studyAttributes = [],
        array $moleculeAttributes = [],
        ?string $datasetType = null,
        ?User $owner = null,
    ): Molecule {
        $owner ??= $this->owner;

        $study = Study::factory()->create(array_merge([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'project_id' => null,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ], $studyAttributes));

        $molecule = Molecule::factory()->create($moleculeAttributes);
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        if ($datasetType !== null) {
            Dataset::factory()->create([
                'study_id' => $study->id,
                'team_id' => $study->team_id,
                'owner_id' => $study->owner_id,
                'project_id' => $study->project_id,
                'type' => $datasetType,
                'is_public' => true,
                'is_archived' => false,
                'is_deleted' => false,
                'has_nmrium' => true,
            ]);
        }

        return $molecule;
    }

    /**
     * @return array<string, mixed>
     */
    private function libraryPage(string $query = '', ?Team $team = null): array
    {
        $team ??= $this->owner->currentTeam;

        return $this->assertInertiaPageComponent(
            $this->get('/library/'.$team->compound_library_code.$query),
            'Public/CompoundLibrary'
        );
    }

    /**
     * @param  array<string, mixed>  $page
     * @return list<int>
     */
    private function compoundIds(array $page): array
    {
        return collect($page['props']['compounds']['data'])->pluck('id')->all();
    }

    public function test_guest_can_view_compound_library(): void
    {
        $page = $this->libraryPage();

        foreach (['library', 'compounds', 'stats', 'techniques', 'filters'] as $prop) {
            $this->assertArrayHasKey($prop, $page['props']);
        }

        $this->assertSame($this->owner->name, $page['props']['library']['name']);
        $this->assertSame(
            $this->owner->currentTeam->compoundLibraryUrl(),
            $page['props']['library']['share_url']
        );
    }

    public function test_personal_library_name_falls_back_to_first_and_last_name(): void
    {
        $this->owner->forceFill(['name' => null, 'first_name' => 'Ada', 'last_name' => 'Lovelace'])->save();

        $this->assertSame('Ada Lovelace', $this->libraryPage()['props']['library']['name']);
    }

    public function test_unknown_code_returns_not_found(): void
    {
        $this->get('/library/UnknownLibraryCode')->assertNotFound();
    }

    public function test_team_id_does_not_resolve_a_library(): void
    {
        $this->get('/library/'.$this->owner->currentTeam->id)->assertNotFound();
    }

    public function test_teams_receive_a_unique_hidden_library_code_on_creation(): void
    {
        $team = $this->owner->currentTeam;
        $otherTeam = User::factory()->withPersonalTeam()->create()->currentTeam;

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{16}$/', $team->compound_library_code);
        $this->assertNotSame($team->compound_library_code, $otherTeam->compound_library_code);
        $this->assertArrayNotHasKey('compound_library_code', $team->toArray());
        $this->assertSame(url('/library/'.$team->compound_library_code), $team->compoundLibraryUrl());
    }

    public function test_unpublished_compounds_are_listed_as_locked_after_published_ones(): void
    {
        $public = $this->createCompound();
        $private = $this->createCompound(['is_public' => false], ['identifier' => null]);
        $archived = $this->createCompound(['is_archived' => true]);
        $deleted = $this->createCompound(['is_deleted' => true]);

        $trashedProject = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->owner->currentTeam->id,
            'is_deleted' => true,
        ]);
        $inTrashedProject = $this->createCompound(['project_id' => $trashedProject->id]);

        $page = $this->libraryPage();
        $ids = $this->compoundIds($page);
        $locked = collect($page['props']['compounds']['data'])->pluck('is_locked', 'id');

        $this->assertSame($public->id, $ids[0]);
        $this->assertEqualsCanonicalizing([$public->id, $private->id, $archived->id], $ids);
        $this->assertNotContains($deleted->id, $ids);
        $this->assertNotContains($inTrashedProject->id, $ids);

        $this->assertFalse($locked[$public->id]);
        $this->assertTrue($locked[$private->id]);
        $this->assertTrue($locked[$archived->id]);
    }

    public function test_visibility_filter_separates_published_and_unpublished(): void
    {
        $public = $this->createCompound();
        $private = $this->createCompound(['is_public' => false]);

        $this->assertSame([$public->id], $this->compoundIds($this->libraryPage('?visibility=public')));
        $this->assertSame([$private->id], $this->compoundIds($this->libraryPage('?visibility=private')));
        $this->assertSame('all', $this->libraryPage('?visibility=bogus')['props']['filters']['visibility']);
    }

    public function test_compounds_from_other_teams_are_excluded(): void
    {
        $mine = $this->createCompound();
        $otherOwner = User::factory()->withPersonalTeam()->create();
        $theirs = $this->createCompound(owner: $otherOwner);

        $ids = $this->compoundIds($this->libraryPage());

        $this->assertContains($mine->id, $ids);
        $this->assertNotContains($theirs->id, $ids);
    }

    public function test_personal_team_excludes_studies_owned_by_other_users(): void
    {
        $mine = $this->createCompound();
        $otherUser = User::factory()->withPersonalTeam()->create();
        $foreign = $this->createCompound(['owner_id' => $otherUser->id]);

        $ids = $this->compoundIds($this->libraryPage());

        $this->assertContains($mine->id, $ids);
        $this->assertNotContains($foreign->id, $ids);
    }

    public function test_search_matches_name_and_compound_identifier(): void
    {
        $caffeine = $this->createCompound(moleculeAttributes: ['name' => 'Caffeine library token', 'identifier' => 424242]);
        $other = $this->createCompound(moleculeAttributes: ['name' => 'Theobromine', 'identifier' => 515151]);

        $this->assertSame([$caffeine->id], $this->compoundIds($this->libraryPage('?q=caffeine')));
        $this->assertSame([$other->id], $this->compoundIds($this->libraryPage('?q=M515151')));
    }

    public function test_technique_filter_limits_compounds(): void
    {
        $proton = $this->createCompound(datasetType: '1H NMR - 1D');
        $carbon = $this->createCompound(datasetType: '13C NMR - 1D');

        $page = $this->libraryPage('?technique='.urlencode('1H NMR - 1D'));

        $this->assertSame([$proton->id], $this->compoundIds($page));
        $this->assertNotContains($carbon->id, $this->compoundIds($page));
        $this->assertSame('1H NMR - 1D', $page['props']['filters']['technique']);
    }

    public function test_sort_by_name_orders_alphabetically(): void
    {
        $zeta = $this->createCompound(moleculeAttributes: ['name' => 'Zeta compound']);
        $alpha = $this->createCompound(moleculeAttributes: ['name' => 'Alpha compound']);

        $this->assertSame([$alpha->id, $zeta->id], $this->compoundIds($this->libraryPage('?sort=name')));
    }

    public function test_invalid_sort_falls_back_to_recent(): void
    {
        $page = $this->libraryPage('?sort=bogus');

        $this->assertSame('recent', $page['props']['filters']['sort']);
    }

    public function test_stats_refresh_after_a_study_is_published(): void
    {
        $molecule = $this->createCompound(['is_public' => false], datasetType: '1H NMR - 1D');

        $before = $this->libraryPage();
        $this->assertSame(0, $before['props']['stats']['compounds']);
        $this->assertSame(1, $before['props']['unpublished']['compounds']);

        Study::query()->update(['is_public' => true, 'updated_at' => now()->addMinute()]);
        Dataset::query()->update(['is_public' => true]);

        $page = $this->libraryPage();

        $this->assertSame(1, $page['props']['stats']['compounds']);
        $this->assertSame(1, $page['props']['stats']['samples']);
        $this->assertSame(0, $page['props']['unpublished']['compounds']);
        $this->assertSame(
            [['label' => '1H NMR - 1D', 'compounds' => 1, 'unpublished_compounds' => 0]],
            $page['props']['techniques']
        );
        $this->assertSame([$molecule->id], $this->compoundIds($page));
    }

    public function test_stats_and_technique_totals_are_included(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->owner->currentTeam->id,
            'is_deleted' => false,
        ]);

        $proton = $this->createCompound(['project_id' => $project->id], datasetType: '1H NMR - 1D');
        $this->createCompound(datasetType: '1H NMR - 1D / 13C NMR - 1D');
        $private = $this->createCompound(['is_public' => false], datasetType: '13C NMR - 1D / HSQC');

        $page = $this->libraryPage();

        $this->assertSame(2, $page['props']['stats']['compounds']);
        $this->assertSame(2, $page['props']['stats']['samples']);
        $this->assertSame(2, $page['props']['stats']['spectra']);
        $this->assertSame(1, $page['props']['stats']['projects']);
        $this->assertSame(2, $page['props']['stats']['techniques']);
        $this->assertSame(['compounds' => 1, 'samples' => 1, 'spectra' => 1], $page['props']['unpublished']);
        $this->assertSame([
            ['label' => '1H NMR - 1D', 'compounds' => 2, 'unpublished_compounds' => 0],
            ['label' => '13C NMR - 1D', 'compounds' => 1, 'unpublished_compounds' => 1],
            ['label' => 'HSQC', 'compounds' => 0, 'unpublished_compounds' => 1],
        ], $page['props']['techniques']);

        $cards = collect($page['props']['compounds']['data'])->keyBy('id');
        $this->assertSame(1, $cards[$proton->id]['workspace_samples_count']);
        $this->assertSame(['1H NMR - 1D' => 1], $cards[$proton->id]['workspace_experiment_type_counts']);
        $this->assertSame(['13C NMR - 1D' => 1, 'HSQC' => 1], $cards[$private->id]['workspace_experiment_type_counts']);
        $this->assertArrayNotHasKey('library_has_public_study', $cards[$private->id]);

        $this->assertSame([$private->id], $this->compoundIds($this->libraryPage('?technique=HSQC')));
    }
}
