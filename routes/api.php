<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\FileSystemController;
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

    Route::prefix('files')->group(function () {
        Route::get('/children/{study}/{file}', [FileSystemController::class, 'children']);
    });    
});