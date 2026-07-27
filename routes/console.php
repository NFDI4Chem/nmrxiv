<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/
Artisan::command('nmrxiv', function () {
    $this->comment('Welcome to nmrXiv!');
})->purpose('Display nmrxiv info');

Schedule::command('nmrxiv:publish-embargo-projects')->daily();
Schedule::command('nmrxiv:delete-projects')->daily();
Schedule::command('nmrxiv:index-molecules')->daily();
Schedule::command('nmrxiv:index-spectra-metadata-stats')->daily();
Schedule::command('nmrxiv:delete-citations')->weekly();
Schedule::command('nmrxiv:delete-authors')->weekly();
if (App::environment('production')) {
    Schedule::command('nmrxiv:backup-postgres-dump')
        ->daily()
        ->onOneServer()
        ->withoutOverlapping();
}
Schedule::command('nmrxiv:backup-cleanup')->monthly()->onOneServer();
Schedule::command('nmrxiv:repair-missing-compound-info')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();
