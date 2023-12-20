<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\Auth\VerificationController;
use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\FileSystemController;
use App\Http\Controllers\API\SubmissionController;
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
Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/logout', [LoginController::class, 'logout']);
        Route::get('/user/info', [UserController::class, 'info']);
        Route::get('/email/resend', [VerificationController::class, 'resend']);
    });
});

Route::prefix('v1')->group(function () {

    // Search
    Route::post('/search/{smiles?}', [SearchController::class, 'search']);

    Route::prefix('files')->group(function () {
        Route::get('/children/{file}', [FileSystemController::class, 'children']);
    });

    Route::get('/list/{model}', [DataController::class, 'all'])
        ->name('public.projects');

    Route::get('/{id}', [DataController::class, 'id'])
        ->name('public.project');

    Route::prefix('submission')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/create', [SubmissionController::class, 'create'])->name('submission.create');
            Route::get('/retrieve', [SubmissionController::class, 'retrieve'])->name('submission.retrieve');

            Route::get('/{eln}/{id}/{action}', [SubmissionController::class, 'relay'])->name('submission.retrieve');
        });
    });

    Route::prefix('schemas')->group(function () {
        Route::prefix('bioschemas')->group(function () {
            Route::get('/', [DataCatalogController::class, 'dataCatalogSchema'])->name('bioschemas.datacatalog');
            Route::get('/{id}', [BioschemasController::class, 'modelSchemaByID'])->name('bioschemas.id');
        });

        Route::prefix('datacite')->group(function () {
            Route::get('/{id}', [DataCiteController::class, 'modelSchemaByID']);
        });
    });
});
