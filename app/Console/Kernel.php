<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\RetryFailedJobs::class,
        \App\Console\Commands\LogFailedJobs::class,
        \App\Console\Commands\CleanFailedJobs::class,
        \App\Console\Commands\CreateFailedJobsReport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('failedjobs:retry')->everyFiveMinutes();
        ///$schedule->command('failedjobs:report')->dailyAt('00:00');
        //$schedule->command('failedjobs:clean')->dailyAt('00:01');
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
