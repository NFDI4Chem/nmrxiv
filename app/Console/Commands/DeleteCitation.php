<?php

namespace App\Console\Commands;

use App\Jobs\DeleteCitations;
use App\Models\Citation;
use Illuminate\Console\Command;

class DeleteCitation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:delete-citations {ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Delete citations from citation's table which are not attached to any projects.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $ids = $this->argument('ids') ? explode(',', $this->argument('ids')) : null;
        if ($ids) {
            $citations = Citation::whereIn('id', $ids)->get();
        } else {
            $citations = Citation::all();
        }
        DeleteCitations::dispatch($citations);
    }
}
