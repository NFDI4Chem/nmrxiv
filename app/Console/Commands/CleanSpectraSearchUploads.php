<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('nmrxiv:clean-spectra-search-uploads {--minutes=60 : Delete uploads older than this many minutes}')]
#[Description('Delete leftover spectrum search uploads from temporary storage')]
class CleanSpectraSearchUploads extends Command
{
    public function handle(): int
    {
        $disk = Storage::disk(config('filesystems.default'));
        $prefix = trim((string) config('nmrxiv.spectra_search.temp_prefix'), '/');
        $cutoff = now()->subMinutes((int) $this->option('minutes'))->getTimestamp();

        $deleted = 0;
        foreach ($disk->allFiles($prefix) as $path) {
            if ($disk->lastModified($path) < $cutoff && $disk->delete($path)) {
                $deleted++;
            }
        }

        $this->info("Deleted {$deleted} leftover spectrum search uploads.");

        return self::SUCCESS;
    }
}
