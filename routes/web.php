<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Admin\ConsoleController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\FileSystemController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\StudyInvitationController;
use App\Http\Controllers\StudyMemberController;
use App\Http\Controllers\DraftController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Inertia\Inertia;

Route::group([
    'prefix' => 'auth'
], function () {
    Route::get('/login/{service}', [SocialController::class, 'redirectToProvider']);
    Route::get('/login/{service}/callback', [SocialController::class, 'handleProviderCallback']);    
    Route::get('/checkPassword', [UsersController::class, 'checkPassword'])
        ->name('auth.checkPassword'); 
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }else{
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
})->name('welcome');

Route::supportBubble();

Route::get('download/{code}/datasets/{dataset}/{filename}', [DatasetController::class, 'download'])
    ->name('dataset.download');

Route::get('{code}/studies/{study}/file/{filename}', [StudyController::class, 'file'])
    ->name('study.file');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('projects/{slug}', [ProjectController::class, 'publicProjectView'])
    ->name('public.project');
Route::get('projects', [ProjectController::class, 'publicProjectsView'])
    ->name('public.projects');
Route::get('datasets/{slug}', [DatasetController::class, 'publicDatasetView'])
    ->name('public.dataset');
Route::get('datasets', [DatasetController::class, 'publicDatasetsView'])
    ->name('public.datasets');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::group([
        'prefix' => 'dashboard'
    ], function () {
        Route::get('shared-with-me', [DashboardController::class, 'sharedWithMe'])
            ->name('shared-with-me');
        Route::get('starred', [DashboardController::class, 'starred'])
            ->name('starred');
        Route::get('trashed', [DashboardController::class, 'trashed'])
            ->name('trashed');
        Route::get('recent', [DashboardController::class, 'recent'])
            ->name('recent');
            
        Route::post('/storage/signed-draft-storage-url',  [FileSystemController::class, 'signedDraftStorageURL']);
        Route::post('/storage/signed-storage-url',  [FileSystemController::class, 'signedStorageURL']);

        Route::get('/drafts',  [DraftController::class, 'all']);
        
        Route::get('projects/{project}', [ProjectController::class, 'show'])
            ->name('dashboard.projects');
        Route::get('projects/{project}/settings', [ProjectController::class, 'settings'])
            ->name('dashboard.project.settings');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])
            ->name('dashboard.project.destroy');
        Route::post('projects/create', [ProjectController::class, 'store'])
            ->name('dashboard.project.create');
        Route::put('projects/{project}/update', [ProjectController::class, 'update'])
            ->name('dashboard.project.update');
        Route::get('projects/{project}/activity', [ProjectController::class, 'activity'])
            ->name('dashboard.project.activity');
        Route::post('projects/{project}/toggleStarred', [ProjectController::class, 'toggleStarred'])
            ->name('projects.toggle-starred');

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

        Route::get('/study-invitations/{invitation}', [StudyInvitationController::class, 'acceptInvitation'])
            ->middleware(['signed'])
            ->name('study-invitations.accept');
        Route::delete('/study-invitations/{invitation}', [StudyInvitationController::class, 'destroyInvitation'])
            ->name('study-invitations.destroy');


        Route::post('datasets/{dataset}/nmriumInfo', [DatasetController::class, 'nmriumInfo'])
            ->name('dashboard.datasets.nmriumInfo');

        Route::get('drafts/{draft}/files', [DraftController::class, 'files'])
            ->name('dashboard.draft.files');
        Route::get('drafts/{draft}/annotate', [DraftController::class, 'annotate'])
            ->name('dashboard.draft.annotate');
        Route::post('drafts/{draft}/process', [DraftController::class, 'process'])
            ->name('dashboard.draft.process');
            Route::post('drafts/{draft}/complete', [DraftController::class, 'complete'])
            ->name('dashboard.draft.complete');
    });
});

Route::group([
    'prefix' => 'admin'
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
