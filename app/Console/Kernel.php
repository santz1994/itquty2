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
        Commands\TestDatabaseColumns::class,
        Commands\TestViewFixes::class,
        Commands\TestAllViewFixes::class,
        Commands\TestCriticalFixes::class,
        Commands\CheckNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run notification checks every hour for overdue tickets and expiring warranties
        $schedule->command('notifications:check --overdue --warranty')->hourly();
        
        // Clean up old notifications daily at 2 AM
        $schedule->command('notifications:check --cleanup')->dailyAt('02:00');
        
        // Send daily digest to admins at 8 AM
        $schedule->command('notifications:check --digest')->dailyAt('08:00');
        // $schedule->command('migrate')->daily();
        // $schedule->command('db:seed')->daily();
    }
}
