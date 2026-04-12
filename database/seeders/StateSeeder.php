<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'California'],
            ['name' => 'New York'],
            ['name' => 'Texas'],
        ];
        State::insert($states);
    }
}
