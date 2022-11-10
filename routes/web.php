<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ConsoleController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CitationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\FileSystemController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\StudyInvitationController;
use App\Http\Controllers\StudyMemberController;
use App\Http\Controllers\TeamController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::get('/login/{service}', [SocialController::class, 'redirectToProvider']);
    Route::get('/login/{service}/callback', [SocialController::class, 'handleProviderCallback']);
    Route::get('/checkPassword', [UsersController::class, 'checkPassword'])
        ->name('auth.checkPassword');
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    } else {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
})->name('welcome');

Route::supportBubble();

Route::group(['middleware' => 'verified'], function () {
    // Teams...
    if (Jetstream::hasTeamFeatures()) {
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('app.teams.destroy');
    }
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    // License
    Route::get('licenses', [LicenseController::class, 'index'])
                ->name('licenses');
    Route::get('licenses/{id}', [LicenseController::class, 'getLicensebyId'])
                ->name('license');

    // Authors
    Route::post('authors/{project}', [AuthorController::class, 'updateAuthor'])
                ->name('update-author');

    //Citation
    Route::post('citation/{project}', [CitationController::class, 'updateCitation'])
    ->name('update-citation');

    Route::post('/onboarding/{status}', [DashboardController::class, 'onboardingStatus'])
            ->name('onboarding.complete');

    Route::get('projects/status/{project}/queue', [ProjectController::class, 'status'])
            ->name('project.status');

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::group([
        'prefix' => 'dashboard',
    ], function () {
        Route::get('ssubmission', [DashboardController::class, 'dashboard'])
            ->name('submission');
        Route::get('shared-with-me', [DashboardController::class, 'sharedWithMe'])
            ->name('shared-with-me');
        Route::get('starred', [DashboardController::class, 'starred'])
            ->name('starred');
        Route::get('trashed', [DashboardController::class, 'trashed'])
            ->name('trashed');
        Route::get('recent', [DashboardController::class, 'recent'])
            ->name('recent');

        Route::post('/storage/signed-draft-storage-url', [FileSystemController::class, 'signedDraftStorageURL']);
        Route::post('/storage/signed-storage-url', [FileSystemController::class, 'signedStorageURL']);
        Route::get('/storage/msdata-id/{file_id}', [FileSystemController::class, 'getMsDataId']);

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

        Route::post('projects/{project}/publish', [ProjectController::class, 'publish'])
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
        Route::post('datasets/{dataset}/preview', [DatasetController::class, 'preview'])
            ->name('dashboard.dataset.preview');

        Route::get('drafts/{draft}/files', [DraftController::class, 'files'])
            ->name('dashboard.draft.files');
        Route::delete('drafts/{draft}/files/{filesystemobject}', [DraftController::class, 'deleteFSO'])
            ->name('dashboard.draft.files.delete');
        Route::get('drafts/{draft}/annotate', [DraftController::class, 'annotate'])
            ->name('dashboard.draft.annotate');
        Route::post('drafts/{draft}/process', [DraftController::class, 'process'])
            ->name('dashboard.draft.process');
        Route::post('drafts/{draft}/complete', [DraftController::class, 'complete'])
            ->name('dashboard.draft.complete');
    });
});

Route::group([
    'prefix' => 'admin',
], function () {
    Route::group(['middleware' => ['auth', 'permission:manage roles|view statistics|manage platform']], function () {
        Route::get('console', [ConsoleController::class, 'index'])
        ->name('console');

        Route::group(['middleware' => ['permission:manage roles|manage platform']], function () {
            // Users
            Route::get('users', [UsersController::class, 'index'])
            ->name('console.users');

            Route::get('users/create', [UsersController::class, 'create'])
            ->name('console.users.create');

            Route::post('users', [UsersController::class, 'store'])
            ->name('console.users.store');

            Route::get('users/edit/{user}', [UsersController::class, 'edit'])
            ->name('console.users.edit');

            Route::put('users/edit/{user}', [UsersController::class, 'update'])
            ->name('console.users.update');

            Route::put('users/edit/{user}/password', [UsersController::class, 'updatePassword'])
            ->name('console.users.update-password');

            Route::put('users/edit/{user}/role', [UsersController::class, 'updateRole'])
            ->name('console.users.update-role');

            Route::delete('users/edit/{user}/photo', [UsersController::class, 'destroyPhoto'])
            ->name('console.users.destroy-photo');
        });

        // Adding routes for announcements section
        Route::group(['middleware' => ['auth', 'permission:manage roles|manage platform']], function () {
            // Announcements
            Route::get('announcements', [AnnouncementController::class, 'index'])
            ->name('console.announcements');

            Route::post('announcements/create', [AnnouncementController::class, 'create'])
            ->name('console.announcements.create');

            Route::post('announcements/{announcement}', [AnnouncementController::class, 'update'])
            ->name('console.announcements.edit');

            Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])
            ->name('console.announcements.destroy');
        });
    });
});

Route::get('{id}', [ApplicationController::class, 'resolveIdentifier'])->where('id', '(P|S|D|M|p|s|d|m)[0-9]+')
    ->name('public');

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

Route::get('projects/{project}/studies', [ProjectController::class, 'publicStudies'])
    ->name('project.studies');

Route::get('projects/{owner}/{slug}', [ProjectController::class, 'publicProjectView'])
    ->name('public.project');

Route::get('projects', [ProjectController::class, 'publicProjectsView'])
    ->name('public.projects');

Route::get('datasets/{dataset}/nmriumInfo', [DatasetController::class, 'fetchNMRium'])
    ->name('dashboard.datasets.nmrium');

Route::get('datasets/{owner}/{slug}', [DatasetController::class, 'publicDatasetView'])
    ->name('public.dataset');

Route::get('datasets', [DatasetController::class, 'publicDatasetsView'])
->name('public.datasets');
