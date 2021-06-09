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
        // Commands\Inspire::class,
        \App\Console\Commands\CronMinimumStockReminder::class,
        \App\Console\Commands\CronMonitoringInvoice::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('cron:minimumStockReminder')
                ->dailyAt('08:30')
                ->timezone('Asia/Colombo');
        $schedule->command('cron:monitoringInvoice')
                ->monthlyOn(1, '08:30')
                ->timezone('Asia/Colombo');
    }
}
