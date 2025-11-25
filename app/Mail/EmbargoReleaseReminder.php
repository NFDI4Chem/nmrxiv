<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class EmbargoReleaseReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    public $daysUntilRelease;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($project, int $daysUntilRelease)
    {
        $this->project = $project;
        $this->daysUntilRelease = $daysUntilRelease;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $releaseDate = Carbon::parse($this->project->release_date)->format('M d, Y');

        $subject = match ($this->daysUntilRelease) {
            7 => 'Your embargo project will be released in 1 week',
            3 => 'Your embargo project will be released in 3 days',
            1 => 'Your embargo project will be released tomorrow',
            default => 'Your embargo project release is approaching'
        };

        return $this->markdown('vendor.mail.embargo-release-reminder', [
            'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id),
            'projectName' => $this->project->name,
            'releaseDate' => $releaseDate,
            'daysUntilRelease' => $this->daysUntilRelease,
        ])->subject($subject.' - '.$this->project->name);
    }
}
