<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
            'user.permissions' => fn () => $user ?
                $user->getPermissionsViaRoles()->pluck('name')
                : null,
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'user.roles' => fn () => $user ?
                $user->getRoleNames()
                : null,
            'user.notifications' => fn () => $user ?
                $user->unreadNotifications : null,
            'twitter' => (env('TWITTER_CLIENT_ID') !== null && env('TWITTER_CLIENT_ID') !== ''),
            'github' => (env('GITHUB_CLIENT_ID') !== null && env('GITHUB_CLIENT_ID') !== ''),
            'orcid' => (env('ORCID_CLIENT_ID') !== null && env('ORCID_CLIENT_ID') !== ''),
            'config.announcements' => Schema::hasTable('announcements') ? Announcement::active() : null,
            'url' => env('APP_URL'),
            'nmriumURL' => env('NMRIUM_URL'),
            'team' => $user ? $user->currentTeam : null,
            'environment' => env('APP_ENV'),
            'MEILISEARCH_HOST' => (env('MEILISEARCH_HOST')),
            'MEILISEARCH_PUBLICKEY' => (env('MEILISEARCH_PUBLICKEY')),
            'SCOUT_PREFIX' => (env('SCOUT_PREFIX')),
            'europemcWSApi' => (env('EUROPEMC_WS_API')),
            'dataciteURL' => env('DATACITE_ENDPOINT'),
            'collOffPeriod' => env('COOL_OFF_PERIOD'),
            'orcidSearchApi' => env('ORCID_ID_SEARCH_API'),
            'orcidPersonApi' => env('ORCID_ID_PERSON_API'),
            'orcidEmploymentApi' => env('ORCID_ID_EMPLOYMENT_API'),
        ]);
    }
}
