<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->citations as $citation) {
            if (! DB::table('citation_project')->where('citation_id', $citation->id)->exists()) {
                $citation->delete();
            }
        }
    }
}
