<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanSpectraSearchUploadsCommandTest extends TestCase
{
    public function test_deletes_only_old_spectrum_search_uploads(): void
    {
        config(['nmrxiv.spectra_search.temp_prefix' => 'spectra-search']);
        $disk = Storage::fake(config('filesystems.default'));
        $disk->put('spectra-search/old/proton.zip', 'old');
        $disk->put('spectra-search/new/proton.zip', 'new');
        $disk->put('other/keep.zip', 'keep');
        touch($disk->path('spectra-search/old/proton.zip'), now()->subHours(2)->getTimestamp());
        touch($disk->path('other/keep.zip'), now()->subHours(2)->getTimestamp());

        $this->artisan('nmrxiv:clean-spectra-search-uploads')
            ->expectsOutput('Deleted 1 leftover spectrum search uploads.')
            ->assertSuccessful();

        $disk->assertMissing('spectra-search/old/proton.zip');
        $disk->assertExists('spectra-search/new/proton.zip');
        $disk->assertExists('other/keep.zip');
    }
}
