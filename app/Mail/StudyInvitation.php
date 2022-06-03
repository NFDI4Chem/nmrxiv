<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\StudyInvitation as StudyInvitationModel;

class StudyInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The study invitation instance.
     *
     * @var \App\Models\StudyInvitation
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\StudyInvitation  $invitation
     * @return void
     */
    public function __construct(StudyInvitationModel $invitation)
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
        return $this->markdown('vendor.mail.study-invitation', ['acceptUrl' => URL::signedRoute('study-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('Study Invitation'));
    }
}
