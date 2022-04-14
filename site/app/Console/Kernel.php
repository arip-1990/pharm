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
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('import:category')->daily();
        $schedule->command('import:store')->dailyAt('0:10');
        $schedule->command('import:product')->dailyAt('0:20');
        $schedule->command('import:offer')->dailyAt('1:00');
        $schedule->command('import:offer change')->everyMinute();
        $schedule->command('import:offer stock')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
