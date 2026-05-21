<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\PublishEmbargoProject;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PublishEmbargoProjectTest extends TestCase
{
    use RefreshDatabase;

    private PublishEmbargoProject $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishEmbargoProject;
    }

    public function test_publish_rejects_project_that_is_already_public(): void
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'status' => 'embargo',
            'doi' => '10.1234/public',
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['Project is already public.'], $exception->errors()['publish']);
        }
    }

    public function test_publish_rejects_non_embargo_project(): void
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'draft',
            'doi' => '10.1234/draft',
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['Project is not in embargo status.'], $exception->errors()['publish']);
        }
    }

    public function test_publish_requires_doi(): void
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'embargo',
            'doi' => null,
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['A DOI is required before publishing this project.'], $exception->errors()['publish']);
        }
    }
}
