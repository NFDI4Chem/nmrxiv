<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteCitations;
use App\Models\Citation;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteCitationsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        $citations = Citation::factory()->count(3)->make();
        DeleteCitations::dispatch($citations);

        Queue::assertPushed(DeleteCitations::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $citations = Citation::factory()->count(3)->make();
        $job = new DeleteCitations($citations);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $citations = Citation::factory()->count(3)->make();
        $job = new DeleteCitations($citations);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_handle_deletes_citations_not_attached_to_projects(): void
    {
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();
        $citation3 = Citation::factory()->create();

        // Attach only citation1 to a project
        DB::table('citation_project')->insert([
            'citation_id' => $citation1->id,
            'project_id' => 1,
        ]);

        $citations = collect([$citation1, $citation2, $citation3]);

        $job = new DeleteCitations($citations);
        $job->handle();

        // Citation1 should still exist (has project)
        $this->assertDatabaseHas('citations', ['id' => $citation1->id]);

        // Citation2 and Citation3 should be deleted (no projects)
        $this->assertDatabaseMissing('citations', ['id' => $citation2->id]);
        $this->assertDatabaseMissing('citations', ['id' => $citation3->id]);
    }

    public function test_handle_keeps_all_citations_if_all_attached_to_projects(): void
    {
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();

        DB::table('citation_project')->insert([
            ['citation_id' => $citation1->id, 'project_id' => 1],
            ['citation_id' => $citation2->id, 'project_id' => 1],
        ]);

        $citations = collect([$citation1, $citation2]);

        $job = new DeleteCitations($citations);
        $job->handle();

        $this->assertDatabaseHas('citations', ['id' => $citation1->id]);
        $this->assertDatabaseHas('citations', ['id' => $citation2->id]);
    }

    public function test_handle_deletes_all_citations_if_none_attached_to_projects(): void
    {
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();

        $citations = collect([$citation1, $citation2]);

        $job = new DeleteCitations($citations);
        $job->handle();

        $this->assertDatabaseMissing('citations', ['id' => $citation1->id]);
        $this->assertDatabaseMissing('citations', ['id' => $citation2->id]);
    }

    public function test_handle_keeps_citation_attached_only_to_study(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $citation = Citation::factory()->create();

        DB::table('citation_study')->insert([
            'citation_id' => $citation->id,
            'study_id' => $study->id,
            'user' => (string) $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $job = new DeleteCitations(collect([$citation]));
        $job->handle();

        $this->assertDatabaseHas('citations', ['id' => $citation->id]);
    }

    public function test_handle_with_empty_citations_array(): void
    {
        $citations = collect([]);

        $job = new DeleteCitations($citations);
        $job->handle();

        // Should not throw any errors
        $this->assertTrue(true);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        $citations = Citation::factory()->count(3)->make();
        DeleteCitations::dispatch($citations)->onQueue('deletions');

        Queue::assertPushedOn('deletions', DeleteCitations::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        $citations = Citation::factory()->count(3)->make();
        DeleteCitations::dispatch($citations)->delay(now()->addMinutes(5));

        Queue::assertPushed(DeleteCitations::class);
    }
}
