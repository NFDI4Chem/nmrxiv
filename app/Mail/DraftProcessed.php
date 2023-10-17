<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DraftProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $releasedToday = false;
        $releaseDate = Carbon::parse($this->project->release_date);

        if ($releaseDate->isToday()) {
            $releasedToday = true;
        }

        return $this->markdown('vendor.mail.draft-processed', [
            'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id),
            'project' => $this->project,
            'releasedToday' => $releasedToday,
            'releaseDate' => explode(' ', $releaseDate)[0],
            'publicUrl' => url(config('app.url').'/'.explode(':', $this->project->identifier)[1]),
        ])->subject(__('Submission Processed'.' - '.$this->project->name));
    }
}
