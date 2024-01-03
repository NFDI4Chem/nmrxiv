<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class StudyPublish extends Mailable
{
    use Queueable, SerializesModels;

    public $studies;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($studies)
    {
        $this->studies = $studies;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $releaseToday = false;
        $releaseDate = Carbon::parse($this->studies[0]['release_date']);

        if ($releaseDate->isToday()) {
            $releaseToday = true;
        }

        return $this->markdown('vendor.mail.study-published', [
            'url' => url(config('app.url').'/spectra'),
            'samples' => $this->studies,
            'releaseToday' => $releaseToday,
            'releaseDate' => $releaseDate,
        ])->subject(__('Submission Processed.'));
    }
}
