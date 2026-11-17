<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
     */
    public function handle(): void
    {
        foreach ($this->authors as $author) {
            // Samples published without a project (e.g. ELN submissions) link their authors only through author_study.
            if (! DB::table('author_project')->where('author_id', $author->id)->exists()
                && ! DB::table('author_study')->where('author_id', $author->id)->exists()) {
                $author->delete();
            }
        }
    }
}
