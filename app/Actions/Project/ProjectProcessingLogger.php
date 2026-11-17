<?php

namespace App\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ProjectProcessingLogger
{
    /**
     * Add a processing log entry to the project.
     *
     * Persists via a targeted query update so in-memory attributes on the
     * job's Project instance are not overwritten mid-run.
     *
     * @param  array<string, mixed>  $context
     */
    public function log(Project $project, string $level, string $stage, string $message, array $context = []): void
    {
        $logEntry = [
            'timestamp' => now()->toISOString(),
            'level' => strtoupper($level),
            'stage' => $stage,
            'message' => $message,
            'context' => $context,
        ];

        $project->refresh();
        $existingLogs = $this->getLogs($project);
        $existingLogs[] = $logEntry;

        Project::whereKey($project->id)->update([
            'process_logs' => $existingLogs,
        ]);

        $project->setAttribute('process_logs', $existingLogs);

        $logLevel = strtolower($level);
        if (! in_array($logLevel, ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'], true)) {
            $logLevel = 'info';
        }

        Log::{$logLevel}($message, array_merge([
            'project_id' => $project->id,
            'stage' => $stage,
        ], $context));
    }

    /**
     * Get all processing logs for a project, normalizing legacy formats.
     *
     * Legacy entries look like: [{ "<unix_ts>": "Moving files...<br/>..." }].
     *
     * @return array<int, array{timestamp: string, level: string, stage: string, message: string, context: array<string, mixed>}>
     */
    public function getLogs(Project $project): array
    {
        $raw = $project->process_logs ?? [];

        if (! is_array($raw)) {
            return [];
        }

        $normalized = [];

        foreach ($raw as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            if ($this->isStructuredEntry($entry)) {
                $normalized[] = [
                    'timestamp' => (string) ($entry['timestamp'] ?? now()->toISOString()),
                    'level' => strtoupper((string) ($entry['level'] ?? 'INFO')),
                    'stage' => (string) ($entry['stage'] ?? 'legacy'),
                    'message' => (string) ($entry['message'] ?? ''),
                    'context' => is_array($entry['context'] ?? null) ? $entry['context'] : [],
                ];

                continue;
            }

            $legacy = $this->normalizeLegacyEntry($entry);
            if ($legacy !== null) {
                $normalized[] = $legacy;
            }
        }

        return $normalized;
    }

    /**
     * Clear all processing logs for a project.
     */
    public function clearLogs(Project $project): void
    {
        Project::whereKey($project->id)->update([
            'process_logs' => [],
        ]);

        $project->setAttribute('process_logs', []);
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function isStructuredEntry(array $entry): bool
    {
        return array_key_exists('message', $entry) || array_key_exists('stage', $entry);
    }

    /**
     * Convert a legacy timestamp-keyed entry into the structured shape.
     *
     * @param  array<string|int, mixed>  $entry
     * @return array{timestamp: string, level: string, stage: string, message: string, context: array<string, mixed>}|null
     */
    private function normalizeLegacyEntry(array $entry): ?array
    {
        if ($entry === []) {
            return null;
        }

        $timestampKey = array_key_first($entry);
        $message = $entry[$timestampKey] ?? null;

        if (! is_string($message) && ! is_numeric($message)) {
            return null;
        }

        $message = strip_tags(str_replace(['<br/>', '<br>', '<br />'], "\n", (string) $message));
        $message = trim(preg_replace("/[ \t]+/", ' ', $message) ?? $message);

        $isoTimestamp = now()->toISOString();
        if (is_numeric($timestampKey)) {
            $isoTimestamp = now()->setTimestamp((int) $timestampKey)->toISOString();
        }

        return [
            'timestamp' => $isoTimestamp,
            'level' => 'INFO',
            'stage' => 'legacy',
            'message' => $message,
            'context' => [],
        ];
    }
}
