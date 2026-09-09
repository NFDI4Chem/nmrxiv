<?php

namespace App\Mail;

use App\Models\Study;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BagitGenerationSucceeded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Study $study,
        public ?string $archiveUrl = null,
    ) {}

    public function build()
    {
        return $this->markdown('vendor.mail.bagit-generation-succeeded', [
            'study' => $this->study,
            'archiveUrl' => $this->archiveUrl ?? $this->study->bagit_archive_link,
            'publicUrl' => $this->study->public_url,
            'sampleUrl' => url(config('app.url').'/sample/S'.$this->study->getRawOriginal('identifier')),
        ])->subject(__('BagIt archive is ready for '.$this->study->name));
    }
}
