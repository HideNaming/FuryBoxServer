<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\Box;
use App\Events\AuctionEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Events\BoxEvent;
use App\Events\Stats;
use App\Models\Feed;
use App\Models\AuctionLot;
use App\Models\AuctionRate;
use App\Models\User;

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
        $schedule->call(function () {
            while (true) {
                (new \App\Http\Controllers\API\V1\PaymentController)->handleQiwi();
                
                $boxes = Box::all();
                foreach ($boxes as $box) {
                    $box->views = ceil(rand(25, 50) / $box->price * 500);
                    $box->opens = $box->opens + rand(0, 1);
                    $box->save();
                    event(new BoxEvent($box));
                }

                AuctionLot::whereDate('created_at', Carbon::now()->subMonth())->delete();
                AuctionRate::whereDate('created_at', Carbon::now()->subMonth())->delete();

                $lot = AuctionLot::where("updated", "<=", Carbon::now()->subMinutes(rand(20, 60))->timestamp)->where('finished', '>', \Carbon\Carbon::now()->timestamp)->whereNull('user_id')->inRandomOrder()->first();

                if (!is_null($lot)) {
                    $languages = [
                        "ru_RU",
                        "en_US"
                    ];
                    $lang = $languages[array_rand($languages)];

                    $faker = \Faker\Factory::create($lang);
                    $gender = $faker->randomElement(['male', 'female']);

                    $lot->rate = $faker->firstName($gender) . ' ' . $faker->lastName($gender);
                    $lot->price = ceil($lot->price + $lot->stap);
                    $lot->updated = Carbon::now()->timestamp;
                    $lot->save();
                    event(new AuctionEvent($lot));
                }

                AuctionLot::where('finished', '<', \Carbon\Carbon::now()->timestamp)->whereNotNull('user_id')->whereNull('finish')->get()->each(function ($item) {
                    $languages = [
                        "ru_RU",
                        "en_US"
                    ];
                    $lang = $languages[array_rand($languages)];

                    $faker = \Faker\Factory::create($lang);
                    $gender = $faker->randomElement(['male', 'female']);

                    $item->rate = $faker->firstName($gender) . ' ' . $faker->lastName($gender);
                    $item->price = ceil($item->price + $item->stap);
                    $item->updated = Carbon::now()->timestamp;
                    $item->finish = 1;
                    $item->save();
                    AuctionRate::where('auction_id', $item->id)->whereNull('status')->get()->each(function ($rate) {
                        $rate->status = 'lose';
                        $rate->save();
                        $user = User::find($rate->user_id);
                        if (!is_null($user)) {
                            $user->money = $user->money + $rate->price;
                            $user->save();
                        }
                    });
                    event(new AuctionEvent($item));
                });

                AuctionLot::where('finished', '<', \Carbon\Carbon::now()->timestamp)->whereNull('user_id')->whereNull('finish')->get()->each(function ($item) {
                    $item->finish = 1;
                    $item->save();
                    event(new AuctionEvent($item));
                });

                if (AuctionLot::whereNull('finish')->count() == 0) {
                    AuctionLot::factory()->count(rand(40, 100))->create();
                }

                $feed = Feed::factory()->count(rand(1, 2))->create();
                Feed::whereDate('created_at', Carbon::yesterday()->subDays(1))->delete();
                event(new Stats($feed));
                sleep(rand(30, 60));
            }
        })->everyMinute();
        // $schedule->command('inspire')->hourly();
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
