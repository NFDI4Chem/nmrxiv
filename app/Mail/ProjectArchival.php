<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectArchival extends Mailable
{
    use Queueable, SerializesModels;

    private $project;

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
        return $this->markdown('vendor.mail.project-archival', [
            'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id.'/settings'),
            'projectName' => $this->project->name,
        ])->subject(__('Project archived successfully'.' - '.$this->project->name));
    }
}
