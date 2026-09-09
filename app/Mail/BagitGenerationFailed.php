<?php

namespace App\Mail;

use App\Models\Study;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class BagitGenerationFailed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Study $study,
        public string $reason,
        public Throwable $exception,
        public int $attempts,
    ) {}

    public function build()
    {
        return $this->markdown('vendor.mail.bagit-generation-failed', [
            'study' => $this->study,
            'reason' => $this->reason,
            'exception' => $this->exception,
            'attempts' => $this->attempts,
            'url' => url(config('app.url').'/dashboard/studies/'.$this->study->id),
        ])->subject(__('BagIt metadata generation failed for '.$this->study->name));
    }
}
