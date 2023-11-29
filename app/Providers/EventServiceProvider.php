<?php

namespace App\Providers;

use App\Events\DraftProcessed;
use App\Events\ProjectArchival as ProjectArchivalEvent;
use App\Events\ProjectDeletion as ProjectDeletionEvent;
use App\Events\ProjectInvite as ProjectInviteEvent;
use App\Events\StudyInvite as StudyInviteEvent;
use App\Listeners\ProjectArchival as ProjectArchivalListener;
use App\Listeners\ProjectDeletion as ProjectDeletionListener;
use App\Listeners\ProjectInvite as ProjectInviteListener;
use App\Listeners\SendDraftProcessedNotification;
use App\Listeners\StudyInvite as StudyInviteListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;

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
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(function (TakeImpersonation $event) {
            session()->put([
                'password_hash_sanctum' => $event->impersonated->getAuthPassword(),
            ]);
        });

        Event::listen(function (LeaveImpersonation $event) {
            session()->remove('password_hash_web');
            session()->put([
                'password_hash_sanctum' => $event->impersonator->getAuthPassword(),
            ]);
            Auth::setUser($event->impersonator);
        });
    }
}
