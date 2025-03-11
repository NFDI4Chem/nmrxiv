<?php

namespace App\Console\Commands;

use App\Jobs\DeleteAuthors;
use App\Models\Author;
use Illuminate\Console\Command;

class DeleteAuthor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:delete-authors {ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Delete authors from author's table which are not attached to any projects.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $ids = $this->argument('ids') ? explode(',', $this->argument('ids')) : null;
        if ($ids) {
            $authors = Author::whereIn('id', $ids)->get();
        } else {
            $authors = Author::all();
        }
        DeleteAuthors::dispatch($authors);
    }
}
