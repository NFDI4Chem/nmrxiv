<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmbargoPublicationFailed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public string $reason,
        public ?Validation $validation = null,
        public ?string $exceptionClass = null,
        public bool $admin = false,
    ) {}

    public function build()
    {
        return $this->markdown('vendor.mail.embargo-publication-failed', [
            'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id),
            'project' => $this->project,
            'reason' => $this->reason,
            'validationFailures' => $this->validationFailures(),
            'exceptionClass' => $this->exceptionClass,
            'admin' => $this->admin,
        ])->subject(__('Embargo publication failed - '.$this->project->name));
    }

    private function validationFailures(): array
    {
        $report = $this->validation?->report;

        if (! is_array($report)) {
            return [];
        }

        $failures = [];
        $project = $report['project'] ?? [];

        $labels = [
            'title' => 'Project name',
            'description' => 'Project description',
            'keywords' => 'Project keywords',
            'citations' => 'Project citations',
            'authors' => 'Project authors',
            'license' => 'Project license',
            'image' => 'Project profile image',
        ];

        $hasCitationDetailFailures = $this->hasCitationDetailFailures($project);

        foreach ($labels as $field => $label) {
            if ($field === 'citations' && $hasCitationDetailFailures) {
                continue;
            }

            $this->addFailure($failures, $label, $project[$field] ?? null);
        }

        foreach (($project['citations_detail'] ?? []) as $citationIndex => $citation) {
            $citationLabel = $citation['name'] ?? 'Citation '.($citationIndex + 1);

            $this->addFailure($failures, "{$citationLabel}: DOI", $citation['doi'] ?? null);
        }

        foreach (($project['studies'] ?? []) as $studyIndex => $study) {
            $studyLabel = $study['name'] ?? 'Sample '.($studyIndex + 1);

            $studyLabels = [
                'title' => 'sample title',
                'description' => 'sample description',
                'keywords' => 'sample keywords',
                'sample' => 'sample metadata',
                'nmrium_info' => 'spectra',
                'molecules' => 'compound information',
            ];

            foreach ($studyLabels as $field => $label) {
                $this->addFailure($failures, "{$studyLabel}: {$label}", $study[$field] ?? null);
            }

            foreach (($study['datasets'] ?? []) as $datasetIndex => $dataset) {
                $datasetLabel = $dataset['name'] ?? "{$studyLabel}: spectral dataset ".($datasetIndex + 1);

                $datasetLabels = [
                    'files' => 'files',
                    'nmrium_info' => 'spectra',
                    'assay' => 'assay metadata',
                    'assignments' => 'assignments',
                ];

                foreach ($datasetLabels as $field => $label) {
                    $this->addFailure($failures, "{$datasetLabel}: {$label}", $dataset[$field] ?? null);
                }
            }
        }

        return $failures;
    }

    private function addFailure(array &$failures, string $label, mixed $value): void
    {
        if (is_string($value) && str_starts_with($value, 'false|')) {
            $rule = substr($value, 6);

            if ($this->isRequiredRule($rule)) {
                $failures[] = $label.' ('.$this->formatRule($rule).')';
            }
        }
    }

    private function formatRule(string $rule): string
    {
        return str_replace('|', ', ', $rule);
    }

    private function isRequiredRule(string $rule): bool
    {
        return in_array('required', explode('|', $rule), true);
    }

    private function hasCitationDetailFailures(array $project): bool
    {
        foreach (($project['citations_detail'] ?? []) as $citation) {
            $value = $citation['doi'] ?? null;

            if (is_string($value) && str_starts_with($value, 'false|') && $this->isRequiredRule(substr($value, 6))) {
                return true;
            }
        }

        return false;
    }
}
