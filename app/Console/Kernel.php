<?php

namespace App\Console;

use App\Jobs\CheckCoolerWarning;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\CheckTask;
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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (env('APP_NAME') == 'Ccrp') {
            $schedule->call(function () {
                $companyIds = Company::whereHas('useSettings', function ($query) {
                    $query->where('setting_id', Company::单位设置_开启冰箱整体离线巡检)->where('value', 1);
                })->pluck('id');
                dispatch(new CheckCoolerWarning($companyIds));
            })->everyMinute();
        }

        if (env('APP_NAME') == 'Ccrp2') {
            $schedule->call(function () {
                (new CheckTask())->buildTask();
            })->monthly()->description('生成巡检单');
        }

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
