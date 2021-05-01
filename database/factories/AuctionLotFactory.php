<?php

namespace Database\Factories;

use App\Models\AuctionLot;
use App\Models\Loot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AuctionLotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuctionLot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $loot = Loot::where('price', '>=', 3000)->inRandomOrder()->first();
        $price = round(($loot->price / rand(2, 12)), 2);

        return [
            'loot_id' => $loot->id,
            'price' => $price,
            'stap' => round($price / 20),
            'start' => $price,
            'updated' => Carbon::now()->timestamp,
            'finished' => Carbon::now()->addMinutes(rand(360, 1440))->timestamp
        ];
    }
}
