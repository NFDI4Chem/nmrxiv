<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteCitations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var array
     */
    private $citations;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($citations)
    {
        $this->citations = $citations;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->citations as $citation) {
            if (! DB::table('citation_project')->where('citation_id', $citation->id)->exists()) {
                $citation->delete();
            }
        }
    }
}
