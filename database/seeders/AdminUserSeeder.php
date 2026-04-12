<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'contact_number' => '1234567890',
            ]
        );

        $role = Role::firstOrCreate(['name' => 'Admin']);
        if (! $user->roles()->where('name', 'Admin')->exists()) {
            $user->roles()->attach($role->id);
        }

        $this->command->info('Admin user created with Admin role: admin@admin.com / password');
    }
}
