<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stat;

class StatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imports = [
            'gift',
            'online',
         ];
 
         foreach ($imports as $import) {
              Stat::create(['slug' => $import]);
         }
    }
}
