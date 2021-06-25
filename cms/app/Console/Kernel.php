<?php

namespace App\Console;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
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
        'App\Console\Commands\TargetSalesmanUpd',
        'App\Console\Commands\OrderReport',
        'App\Console\Commands\PartyDetReports',
        'App\Console\Commands\EmployeeDetReports',
        'App\Console\Commands\ProductOrderDetailReport',
        'App\Console\Commands\DemoCron',
        'App\Console\Commands\UpdateLocation',
        'App\Console\Commands\AutoCheckOut',
        'App\Console\Commands\TallyOrder',
        'App\Console\Commands\TallyProduct',
        'App\Console\Commands\TallyParty',

    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $schedule->command('TargetSalesmanUpd:report')->monthlyOn(1,'01:30');
        $schedule->command('inspire')
                 ->hourly();
        $schedule->command('update:location')->everyFifteenMinutes();
        $schedule->command('order:report')->everyMinute();
        $schedule->command('product-sales:report')->everyMinute();
        $schedule->command('PartyDetails:report')->everyMinute();
        $schedule->command('EmployeeDetails:report')->everyMinute();
        $schedule->command('saleman-party-wise:report')->everyMinute();
        $schedule->command('stock:report')->everyMinute();
        $schedule->command('party-wise-return:report')->everyMinute();
        $schedule->command('product-order-detail:report')->everyMinute();
        $schedule->command('attendance:autocheckout')->hourly();
          $schedule->command('tally:order')->everyMinute();
        $schedule->command('tally:product')->hourly();
         $schedule->command('tally:party')->hourly();
        $schedule->command('queue:work --once')
        ->everyMinute();

        
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
