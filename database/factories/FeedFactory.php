<?php

namespace Database\Factories;

use App\Models\Feed;
use App\Models\Box;
use App\Models\Loot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Decimal;

class FeedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feed::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $languages = [
            "ru_RU",
            "en_US"
        ];
        $lang = $languages[array_rand($languages)];

        $faker = \Faker\Factory::create($lang);
        $gender = $faker->randomElement(['male', 'female']);

        return array_merge([
            'name' => $faker->firstName($gender) . ' ' . $faker->lastName($gender),
        ], $this->facker());
    }

    public function facker()
    {
        $box = Box::inRandomOrder()->first();
        $loot = Loot::where('box_id', $box->id)->inRandomOrder()->first();

        if ($loot) {
            if ((float) $loot->price < (float) $box->price) {
                return $this->facker();
            }
            return [
                "box_id" => $box->id,
                "loot_id" => $loot->id
            ];
        } else {
            return $this->facker();
        }
    }
}
