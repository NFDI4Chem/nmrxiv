<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DraftProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function build()
    {
        $project = $this->project;

        /**
         * This mail is sent after processing completes.
         * Whether it's an embargo vs immediate public release should be based on
         * the project's actual published state, not a timezone-sensitive date
         * comparison.
         */
        $releasedToday = (bool) ($project->is_public ?? false);

        $releaseDate = filled($project->release_date)
            ? Carbon::parse($project->release_date)
            : null;

        // Safely handle identifier parsing
        $publicUrlPath = '';
        if ($project->identifier && str_contains($project->identifier, ':')) {
            $identifierParts = explode(':', $project->identifier);
            if (count($identifierParts) > 1) {
                $publicUrlPath = $identifierParts[1];
            }
        }

        return $this->markdown('vendor.mail.draft-processed', [
            'url' => url(config('app.url').'/dashboard/projects/'.$project->id),
            'project' => $project,
            'releasedToday' => $releasedToday,
            'releaseDate' => $releaseDate?->toDateString(),
            'publicUrl' => $publicUrlPath
                ? url(config('app.url').'/'.$publicUrlPath)
                : url(config('app.url').'/dashboard/projects/'.$project->id),
        ])->subject(__('Submission Processed'.' - '.$project->name));
    }
}
