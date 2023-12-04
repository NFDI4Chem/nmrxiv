<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\FileSystemController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasController;
use App\Http\Controllers\API\Schemas\Bioschemas\DataCatalogController;
use App\Http\Controllers\API\Schemas\DataCite\DataCiteController;
use App\Http\Controllers\API\SearchController;
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

    Route::get('projects', [ProjectController::class, 'all'])
        ->name('public.projects');

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
