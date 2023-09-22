<?php

namespace App\Providers;

use App\Events\DraftProcessed;
use App\Listeners\StudyInvite as StudyInviteListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Events\StudyInvite as StudyInviteEvent;
use App\Listeners\SendDraftProcessedNotification;
use App\Events\ProjectInvite as ProjectInviteEvent;
use App\Events\ProjectArchival as ProjectArchivalEvent;
use App\Events\ProjectDeletion as ProjectDeletionEvent;
use App\Listeners\ProjectInvite as ProjectInviteListener;
use App\Listeners\ProjectArchival as ProjectArchivalListener;
use App\Listeners\ProjectDeletion as ProjectDeletionListener;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DraftProcessed::class => [
            SendDraftProcessedNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // add your listeners (aka providers) here
            \SocialiteProviders\Orcid\OrcidExtendSocialite::class.'@handle',
        ],
        ProjectDeletionEvent::class => [
            ProjectDeletionListener::class,
        ],
        ProjectArchivalEvent::class => [
            ProjectArchivalListener::class,
        ],
        ProjectInviteEvent::class => [
            ProjectInviteListener::class,
        ],
        StudyInviteEvent::class => [
            StudyInviteListener::class,
        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
