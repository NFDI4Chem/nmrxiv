<?php

namespace App\Mail;

use App\Models\Draft;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ElnSubmissionFailed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Draft $draft,
        public string $step,
        public ?string $error = null,
        public bool $admin = false,
    ) {}

    public function build()
    {
        $eln = match (strtolower((string) $this->draft->eln)) {
            'chemotion' => 'Chemotion',
            'nobs' => 'NoBS',
            default => ucfirst((string) $this->draft->eln),
        };

        return $this->markdown('vendor.mail.eln-submission-failed', [
            'eln' => $eln,
            'draft' => $this->draft,
            'step' => $this->step,
            'error' => $this->admin ? $this->error : null,
            'admin' => $this->admin,
        ])->subject(__(':eln submission could not be completed - :id', ['eln' => $eln, 'id' => $this->draft->external_id]));
    }
}
