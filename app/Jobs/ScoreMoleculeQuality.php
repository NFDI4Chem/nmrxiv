<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\Study;
use App\Support\Public\MoleculeQualityScorer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScoreMoleculeQuality implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 300;

    public function __construct(
        public readonly ?Study $study = null,
        public readonly ?Project $project = null,
    ) {}

    public static function forStudy(Study $study): self
    {
        return new self(study: $study);
    }

    public static function forProject(Project $project): self
    {
        return new self(project: $project);
    }

    public function uniqueId(): string
    {
        if ($this->study) {
            return 'study:'.$this->study->id;
        }

        if ($this->project) {
            return 'project:'.$this->project->id;
        }

        return 'noop';
    }

    public function handle(MoleculeQualityScorer $scorer): void
    {
        if ($this->study) {
            $scorer->rescoreForStudy($this->study);

            return;
        }

        if ($this->project) {
            $scorer->rescoreForProject($this->project);
        }
    }
}
