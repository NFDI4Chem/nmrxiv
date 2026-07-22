<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ConsoleController;
use App\Http\Controllers\Admin\CurationController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\API\Auth\VerificationController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\MyWelcomeController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CASController;
use App\Http\Controllers\ChemistryStandardizeController;
use App\Http\Controllers\CitationController;
use App\Http\Controllers\CommunityContributionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\FileSystemController;
use App\Http\Controllers\FundingReferenceController;
use App\Http\Controllers\OEmbedController;
use App\Http\Controllers\OrcidController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\PublicSearchController;
use App\Http\Controllers\PublicStatsController;
use App\Http\Controllers\RorController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\StudyInvitationController;
use App\Http\Controllers\StudyMemberController;
use App\Http\Controllers\SupportBubbleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserPreferencesController;
use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Spatie\WelcomeNotification\WelcomesNewUsers;

Route::prefix('auth')->group(function () {
    Route::get('/login/{service}', [SocialController::class, 'redirectToProvider']);
    Route::get('/login/{service}/callback', [SocialController::class, 'handleProviderCallback']);
    Route::get('/checkPassword', [UsersController::class, 'checkPassword'])
        ->name('auth.checkPassword');
});

// ORCID Routes with rate limiting
Route::middleware(['throttle:60,1'])->prefix('orcid')->group(function () {
    Route::get('/search', [OrcidController::class, 'search']);
    Route::get('/{orcidId}/person', [OrcidController::class, 'person']);
    Route::get('/{orcidId}/employment', [OrcidController::class, 'employment']);
});

Route::get('/', function () {
    // if (Auth::check()) {
    //     return redirect()->route('dashboard');
    // } else {
    return Inertia::render('Welcome', [
        'spectra' => Cache::rememberForever('stats.spectra', function () {
            return Dataset::where('is_public', true)->get()->count();
        }),
        'projects' => Cache::rememberForever('stats.projects', function () {
            return Project::where('is_public', true)->get()->count();
        }),
        'embargoed_projects' => Cache::rememberForever('stats.embargoed_projects', function () {
            return Project::where('is_public', false)->where('release_date', '>', Carbon::now())->where('is_deleted', false)->count();
        }),
        'compounds' => Cache::rememberForever('stats.compounds', function () {
            return Molecule::whereNotNull('identifier')->get()->count();
        }),
        'techniques' => Cache::rememberForever('stats.techniques', function () {
            return Dataset::where('is_public', true)->get()->unique('type')->count();
        }),
    ]);
    // }
})->name('landing');

Route::get('/about-us', function () {
    return Inertia::render('About', [
        'projects' => Cache::rememberForever('stats.projects', function () {
            return Project::where('is_public', true)->get()->count();
        }),
        'compounds' => Cache::rememberForever('stats.compounds', function () {
            return Molecule::whereNotNull('identifier')->get()->count();
        }),
    ]);
})->name('about');

Route::get('/faqs', function () {
    return Inertia::render('FAQs');
})->name('faqs');

Route::get('/predict', function () {
    return Inertia::render('Predict');
})->name('predict');

Route::get('/stats', [PublicStatsController::class, 'index'])->name('stats');

// Custom support bubble route with rate limiting and enhanced security
Route::post('support-bubble', [SupportBubbleController::class, 'submit'])
    ->middleware(['throttle:support-bubble'])
    ->name('supportBubble.submit');

Route::impersonate();

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

// New structured routes - must come before more general project routes
Route::get('/compound/{id}', [ApplicationController::class, 'resolveCompound'])->where('id', '(M|m)[0-9]+')
    ->name('public.compound');

Route::get('/sample/{id}', [ApplicationController::class, 'resolveSample'])->where('id', '(S|s)[0-9]+')
    ->name('public.sample');

Route::get('/sample/{study}/nmriumInfo', [StudyController::class, 'fetchPublicNMRium'])
    ->where('study', '([0-9]+|(NMRXIV:)?(S|s)[0-9]+)')
    ->name('public.sample.nmrium');

Route::get('/project/{id}', [ApplicationController::class, 'resolveProject'])->where('id', '(P|p)[0-9]+')
    ->name('public.project.id');

Route::get('/dataset/{id}', [ApplicationController::class, 'resolveDataset'])->where('id', '(D|d)[0-9]+')
    ->name('public.dataset.id');

Route::get('/dataset/{dataset}/nmriumInfo', [DatasetController::class, 'fetchPublicNMRium'])
    ->where('dataset', '([0-9]+|(NMRXIV:)?(D|d)[0-9]+)')
    ->name('public.dataset.nmrium');

Route::get('project/{url}', [ProjectController::class, 'review'])->name('project.preview');
Route::get('project/{url}/studies', [ProjectController::class, 'reviewerStudies'])->name('studies.preview');
Route::get('study/{obfuscationCode}/{study}/{model}', [StudyController::class, 'preview2'])->name('preview');

Route::middleware('verified')->group(function () {
    if (Jetstream::hasTeamFeatures()) {
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('app.teams.destroy');
    }
});

Route::middleware('web', WelcomesNewUsers::class)->group(function () {
    Route::get('welcome/{user}', [MyWelcomeController::class, 'showWelcomeForm'])->name('welcome');
    Route::post('welcome/{user}', [MyWelcomeController::class, 'savePassword'])->name('password.set');
});

// ROR API - publicly accessible with rate limiting
Route::get('ror/search', [RorController::class, 'search'])
    ->middleware('throttle:60,1')
    ->name('ror.search');

Route::middleware('auth', 'verified')->group(function () {
    // License
    Route::get('licenses', [LicenseController::class, 'index'])
        ->name('licenses');
    Route::get('licenses/{id}', [LicenseController::class, 'getLicensebyId'])
        ->name('license');

    // Authors
    Route::post('authors/{project}', [AuthorController::class, 'save'])
        ->name('author.save');

    Route::delete('authors/{project}/delete', [AuthorController::class, 'destroy'])
        ->name('author.delete');

    Route::post('authors/{project}/updateRole', [AuthorController::class, 'updateRole'])
        ->name('author.updateRole');

    // Citation
    Route::post('citations/{project}', [CitationController::class, 'save'])
        ->name('citation.save');

    Route::delete('citations/{project}/delete', [CitationController::class, 'destroy'])
        ->name('citation.delete');

    Route::post('citations/study/{study}', [CitationController::class, 'saveStudy'])
        ->name('citation.study.save');

    Route::delete('citations/study/{study}/delete', [CitationController::class, 'destroyStudy'])
        ->name('citation.study.delete');

    // Funding references
    Route::post('funding-references/{project}', [FundingReferenceController::class, 'save'])
        ->name('fundingReference.save');

    Route::delete('funding-references/{project}/delete', [FundingReferenceController::class, 'destroy'])
        ->name('fundingReference.delete');

    Route::post('/onboarding/{status}', [DashboardController::class, 'onboardingStatus'])
        ->name('onboarding.complete');

    Route::post('/primer/skip', [DashboardController::class, 'skipPrimer'])
        ->name('primer.skip');

    Route::put('/user/preferences', [UserPreferencesController::class, 'update'])
        ->middleware('throttle:30,1')
        ->name('user.preferences.update');

    Route::get('projects/status/{project}/queue', [ProjectController::class, 'status'])
        ->name('project.status');

    Route::get('projects/{project}/validation', [ProjectController::class, 'validationReport'])
        ->name('project.validation');

    Route::post('users/notification/{user}/markAsRead', [UsersController::class, 'markNotificationAsRead'])
        ->name('users.markNotificationAsRead');

    Route::post('users/notification/markAllAsRead', [UsersController::class, 'markAllNotificationAsRead'])
        ->name('users.markAllNotificationAsRead');

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('upload', [UploadController::class, 'upload'])->name('upload');
    Route::get('community-contribution', [CommunityContributionController::class, 'show'])
        ->name('community-contribution');
    Route::post('community-contribution/drafts/{draft}/publish-studies', [CommunityContributionController::class, 'publishStudies'])
        ->middleware('throttle:10,1')
        ->name('community-contribution.publish-studies');
    Route::get('publish/{draft}', [UploadController::class, 'publish'])->name('publish');

    // CAS Common Chemistry API Proxy
    Route::get('/cas/detail', [CASController::class, 'fetchCasData'])->name('cas.detail');

    // Chemistry standardize API proxy (avoids browser CORS to external chem services)
    Route::post('/chemistry/standardize', [ChemistryStandardizeController::class, 'standardize'])
        ->middleware('throttle:60,1')
        ->name('chemistry.standardize');

    Route::prefix('dashboard')->group(function () {
        Route::get('ssubmission', [DashboardController::class, 'dashboard'])
            ->name('submission');
        Route::get('shared-with-me', function () {
            return redirect()->route('dashboard', ['workspace' => 'shared']);
        })->name('shared-with-me');
        Route::get('starred', function () {
            return redirect()->route('dashboard', ['workspace' => 'starred']);
        })->name('starred');
        Route::get('trashed', function () {
            return redirect()->route('dashboard', ['workspace' => 'trashed']);
        })->name('trashed');
        Route::get('recent', function () {
            return redirect()->route('dashboard', ['workspace' => 'recent']);
        })->name('recent');

        Route::post('/storage/signed-draft-storage-url', [FileSystemController::class, 'signedDraftStorageURL']);
        Route::post('/storage/signed-storage-url', [FileSystemController::class, 'signedStorageURL']);

        Route::get('/drafts', [DraftController::class, 'all']);

        Route::get('projects/{project}', [ProjectController::class, 'show'])
            ->name('dashboard.projects');
        Route::get('projects/{project}/settings', [ProjectController::class, 'settings'])
            ->name('dashboard.project.settings');
        Route::get('projects/{project}/studies', [ProjectController::class, 'studies'])
            ->name('dashboard.project.studies');
        Route::put('projects/{project}', [ProjectController::class, 'restore'])
            ->name('dashboard.project.restore');
        Route::put('projects/{project}/toggle-archive', [ProjectController::class, 'toggleArchive'])
            ->name('dashboard.project.toggle-archive');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])
            ->name('dashboard.project.destroy');
        Route::post('projects/create', [ProjectController::class, 'store'])
            ->name('dashboard.project.create');
        Route::put('projects/{project}/update', [ProjectController::class, 'update'])
            ->name('dashboard.project.update');
        Route::get('projects/{project}/activity', [ProjectController::class, 'activity'])
            ->name('dashboard.project.activity');
        Route::get('projects/{project}/validation', [ProjectController::class, 'validation'])
            ->name('dashboard.project.validation');
        Route::put('projects/{project}/updateReleaseDate', [ProjectController::class, 'updateReleaseDate'])
            ->name('dashboard.project.updateReleaseDate');

        Route::put('projects/{project}/releaseNow', [ProjectController::class, 'publishEmbargoProject'])
            ->name('dashboard.project.publishEmbargoProject');

        Route::put('projects/{project}/publish', [ProjectController::class, 'publish'])
            ->name('dashboard.project.publish');

        Route::post('projects/{project}/members', [ProjectMemberController::class, 'memberStore'])
            ->name('project-members.store');
        Route::put('/projects/{project}/members/{user}', [ProjectMemberController::class, 'updateMemberRole'])
            ->name('project-members.update');
        Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'removeMember'])
            ->name('project-members.destroy');

        Route::get('/project-invitations/{invitation}', [ProjectInvitationController::class, 'acceptInvitation'])
            ->middleware(['signed'])
            ->name('project-invitations.accept');
        Route::delete('/project-invitations/{invitation}', [ProjectInvitationController::class, 'destroyInvitation'])
            ->name('project-invitations.destroy');

        Route::get('studies/{study}', [StudyController::class, 'show'])
            ->name('dashboard.studies');
        Route::get('studies/{study}/files', [StudyController::class, 'files'])
            ->name('dashboard.study.files');
        Route::get('studies/{study}/annotations', [StudyController::class, 'annotations'])
            ->name('dashboard.study.annotations');
        Route::get('studies/{study}/datasets', [StudyController::class, 'datasets'])
            ->name('dashboard.study.datasets');
        Route::get('studies/{study}/settings', [StudyController::class, 'settings'])
            ->name('dashboard.study.settings');
        Route::delete('studies/{study}', [StudyController::class, 'destroy'])
            ->name('dashboard.study.destroy');
        Route::post('studies/create', [StudyController::class, 'store'])
            ->name('dashboard.study.create');
        Route::put('studies/{study}/update', [StudyController::class, 'update'])
            ->name('dashboard.study.update');
        Route::get('studies/{study}/activity', [StudyController::class, 'activity'])
            ->name('dashboard.study.activity');

        Route::post('studies/{study}/members', [StudyMemberController::class, 'memberStore'])
            ->name('study-members.store');
        Route::put('/studies/{study}/members/{user}', [StudyMemberController::class, 'updateMemberRole'])
            ->name('study-members.update');
        Route::delete('/studies/{study}/members/{user}', [StudyMemberController::class, 'removeMember'])
            ->name('study-members.destroy');

        Route::get('studies/{study}/nmriumVersions', [StudyController::class, 'nmriumVersions'])
            ->name('dashboard.studies.nmriumVersions');
        Route::get('studies/{study}/nmriumInfo', [StudyController::class, 'fetchNMRium'])
            ->name('dashboard.studies.nmrium');
        Route::post('studies/{study}/nmriumInfo', [StudyController::class, 'nmriumInfo'])
            ->name('dashboard.studies.nmriumInfo');
        Route::post('studies/{study}/snapshot', [StudyController::class, 'snapshot'])
            ->name('dashboard.study.snapshot');

        Route::post('studies/{study}/molecule', [StudyController::class, 'moleculeStore'])
            ->name('study-molecule.store');
        Route::delete('studies/{study}/molecule/{molecule}', [StudyController::class, 'moleculeDetach'])
            ->name('study-molecule.delete');

        Route::get('/study-invitations/{invitation}', [StudyInvitationController::class, 'acceptInvitation'])
            ->middleware(['signed'])
            ->name('study-invitations.accept');
        Route::delete('/study-invitations/{invitation}', [StudyInvitationController::class, 'destroyInvitation'])
            ->name('study-invitations.destroy');

        Route::get('datasets/{dataset}/nmriumVersions', [DatasetController::class, 'nmriumVersions'])
            ->name('dashboard.datasets.nmriumVersions');
        Route::get('datasets/{dataset}/nmriumInfo', [DatasetController::class, 'fetchNMRium'])
            ->name('dashboard.datasets.nmrium');
        Route::post('datasets/{dataset}/nmriumInfo', [DatasetController::class, 'nmriumInfo'])
            ->name('dashboard.datasets.nmriumInfo');
        Route::post('datasets/{dataset}/snapshot', [DatasetController::class, 'snapshot'])
            ->name('dashboard.dataset.snapshot');
        Route::put('datasets/{dataset}/assignments', [DatasetController::class, 'updateAssignments'])
            ->name('dashboard.datasets.assignments.update');

        Route::get('drafts/{draft}/show', [DraftController::class, 'show'])
            ->name('dashboard.draft.show');
        Route::get('drafts/{draft}/info', [DraftController::class, 'info'])
            ->name('dashboard.draft.info');
        Route::get('drafts/{draft}/status', [DraftController::class, 'status'])
            ->name('dashboard.draft.status');
        Route::post('drafts/{draft}/provisional-doi', [DraftController::class, 'storeProvisionalDoi'])
            ->name('dashboard.draft.provisional-doi.store');
        Route::delete('drafts/{draft}/provisional-doi', [DraftController::class, 'destroyProvisionalDoi'])
            ->name('dashboard.draft.provisional-doi.destroy');
        Route::get('drafts/{draft}/files', [DraftController::class, 'files'])
            ->name('dashboard.draft.files');
        Route::get('drafts/{draft}/sample-folders', [DraftController::class, 'sampleFolders'])
            ->name('dashboard.draft.sample-folders');
        Route::get('drafts/{draft}/missing-files', [DraftController::class, 'missingFiles'])
            ->name('dashboard.draft.missing-files');
        Route::put('drafts/{draft}', [DraftController::class, 'update'])
            ->name('dashboard.draft.update');
        Route::delete('drafts/{draft}/files/{filesystemobject}', [FileSystemController::class, 'deleteFSO'])
            ->name('dashboard.draft.files.delete');
        Route::post('drafts/{draft}/sample-folders/{filesystemobject}/reset', [DraftController::class, 'resetSampleFolder'])
            ->name('dashboard.draft.sample-folder.reset');
        Route::get('drafts/{draft}/annotate', [DraftController::class, 'annotate'])
            ->name('dashboard.draft.annotate');
        Route::post('drafts/{draft}/process', [DraftController::class, 'process'])
            ->name('dashboard.draft.process');
        Route::post('drafts/{draft}/complete', [DraftController::class, 'complete'])
            ->name('dashboard.draft.complete');
    });
});

Route::prefix('admin')->group(function () {
    Route::middleware('auth', 'permission:manage roles|view statistics|manage platform')->group(function () {
        Route::get('console', [ConsoleController::class, 'index'])
            ->name('console');

        Route::middleware('permission:manage roles|manage platform')->group(function () {
            // Users
            Route::get('users', [UsersController::class, 'index'])
                ->name('console.users');

            Route::get('users/create', [UsersController::class, 'create'])
                ->name('console.users.create');

            Route::post('users', [UsersController::class, 'store'])
                ->name('console.users.store');

            Route::get('users/edit/{user}', [UsersController::class, 'edit'])
                ->name('console.users.edit');

            Route::get('users/impersonate/{user}', [UsersController::class, 'impersonate'])
                ->name('console.users.impersonate');

            Route::put('users/edit/{user}', [UsersController::class, 'update'])
                ->name('console.users.update');

            Route::put('users/edit/{user}/password', [UsersController::class, 'updatePassword'])
                ->name('console.users.update-password');

            Route::middleware('permission:manage roles')->group(function () {
                Route::put('users/edit/{user}/role', [UsersController::class, 'updateRole'])
                    ->name('console.users.update-role');
            });

            Route::delete('users/edit/{user}/photo', [UsersController::class, 'destroyPhoto'])
                ->name('console.users.destroy-photo');
        });

        // Adding routes for announcements section
        Route::middleware('auth', 'permission:manage roles|manage platform')->group(function () {
            // Announcements
            Route::get('announcements', [AnnouncementController::class, 'index'])
                ->name('console.announcements');

            Route::post('announcements', [AnnouncementController::class, 'create'])
                ->name('console.announcements.create');

            Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])
                ->name('console.announcements.edit');

            Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])
                ->name('console.announcements.destroy');
        });

        // Adding routes for submissions curation
        Route::middleware('auth', 'permission:manage roles|manage platform')->group(function () {
            // Spectra
            Route::get('spectra', [CurationController::class, 'spectra'])
                ->name('console.spectra');
            Route::get('snapshots', [CurationController::class, 'snapshots'])
                ->name('console.spectra.snapshots');
        });

    });
});

// Legacy /spectra URLs
Route::get('/spectra', function (Request $request) {
    $compound = $request->query('compound');
    if ($compound) {
        return redirect()->route('public.compound', ['id' => 'M'.$compound], 301);
    }

    return redirect()->route('public.projects', [], 301);
});

// Keep the old generic resolver for backward compatibility but redirect to new URLs
Route::get('{id}', function ($id) {
    $resolvedModel = resolveIdentifier($id);
    $namespace = $resolvedModel['namespace'];

    if ($namespace === 'Project') {
        return redirect()->route('public.project.id', ['id' => $id], 301);
    } elseif ($namespace === 'Study') {
        return redirect()->route('public.sample', ['id' => $id], 301);
    } elseif ($namespace === 'Molecule') {
        return redirect()->route('public.compound', ['id' => $id], 301);
    } elseif ($namespace === 'Dataset') {
        return redirect()->route('public.dataset.id', ['id' => $id], 301);
    }

    // Fallback to original resolver for unknown types
    return app(ApplicationController::class)->resolve(request(), $id);
})->where('id', '(P|S|D|M|p|s|d|m)[0-9]+')
    ->name('public');

// Search / browse page
Route::get('/compounds', [ApplicationController::class, 'compounds'])->name('compounds');

Route::get('/search', [PublicSearchController::class, 'index'])->name('search');

Route::get('/badge/doi/{id}', [ApplicationController::class, 'resolveBadge'])
    ->name('badge.doi');

Route::get('{username}/download/{project}/{key?}', [DownloadController::class, 'downloadFromProject'])
    ->name('download');

Route::get('{username}/datasets/{project}/{study?}/{dataset?}', [DownloadController::class, 'downloadSet'])
    ->name('download.set');

Route::get('{username}/download/{project}/{study}/{filename}', [DownloadController::class, 'download'])
    ->name('dataset.download');

Route::get('{code}/studies/{study}/file/{filename}', [StudyController::class, 'file'])
    ->name('study.file');

Route::get('projects/{project}/toggleUpVote', [ProjectController::class, 'toggleUpVote'])
    ->name('project.toggle-upvote');

Route::get('projects/{project}/toggleStarred', [ProjectController::class, 'toggleStarred'])
    ->name('project.toggle-starred');

Route::get('studies/{study}/toggleStarred', [StudyController::class, 'toggleStarred'])
    ->name('study.toggle-starred');

Route::get('studies/{study}/nmriumInfo', [StudyController::class, 'fetchPublicNMRium'])
    ->name('public.studies.nmrium');

Route::get('projects/{project}/studies', [ProjectController::class, 'publicStudies'])
    ->name('project.studies');

Route::get('projects/{owner}/{slug}', [ProjectController::class, 'publicProjectView'])
    ->name('public.project');

Route::get('projects', [ProjectController::class, 'publicProjectsView'])
    ->name('public.projects');

Route::get('datasets/{dataset}/nmriumInfo', [DatasetController::class, 'fetchPublicNMRium'])
    ->name('public.datasets.nmrium');

Route::get('datasets/{slug}', [DatasetController::class, 'publicDatasetView'])
    ->name('public.dataset');

// oEmbed service endpoint - returns oEmbed JSON response for external embedding
// Supports oEmbed 1.0 specification for rich content embedding
// Rate limited to prevent abuse and enumeration attacks
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('services/oembed', [OEmbedController::class, 'spectra']);
    Route::get('embed/{id}', [OEmbedController::class, 'embed'])->name('embed');
});

// Test route for Octane
Route::get('/octane-test', function () {
    return [
        'status' => 'Octane is working!',
        'server' => 'frankenphp',
        'php_version' => PHP_VERSION,
    ];
});
