<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tab' => ['nullable', 'string', Rule::in(['projects', 'samples'])],
            'projects_page' => ['nullable', 'integer', 'min:1'],
            'samples_page' => ['nullable', 'integer', 'min:1'],
            'projects_per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'samples_per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'projects_q' => ['nullable', 'string', 'max:255'],
            'samples_q' => ['nullable', 'string', 'max:255'],
            'projects_status' => ['nullable', 'string', Rule::in(['all', 'draft', 'published', 'archived', 'embargo'])],
            'samples_status' => ['nullable', 'string', Rule::in(['all', 'public', 'private'])],
            'workspace' => ['nullable', 'string', Rule::in(['default', 'shared', 'recent', 'starred', 'trashed'])],
            'action' => ['nullable', 'string', 'max:255'],
            'draft_id' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $tab = $this->query('tab');
        if (! in_array($tab, ['projects', 'samples'], true)) {
            $tab = 'projects';
        }

        $projectsStatus = $this->query('projects_status');
        if (! in_array($projectsStatus, ['all', 'draft', 'published', 'archived', 'embargo'], true)) {
            $projectsStatus = 'all';
        }

        $samplesStatus = $this->query('samples_status');
        if (! in_array($samplesStatus, ['all', 'public', 'private'], true)) {
            $samplesStatus = 'all';
        }

        $workspace = $this->query('workspace');
        if (! in_array($workspace, ['shared', 'recent', 'starred', 'trashed'], true)) {
            $workspace = 'default';
        }

        $this->merge([
            'tab' => $tab,
            'workspace' => $workspace,
            'projects_status' => $projectsStatus,
            'samples_status' => $samplesStatus,
            'projects_page' => max(1, (int) $this->query('projects_page', 1)),
            'samples_page' => max(1, (int) $this->query('samples_page', 1)),
            'projects_per_page' => max(1, min(50, (int) $this->query('projects_per_page', 10))),
            'samples_per_page' => max(1, min(50, (int) $this->query('samples_per_page', 12))),
        ]);
    }

    /**
     * Normalized dashboard query state for Inertia and redirects.
     *
     * @return array<string, mixed>
     */
    public function dashboardFilters(): array
    {
        $projectsPerPage = min(max(1, (int) $this->input('projects_per_page', 10)), 50);
        $samplesPerPage = min(max(1, (int) $this->input('samples_per_page', 12)), 50);

        $projectsQ = trim((string) $this->input('projects_q', ''));
        $samplesQ = trim((string) $this->input('samples_q', ''));

        return [
            'tab' => $this->input('tab', 'projects'),
            'workspace' => $this->input('workspace', 'default'),
            'projects_page' => max(1, (int) $this->input('projects_page', 1)),
            'samples_page' => max(1, (int) $this->input('samples_page', 1)),
            'projects_per_page' => $projectsPerPage,
            'samples_per_page' => $samplesPerPage,
            'projects_q' => $projectsQ,
            'samples_q' => $samplesQ,
            'projects_status' => $this->input('projects_status', 'all'),
            'samples_status' => $this->input('samples_status', 'all'),
            'action' => $this->query('action'),
            'draft_id' => $this->query('draft_id') !== null && $this->query('draft_id') !== ''
                ? (int) $this->query('draft_id')
                : null,
        ];
    }
}
