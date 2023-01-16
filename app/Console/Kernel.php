<?php

namespace App\Console;

use App\Jobs\RefreshKuliahLog;
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
        Commands\DailyRefreshKuliaLog::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->call(function () {

        //     //Pengecekan apakah cronjob berhasil atau tidak
        //     //Mencatat info log 
        //     Log::info('Cronjob berhasil dijalankan');
        // })->everyTwoMinutes();
        $schedule->job(new RefreshKuliahLog)
                ->daily()
                ->onSuccess(function() {
                    echo "Masuk on success!\n";
                })
                ->onFailure(function () {
                    echo "Masuk on failed!\n";
                });
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
