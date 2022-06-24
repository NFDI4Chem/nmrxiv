<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\ProjectInvitation as ProjectInvitationModel;

class ProjectInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The project invitation instance.
     *
     * @var \App\Models\ProjectInvitation
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\ProjectInvitation  $invitation
     * @return void
     */
    public function __construct(ProjectInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('vendor.mail.project-invitation', ['acceptUrl' => URL::signedRoute('project-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('Project Invitation'));
    }
}
