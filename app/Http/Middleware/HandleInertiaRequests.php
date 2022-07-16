<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
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
        return array_merge(parent::share($request), [
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
            'user.permissions' => fn () => $request->user() ?
                $request->user()->getPermissionsViaRoles()->pluck('name')
                : null,
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'user.roles' => fn () => $request->user() ?
                $request->user()->getRoleNames()
                : null,
            'twitter' => (env('TWITTER_CLIENT_ID') !== null && env('TWITTER_CLIENT_ID') !== ''),
            'github' => (env('GITHUB_CLIENT_ID') !== null && env('GITHUB_CLIENT_ID') !== ''),
            'orcid' => (env('ORCID_CLIENT_ID') !== null && env('ORCID_CLIENT_ID') !== ''),
            'config.announcements' => Announcement::active(),
            'url' => env('APP_URL'),
            'nmriumURL' => env('NMRIUM_URL'),
            'environment' => env('APP_ENV'),
            'MEILISEARCH_HOST' => (env('MEILISEARCH_HOST')),
            'MEILISEARCH_PUBLICKEY' => (env('MEILISEARCH_PUBLICKEY')),
            'SCOUT_PREFIX' => (env('SCOUT_PREFIX')),
        ]);
    }
}
