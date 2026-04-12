<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['state_id' => 1, 'name' => 'Los Angeles'],
            ['state_id' => 1, 'name' => 'San Francisco'],
            ['state_id' => 2, 'name' => 'New York City'],
            ['state_id' => 3, 'name' => 'Austin'],
        ];
        City::insert($cities);
    }
}
