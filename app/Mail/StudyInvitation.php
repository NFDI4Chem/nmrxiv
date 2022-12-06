<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\StudyInvitation as StudyInvitationModel;

class StudyInvitation extends Mailable implements ShouldQueue
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
        dd($this->invitation);
        $invitedBy = User::find($this->invitation->invited_by);
        
        return $this->markdown('vendor.mail.study-invitation', ['invitedBy' => $invitedBy ? $invitedBy->name : "", 'acceptUrl' => URL::signedRoute('study-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('Study Invitation'));
    }
}
