<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Coupon;

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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function() {
            $coupons = Coupon::all();
            foreach ($coupons as $coupon) {
                $expire_date = new \DateTime($coupon['expire_date']);
                $now = new \DateTime('now');
                if($expire_date <= $now) {
                    $coupon_id = $coupon['id'];
                    $coupon_obj = Coupon::find($coupon_id);
                    $coupon_obj->expired = true;
                    $coupon_obj->save();
                }
            }
        })->everyFiveMinutes();
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
