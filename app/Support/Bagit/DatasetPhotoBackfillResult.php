<?php

namespace App\Support\Bagit;

/**
 * Counters and console messages collected while backfilling the dataset
 * photos of a single study, so callers (the artisan command, queued jobs)
 * decide for themselves how — or whether — to report them.
 */
class DatasetPhotoBackfillResult
{
    public int $processed = 0;

    public int $skippedHasPhoto = 0;

    public int $skippedNoFile = 0;

    public int $skippedNoMatch = 0;

    public int $skippedNoImage = 0;

    public int $failed = 0;

    /**
     * Messages in the order they were produced; `error` entries are the ones
     * the command prints with $this->error(), the rest with $this->line().
     *
     * @var list<array{type: 'line'|'error', text: string}>
     */
    public array $messages = [];

    public function line(string $text): void
    {
        $this->messages[] = ['type' => 'line', 'text' => $text];
    }

    public function error(string $text): void
    {
        $this->messages[] = ['type' => 'error', 'text' => $text];
    }
}
