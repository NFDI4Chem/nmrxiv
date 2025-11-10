<?php

namespace Tests\Unit\Models;

use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_projects(): void
    {
        $license = License::factory()->create();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $license->projects());
    }

    public function test_it_has_many_studies(): void
    {
        $license = License::factory()->create();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $license->studies());
    }

    public function test_it_has_many_datasets(): void
    {
        $license = License::factory()->create();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $license->datasets());
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'title',
            'slug',
            'spdx_id',
            'url',
            'description',
            'body',
            'category',
        ];

        $license = new License;
        $this->assertEquals($fillable, $license->getFillable());
    }

    public function test_it_can_be_created_with_factory(): void
    {
        $license = License::factory()->create();

        $this->assertInstanceOf(License::class, $license);
        $this->assertDatabaseHas('licenses', [
            'id' => $license->id,
            'title' => $license->title,
            'slug' => $license->slug,
            'spdx_id' => $license->spdx_id,
        ]);
    }

    public function test_it_has_timestamps(): void
    {
        $license = License::factory()->create();

        $this->assertNotNull($license->created_at);
        $this->assertNotNull($license->updated_at);
    }

    public function test_it_can_be_created_with_specific_attributes(): void
    {
        $license = License::factory()->create([
            'title' => 'MIT License',
            'slug' => 'mit-license',
            'spdx_id' => 'MIT',
            'url' => 'https://opensource.org/licenses/MIT',
            'description' => 'A permissive license',
            'body' => 'Permission is hereby granted...',
            'category' => 'permissive',
        ]);

        $this->assertEquals('MIT License', $license->title);
        $this->assertEquals('mit-license', $license->slug);
        $this->assertEquals('MIT', $license->spdx_id);
        $this->assertEquals('https://opensource.org/licenses/MIT', $license->url);
        $this->assertEquals('A permissive license', $license->description);
        $this->assertEquals('Permission is hereby granted...', $license->body);
        $this->assertEquals('permissive', $license->category);
    }

    public function test_factory_creates_unique_slugs(): void
    {
        $license1 = License::factory()->create();
        $license2 = License::factory()->create();

        $this->assertNotEquals($license1->slug, $license2->slug);
    }

    public function test_all_required_fields_are_fillable(): void
    {
        $data = [
            'title' => 'Creative Commons',
            'slug' => 'creative-commons',
            'spdx_id' => 'CC-BY-4.0',
            'url' => 'https://creativecommons.org/licenses/by/4.0/',
            'description' => 'Creative Commons Attribution 4.0 International',
            'body' => 'You are free to share and adapt...',
            'category' => 'creative-commons',
        ];

        $license = License::create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $license->$key);
        }
    }

    public function test_license_model_uses_factory_trait(): void
    {
        $this->assertTrue(method_exists(License::class, 'factory'));
    }
}
