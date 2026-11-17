<?php

namespace Tests\Unit\Actions;

use App\Actions\Project\ProjectProcessingLogger;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectProcessingLoggerTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private ProjectProcessingLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $user->id,
            'process_logs' => null,
        ]);
        $this->logger = new ProjectProcessingLogger;
    }

    public function test_log_appends_structured_entries_in_order(): void
    {
        $this->logger->log($this->project, 'info', 'queued', 'Queued for processing.');
        $this->logger->log($this->project, 'warning', 'moving_files', 'Moving files.', ['folder_count' => 2]);

        $this->project->refresh();
        $logs = $this->logger->getLogs($this->project);

        $this->assertCount(2, $logs);
        $this->assertSame('queued', $logs[0]['stage']);
        $this->assertSame('INFO', $logs[0]['level']);
        $this->assertSame('Queued for processing.', $logs[0]['message']);
        $this->assertSame('moving_files', $logs[1]['stage']);
        $this->assertSame('WARNING', $logs[1]['level']);
        $this->assertSame(['folder_count' => 2], $logs[1]['context']);
        $this->assertNotEmpty($logs[0]['timestamp']);
    }

    public function test_get_logs_normalizes_legacy_entries(): void
    {
        $timestamp = now()->subHour()->timestamp;
        $this->project->process_logs = [
            [$timestamp => 'Moving files in progress<br/> Moving files complete'],
        ];
        $this->project->save();

        $logs = $this->logger->getLogs($this->project->fresh());

        $this->assertCount(1, $logs);
        $this->assertSame('INFO', $logs[0]['level']);
        $this->assertSame('legacy', $logs[0]['stage']);
        $this->assertStringContainsString('Moving files in progress', $logs[0]['message']);
        $this->assertStringContainsString('Moving files complete', $logs[0]['message']);
        $this->assertStringNotContainsString('<br/>', $logs[0]['message']);
    }

    public function test_get_logs_preserves_structured_entries(): void
    {
        $entry = [
            'timestamp' => now()->toISOString(),
            'level' => 'ERROR',
            'stage' => 'failed',
            'message' => 'Something broke',
            'context' => ['exception' => 'RuntimeException'],
        ];

        $this->project->process_logs = [$entry];
        $this->project->save();

        $logs = $this->logger->getLogs($this->project->fresh());

        $this->assertCount(1, $logs);
        $this->assertSame($entry['message'], $logs[0]['message']);
        $this->assertSame('ERROR', $logs[0]['level']);
        $this->assertSame('failed', $logs[0]['stage']);
        $this->assertSame(['exception' => 'RuntimeException'], $logs[0]['context']);
    }

    public function test_clear_logs_empties_process_logs(): void
    {
        $this->logger->log($this->project, 'info', 'started', 'Started');
        $this->logger->clearLogs($this->project);

        $this->project->refresh();
        $this->assertSame([], $this->logger->getLogs($this->project));
    }
}
