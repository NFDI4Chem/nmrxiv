<?php

namespace App\Notifications;

use App\Mail\ProjectInactivityReportToAdmins as ProjectInactivityReportToAdminsMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class ProjectInactivityReportToAdmins extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param array<int, array{id:int,name:string,owner:string,updated_at:string}> $projects */
    public function __construct(private array $projects)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): Mailable
    {
        return (new ProjectInactivityReportToAdminsMailable($this->projects))->to($notifiable->email);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
