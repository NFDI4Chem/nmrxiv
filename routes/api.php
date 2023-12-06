<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\DatasetController;
use App\Http\Controllers\API\FileSystemController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasController;
use App\Http\Controllers\API\Schemas\Bioschemas\DataCatalogController;
use App\Http\Controllers\API\Schemas\DataCite\DataCiteController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\StudyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// routes/api.php

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/register', [RegisterController::class, 'register']);
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/logout', [LoginController::class, 'logout']);
            Route::get('/user/info', [UserController::class, 'info']);
        });
    });

    // Search
    Route::post('/search/{smiles?}', [SearchController::class, 'search']);

    Route::prefix('files')->group(function () {
        Route::get('/children/{file}', [FileSystemController::class, 'children']);
    });

    //Public Projects
    Route::get('projects', [ProjectController::class, 'all'])
        ->name('public.projects');

    Route::get('project', [ProjectController::class, 'id'])
        ->name('public.project');

    //Public Samples
    Route::get('samples', [StudyController::class, 'all'])
        ->name('public.samples');

    Route::get('sample', [StudyController::class, 'id'])
        ->name('public.sample');

    //Public Datasets
    Route::get('datasets', [DatasetController::class, 'all'])
        ->name('public.datasets');

    Route::get('dataset', [DatasetController::class, 'id'])
        ->name('public.dataset');

    Route::prefix('schemas')->group(function () {
        Route::prefix('bioschemas')->group(function () {
            Route::get('/', [DataCatalogController::class, 'dataCatalogSchema'])->name('bioschemas.datacatalog');
            Route::get('/{username}/{project}/{study?}/{dataset?}', [BioschemasController::class, 'modelSchemaByName'])->name('bioschemas.model');
            Route::get('/{id}', [BioschemasController::class, 'modelSchemaByID'])->name('bioschemas.id');
        });

        Route::prefix('datacite')->group(function () {
            Route::get('/{username}/{project}/{study?}/{dataset?}', [DataCiteController::class, 'modelSchemaByName']);
            Route::get('/{id}', [DataCiteController::class, 'modelSchemaByID']);
        });
    });
});
