<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudyPublish extends Mailable
{
    use Queueable, SerializesModels;

    public $studies;

    public function __construct($studies)
    {
        $this->studies = $studies;
    }

    public function build()
    {
        $studies = $this->studies;

        return $this->markdown('vendor.mail.study-published', [
            // A single published sample links straight to its public page, several fall back to the project list.
            'url' => count($studies) === 1 && filled(data_get(collect($studies)->first(), 'public_url'))
                ? data_get(collect($studies)->first(), 'public_url')
                : url(config('app.url').'/projects'),
            'samples' => $studies,
        ])->subject(__('Submission Processed.'));
    }
}
