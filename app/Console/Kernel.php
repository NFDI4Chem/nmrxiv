<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('nmrxiv:publish')->daily();
        $schedule->command('nmrxiv:delete-projects')->daily();
        $schedule->command('nmrxiv:index-molecules')->daily();
        $schedule->command('nmrxiv:delete-citations')->weekly();
        $schedule->command('nmrxiv:delete-authors')->weekly();
        if (App::environment('production')) {
            $schedule->command('nmrxiv:backup-postgres-dump')->daily();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
