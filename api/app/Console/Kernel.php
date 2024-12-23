<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /** Define the application's command schedule. */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('import:category')->daily();
        $schedule->command('import:store')->dailyAt('0:10');
        $schedule->command('import:product')->dailyAt('0:15');
        $schedule->command('import:offer')->dailyAt('0:30');

        $schedule->command('search:reindex')->dailyAt('1:00');

        $schedule->command('import:offer change')->everyFiveMinutes()->unlessBetween('0:00', '1:30');
//        $schedule->command('import:offer stock')->everyFiveMinutes()->unlessBetween('0:00', '1:00');

//        $schedule->command('order:send')->everyMinute();

//        $schedule->command('import:emptyProduct')->hourly();
//        $schedule->command('export:emptyProduct description')->dailyAt('1:00');
    }

    /** Register the commands for the application. */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
