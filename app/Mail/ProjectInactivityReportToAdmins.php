<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectInactivityReportToAdmins extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array<int, array{id:int,name:string,owner:string,updated_at:string}> $projects */
    public function __construct(private array $projects)
    {
        //
    }

    public function build(): self
    {
        return $this->markdown('vendor.mail.project-inactivity-report-admins', [
            'projects' => $this->projects,
            'thresholdMonths' => (int) config('inactivity.grace_months', 6),
        ])->subject(__('Inactive projects report'));
    }
}
