<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Calls other seeders
        $this->call([
            StateSeeder::class,
            CitySeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
        ]);

        User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
    }
}
