<?php

namespace Database\Factories;

use App\Models\Draft;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FileSystemObject>
 */
class FileSystemObjectFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word().'.'.$this->faker->fileExtension(),
            'uuid' => Str::uuid(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'relative_url' => '/'.$this->faker->slug().'/'.$this->faker->word(),
            'path' => $this->faker->filePath(),
            'type' => $this->faker->randomElement(['file', 'directory']), // Changed from 'folder' to 'directory'
            'key' => Str::uuid(),
            'is_public' => false,
            'is_deleted' => false,
            'is_archived' => false,
            'is_original' => true,
            'is_verified' => false,
            'is_processed' => false,
            'is_root' => false,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'level' => $this->faker->numberBetween(0, 3),
            'has_children' => false,
            'file_size' => $this->faker->numberBetween(1024, 1048576), // 1KB to 1MB
            'integrity_status' => $this->faker->randomElement(['pending', 'verified', 'failed']),
            'status' => $this->faker->randomElement(['present', 'missing']),
            'checksum_md5' => md5($this->faker->text()),
            'checksum_sha256' => hash('sha256', $this->faker->text()),
            'checksum_algorithm' => 'sha256',
        ];
    }

    /**
     * Configure the factory for a file type.
     */
    public function file(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'file',
            'has_children' => false,
        ]);
    }

    /**
     * Configure the factory for a directory type.
     */
    public function directory(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'directory',
            'has_children' => true,
        ]);
    }

    /**
     * Configure the factory for a missing status.
     */
    public function missing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'missing',
        ]);
    }

    /**
     * Configure the factory for a complete status.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'complete',
        ]);
    }

    /**
     * Configure the factory for root level files.
     */
    public function rootLevel(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 0,
            'parent_id' => null,
        ]);
    }

    /**
     * Configure the factory for child files.
     */
    public function childLevel(int $level = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => $level,
        ]);
    }

    /**
     * Configure the factory to belong to a draft.
     */
    public function forDraft(Draft $draft): static
    {
        return $this->state(fn (array $attributes) => [
            'draft_id' => $draft->id,
        ]);
    }

    /**
     * Configure the factory to belong to a project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }

    /**
     * Configure the factory to belong to a study.
     */
    public function forStudy(Study $study): static
    {
        return $this->state(fn (array $attributes) => [
            'study_id' => $study->id,
        ]);
    }
}
