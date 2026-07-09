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

    public function __construct($project, $studies)
    {
        $this->project = $project;
        $this->studies = $studies;
    }

    public function build()
    {
        $releasedToday = false;

        if ($this->project) {
            $releasedToday = (bool) ($this->project->is_public ?? false);

            $releaseDate = filled($this->project->release_date)
                ? Carbon::parse($this->project->release_date)->toDateString()
                : null;

            return $this->markdown('vendor.mail.project-published-notify-admins', [
                'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id),
                'projectName' => $this->project->name,
                'projectId' => $this->project->id,
                'releasedToday' => $releasedToday,
                'releaseDate' => $releaseDate,
            ])->subject(__('A project has been published'.' - '.$this->project->name));
        } elseif ($this->studies) {
            $studies = $this->studies;

            return $this->markdown('vendor.mail.study-published-notify-admins', [
                'url' => url(config('app.url').'/projects'),
                'samples' => $studies,
            ])->subject(__('Sample(s) has been published.'));

        }

    }
}
