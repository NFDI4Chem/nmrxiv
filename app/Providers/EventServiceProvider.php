<?php

namespace App\Providers;

use App\Events\DraftProcessed;
use App\Events\StudyPublish;
use App\Listeners\SendDraftProcessedNotification;
use App\Listeners\StudyPublish as StudyPublishListener;
use App\Services\Socialite\NFDIAAI\Provider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DraftProcessed::class => [
            SendDraftProcessedNotification::class,
        ],
        StudyPublish::class => [
            StudyPublishListener::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
        Event::listen(function (SocialiteWasCalled $event) {
            // Canonical slug for NFDI AAI provider is 'regapp' (matches IdP registered callback URI)
            $event->extendSocialite('regapp', Provider::class);
        });
    }
}
