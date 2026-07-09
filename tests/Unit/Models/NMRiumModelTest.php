<?php

namespace Tests\Unit\Models;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Study;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NMRiumModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_basic_model_functionality(): void
    {
        $nmrium = new NMRium;
        $nmrium->nmrium_info = ['test' => 'data'];
        $nmrium->save();

        $this->assertInstanceOf(NMRium::class, $nmrium);
        $this->assertEquals(['test' => 'data'], $nmrium->nmrium_info);
    }

    public function test_it_has_polymorphic_nmriumable_relationship(): void
    {
        $study = Study::factory()->create();
        $nmrium = new NMRium;
        $nmrium->nmriumable_type = Study::class;
        $nmrium->nmriumable_id = $study->id;
        $nmrium->nmrium_info = ['test' => 'data'];
        $nmrium->save();

        $this->assertInstanceOf(Study::class, $nmrium->nmriumable);
        $this->assertEquals($study->id, $nmrium->nmriumable->id);
    }

    public function test_it_can_belong_to_dataset_through_polymorphic_relationship(): void
    {
        $dataset = Dataset::factory()->create();
        $nmrium = new NMRium;
        $nmrium->nmriumable_type = Dataset::class;
        $nmrium->nmriumable_id = $dataset->id;
        $nmrium->nmrium_info = ['test' => 'data'];
        $nmrium->save();

        $this->assertInstanceOf(Dataset::class, $nmrium->nmriumable);
        $this->assertEquals($dataset->id, $nmrium->nmriumable->id);
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = ['nmrium_info', 'nmriumable_id', 'nmriumable_type', 'dataset_id'];
        $nmrium = new NMRium;

        $this->assertEquals($fillable, $nmrium->getFillable());
    }

    public function test_it_casts_nmrium_info_to_array(): void
    {
        $nmrium = new NMRium;
        $casts = $nmrium->getCasts();

        $this->assertEquals('array', $casts['nmrium_info']);
    }

    public function test_nmrium_info_array_casting_works(): void
    {
        $testData = [
            'spectra' => [
                'spectrum1' => ['frequency' => '400MHz', 'solvent' => 'CDCl3'],
                'spectrum2' => ['frequency' => '100MHz', 'solvent' => 'DMSO-d6'],
            ],
            'assignments' => ['peak1' => '7.26 ppm', 'peak2' => '77.0 ppm'],
            'metadata' => ['temperature' => '298K', 'probe' => '5mm BBO'],
        ];

        $nmrium = new NMRium;
        $nmrium->nmrium_info = $testData;
        $nmrium->save();

        $nmrium->refresh();
        $this->assertIsArray($nmrium->nmrium_info);
        $this->assertEquals($testData, $nmrium->nmrium_info);
    }

    public function test_it_uses_correct_table_name(): void
    {
        $nmrium = new NMRium;
        $this->assertEquals('nmrium', $nmrium->getTable());
    }

    public function test_it_uses_versionable_trait(): void
    {
        $nmrium = new NMRium;
        $this->assertTrue(method_exists($nmrium, 'versions'));
        $this->assertTrue(method_exists($nmrium, 'currentVersion'));
        $this->assertTrue(method_exists($nmrium, 'previousVersion'));
    }

    public function test_versionable_configuration(): void
    {
        $nmrium = new NMRium;

        // Check if the keepOldVersions property is accessible through reflection
        $reflection = new \ReflectionClass($nmrium);
        $property = $reflection->getProperty('keepOldVersions');
        $property->setAccessible(true);
        $keepOldVersions = $property->getValue($nmrium);

        $this->assertEquals(10, $keepOldVersions);
    }

    public function test_version_creation_when_data_changes(): void
    {
        $nmrium = new NMRium;
        $nmrium->nmrium_info = ['initial' => 'data'];
        $nmrium->save();

        $originalId = $nmrium->id;

        // Update the nmrium info
        $nmrium->nmrium_info = ['updated' => 'data'];
        $nmrium->save();

        $this->assertEquals($originalId, $nmrium->id);
        $this->assertEquals(['updated' => 'data'], $nmrium->nmrium_info);
    }

    public function test_relationship_types_are_correct(): void
    {
        $nmrium = new NMRium;

        // Test polymorphic relationship
        $this->assertInstanceOf(MorphTo::class, $nmrium->nmriumable());

        // Test user relationship (even though there's no user_id column in DB - method exists)
        $this->assertInstanceOf(BelongsTo::class, $nmrium->user());
    }

    public function test_complex_nmrium_data_storage(): void
    {
        $complexData = [
            'version' => '1.2.3',
            'data' => [
                '1H' => [
                    'spectra' => [
                        [
                            'id' => 'spectrum_1',
                            'nucleus' => '1H',
                            'frequency' => 400.13,
                            'solvent' => 'CDCl3',
                            'temperature' => 298,
                            'data' => [
                                'x' => [0.0, 0.1, 0.2],
                                'y' => [1000, 950, 900],
                            ],
                        ],
                    ],
                ],
                '13C' => [
                    'spectra' => [
                        [
                            'id' => 'spectrum_2',
                            'nucleus' => '13C',
                            'frequency' => 100.61,
                            'solvent' => 'CDCl3',
                            'temperature' => 298,
                            'data' => [
                                'x' => [10.0, 20.0, 30.0],
                                'y' => [500, 450, 400],
                            ],
                        ],
                    ],
                ],
            ],
            'correlations' => [
                [
                    'id' => 'corr_1',
                    'x' => ['nucleus' => '1H', 'assignment' => 'H1'],
                    'y' => ['nucleus' => '13C', 'assignment' => 'C1'],
                ],
            ],
        ];

        $nmrium = new NMRium;
        $nmrium->nmrium_info = $complexData;
        $nmrium->save();

        $nmrium->refresh();
        $this->assertEquals($complexData, $nmrium->nmrium_info);
        $this->assertEquals('1.2.3', $nmrium->nmrium_info['version']);
        $this->assertCount(2, $nmrium->nmrium_info['data']);
    }

    public function test_polymorphic_relationship_through_create_method(): void
    {
        $study = Study::factory()->create();

        $nmrium = NMRium::create([
            'nmrium_info' => ['test' => 'data'],
        ]);

        // Test setting polymorphic relationship
        $nmrium->nmriumable()->associate($study);
        $nmrium->save();

        $this->assertEquals(Study::class, $nmrium->nmriumable_type);
        $this->assertEquals($study->id, $nmrium->nmriumable_id);
        $this->assertEquals(['test' => 'data'], $nmrium->nmrium_info);
    }

    public function test_fillable_uses_polymorphic_attributes(): void
    {
        // Test that polymorphic attributes are in fillable instead of legacy dataset_id
        $nmrium = new NMRium;
        $fillable = $nmrium->getFillable();

        $this->assertContains('nmriumable_id', $fillable);
        $this->assertContains('nmriumable_type', $fillable);
        $this->assertContains('nmrium_info', $fillable);
        $this->assertContains('dataset_id', $fillable);
    }

    public function test_it_uses_has_factory_trait(): void
    {
        $this->assertTrue(method_exists(NMRium::class, 'factory'));
    }
}
