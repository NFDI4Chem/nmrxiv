<?php

namespace Tests\Feature\Study;

use App\Actions\Study\UpdateStudy;
use App\Models\Dataset;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\Ticker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateStudyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Team $team;

    protected Project $project;

    protected UpdateStudy $updater;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $this->updater = new UpdateStudy;
    }

    public function test_update_requires_name(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $this->expectException(ValidationException::class);

        $this->updater->update($study, []);
    }

    public function test_update_requires_license_when_making_public(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'license_id' => null,
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The license field is required when the study is made public.');

        $this->updater->update($study, [
            'name' => 'Test Study',
            'is_public' => 'true',
        ]);
    }

    public function test_update_basic_fields(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'name' => 'Original Name',
            'description' => 'Original Description',
        ]);

        $this->updater->update($study, [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        $study->refresh();

        $this->assertEquals('Updated Name', $study->name);
        $this->assertEquals('updated-name', $study->slug);
        $this->assertEquals('Updated Description', $study->description);
    }

    public function test_update_preserves_missing_fields(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'name' => 'Original Name',
            'description' => 'Original Description',
            'color' => '#FF0000',
            'starred' => true,
            'location' => 'Lab 1',
        ]);

        $this->updater->update($study, [
            'name' => 'Updated Name',
        ]);

        $study->refresh();

        $this->assertEquals('Updated Name', $study->name);
        $this->assertEquals('Original Description', $study->description);
        $this->assertEquals('#FF0000', $study->color);
        $this->assertTrue($study->starred);
        $this->assertEquals('Lab 1', $study->location);
    }

    public function test_update_all_optional_fields(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $this->updater->update($study, [
            'name' => 'Complete Update',
            'description' => 'New Description',
            'color' => '#00FF00',
            'starred' => true,
            'location' => 'Lab 2',
            'obfuscationcode' => 'ABC123',
            'type' => 'NMR',
            'species' => 'Mouse',
            'access' => 'link',
            'access_type' => 'editor',
            'study_photo_path' => 'path/to/photo.jpg',
        ]);

        $study->refresh();

        $this->assertEquals('Complete Update', $study->name);
        $this->assertEquals('New Description', $study->description);
        $this->assertEquals('#00FF00', $study->color);
        $this->assertTrue($study->starred);
        $this->assertEquals('Lab 2', $study->location);
        $this->assertEquals('ABC123', $study->obfuscationcode);
        $this->assertEquals('NMR', $study->type);
        $this->assertEquals('Mouse', $study->species);
        $this->assertEquals('link', $study->access);
        $this->assertEquals('editor', $study->access_type);
        $this->assertEquals('path/to/photo.jpg', $study->study_photo_path);
    }

    public function test_update_syncs_tags_when_provided(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $this->updater->update($study, [
            'name' => 'Tagged Study',
            'tags_array' => ['tag1', 'tag2', 'tag3'],
        ]);

        $study->refresh();

        $this->assertCount(3, $study->tags);
    }

    public function test_update_does_not_sync_tags_when_not_provided(): void
    {
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $study->attachTags(['existing-tag']);

        $this->updater->update($study, [
            'name' => 'Updated Study',
        ]);

        $study->refresh();

        $this->assertCount(1, $study->tags);
    }

    public function test_update_assigns_license_to_study_and_datasets(): void
    {
        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => null,
        ]);

        $dataset1 = Dataset::factory()->create([
            'study_id' => $study->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => null,
        ]);

        $dataset2 = Dataset::factory()->create([
            'study_id' => $study->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => null,
        ]);

        $this->updater->update($study, [
            'name' => 'Licensed Study',
            'license_id' => $license->id,
        ]);

        $study->refresh();
        $dataset1->refresh();
        $dataset2->refresh();

        $this->assertEquals($license->id, $study->license_id);
        $this->assertEquals($license->id, $dataset1->license_id);
        $this->assertEquals($license->id, $dataset2->license_id);
    }

    public function test_update_does_not_override_existing_dataset_license(): void
    {
        $license1 = License::factory()->create();
        $license2 = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => null,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $license1->id,
        ]);

        $this->updater->update($study, [
            'name' => 'Licensed Study',
            'license_id' => $license2->id,
        ]);

        $dataset->refresh();

        $this->assertEquals($license1->id, $dataset->license_id);
    }

    public function test_update_makes_study_public_with_license(): void
    {
        Ticker::factory()->create(['type' => 'sample', 'index' => 0]);
        Ticker::factory()->create(['type' => 'molecule', 'index' => 0]);
        Ticker::factory()->create(['type' => 'dataset', 'index' => 0]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $study->refresh();

        $this->assertTrue($study->is_public);
    }

    public function test_update_assigns_sample_identifier_when_publishing(): void
    {
        $sampleTicker = Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        $moleculeTicker = Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        $datasetTicker = Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'identifier' => null,
        ]);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $sample->refresh();
        $sampleTicker->refresh();

        $this->assertEquals(6, $sample->getRawOriginal('identifier'));
        $this->assertEquals(6, $sampleTicker->index);
    }

    public function test_update_preserves_existing_sample_identifier(): void
    {
        $sampleTicker = Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        $moleculeTicker = Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        $datasetTicker = Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'identifier' => 100,
        ]);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $sample->refresh();
        $sampleTicker->refresh();

        $this->assertEquals(100, $sample->getRawOriginal('identifier'));
        $this->assertEquals(5, $sampleTicker->index);
    }

    public function test_update_assigns_molecule_identifiers_when_publishing(): void
    {
        $sampleTicker = Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        $moleculeTicker = Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        $datasetTicker = Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'identifier' => 1,
        ]);

        $molecule1 = Molecule::factory()->create(['identifier' => null]);
        $molecule2 = Molecule::factory()->create(['identifier' => null]);

        $sample->molecules()->attach($molecule1->id);
        $sample->molecules()->attach($molecule2->id);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $molecule1->refresh();
        $molecule2->refresh();
        $moleculeTicker->refresh();

        $this->assertEquals(11, $molecule1->getRawOriginal('identifier'));
        $this->assertEquals(12, $molecule2->getRawOriginal('identifier'));
        $this->assertEquals(12, $moleculeTicker->index);
    }

    public function test_update_preserves_existing_molecule_identifiers(): void
    {
        $sampleTicker = Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        $moleculeTicker = Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        $datasetTicker = Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'identifier' => 1,
        ]);

        $molecule1 = Molecule::factory()->create(['identifier' => 100]);
        $molecule2 = Molecule::factory()->create(['identifier' => null]);

        $sample->molecules()->attach($molecule1->id);
        $sample->molecules()->attach($molecule2->id);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $molecule1->refresh();
        $molecule2->refresh();
        $moleculeTicker->refresh();

        $this->assertEquals(100, $molecule1->getRawOriginal('identifier'));
        $this->assertEquals(11, $molecule2->getRawOriginal('identifier'));
        $this->assertEquals(11, $moleculeTicker->index);
    }

    public function test_update_handles_study_without_sample(): void
    {
        $sampleTicker = Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        $moleculeTicker = Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        $datasetTicker = Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $study->refresh();

        $this->assertTrue($study->is_public);
        $this->assertNull($study->sample);
    }

    public function test_update_does_not_change_datasets_when_not_publishing(): void
    {
        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'license_id' => $license->id,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'identifier' => null,
            'is_public' => false,
            'release_date' => null,
        ]);

        $this->updater->update($study, [
            'name' => 'Updated Study',
            'is_public' => false,
        ]);

        $dataset->refresh();

        $this->assertNull($dataset->getRawOriginal('identifier'));
        $this->assertFalse($dataset->is_public);
        $this->assertNull($dataset->release_date);
    }

    public function test_update_handles_empty_datasets_collection(): void
    {
        Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $study->refresh();

        $this->assertTrue($study->is_public);
        $this->assertCount(0, $study->datasets);
    }

    public function test_update_handles_sample_with_no_molecules(): void
    {
        $sampleTicker = Ticker::factory()->create(['type' => 'sample', 'index' => 5]);
        $moleculeTicker = Ticker::factory()->create(['type' => 'molecule', 'index' => 10]);
        $datasetTicker = Ticker::factory()->create(['type' => 'dataset', 'index' => 20]);

        $license = License::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'identifier' => null,
        ]);

        $this->updater->update($study, [
            'name' => 'Public Study',
            'is_public' => true,
            'license' => $license->id,
            'license_id' => $license->id,
        ]);

        $sample->refresh();
        $moleculeTicker->refresh();

        $this->assertEquals(6, $sample->getRawOriginal('identifier'));
        $this->assertEquals(10, $moleculeTicker->index);
    }
}
