<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectDeletionReminder extends Mailable
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
        return $this->markdown('vendor.mail.project-deletion-reminder', [
            'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id),
            'projectName' => $this->project->name,
            'deletedOn' => explode(' ', $this->project->deleted_on)[0],
            'dueDate'   => explode(' ',Carbon::parse($this->project->deleted_on)->addDays(30))[0],
        ])->subject(__('Your project would be deleted soon'.' - '.$this->project->name));
    }
}
