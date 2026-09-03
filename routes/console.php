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
// Staggered away from the backup dump (both defaulted to ->daily(), i.e. midnight) since this rebuilds the mols/fps RDKit tables via DROP/CREATE DDL
Schedule::command('nmrxiv:index-molecules')->dailyAt('02:00');
Schedule::command('nmrxiv:index-spectra-metadata-stats')->daily();
Schedule::command('nmrxiv:index-public-molecule-catalog')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();
Schedule::command('nmrxiv:delete-citations')->weekly();
Schedule::command('nmrxiv:delete-authors')->weekly();
if (App::environment('production')) {
    Schedule::command('nmrxiv:backup-postgres-dump')
        ->dailyAt('04:00')
        ->onOneServer()
        ->withoutOverlapping();
}
Schedule::command('nmrxiv:backup-cleanup')->monthly()->onOneServer();
Schedule::command('nmrxiv:repair-missing-compound-info')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();
