<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ProjectDeletion extends Mailable
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
        $coolOffPeriod = (int) env('COOL_OFF_PERIOD', '30');

        return $this->markdown('vendor.mail.project-deletion', [
            'url' => url(config('app.url').'/dashboard/projects/'.$this->project->id.'/settings'),
            'projectName' => $this->project->name,
            'deletedOn' => explode(' ', $this->project->deleted_on)[0],
            'dueDate' => explode(' ', Carbon::parse($this->project->deleted_on)->addDays($coolOffPeriod))[0],
        ])->subject(__('Your project has been moved to trash'.' - '.$this->project->name));
    }
}
