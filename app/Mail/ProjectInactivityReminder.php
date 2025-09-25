<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectInactivityReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $projectOrList;

    /**
     * Accept either a single Project or an array list for digest emails.
     */
    public function __construct($projectOrList)
    {
        $this->projectOrList = $projectOrList;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Digest mode when an array is passed
        if (is_array($this->projectOrList)) {
            return $this->markdown('vendor.mail.project-inactivity-reminder', [
                'digest' => true,
                'projects' => $this->projectOrList,
                'thresholdMonths' => (int) config('inactivity.grace_months', 6),
            ])->subject(__('Your inactive projects digest'));
        }

        // Single project mode (backwards compatible)
        $project = $this->projectOrList;

        return $this->markdown('vendor.mail.project-inactivity-reminder', [
            'url' => url(config('app.url').'/dashboard/projects/'.$project->id),
            'projectName' => $project->name,
            'lastUpdated' => explode(' ', \Illuminate\Support\Carbon::parse($project->updated_at))[0],
            'digest' => false,
        ])->subject(__('Your project has been inactive'.' - '.$project->name));
    }
}
