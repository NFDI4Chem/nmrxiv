<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DraftProcessedNotifyAdmins extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    public $studies;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($project, $studies)
    {
        $this->project = $project;
        $this->studies = $studies;
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

        if ($this->project) {
            return $this->markdown('vendor.mail.project-published-notify-admins', [
                'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id),
                'projectName' => $this->project->name,
                'projectId' => $this->project->id,
                'releasedToday' => $releasedToday,
            ])->subject(__('A project has been published'.' - '.$this->project->name));
        } elseif ($this->studies) {
            return $this->markdown('vendor.mail.study-published-notify-admins', [
                'url' => url(config('app.url').'/spectra'),
                'samples' => $this->studies,
                'releasedToday' => $releasedToday,
            ])->subject(__('Sample(s) has been published.'));

        }

    }
}
