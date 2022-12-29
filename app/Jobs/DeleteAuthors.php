<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
            if (! DB::table('author_project')->where('author_id', $author->id)->exists()) {
                $author->delete();
            }
        }
    }
}
