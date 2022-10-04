<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('import:category')->daily();
        $schedule->command('import:store')->dailyAt('0:05');
        $schedule->command('import:product')->dailyAt('0:10');
        $schedule->command('import:offer')->dailyAt('0:30');
        $schedule->command('import:offer change')->everyMinute();
        $schedule->command('import:offer stock')->everyMinute();

//        $schedule->command('import:emptyProduct')->hourly();
//        $schedule->command('export:emptyProduct description')->dailyAt('1:00');
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
