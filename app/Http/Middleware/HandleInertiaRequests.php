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
     * @return array
     */
    public function share(Request $request)
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'flash' => function () use ($request) {
                return [
                    'message' => $request->session()->get('message'),
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'auth.user.permissions' => fn () => $user ?
                $user->getPermissionsViaRoles()->pluck('name')
                : null,
            'auth.user.roles' => fn () => $user ?
                $user->getRoleNames()
                : null,
            'auth.user.teamRole' => fn () => $user ? $user->teamRole($user->currentTeam) : null,
            'auth.user.notifications' => fn () => $user ?
                $user->unreadNotifications : null,
            'twitter' => (config('services.twitter.client_id') !== null && config('services.twitter.client_id') !== ''),
            'github' => (config('services.github.client_id') !== null && config('services.github.client_id') !== ''),
            'orcid' => (config('services.orcid.client_id') !== null && config('services.orcid.client_id') !== ''),
            'nfdiaai' => (config('services.regapp.client_id') !== null && config('services.regapp.client_id') !== ''),
            'config.announcements' => Schema::hasTable('announcements') ? Announcement::active() : null,
            'url' => config('app.url'),
            'nmriumURL' => config('external-links.nmrium_url'),
            'spectraParserUrl' => rtrim((string) config('external-links.nmrkit_url'), '/').'/latest/spectra/parse/url',
            'team' => $user ? $user->currentTeam : null,
            'environment' => config('app.env'),
            'MEILISEARCH_HOST' => config('scout.meilisearch.host'),
            'MEILISEARCH_PUBLICKEY' => config('scout.meilisearch.public_key'),
            'SCOUT_PREFIX' => config('scout.prefix'),
            'europemcWSApi' => config('external-links.europemc_ws_api'),
            'dataciteURL' => config('doi.datacite.endpoint'),
            'coolOffPeriod' => config('nmrxiv.cool_off_period'),
            'mailFromAddress' => config('mail.from.address'),
            'chemistryStandardizeUrl' => config('services.chemistry_standardize.url'),
            'orcidSearchApi' => config('orcid.search_api'),
            'orcidPersonApi' => config('orcid.person_api'),
            'michiStandardsUrl' => config('external-links.michi_standards_url'),
            'orcidEmploymentApi' => config('orcid.employment_api'),
            'CM_API' => config('external-links.cm_api'),
            'CROSSREF_API' => config('external-links.crossref_api'),
            'DATACITE_API' => config('external-links.datacite_api'),
        ]);
    }
}
