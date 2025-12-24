<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'admin@local.test'],
            [
                'name' => 'Super Admin',
                'role' => 'admin',
                'password' => Hash::make('admin12345'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'manager@local.test'],
            [
                'name' => 'Manager',
                'role' => 'manager',
                'password' => Hash::make('manager12345'),
            ]
        );
    }
}
