<?php

namespace App\Jobs;

use App\Models\AuthorProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteAuthors implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var array
     */
    private $authors;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($authors)
    {
        $this->authors = $authors;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->authors as $author) {
            if (! AuthorProject::where('author_id', $author->id)->exists()) {
                $author->delete();
            }
        }
    }
}
