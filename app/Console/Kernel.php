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
        Commands\ParentDashboard::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('ParentDashboard:cron')
                 ->dailyAt('3:00');
        $schedule->command('ParentPhm:cron')
                 ->dailyAt('3:01');
        $schedule->command('LookerData:cron')
                 ->dailyAt('3:02');
        $schedule->command('SnowSchema:cron')
                 ->dailyAt('3:03');
        $schedule->command('phm:cron')
                 ->everyMinute();
        $schedule->command('downloadPdf:cron')
                 ->everyFiveMinutes();
                 
        $schedule->command('Weekly:cron')
                 ->sundays();
        $schedule->command('WeeklyPDF:cron')
                 ->hourly()
                 ->days([0]);
        $schedule->command('Monthly:cron')
                ->monthly();
        $schedule->command('MonthlyPDF:cron')
                ->hourly()
                ->monthly();
        $schedule->command('Quarterly:cron')
                ->quarterly();
        $schedule->command('QuaterlyPDF:cron')
                ->hourly()
                ->quarterly();

        // $schedule->command('PatientSummary_Once:cron')
        //          ->everyFiveMinutes();
        // $schedule->command('PatientSummary_Second:cron')
        //          ->everyFiveMinutes();
        // $schedule->command('PatientSummary_Third:cron')
        //          ->everyTenMinutes();
                 
        // $schedule->command('PatientSummary_Weekly:cron')
        //          ->sundays()
        //          ->runInBackground();
        $schedule->command('PatientSummary_Monthly:cron')
                ->monthly();
        $schedule->command('PatientSummary_Quaterly:cron')
                ->quarterly();


        $schedule->command('SessionTimeoutUsers:cron')
                 ->everyMinute();
        
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
