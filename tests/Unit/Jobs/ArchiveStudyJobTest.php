<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ArchiveStudy;
use App\Models\Project;
use Aws\S3\S3Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveStudyJobTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->project = Project::factory()->create();
    }

    public function test_it_implements_should_be_unique_interface(): void
    {
        $job = new ArchiveStudy($this->project);

        $this->assertInstanceOf(ShouldBeUnique::class, $job);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ArchiveStudy($this->project);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_it_has_zero_timeout(): void
    {
        $job = new ArchiveStudy($this->project);

        $this->assertEquals(0, $job->timeout);
    }

    public function test_it_stores_project_in_constructor(): void
    {
        $job = new ArchiveStudy($this->project);

        $this->assertEquals($this->project->id, $job->project->id);
    }

    public function test_unique_id_returns_project_id(): void
    {
        $job = new ArchiveStudy($this->project);

        $this->assertEquals($this->project->id, $job->uniqueId());
    }

    public function test_unique_id_returns_string(): void
    {
        $job = new ArchiveStudy($this->project);

        $this->assertIsString($job->uniqueId());
    }

    public function test_storage_client_returns_s3_client_instance(): void
    {
        $job = new ArchiveStudy($this->project);

        $reflection = new \ReflectionClass($job);
        $method = $reflection->getMethod('storageClient');
        $method->setAccessible(true);

        $client = $method->invoke($job);

        $this->assertInstanceOf(S3Client::class, $client);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ArchiveStudy($this->project);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }
}
