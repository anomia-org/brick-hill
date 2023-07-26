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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('shop:schedule')->everyMinute()->onOneServer();
        $schedule->command('membership:bucks')->daily()->evenInMaintenanceMode()->onOneServer();
        $schedule->command('shop:special-data')->daily()->evenInMaintenanceMode()->onOneServer();
        $schedule->command('grant:classic')->daily()->onOneServer();
        $schedule->command('passport:purge')->daily()->onOneServer();
        $schedule->command('forum:cache')->daily()->evenInMaintenanceMode()->onOneServer();
        $schedule->command('trade:value-update')->daily()->evenInMaintenanceMode()->onOneServer();

        $schedule->command('log:create')->hourly()->onOneServer();

        $schedule->command('lottery:roll')->lastDayOfMonth('22:00')->evenInMaintenanceMode()->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
