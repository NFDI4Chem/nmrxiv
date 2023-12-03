<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\FileSystemController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\Schemas\Bioschema\BiochemaController;
use App\Http\Controllers\API\Schemas\Bioschema\DataCatalogController;
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
        Route::prefix('bioschema')->group(function () {
            Route::get('/', [DataCatalogController::class, 'dataCatalogSchema'])->name('bioschema.datacatalog');
            Route::get('/{username}/{project}/{study?}/{dataset?}', [BiochemaController::class, 'modelSchemaByName'])->name('bioschema.model');
            Route::get('/{id}', [BiochemaController::class, 'modelSchemaByID'])->name('bioschema.id');
        });

        Route::prefix('datacite')->group(function () {
            Route::get('/{username}/{project}/{study?}/{dataset?}', [DataCiteController::class, 'modelSchemaByName']);
            Route::get('/{id}', [DataCiteController::class, 'modelSchemaByID']);
        });
    });
});
