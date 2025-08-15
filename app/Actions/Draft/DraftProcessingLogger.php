<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use Illuminate\Support\Facades\Log;

class DraftProcessingLogger
{
    /**
     * Add a processing log entry to the draft.
     */
    public function log(Draft $draft, string $level, string $message, array $context = []): void
    {
        $logEntry = [
            'timestamp' => now()->toISOString(),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
        ];

        // Get existing logs or initialize empty array
        $existingLogs = $draft->processing_logs ?? [];

        // Append new log to existing logs
        $existingLogs[] = $logEntry;

        // Update draft with new logs
        $draft->update(['processing_logs' => $existingLogs]);

        // Also log to Laravel logs
        Log::{$level}($message, array_merge(['draft_id' => $draft->id], $context));
    }

    /**
     * Add multiple log entries at once.
     */
    public function logBatch(Draft $draft, array $logs): void
    {
        $logEntries = [];

        foreach ($logs as $log) {
            $logEntry = [
                'timestamp' => now()->toISOString(),
                'level' => strtoupper($log['level']),
                'message' => $log['message'],
                'context' => $log['context'] ?? [],
            ];

            $logEntries[] = $logEntry;

            // Also log to Laravel logs
            Log::{$log['level']}($log['message'], array_merge(['draft_id' => $draft->id], $log['context'] ?? []));
        }

        // Get existing logs or initialize empty array
        $existingLogs = $draft->processing_logs ?? [];

        // Append new logs to existing logs
        $allLogs = array_merge($existingLogs, $logEntries);

        // Update draft with combined logs
        $draft->update(['processing_logs' => $allLogs]);
    }

    /**
     * Clear all processing logs for a draft.
     */
    public function clearLogs(Draft $draft): void
    {
        $draft->update(['processing_logs' => []]);
    }

    /**
     * Get all processing logs for a draft.
     */
    public function getLogs(Draft $draft): array
    {
        return $draft->processing_logs ?? [];
    }
}
