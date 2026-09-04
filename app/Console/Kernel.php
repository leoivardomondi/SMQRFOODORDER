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
        // Remind admins and branch managers every 15 minutes of pending advance/future orders
        $schedule->command('app:send-future-order-reminders')->everyFifteenMinutes();

        // Send promotional emails for items on offer at 12:00 PM and 7:00 PM
        $schedule->command('app:send-daily-offer-promotions --slot=12PM')->dailyAt('12:00');
        $schedule->command('app:send-daily-offer-promotions --slot=7PM')->dailyAt('19:00');
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
