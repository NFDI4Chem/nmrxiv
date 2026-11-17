<?php

namespace Tests\Feature;

use App\Actions\Author\SyncProjectAuthors;
use App\Actions\Author\SyncStudyAuthors;
use App\Models\Author;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncStudyAuthorsTest extends TestCase
{
    use RefreshDatabase;

    private function makeProjectAndStudy(): array
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'project_id' => $project->id,
        ]);

        return [$project, $study];
    }

    public function test_study_authors_are_synced_with_order_and_contributor_type(): void
    {
        [, $study] = $this->makeProjectAndStudy();

        $first = Author::factory()->create();
        $first->contributor_type = 'Researcher';
        $second = Author::factory()->create();
        $second->contributor_type = 'DataCurator';

        $ids = app(SyncStudyAuthors::class)->handle($study, [$first, $second]);

        $this->assertSame([$first->id, $second->id], $ids);
        $this->assertDatabaseHas('author_study', [
            'study_id' => $study->id,
            'author_id' => $first->id,
            'contributor_type' => 'Researcher',
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('author_study', [
            'study_id' => $study->id,
            'author_id' => $second->id,
            'contributor_type' => 'DataCurator',
            'sort_order' => 2,
        ]);
    }

    public function test_resync_replaces_previous_study_authors(): void
    {
        [, $study] = $this->makeProjectAndStudy();

        $old = Author::factory()->create();
        $new = Author::factory()->create();

        $action = app(SyncStudyAuthors::class);
        $action->handle($study, [$old]);
        $action->handle($study, [$new]);

        $this->assertSame([$new->id], $study->fresh()->studyAuthors->pluck('id')->all());
    }

    public function test_eln_author_already_in_authors_table_is_not_duplicated_on_study(): void
    {
        [$project, $study] = $this->makeProjectAndStudy();

        // Author row left over from an earlier submission, not linked to this project.
        $existing = Author::factory()->create([
            'given_name' => 'Till',
            'family_name' => 'Opatz',
            'orcid_id' => '0000-0002-3266-4050',
            'affiliation' => 'Department of Chemistry',
        ]);

        $authors = app(SyncProjectAuthors::class)->handle($project, [[
            'given_name' => 'Till',
            'family_name' => 'Opatz',
            'orcid_id' => '0000-0002-3266-4050',
            'email_id' => null,
            'affiliation' => 'Department Chemie',
            'contributor_type' => 'Researcher',
        ]]);

        app(SyncStudyAuthors::class)->handle($study, $authors);

        // What ProcessSubmission does for samples: copy project authors onto the study.
        $project = $project->fresh()->load('authors');
        $pivot = [];
        foreach ($project->authors as $author) {
            $pivot[$author->id] = [
                'contributor_type' => $author->pivot->contributor_type,
                'sort_order' => $author->pivot->sort_order,
            ];
        }
        $study->studyAuthors()->syncWithoutDetaching($pivot);

        $studyAuthorIds = $study->fresh()->studyAuthors->pluck('id')->all();

        $this->assertCount(1, $studyAuthorIds);
        $this->assertSame($project->authors->pluck('id')->all(), $studyAuthorIds);
        $this->assertNotContains($existing->id, $studyAuthorIds);
    }
}
