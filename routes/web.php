<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Admin\ConsoleController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\FileSystemController;
use App\Http\Controllers\StudyController;
use App\Models\Project;
use App\Http\Controllers\Admin\AnnouncementController;

Route::group([
    'prefix' => 'auth'
], function () {
    Route::get('/login/{service}', [SocialController::class, 'redirectToProvider']);
    Route::get('/login/{service}/callback', [SocialController::class, 'handleProviderCallback']);    
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::supportBubble();

Route::get('{code}/studies/{study}/file/{filename}', [StudyController::class, 'file'])
        ->name('study.file');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function (Request $request) {
    $user = $request->user();
    $team = $user->currentTeam;
    if ($team) {
        $team->users = $team->allUsers();
    }
    $projects = Project::where('owner_id', $user->id)->where('team_id', $team->id)->get();
    return Inertia::render('Dashboard', [
        'team' => $team,
        'projects' => $projects
    ]);
})->name('dashboard');

Route::get('projects/{slug}', [ProjectController::class, 'publicProjectView'])->name('public.project');
Route::get('projects', [ProjectController::class, 'publicProjectsView'])->name('public.projects');
Route::get('datasets/{slug}', [DatasetController::class, 'publicDatasetView'])->name('public.dataset');
Route::get('datasets', [DatasetController::class, 'publicDatasetsView'])->name('public.datasets');

Route::group(['middleware' => ['auth']], function () {
    Route::group([
        'prefix' => 'dashboard'
    ], function () {
        Route::post('/storage/signed-storage-url',  [FileSystemController::class, 'signedStorageURL']);
        
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
        Route::get('projects/{project}/checkIfUserHasPassword', [ProjectController::class, 'checkIfUserHasPassword'])
            ->name('dashboard.project.checkIfUserHasPassword'); 
        
        
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
    });
});

Route::group([
    'prefix' => 'admin'
], function () {
    Route::group(['middleware' => ['auth', 'permission:manage roles|view statistics|manage platform']], function () {
    
        Route::get('console', [ConsoleController::class, 'index'])
        ->name('console');

        Route::group(['middleware' => ['auth', 'permission:manage roles|manage platform']], function () {
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
