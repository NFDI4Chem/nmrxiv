<?php

namespace Tests\Unit\Support;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use App\Support\Nmr\InstrumentTypeManufacturerResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstrumentTypeManufacturerResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_maps_sample_folder_instrument_types_to_manufacturers(): void
    {
        $this->assertSame('Bruker', InstrumentTypeManufacturerResolver::toManufacturer('bruker'));
        $this->assertSame('JEOL', InstrumentTypeManufacturerResolver::toManufacturer('joel'));
        $this->assertSame('Magritek', InstrumentTypeManufacturerResolver::toManufacturer('magritek'));
        $this->assertSame('JCAMP', InstrumentTypeManufacturerResolver::toManufacturer('jcamp'));
        $this->assertSame('Varian', InstrumentTypeManufacturerResolver::toManufacturer('varian'));
        $this->assertNull(InstrumentTypeManufacturerResolver::toManufacturer('nmredata'));
    }

    public function test_resolves_manufacturer_from_dataset_folder(): void
    {
        $dataset = $this->makeDatasetWithFolder('bruker');

        $this->assertSame('Bruker', InstrumentTypeManufacturerResolver::forDataset($dataset));
    }

    public function test_resolves_manufacturer_from_child_folder(): void
    {
        $dataset = $this->makeDataset();

        $folder = FileSystemObject::factory()->directory()->create([
            'study_id' => $dataset->study_id,
            'instrument_type' => null,
        ]);

        FileSystemObject::factory()->file()->create([
            'parent_id' => $folder->id,
            'study_id' => $dataset->study_id,
            'instrument_type' => 'jcamp',
        ]);

        $dataset->update(['fs_id' => $folder->id]);
        $dataset->refresh();

        $this->assertSame('JCAMP', InstrumentTypeManufacturerResolver::forDataset($dataset));
    }

    public function test_resolves_manufacturer_from_parent_folder(): void
    {
        $dataset = $this->makeDataset();

        $parent = FileSystemObject::factory()->directory()->create([
            'study_id' => $dataset->study_id,
            'instrument_type' => 'magritek',
        ]);

        $folder = FileSystemObject::factory()->directory()->create([
            'parent_id' => $parent->id,
            'study_id' => $dataset->study_id,
            'instrument_type' => null,
        ]);

        $dataset->update(['fs_id' => $folder->id]);
        $dataset->refresh();

        $this->assertSame('Magritek', InstrumentTypeManufacturerResolver::forDataset($dataset));
    }

    private function makeDatasetWithFolder(string $instrumentType): Dataset
    {
        $dataset = $this->makeDataset();

        $folder = FileSystemObject::factory()->directory()->create([
            'study_id' => $dataset->study_id,
            'instrument_type' => $instrumentType,
        ]);

        $dataset->update(['fs_id' => $folder->id]);
        $dataset->refresh();

        return $dataset;
    }

    private function makeDataset(): Dataset
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => $project->id,
        ]);

        return Dataset::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
        ]);
    }
}
